<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('landingpage');
})->name('home');


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [
    AuthController::class,
    'login'
])->name('login.submit');

Route::get('/forgot-password', [
    AuthController::class,
    'showForgotPasswordForm'
])->middleware('guest')->name('password.request');

Route::post('/forgot-password', [
    AuthController::class,
    'sendResetLinkEmail'
])->middleware('throttle:5,1')->name('password.email');

Route::get('/reset-password/{token}', [
    AuthController::class,
    'showResetPasswordForm'
])->name('password.reset');

Route::post('/reset-password', [
    AuthController::class,
    'resetPassword'
])->middleware('throttle:5,1')->name('password.update');


Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', [
    AuthController::class,
    'register'
])->name('register.submit');


Route::post('/logout', [
    AuthController::class,
    'logoutWeb'
])->name('logout');

Route::patch('/profile', [
    AuthController::class,
    'updateProfile'
])->middleware('auth')->name('profile.update');

Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [AuthController::class, 'googleRedirect'])
        ->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])
        ->name('auth.google.callback');
});


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
|
| Dashboard tetap tanpa auth middleware
| karena auth dicek langsung di controller
|
*/

Route::get('/dashboard', [
    DashboardController::class,
    'index'
])->name('dashboard');

Route::patch('/admin/users/{targetUser}', [
    DashboardController::class,
    'updateAdminUser'
])->name('admin.users.update');

Route::patch('/admin/payments/{payment}', [
    DashboardController::class,
    'updateAdminPayment'
])->name('admin.payments.update');


/*
|--------------------------------------------------------------------------
| CV ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/upload-cv', [
    CvController::class,
    'uploadCV'
])->name('cv.upload');

Route::get('/cv/{id}/score', [
    PaymentController::class,
    'getScore'
]);


/*
|--------------------------------------------------------------------------
| INTERVIEW ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/submit-interview', [
    InterviewController::class,
    'submit'
]);

Route::get('/interviews/by-cv/{cvId}', function ($cvId) {
    return \App\Models\Interview::where('cv_id', $cvId)
        ->latest()
        ->take(10)
        ->get();
});
Route::post('/generate-interview', [
    InterviewController::class,
    'generate'
]);

Route::post('/interview/transcribe', [
    InterviewController::class,
    'transcribe'
]);


/*
|--------------------------------------------------------------------------
| TEST ROUTES (OPTIONAL)
|--------------------------------------------------------------------------
*/

Route::get('/test-jobs', function () {
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'message' => 'Unauthorized'
        ], 401);
    }

    $jobData = \App\Models\JobRecommendation::where(
        'user_id',
        $user->id
    )->get();

    return response()->json([
        'count' => $jobData->count(),
        'data' => $jobData
    ]);
});


Route::get('/test-jobs-view', function () {
    $user = Auth::user();

    if (!$user) {
        abort(401);
    }

    $jobData = \App\Models\JobRecommendation::where(
        'user_id',
        $user->id
    )->get()->map(function ($job) {
        return [
            'job_title' => $job->job_title ?? '-',
            'company' => $job->company ?? 'Unknown Company',
            'match_score' => $job->match_score ?? 0,
            'tags' => [],
            'link' => $job->job_link ?? null,
        ];
    });

    return view('jobs_test', [
        'jobData' => $jobData
    ]);
})->middleware('auth');


/*
|--------------------------------------------------------------------------
| PAYMENT ROUTES (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| MIDTRANS CALLBACK
|--------------------------------------------------------------------------
|
| WAJIB TANPA AUTH
| karena Midtrans server yang akses
|
*/

Route::post('/payment/callback', [PaymentController::class, 'callback']);

Route::get('/payment/finish', function () {
    return view('payment_finish');
});

    /*
    |--------------------------------------------------------------------------
    | Create Midtrans Snap Token
    |--------------------------------------------------------------------------
    */

    Route::post('/payment/{cv_id}', [
        PaymentController::class,
        'createPayment'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Frontend Polling Result Link
    |--------------------------------------------------------------------------
    */

    Route::get('/payment/result/{order_id}', [
        PaymentController::class,
        'getResultLink'
    ]);


Route::post('/subscribe', [PaymentController::class, 'subscribe']);
Route::post('/subscription/confirm', [PaymentController::class, 'confirmSubscription'])
    ->middleware('auth');


/*
|--------------------------------------------------------------------------
| PREMIUM RESULT PAGE
|--------------------------------------------------------------------------
*/

Route::get('/premium/result/{payment}', [
    PaymentController::class,
    'showResult'
])
->middleware('signed:relative')
->name('premium.result');
