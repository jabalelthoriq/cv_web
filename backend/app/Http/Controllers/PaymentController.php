<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cv;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use App\Models\JobRecommendation;
use App\Models\Payment;
class PaymentController extends Controller
{
   public function getScore($id)
{
    $cv = Cv::findOrFail($id);

    return response()->json([
        'score' => $cv->score,
        'status' => $cv->analysis['status'] ?? 'Processing'
    ]);
}
public function subscribe(Request $request)
{
    try {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Silakan login terlebih dahulu'
            ], 401);
        }

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $plan = $request->plan;

        $prices = [
            'plus' => 2000,
            'pro'  => 5000,
        ];

        if (!isset($prices[$plan])) {
            return response()->json([
                'message' => 'Plan tidak valid'
            ], 400);
        }

        $orderId = 'SUB-' . strtoupper($plan) . '-' . uniqid();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $prices[$plan],
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name ?? 'User',
                'email' => auth()->user()->email ?? 'user@mail.com',
            ],
            'item_details' => [
                [
                    'id' => $plan,
                    'price' => $prices[$plan],
                    'quantity' => 1,
                    'name' => strtoupper($plan) . ' PLAN',
                ]
            ]
        ];

        Payment::create([
    'order_id' => $orderId,
    'user_id' => auth()->id(),
    'type' => 'subscription',
    'plan' => $plan,
    'status' => 'pending'
]);
        // 🔥 ambil snap token
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        if (!$snapToken) {
            throw new \Exception('Snap token gagal dibuat');
        }

        return response()->json([
            'snap_token' => $snapToken,
            'order_id' => $orderId
        ]);

    } catch (\Exception $e) {
        \Log::error('MIDTRANS ERROR: ' . $e->getMessage());

        return response()->json([
            'message' => 'Gagal membuat pembayaran',
            'error' => $e->getMessage()
        ], 500);
    }
}

private function processSubscription($payment)
{
    $userId = $payment->user_id;

    if (!$userId) {
        \Log::error("❌ user_id kosong di payment");
        return;
    }

    // 🔥 tentukan durasi
    $duration = match ($payment->plan) {
        'plus' => now()->addMonth(),
        'pro'  => now()->addMonths(3),
        default => now()->addMonth()
    };

    // cek subscription aktif
    $existing = \App\Models\Subscription::where('user_id', $userId)
        ->where('status', 'active')
        ->first();

    if ($existing) {

        // 🔥 kalau masih aktif → extend dari expired_at
        $newExpired = $existing->expired_at > now()
            ? \Carbon\Carbon::parse($existing->expired_at)->add(
                $payment->plan === 'pro' ? 3 : 1,
                'month'
              )
            : $duration;

        $existing->update([
            'plan' => $payment->plan,
            'expired_at' => $newExpired
        ]);

    } else {

        \App\Models\Subscription::create([
            'user_id' => $userId,
            'plan' => $payment->plan,
            'started_at' => now(),
            'expired_at' => $duration,
            'status' => 'active'
        ]);
    }

    $payment->update([
        'status' => 'paid',
        'paid_at' => now(),
    ]);

    \Log::info("✅ Subscription aktif user {$userId} => {$payment->plan}");
}

public function confirmSubscription(Request $request)
{
    $request->validate([
        'order_id' => 'required|string',
    ]);

    $payment = Payment::where('order_id', $request->order_id)
        ->where('user_id', auth()->id())
        ->where('type', 'subscription')
        ->first();

    if (!$payment) {
        return response()->json([
            'message' => 'Payment subscription tidak ditemukan'
        ], 404);
    }

    if ($payment->status === 'paid') {
        return response()->json([
            'status' => 'success',
            'plan' => $payment->plan
        ]);
    }

    try {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;

        $status = (array) \Midtrans\Transaction::status($payment->order_id);
        $transactionStatus = $status['transaction_status'] ?? null;
        $fraudStatus = $status['fraud_status'] ?? null;

        if ($transactionStatus === 'settlement' ||
            ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
            $this->processSubscription($payment);

            return response()->json([
                'status' => 'success',
                'plan' => $payment->plan
            ]);
        }

        if (in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
            $payment->update([
                'status' => $transactionStatus === 'expire' ? 'expired' : 'cancelled'
            ]);
        }

        return response()->json([
            'status' => $transactionStatus ?: 'pending'
        ], 202);

    } catch (\Exception $e) {
        \Log::error('MIDTRANS SUBSCRIPTION CONFIRM ERROR: ' . $e->getMessage());

        return response()->json([
            'message' => 'Gagal konfirmasi pembayaran',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function createPayment($cv_id)
{
    $cv = Cv::find($cv_id);

    if (!$cv) {
        return response()->json([
            'message' => 'CV tidak ditemukan'
        ], 404);
    }

    \Midtrans\Config::$serverKey = config('midtrans.server_key');
    \Midtrans\Config::$isProduction = false;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    $order_id = 'CV-' . $cv->id . '-' . time();

    // simpan ke DB
    Payment::create([
        'order_id' => $order_id,
        'cv_id' => $cv->id,
        'type' => 'cv',
        'status' => 'pending'
    ]);

    $params = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => 10000
        ],
        'customer_details' => [
            'first_name' => $cv->name ?? 'Customer',
            'email' => $cv->email ?? 'guest@careersense.com',
        ],
        'callbacks' => [
    'finish' => url('/payment/finish')
]
        
    ];

    $snapToken = \Midtrans\Snap::getSnapToken($params);

    return response()->json([
        'snap_token' => $snapToken,
        'order_id' => $order_id
    ]);
}

   public function callback(Request $request)
{
    \Log::info('=== MIDTRANS CALLBACK MASUK ===');
    \Log::info($request->all());

    // config midtrans
    \Midtrans\Config::$serverKey = config('midtrans.server_key');

    $serverKey = config('midtrans.server_key');

    // ambil data
    $order_id = $request->order_id;
    $status_code = $request->status_code;
    $gross_amount = $request->gross_amount;
    $signature_key = $request->signature_key;

    // validasi signature
    $expectedSignature = hash(
        'sha512',
        $order_id . $status_code . $gross_amount . $serverKey
    );

    if ($expectedSignature !== $signature_key) {
        \Log::error('❌ Invalid signature dari Midtrans');
        return response()->json(['message' => 'Invalid signature'], 403);
    }

    // ambil status
    $transactionStatus = $request->transaction_status;
    $fraudStatus = $request->fraud_status ?? null;

    \Log::info("STATUS: $transactionStatus | FRAUD: $fraudStatus");

    // ambil payment dari DB
    $payment = Payment::where('order_id', $order_id)->first();

    if (!$payment) {
        \Log::warning("Payment notification ignored, order tidak ditemukan: $order_id");
        return response()->json(['status' => 'ignored', 'reason' => 'order_not_found']);
    }

    // 🛡️ idempotent (hindari double update)
    if ($payment->status === 'paid') {
        \Log::info("⚠️ Sudah pernah diproses: $order_id");
        return response()->json(['status' => 'already_processed']);
    }

    // handle status
    if (in_array($transactionStatus, ['capture', 'settlement'])) {

    // khusus capture harus cek fraud
    if ($transactionStatus == 'capture' && $fraudStatus !== 'accept') {
        \Log::info("❌ Fraud detected: $order_id");
        return response()->json(['status' => 'fraud']);
    }

    // 🔥 bedakan type payment
    if ($payment->type === 'cv') {
        $this->processSuccess($payment);

    } elseif ($payment->type === 'subscription') {
        $this->processSubscription($payment);
    }

} elseif ($transactionStatus == 'pending') {

    \Log::info("⏳ Pending: $order_id");

} elseif ($transactionStatus == 'expire') {

    $payment->update(['status' => 'expired']);

} elseif ($transactionStatus == 'cancel') {

    $payment->update(['status' => 'cancelled']);
}

    return response()->json(['status' => 'ok']);
}

private function processSuccess($payment)
{
    $cv = Cv::with('jobRecommendations')->find($payment->cv_id);

    if (!$cv) {
        \Log::error("❌ CV tidak ditemukan: " . $payment->cv_id);
        return;
    }

    $premiumLink = URL::temporarySignedRoute(
        'premium.result',
        now()->addHours(6),
        ['payment' => $payment->id],
        false
    );

    $payment->update([
        'status' => 'paid',
        'result_link' => $premiumLink
    ]);

    \Log::info("✅ Payment SUCCESS: " . $payment->order_id);
}

    public function getResultLink($order_id)
    {
        $payment = Payment::where('order_id', $order_id)->first();

        if (!$payment) {
            return response()->json([
                'status' => 'not_found'
            ], 404);
        }

        if ($payment->status !== 'paid') {
            $this->syncCvPaymentStatusFromMidtrans($payment);
            $payment->refresh();
        }

        if ($payment->status === 'paid' && $payment->type === 'cv' && $this->needsFreshPremiumLink($payment)) {
            $this->processSuccess($payment);
            $payment->refresh();
        }

        if ($payment->status !== 'paid' || empty($payment->result_link)) {
            return response()->json([
                'status' => 'waiting'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'link' => $payment->result_link
        ]);
    }

    private function syncCvPaymentStatusFromMidtrans(Payment $payment): void
    {
        if ($payment->type !== 'cv') {
            return;
        }

        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $status = (array) \Midtrans\Transaction::status($payment->order_id);
            $transactionStatus = $status['transaction_status'] ?? null;
            $fraudStatus = $status['fraud_status'] ?? null;

            if ($transactionStatus === 'settlement' ||
                ($transactionStatus === 'capture' && in_array($fraudStatus, [null, 'accept'], true))) {
                $this->processSuccess($payment);
                return;
            }

            if ($transactionStatus === 'expire') {
                $payment->update(['status' => 'expired']);
                return;
            }

            if (in_array($transactionStatus, ['cancel', 'deny'], true)) {
                $payment->update(['status' => 'cancelled']);
            }
        } catch (\Throwable $e) {
            \Log::warning("Gagal sync status Midtrans untuk {$payment->order_id}: {$e->getMessage()}");
        }
    }

    private function needsFreshPremiumLink(Payment $payment): bool
    {
        return empty($payment->result_link)
            || str_contains($payment->result_link, 'token=')
            || strlen($payment->result_link) > 1900;
    }

    public function showResult(Request $request, Payment $payment)
    {
        if (!$request->hasValidSignature(false)) {
            abort(403, 'Invalid signature.');
        }

        $payment = Payment::with([
            'cv.jobRecommendations' => fn ($query) => $query->orderByDesc('match_score'),
        ])->findOrFail($payment->id);

        if ($payment->status !== 'paid' || $payment->type !== 'cv') {
            abort(403, 'Akses premium belum aktif.');
        }

        $cv = $payment->cv;

        if (!$cv) {
            abort(404, 'Data CV tidak ditemukan.');
        }

        $topJob = $this->topMatchedJobForCv($cv);

        $data = [
            'score' => $cv->score ?? 0,
            'analysis' => $cv->analysis ?? [],
            'file_name' => $cv->file_path ? basename($cv->file_path) : 'CV',
            'job_tema' => $cv->job_tema,
            'job' => [
                'title' => $topJob?->job_title ?? null,
                'company' => $topJob?->company ?? null,
                'match_score' => $topJob?->match_score,
                'link' => $topJob?->job_link,
                'job_tema' => $topJob?->job_tema ?? $cv->job_tema,
            ],
        ];

        return view('premium.result', compact('data'));
    }

    private function topMatchedJobForCv(Cv $cv): ?JobRecommendation
    {
        $topJob = $cv->jobRecommendations
            ->sortByDesc(fn (JobRecommendation $job) => (int) $job->match_score)
            ->first();

        if ($topJob) {
            return $topJob;
        }

        if (!$cv->user_id) {
            return null;
        }

        return JobRecommendation::query()
            ->where('user_id', $cv->user_id)
            ->where(function ($query) use ($cv) {
                $query->where('cv_id', $cv->id)
                    ->orWhereNull('cv_id');
            })
            ->orderByDesc('match_score')
            ->first();
    }
    
}
