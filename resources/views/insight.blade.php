@extends('layouts.app')

@section('title', 'Insight MBG & Stunting')
@section('page-title', 'Insight MBG & Stunting')
@section('breadcrumb', 'Insight MBG & Stunting')

@push('styles')
<style>
.mbg-hero {
    background: linear-gradient(135deg, #1E40AF 0%, #2563EB 60%, #60A5FA 100%);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 28px;
    color: white;
    position: relative; overflow: hidden;
}
.mbg-hero::after {
    content: '';
    position: absolute; right: -20px; top: -20px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.mbg-hero h2 { font-size: 22px; font-weight: 800; font-family: 'Space Grotesk', sans-serif; margin-bottom: 8px; }
.mbg-hero p  { font-size: 14px; opacity: 0.88; line-height: 1.6; max-width: 680px; }

.cluster-section { margin-bottom: 32px; }
.cluster-section-header {
    display: flex; align-items: center; gap: 14px;
    padding: 16px 20px;
    border-radius: 12px 12px 0 0;
    border: 1px solid var(--border); border-bottom: none;
}
.cluster-section-header .cs-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
}
.cluster-section-body {
    border: 1px solid var(--border);
    border-radius: 0 0 12px 12px;
    background: white;
    padding: 20px;
}
.cluster-foods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 8px;
    margin-top: 12px;
}
.food-chip-detail {
    display: flex; flex-direction: column;
    align-items: center;
    padding: 10px 8px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: var(--surface-3);
    font-size: 12px;
    text-align: center;
    transition: all 0.15s;
}
.food-chip-detail:hover { border-color: var(--primary-pale); background: var(--primary-ghost); }
.food-chip-detail .fc-name { font-weight: 600; color: var(--text-primary); margin-bottom: 3px; }
.food-chip-detail .fc-stat { font-size: 11px; color: var(--text-muted); }

.nutrisi-tip {
    background: var(--primary-ghost);
    border: 1px solid var(--primary-pale);
    border-radius: 10px;
    padding: 14px 16px;
    margin-top: 14px;
}
.nutrisi-tip-title {
    font-size: 13px; font-weight: 700; color: var(--primary);
    margin-bottom: 6px; display: flex; align-items: center; gap: 6px;
}
.nutrisi-tip-list {
    list-style: none; padding: 0;
    display: flex; flex-wrap: wrap; gap: 8px;
}
.nutrisi-tip-list li {
    font-size: 12px; color: var(--text-secondary);
    background: white;
    border: 1px solid var(--border);
    padding: 4px 10px; border-radius: 20px;
    display: flex; align-items: center; gap: 5px;
}

.stunting-banner {
    background: white;
    border: 1px solid #FECACA;
    border-left: 4px solid #EF4444;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
}
.stunting-title { font-size: 16px; font-weight: 700; color: #DC2626; margin-bottom: 6px; font-family: 'Space Grotesk', sans-serif; }
.stunting-desc { font-size: 13px; color: #64748B; line-height: 1.7; }

.rec-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-top: 20px; }
.rec-card {
    background: white; border: 1px solid var(--border);
    border-radius: 12px; padding: 18px;
    box-shadow: var(--shadow-sm);
}
.rec-card-icon { font-size: 28px; margin-bottom: 10px; }
.rec-card-title { font-size: 14px; font-weight: 700; color: var(--text-primary); font-family: 'Space Grotesk', sans-serif; margin-bottom: 6px; }
.rec-card-desc { font-size: 12px; color: var(--text-secondary); line-height: 1.6; }
</style>
@endpush

@section('content')
<!-- MBG Hero Banner -->
<div class="mbg-hero">
    <div style="font-size:11px;font-weight:700;letter-spacing:2px;opacity:0.75;text-transform:uppercase;margin-bottom:8px;">Program Makan Bergizi Gratis 2025</div>
    <h2>Insight Pengelompokan Nutrisi untuk MBG & Pencegahan Stunting</h2>
    <p>Hasil analisis K-Means clustering terhadap 1000+ jenis makanan mengidentifikasi 4 kelompok nutrisi utama yang dapat digunakan sebagai acuan perencanaan menu program MBG dan strategi pencegahan stunting pada anak Indonesia.</p>
</div>

<!-- Stunting Banner -->
<div class="stunting-banner">
    <div class="stunting-title">⚠️ Tentang Stunting di Indonesia</div>
    <div class="stunting-desc">
        Stunting adalah kondisi gagal tumbuh pada anak akibat kekurangan gizi kronis. Indonesia masih berjuang dengan angka prevalensi stunting yang signifikan. 
        Program MBG (Makan Bergizi Gratis) hadir sebagai solusi sistematis untuk memastikan anak-anak Indonesia mendapatkan asupan nutrisi yang cukup dan seimbang setiap harinya. 
        Pemilihan makanan yang tepat berdasarkan profil nutrisinya sangat krusial dalam keberhasilan program ini.
    </div>
</div>

<!-- Cluster Sections -->
@php
    $clusterIcons = ['🥩','🍚','🥗','🥦'];
    $clusterBgs   = ['#EFF6FF','#FFFBEB','#ECFDF5','#F5F3FF'];
    $clusterManfaat = [
        ['Membangun dan memperbaiki jaringan otot anak','Penting untuk pertumbuhan tinggi badan optimal','Mendukung sistem imunitas tubuh','Sumber zat besi mencegah anemia'],
        ['Sumber energi utama untuk aktivitas harian','Mendukung fungsi otak dan konsentrasi belajar','Menyediakan kalori untuk pertumbuhan','Mudah diakses dan terjangkau'],
        ['Profil nutrisi seimbang mendukung tumbuh kembang','Kombinasi protein, karbohidrat, dan lemak sehat','Ideal sebagai menu utama program MBG','Mudah diolah dalam skala besar'],
        ['Kaya vitamin C meningkatkan imunitas','Serat tinggi menyehatkan pencernaan anak','Antioksidan melindungi sel tubuh','Kalsium mendukung pertumbuhan tulang dan gigi'],
    ];
    $clusterRekPagi = [
        'Ayam rebus + nasi putih + sup sayur', 'Nasi merah + orak-arik telur', 'Gado-gado / pepes ikan + nasi', 'Buah segar + yogurt / susu'
    ];
    $clusterRekSiang = [
        'Ikan goreng + tempe + nasi', 'Nasi goreng sayur + tahu', 'Soto ayam + lontong', 'Sayur bayam + wortel + nasi'
    ];
@endphp

@foreach($clusters as $cluster)
<div class="cluster-section">
    <div class="cluster-section-header" style="background:{{ $clusterBgs[$cluster['id']] }};">
        <div class="cs-icon" style="background:{{ $cluster['color'] }}22;">{{ $clusterIcons[$cluster['id']] }}</div>
        <div style="flex:1;">
            <div style="font-size:17px;font-weight:800;color:{{ $cluster['color'] }};font-family:'Space Grotesk',sans-serif;">
                Cluster {{ $cluster['id']+1 }}: {{ $cluster['label'] }}
            </div>
            <div style="font-size:13px;color:var(--text-secondary);margin-top:2px;">
                {{ $cluster['count'] }} jenis makanan dalam kelompok ini
            </div>
        </div>
        <span class="cluster-badge" style="background:{{ $cluster['color'] }}18;color:{{ $cluster['color'] }};font-size:13px;padding:6px 14px;">
            {{ $cluster['count'] }} Makanan
        </span>
    </div>
    <div class="cluster-section-body">
        <p style="font-size:14px;color:var(--text-secondary);line-height:1.7;margin-bottom:14px;">
            {{ $cluster['description'] }}
        </p>

        <!-- Manfaat -->
        <div style="margin-bottom:16px;">
            <div style="font-size:13px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">✅ Manfaat Nutrisi untuk Anak:</div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($clusterManfaat[$cluster['id']] as $manfaat)
                <span style="padding:5px 12px;background:{{ $cluster['color'] }}12;color:{{ $cluster['color'] }};border:1px solid {{ $cluster['color'] }}30;border-radius:20px;font-size:12px;font-weight:600;">
                    {{ $manfaat }}
                </span>
                @endforeach
            </div>
        </div>

        <!-- Daftar makanan -->
        <div>
            <div style="font-size:13px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">🍽️ Makanan dalam Cluster ini:</div>
            <div class="cluster-foods-grid">
                @foreach($cluster['foods'] as $food)
                <div class="food-chip-detail">
                    <div class="fc-name">{{ $food['nama'] }}</div>
                    <div class="fc-stat">{{ $food['kalori'] }} kcal · P:{{ $food['protein'] }}g</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Rekomendasi MBG -->
        <div class="nutrisi-tip">
            <div class="nutrisi-tip-title">
                🎯 Rekomendasi Penggunaan dalam Program MBG:
            </div>
            <ul class="nutrisi-tip-list">
                <li>🌅 Pagi: {{ $clusterRekPagi[$cluster['id']] }}</li>
                <li>☀️ Siang: {{ $clusterRekSiang[$cluster['id']] }}</li>
            </ul>
        </div>
    </div>
</div>
@endforeach

<!-- Rekomendasi Umum -->
<div class="card" style="margin-top:8px;">
    <div class="card-header">
        <div>
            <div class="card-title">💡 Rekomendasi Menu MBG Berdasarkan Hasil Clustering</div>
            <div class="card-subtitle">Kombinasi optimal dari berbagai cluster untuk menu harian yang bergizi</div>
        </div>
    </div>
    <div class="card-body">
        <div class="rec-grid">
            <div class="rec-card">
                <div class="rec-card-icon">🌅</div>
                <div class="rec-card-title">Menu Sarapan Ideal</div>
                <div class="rec-card-desc">Kombinasi Cluster 1 + 2: Protein (telur/tempe) + Karbohidrat (nasi/roti) untuk energi dan tumbuh kembang optimal di awal hari.</div>
            </div>
            <div class="rec-card">
                <div class="rec-card-icon">☀️</div>
                <div class="rec-card-title">Menu Makan Siang</div>
                <div class="rec-card-desc">Fokus pada Cluster 3 (seimbang): Soto ayam, gado-gado, atau pepes ikan yang menyediakan semua nutrisi penting sekaligus.</div>
            </div>
            <div class="rec-card">
                <div class="rec-card-icon">🌆</div>
                <div class="rec-card-title">Camilan & Buah</div>
                <div class="rec-card-desc">Cluster 4: Buah-buahan segar (pisang, pepaya, jeruk) sebagai camilan kaya vitamin C dan serat untuk imunitas anak.</div>
            </div>
            <div class="rec-card">
                <div class="rec-card-icon">🛡️</div>
                <div class="rec-card-title">Strategi Anti-Stunting</div>
                <div class="rec-card-desc">Pastikan menu MBG mengandung makanan dari minimal 3 cluster setiap hari untuk memenuhi kebutuhan 8 parameter nutrisi penting anak.</div>
            </div>
        </div>
    </div>
</div>
@endsection