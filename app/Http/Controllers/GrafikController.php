<?php

namespace App\Http\Controllers;

use App\Services\FoodDataService;
use App\Services\KMeansService;

class GrafikController extends Controller
{
    public function __construct(private FoodDataService $foodService) {}

    public function index()
    {
        return view('grafik');
    }

    public function getData()
    {
        $foods = $this->foodService->getClusteredFoods();
        $kmeans = new KMeansService(4);

        $scatterData = array_map(fn($f) => [
            'x' => $f['protein'],
            'y' => $f['kalori'],
            'nama' => $f['nama'],
            'cluster' => $f['cluster'],
            'label' => $f['cluster_label'],
            'color' => $f['cluster_color'],
        ], $foods);

        // Bar chart: avg nutrients per cluster
        $barData = [];
        for ($i = 0; $i < 4; $i++) {
            $clusterFoods = array_filter($foods, fn($f) => $f['cluster'] === $i);
            $count = count($clusterFoods);
            if ($count > 0) {
                $barData[] = [
                    'cluster' => $kmeans->getClusterLabel($i),
                    'color' => $kmeans->getClusterColor($i),
                    'protein' => round(array_sum(array_column($clusterFoods, 'protein')) / $count, 1),
                    'karbohidrat' => round(array_sum(array_column($clusterFoods, 'karbohidrat')) / $count, 1),
                    'lemak' => round(array_sum(array_column($clusterFoods, 'lemak')) / $count, 1),
                    'kalori' => round(array_sum(array_column($clusterFoods, 'kalori')) / $count, 1),
                    'serat' => round(array_sum(array_column($clusterFoods, 'serat')) / $count, 1),
                    'zat_besi' => round(array_sum(array_column($clusterFoods, 'zat_besi')) / $count, 1),
                    'kalsium' => round(array_sum(array_column($clusterFoods, 'kalsium')) / $count, 1),
                ];
            }
        }

        return response()->json([
            'scatter' => array_values($scatterData),
            'bar' => $barData,
        ]);
    }
}