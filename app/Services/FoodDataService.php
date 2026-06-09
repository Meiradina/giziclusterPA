<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\FoodImage;

class FoodDataService
{
    public function getClusteredFoods()
    {
        $response = Http::get('http://127.0.0.1:5000/cluster');

        if ($response->successful()) {
           return array_map(function ($item) {
            $cluster = (int) ($item['cluster'] ?? 0);
            // mapping kategori berdasarkan cluster
            $kategoriMap = [
                0 => 'Seimbang',
                1 => 'Tinggi Karbohidrat',
                2 => 'Rendah Nutrisi',
                3 => 'Tinggi Energi & Protein',
            ];

            $nama = strtolower(trim($item['Menu'] ?? '-'));

            $bahan_keywords = [

                'tepung',
                'minyak',
                'gula',
                'garam',
                'kemiri',
                'mentega',
                'bumbu',
                'beras',
                'mie soun',
                'soun',
                'tepung roti',
                'maizena',
                'kanji',
                'terigu',
                'penyedap',
                'kaldu',
                'santan mentah',
                'kelapa parut',
                'air',
                'cuka',
                'saus',
                'kecap',
                'cabai rawit',
                'bawang',
                'jahe',
                'kunyit',
                'lengkuas',
            ];

            $isBahan = false;

            if (
                str_contains($nama, 'mentah') ||
                str_contains($nama, 'kering')
            ) {
                $isBahan = true;
            }
            foreach ($bahan_keywords as $k) {
                if (str_contains($nama, $k)) {
                    $isBahan = true;
                    break;
                }
            }

            $kategori_data = $isBahan ? 'bahan' : 'makanan';
            // =========================
            // 🔥 RULE KLASIFIKASI FINAL
            // =========================
            $jenis = match (true) {

                // 🍚 POKOK (HARUS DIAWAL)
                str_starts_with($nama, 'nasi') ||
                str_starts_with($nama, 'mie') ||
                str_starts_with($nama, 'mi ') ||
                str_starts_with($nama, 'lontong') ||
                str_starts_with($nama, 'ketupat') => 'pokok',

                // 🥬 SAYUR (prioritas tinggi biar ga ketukar buah)
                str_contains($nama, 'sayur') ||
                str_contains($nama, 'bayam') ||
                str_contains($nama, 'kangkung') ||
                str_contains($nama, 'wortel') ||
                str_contains($nama, 'daun') ||
                str_contains($nama, 'tumis') ||
                str_contains($nama, 'sop') ||
                str_contains($nama, 'bening') => 'sayur',

                // 🍎 BUAH
                str_contains($nama, 'apel') ||
                str_contains($nama, 'pisang') ||
                str_contains($nama, 'jeruk') ||
                str_contains($nama, 'pepaya') ||
                str_contains($nama, 'mangga') ||
                str_contains($nama, 'semangka') => 'buah',

                // 🍗 HEWANI
                str_contains($nama, 'ayam') ||
                str_contains($nama, 'ikan') ||
                str_contains($nama, 'daging') ||
                str_contains($nama, 'telur') ||
                str_contains($nama, 'hati') => 'hewani',

                // 🥜 NABATI
                str_contains($nama, 'tahu') ||
                str_contains($nama, 'tempe') ||
                str_contains($nama, 'kacang') => 'nabati',

                str_contains($nama, 'susu') ||
                str_contains($nama, 'ultramilk') => 'susu',
                default => 'lainnya',
            };

            return [
                'nama' => $item['Menu'] ?? '-',
                'kalori' => ($item['Energy (kJ)'] ?? 0) * 0.239,
                'protein' => $item['Protein (g)'] ?? 0,
                'lemak' => $item['Fat (g)'] ?? 0,
                'karbohidrat' => $item['Carbohydrates (g)'] ?? 0,
                'serat' => $item['Dietary Fiber (g)'] ?? 0,
                'kalsium' => $item['Calcium (mg)'] ?? 0,
                'vit_c' => $item['Vitamin C (mg)'] ?? $item['Vitamin C(mg)'] ?? 0,
                'zat_besi' => $item['Iron (mg)'] ?? $item['Iron(mg)'] ?? 0,

                    // cluster asli (untuk logika)
                    'cluster' => $cluster,

                    // untuk tampilan (biar 1–4, bukan 0–3)
                    'cluster_display' => $cluster + 1,

                    // kategori hasil interpretasi
                    'kategori' => $kategoriMap[$cluster] ?? 'Cluster ' . ($cluster + 1),

                    // label siap tampil
                    'cluster_label' => 'Cluster ' . ($cluster + 1) . ' - ' . ($kategoriMap[$cluster] ?? ''),
                    'jenis' => $jenis,
                    'kategori_data' => $kategori_data,
            ];
        }, $response->json());
        }

        return [];
    }

    public function generateMBGMenu($userKategori)
    {
        $foods = collect($this->getClusteredFoods());

        // =========================
        // FILTER DATA
        // =========================
        $foods = $foods->filter(function ($item) {

            $nama = strtolower($item['nama']);

            if (
                str_contains($nama, 'mentah') ||
                str_contains($nama, 'minyak') ||
                str_contains($nama, 'bumbu')
            ) {
                return false;
            }

            return true;
        });

        // =========================
        // KEBUTUHAN GIZI
        // =========================
        $kebutuhan = [

            'anak' => [
                'kalori' => 1675,
                'protein' => 39,
                'karbohidrat' => 255,
                'lemak' => 57,
                'serat' => 24,
            ],

            'remaja' => [
                'kalori' => 2300,
                'protein' => 69,
                'karbohidrat' => 337,
                'lemak' => 76,
                'serat' => 32,
            ],

            'ibu' => [
                'kalori' => 2513,
                'protein' => 78,
                'karbohidrat' => 393,
                'lemak' => 62,
                'serat' => 36,
            ],
        ];

        $target = $kebutuhan[$userKategori];

        // =========================
        // KELOMPOK MAKANAN
        // =========================
        $pokok = [
            'Nasi Putih',
            'Nasi Uduk',
            'Nasi Tim Ayam',
            'Nasi Liwet',
            'Nasi Merah',
            'Spaghetti',
            'Bihun Goreng',
        ];

        $hewani = [
            'Daging Ayam Goreng',
            'Ikan Bandeng',
            'Ikan Goreng',
            'Telur Dadar',
            'Ayam Goreng Kalasan, Paha',
            'Chicken Teriyaki',
            'Beef Teriyaki',
            'Beef Yakiniku',
        ];

        $nabati = [
            'Tempe Goreng',
            'Tempe Bacem',
            'Tahu Goreng',
            'Tahu',
            'Tahu Bakso',
        ];

        $sayur = [
            'Sayur Bayam',
            'Sayur Kangkung',
            'Sayur Sop',
            'Cap Cai / Capcay / Cap Cay, Sayur',
            'Bayam Kukus',
            'Tumis Bayam Bersantan',
        ];

        $buah = [
            'Pisang Ambon',
            'Pisang Kepok',
            'Jeruk Manis',
            'Jeruk Bali',
            'Apel Malang, Segar',
            'Buah Naga Merah, Segar',
        ];

        $susu = [
            'Susu Ultra Milk Rasa Coklat 200 Ml',
            'Susu Kedelai',
            'Cimory Fresh Milk Cashew',
        ];

        // =========================
        // AMBIL DATA MAKANAN
        // =========================
        $ambilMakanan = function ($nama) use ($foods) {

            $found = $foods->first(function ($f) use ($nama) {

                return strtolower(trim($f['nama'])) ==
                    strtolower(trim($nama));
            });

            if (!$found) {

                return [
                    'nama' => $nama,
                    'kalori' => 0,
                    'protein' => 0,
                    'karbohidrat' => 0,
                    'lemak' => 0,
                    'serat' => 0,
                ];
            }

            // =========================
            // AMBIL GAMBAR DARI DB
            // =========================
            $gambar = \DB::table('food_images')
                ->whereRaw('LOWER(nama_makanan) = ?', [strtolower($nama)])
                ->value('gambar');

            $found['gambar'] = $gambar
                ? asset('images/Makanan/' . $gambar)
                : asset('images/default-food.png');

            return $found;
        };

        // =========================
        // GENERATE MENU
        // =========================
        $hasil = [];

        for ($i = 0; $i < 5; $i++) {

            $menu = [

                'pokok' => $ambilMakanan(
                    $pokok[array_rand($pokok)]
                ),

                'hewani' => $ambilMakanan(
                    $hewani[array_rand($hewani)]
                ),

                'nabati' => $ambilMakanan(
                    $nabati[array_rand($nabati)]
                ),

                'sayur' => $ambilMakanan(
                    $sayur[array_rand($sayur)]
                ),

                'buah' => $ambilMakanan(
                    $buah[array_rand($buah)]
                ),
            ];

            // kadang tambah susu
            if (rand(0, 1)) {

                $menu['susu'] = $ambilMakanan(
                    $susu[array_rand($susu)]
                );
            }

            // =========================
            // TOTAL NUTRISI
            // =========================
            $total = [
                'kalori' => collect($menu)->sum('kalori'),
                'protein' => collect($menu)->sum('protein'),
                'karbohidrat' => collect($menu)->sum('karbohidrat'),
                'lemak' => collect($menu)->sum('lemak'),
                'serat' => collect($menu)->sum('serat'),
            ];

            // =========================
            // SCORING
            // =========================
            $score =
                abs($target['kalori'] - $total['kalori']) +
                abs($target['protein'] - $total['protein']) +
                abs($target['karbohidrat'] - $total['karbohidrat']) +
                abs($target['lemak'] - $total['lemak']) +
                abs($target['serat'] - $total['serat']);

            // =========================
            // LABEL REKOMENDASI
            // =========================
            if ($score <= 1500) {

                $label = 'Sangat Baik';

            } elseif ($score <= 2500) {

                $label = 'Baik';

            } else {

                $label = 'Cukup';
            }

            $hariList = [
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat'
            ];

            $hasil[] = [

                'hari' => $hariList[$i],

                'hasil' => [
                    'menu' => $menu,

                    'total' => array_map(function ($v) {
                        return round($v, 1);
                    }, $total),

                    'score' => round($score, 1),
                ],

                'target' => $target,

                'rekomendasi' => [
                    'label' => $label
                ],
            ];
        }

        return collect($hasil);
    }
        // =========================
        // 👶 STUNTING (1 hari)
        // =========================
     public function generateStuntingMenu()
    {
        $foods = collect($this->getClusteredFoods());

        // =========================
        // 🔥 FILTER MAKANAN
        // =========================
        $foods = $foods->filter(function ($item) {

            $nama = strtolower($item['nama']);

            if (
                str_contains($nama, 'kopi') ||
                str_contains($nama, 'soda') ||
                str_contains($nama, 'coca') ||
                str_contains($nama, 'fanta') ||
                str_contains($nama, 'sprite') ||
                str_contains($nama, 'mentah') ||
                str_contains($nama, 'biskuit')
            ) {
                return false;
            }

            return true;
        });

        // =========================
        // 🎯 TARGET GIZI STUNTING
        // =========================
        $target = [

            'kalori' => 1400,
            'protein' => 35,
            'zat_besi' => 10,
            'kalsium' => 1000,
            'vit_c' => 45,
        ];

        // =========================
        // 🍱 GROUPING
        // =========================
        $group = $foods->groupBy('jenis');
        $getMenu = function ($group, $jenis, $nutrisi, $limit = 5) {

                if (!isset($group[$jenis])) {
                    return null;
                }

                return $group[$jenis]
                    ->filter(fn($i) => $i['kategori_data'] == 'makanan')
                    ->sortByDesc($nutrisi)
                    ->take($limit)
                    ->random();
        };
        // =========================
        // 🧠 PILIH MAKANAN TERBAIK
        // =========================

        // 🌅 SARAPAN
        $sarapan = [
            $getMenu($group, 'pokok', 'karbohidrat'),
            $getMenu($group, 'hewani', 'protein'),
            $getMenu($group, 'buah', 'vit_c'),
        ];

        // 🍛 SIANG
        $siang = [
            $getMenu($group, 'pokok', 'karbohidrat'),
            $getMenu($group, 'hewani', 'protein'),
            $getMenu($group, 'sayur', 'zat_besi'),
            $getMenu($group, 'buah', 'vit_c'),
        ];

        // 🌙 MALAM
        $malam = [
            $getMenu($group, 'pokok', 'karbohidrat'),
            $getMenu($group, 'hewani', 'protein'),
            $getMenu($group, 'sayur', 'kalsium'),
        ];

        // =========================
        // 📊 TOTAL NUTRISI
        // =========================
        $menuAll = collect([
            ...$sarapan,
            ...$siang,
            ...$malam
        ])->filter();

        $total = [

            'kalori' => round($menuAll->sum('kalori'),1),
            'protein' => round($menuAll->sum('protein'),1),
            'zat_besi' => round($menuAll->sum('zat_besi'),1),
            'kalsium' => round($menuAll->sum('kalsium'),1),
            'vit_c' => round($menuAll->sum('vit_c'),1),
        ];

        // =========================
        // 🧠 AI SCORING
        // =========================
        $score =
            abs($target['kalori'] - $total['kalori']) +
            abs($target['protein'] - $total['protein']) +
            abs($target['zat_besi'] - $total['zat_besi']) +
            abs($target['kalsium'] - $total['kalsium']) +
            abs($target['vit_c'] - $total['vit_c']);

        // =========================
        // 🏆 LABEL HASIL
        // =========================
        if ($score <= 1000) {

            $status = 'Sangat Baik';

        } elseif ($score <= 1800) {

            $status = 'Baik';

        } else {

            $status = 'Cukup';
        }

        return [

            'sarapan' => $sarapan,
            'siang' => $siang,
            'malam' => $malam,

            'total' => $total,

            'target' => $target,

            'score' => round($score,1),

            'status' => $status
        ];
    }

        // =========================
        // 🔧 HELPER
        // =========================
        private function ambil($group, $key)
    {
        if (!isset($group[$key])) return null;

        return $group[$key]->shuffle()->first();
    }

    public function getStats()
    {
        $foods = $this->getClusteredFoods();

        $total = count($foods);

        // ambil cluster unik
        $clusters = array_unique(array_column($foods, 'cluster'));

        return [
            'total_makanan' => $total,
            'total_cluster' => count($clusters),
            'avg_kalori' => $total ? round(array_sum(array_column($foods,'kalori')) / $total, 1) : 0,
            'avg_protein' => $total ? round(array_sum(array_column($foods,'protein')) / $total, 1) : 0,
        ];
    }
}