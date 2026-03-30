<?php

namespace App\Http\Controllers;

use App\Services\FoodDataService;
use App\Services\KMeansService;

class InsightController extends Controller
{
    public function __construct(private FoodDataService $foodService) {}

    public function index()
    {
        $foods = $this->foodService->getClusteredFoods();
        $kmeans = new KMeansService(4);

        $clusters = [];
        for ($i = 0; $i < 4; $i++) {
            $clusterFoods = array_values(array_filter($foods, fn($f) => $f['cluster'] === $i));
            $clusters[] = [
                'id' => $i,
                'label' => $kmeans->getClusterLabel($i),
                'color' => $kmeans->getClusterColor($i),
                'description' => $kmeans->getClusterDescription($i),
                'foods' => $clusterFoods,
                'count' => count($clusterFoods),
            ];
        }

        return view('insight', compact('clusters'));
    }
}