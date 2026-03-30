<?php

namespace App\Services;

class FoodDataService
{
    public function getAllFoods(): array
    {
        return [
            // Protein Tinggi
            ['id' => 1,  'nama' => 'Ayam Rebus',        'kategori' => 'Hewani',   'kalori' => 165, 'protein' => 31.0, 'karbohidrat' => 0.0,  'lemak' => 3.6,  'serat' => 0.0, 'zat_besi' => 1.3, 'kalsium' => 15,  'vitamin_c' => 0],

            // Tinggi Karbohidrat
            ['id' => 2, 'nama' => 'Nasi Putih',         'kategori' => 'Karbohidrat','kalori' => 130,'protein' => 2.7, 'karbohidrat' => 28.0, 'lemak' => 0.3,  'serat' => 0.4, 'zat_besi' => 0.2, 'kalsium' => 10,  'vitamin_c' => 0],

            // Sayuran dan Buah - Tinggi Serat & Vitamin
            ['id' => 3, 'nama' => 'Bayam',              'kategori' => 'Sayuran',  'kalori' => 23,  'protein' => 2.9,  'karbohidrat' => 3.6,  'lemak' => 0.4,  'serat' => 2.2, 'zat_besi' => 2.7, 'kalsium' => 99,  'vitamin_c' => 28],

            // Seimbang
            ['id' => 4, 'nama' => 'Kacang Merah',       'kategori' => 'Nabati',   'kalori' => 127, 'protein' => 8.7,  'karbohidrat' => 22.8, 'lemak' => 0.5,  'serat' => 7.4, 'zat_besi' => 2.9, 'kalsium' => 28,  'vitamin_c' => 1],
        ];
    }

    public function getClusteredFoods(): array
    {
        $foods = $this->getAllFoods();
        $kmeans = new KMeansService(4);
        return $kmeans->cluster($foods);
    }

    public function getStats(): array
    {
        $foods = $this->getClusteredFoods();
        $clusterCounts = array_count_values(array_column($foods, 'cluster'));
        ksort($clusterCounts);

        return [
            'total_makanan' => count($foods),
            'total_cluster' => 4,
            'cluster_counts' => $clusterCounts,
            'avg_kalori' => round(array_sum(array_column($foods, 'kalori')) / count($foods), 1),
            'avg_protein' => round(array_sum(array_column($foods, 'protein')) / count($foods), 1),
        ];
    }
}