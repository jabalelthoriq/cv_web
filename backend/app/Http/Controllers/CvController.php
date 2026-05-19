<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Tambahkan ini
use Illuminate\Support\Facades\Log;  // Untuk mencatat error jika FastAPI mati

class CvController extends Controller
{
    public function uploadCV(Request $request)
{
    $request->validate([
        'cv_file' => 'required|mimes:pdf|max:2048',
    ]);

    try {
        $file = $request->file('cv_file');

        $userId = auth()->id(); // null kalau guest
        $sessionId = session()->getId();

        // Hitung jumlah CV (user atau guest)
        $count = Cv::when($userId, function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->when(!$userId, function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
            })
            ->count();

        // Nama file unik (hindari overwrite)
        $fileName = 'cv_' . time() . '_' . ($count + 1) . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('cv_uploads', $fileName, 'public');

        $cv = Cv::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'file_path' => $path,
            'score' => null, 
            'analysis' => [
                'status' => 'processing',
                'message' => 'AI sedang menganalisis...'
            ],
        ]);

        // Trigger FastAPI (async)
        try {
            Http::timeout(10)->post('http://ai:8000/analyze', [
                'cv_id' => $cv->id,
                'file_path' => $path,
            ]);
        } catch (\Exception $e) {
            Log::warning('FastAPI error: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'data' => $cv
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
}