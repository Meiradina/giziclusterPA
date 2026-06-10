<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIRecipeController extends Controller
{
    public function index()
    {
        return view('ai-recipe');
    }

    public function generate(Request $request)
    {
        // Validasi input
        $request->validate([
            'bahan' => 'required|string|max:255',
        ], [
            'bahan.required' => 'Silakan masukkan bahan terlebih dahulu.',
        ]);

        $bahan = trim($request->input('bahan'));

        $prompt = "
        Kamu adalah AI rekomendasi masakan Indonesia.

        ATURAN:
        - gunakan nama masakan Indonesia yang benar-benar ada
        - jangan mengarang nama makanan
        - gunakan masakan rumahan yang realistis
        - maksimal 3 rekomendasi
        - penjelasan singkat saja

        Format:
        1. Nama masakan
        - bahan tambahan:
        - penjelasan:

        Bahan tersedia:
        $bahan
        ";

        $response = Http::timeout(180)->post(
            'http://127.0.0.1:11434/api/generate',
            [
                'model' => 'qwen2.5:3b',
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.3,
                    'num_predict' => 300,
                ],
            ]
        );

        if (!$response->successful()) {

            return redirect()
                ->route('ai.index')
                ->withErrors([
                    'ai' => 'Gagal mendapatkan respons dari AI.',
                ])
                ->withInput();
        }

        $hasil = $response->json('response') ?? 'Tidak ada hasil.';

        return redirect()
            ->route('ai.index')
            ->with('hasil', $hasil)
            ->withInput();
    }
}