<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FoodDataService;
use App\Models\FoodImage;

class RekomendasiController extends Controller
{
    public function rekomendasi(Request $request, FoodDataService $service)
    {
        try {

            $tujuan = $request->tujuan;
            $userKategori = $request->kategori;

            if ($tujuan === 'mbg') {

                return response()->json([
                    'type' => 'mbg',
                    'data' => $service->generateMBGMenu($userKategori)
                ]);
            }

            return response()->json([
                'type' => 'stunting',
                'data' => $service->generateStuntingMenu()
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }
}