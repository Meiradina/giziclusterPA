<?php

namespace App\Http\Controllers;

use App\Services\FoodDataService;

class DashboardController extends Controller
{
    public function __construct(private FoodDataService $foodService) {}

    public function landing()
    {
        return view('landingpage');
    }

    public function index()
    {
        $stats = $this->foodService->getStats();
        $foods = $this->foodService->getClusteredFoods();

        $clusterDistribution = [];
        for ($i = 0; $i < 4; $i++) {
            $clusterFoods = array_filter($foods, fn($f) => $f['cluster'] === $i);
            $kmeans = new \App\Services\KMeansService(4);
            $clusterDistribution[] = [
                'label' => $kmeans->getClusterLabel($i),
                'color' => $kmeans->getClusterColor($i),
                'count' => count($clusterFoods),
            ];
        }

        return view('dashboard', compact('stats', 'clusterDistribution', 'foods'));
    }
}