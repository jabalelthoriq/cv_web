<?php

namespace App\Http\Controllers;

use App\Models\Cv; // Pastikan Model Cv sudah dibuat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Untuk hit API NLP
use Smalot\PdfParser\Parser; // Untuk ekstrak PDF
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    // public function uploadCV(Request $request)
    // {
    //     // 1. Validasi input
    //     $request->validate([
    //         'cv_file' => 'required|mimes:pdf|max:2048',
    //     ]);

    //     try {
    //         if ($request->file('cv_file')) {
    //             // 2. Simpan file fisik ke storage/app/public/cv_uploads
    //             $file = $request->file('cv_file');
    //             $path = $file->store('cv_uploads', 'public');

    //             // 3. Ekstrak teks dari PDF
    //             $parser = new Parser();
    //             $pdf = $parser->parseFile(storage_path('app/public/' . $path));
    //             $text = $pdf->getText();

    //             // 4. Kirim teks ke Model NLP Anda (Contoh jika menggunakan Flask)
    //             // Ganti URL sesuai dengan endpoint model NLP Anda
    //             $response = Http::post('http://127.0.0.1:5000/analyze', [
    //                 'text' => $text
    //             ]);

    //             $analysisResult = $response->json();

    //             // 5. Simpan data ke Database (Tabel cvs)
    //             $cv = Cv::create([
    //                 'user_id'   => auth()->id(), // Mengambil ID user yang login
    //                 'file_path' => $path,
    //                 'score'     => $analysisResult['score'] ?? 0,
    //                 'analysis'  => $analysisResult, // Array otomatis jadi JSON karena Casting di Model
    //             ]);

    //             return response()->json([
    //                 'status'  => 'success',
    //                 'message' => 'CV berhasil dianalisis',
    //                 'data'    => $cv
    //             ], 200);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status'  => 'error',
    //             'message' => 'Gagal memproses CV: ' . $e->getMessage()
    //         ], 500);
    //     }

    //     return response()->json(['message' => 'File tidak ditemukan'], 400);
    // }

   public function uploadCV(Request $request)
    {
        $request->validate([
            'cv_file' => 'required|mimes:pdf|max:2048',
        ]);

        try {
            if ($request->file('cv_file')) {
                $file = $request->file('cv_file');
                $user = Auth::user();

                // 1. Hitung jumlah CV yang sudah diupload user ini untuk menentukan nomor urut
                // Jika ingin urutan global (semua user), hapus 'where('user_id', ...)'
                $count = Cv::where('user_id', $user->id)->count();
                $nextNumber = $count + 1;

                // 2. Tentukan nama file baru (contoh: cv1.pdf, cv2.pdf)
                $fileName = 'cv' . $nextNumber . '.' . $file->getClientOriginalExtension();
                
                // 3. Simpan file dengan nama khusus ke folder 'cv_uploads'
                $path = $file->storeAs('cv_uploads', $fileName, 'public');

                // 4. Ekstrak Teks dari PDF
                // Ekstrak teks
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile(storage_path('app/public/' . $path));
                $text = $pdf->getText();

                // MEMBERSIHKAN TEKS (Agar aman dari karakter aneh)
                // Menghapus karakter non-printable dan simbol aneh
                $cleanText = preg_replace('/[[:cntrl:]]/', ' ', $text); 
                $cleanText = iconv('UTF-8', 'UTF-8//IGNORE', $cleanText); // Buang karakter non-UTF8



                // 5. Simpan ke Database
                $cv = Cv::create([
                    'user_id'   => $user->id,
                    'file_path' => $path,
                    'score'     => 0,
                    'analysis'  => [
                        'raw_text_debug' => $cleanText,
                        'status' => 'Pending AI Analysis'
                    ],
                ]);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Berhasil diunggah sebagai ' . $fileName,
                    'data'    => $cv
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }
}