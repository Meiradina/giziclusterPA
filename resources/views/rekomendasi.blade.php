@extends('layouts.app')

@section('title', 'Rekomendasi Menu')
@section('page-title', 'Rekomendasi Menu')
@section('breadcrumb', 'Rekomendasi Menu')

@push('styles')
<style>
/* FILTER */
.filter-card {
  margin-bottom: 20px;
}

.filter-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.form-group label {
  font-size: 13px;
  color: #6b7280;
  display: block;
  margin-bottom: 6px;
}

.form-group select {
  width: 100%;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #dbeafe;
  background: #f8fbff;
}

/* INFO */
.info-card {
  margin-bottom: 20px;
  background: #eff6ff;
}

/* MENU GRID */
.menu-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}

/* CARD */
.menu-card {
  background: white;
  border-radius: 16px;
  padding: 18px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}

.menu-title {
  font-weight: 700;
  margin-bottom: 10px;
  color: #1e3a8a;
}

/* LIST STYLE */
.menu-card ul {
  list-style: none;
  padding-left: 0;
}

.menu-card li {
  margin-bottom: 8px;
  font-size: 14px;
}

.menu-image{
  width:100%;
  height:220px;
  object-fit:cover;
  border-radius:14px;
  margin-bottom:14px;
  display:block;
}

.ompreng-wrapper{
  position:relative;
  width:320px;
  margin:auto;
}

.ompreng-bg{
  width:100%;
  display:block;
}

.ompreng-item{
  position:absolute;
  object-fit:contain;
  width:auto;
  height:auto;
  max-width:100%;
  max-height:100%;
}

.nasi{
  height:99px;
  left:28px;
  bottom:58px;
}

.lauk{
  height:90px;
  right:92px;
  bottom:70px;
}

.nabati{
  height:88px;
  right:28px;
  bottom:70px;
}

.sayur{
  height:88px;
  top:70px;
  left:115px;
}

.buah{
  height:75px;
  top:70px;
  right:20px;
}

.susu{
  height:84px;
  top:70px;
  left:18px;
}

.badge-ai {
  display: inline-block;
  background: #dbeafe;
  color: #1e40af;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  margin-top: 10px;
}

.score-box {
  background: #f8fafc;
  padding: 10px;
  border-radius: 10px;
  margin-top: 12px;
  font-size: 13px;
  border: 1px solid #e2e8f0;
}

</style>
@endpush

@section('content')

<div class="main">
  <div class="dashboard-container">
    <div class="content">

      <!-- FILTER -->
      <div class="card filter-card">
        <div class="filter-grid">

          <div class="form-group">
            <label>Kategori Pengguna</label>
            <select id="kategori">
              <option value="anak">Anak-anak</option>
              <option value="remaja">Remaja</option>
              <option value="ibu">Ibu Hamil / Lansia</option>
            </select>
          </div>

          <div class="form-group">
            <label>Tujuan</label>
            <select id="tujuan">
              <option value="mbg">Menu Bergizi (MBG)</option>
              <option value="stunting">Pencegahan Stunting</option>
            </select>
          </div>

          <div class="form-group" style="display:flex;align-items:end;">
            <button class="btn-main" onclick="getRekomendasi()">
              🍽️ Tampilkan Rekomendasi
            </button>
          </div>

        </div>
      </div>

      <!-- INFO -->
      <div class="card info-card" id="infoBox" style="display:none;">
        <div class="card-title">Informasi Kebutuhan Gizi</div>
        <div id="infoText"></div>
      </div>

      <!-- HASIL -->
      <div class="menu-grid" id="menuBox" style="display:none;"></div>

    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
// ================================
// ✅ FUNCTION UTAMA
// ================================
async function getRekomendasi() {

  let kategori = document.getElementById("kategori").value;
  let tujuan = document.getElementById("tujuan").value;

  // validasi
  if (tujuan === "stunting" && kategori !== "anak") {
    alert("Menu stunting hanya tersedia untuk kategori Anak-anak");
    return;
  }

  document.getElementById("infoText").innerHTML = "Loading...";

  try {
    let res = await fetch(`/rekomendasi/data?tujuan=${tujuan}&kategori=${kategori}`);
    let result = await res.json();

    console.log("RESULT:", result);

    // 🔥 VALIDASI
    if (!result || !result.data) {
      alert("Data tidak ditemukan");
      console.error(result);
      return;
    }

    let data = result.data;

// =========================================
// 🟢 MBG (OMPRENG 1 MINGGU)
// =========================================
if (result.type === "mbg") {

  let data = Array.isArray(result.data)
    ? result.data
    : [];

  let html = data.map(item => {

    let menu = item?.hasil?.menu || {};
    let total = item?.hasil?.total || {};
    let target = item?.target || {};
    let rekomendasi = item?.rekomendasi || {};

    return `

      <div class="card menu-card">

        <!-- OMPRENG -->
        <div class="ompreng-wrapper">

          <!-- Background Ompreng -->
          <img
            src="/images/Makanan/Omprengmbg.png"
            class="ompreng-bg"
          >

          <!-- NASI -->
          ${menu?.pokok?.gambar ? `
            <img
              src="${menu.pokok.gambar}"
              class="ompreng-item nasi"
            >
          ` : ''}

          <!-- HEWANI -->
          ${menu?.hewani?.gambar ? `
            <img
              src="${menu.hewani.gambar}"
              class="ompreng-item lauk"
            >
          ` : ''}

          <!-- NABATI -->
          ${menu?.nabati?.gambar ? `
            <img
              src="${menu.nabati.gambar}"
              class="ompreng-item nabati"
            >
          ` : ''}

          <!-- SAYUR -->
          ${menu?.sayur?.gambar ? `
            <img
              src="${menu.sayur.gambar}"
              class="ompreng-item sayur"
            >
          ` : ''}

          <!-- BUAH -->
          ${menu?.buah?.gambar ? `
            <img
              src="${menu.buah.gambar}"
              class="ompreng-item buah"
            >
          ` : ''}

          ${menu?.susu ? `
            <img
              src="${menu.susu.gambar}"
              class="ompreng-item susu"
            >
          ` : ''}

        </div>

        <!-- TITLE -->
        <div class="menu-title">
          🍱 ${item.hari}
        </div>

        <!-- LIST MENU -->
        <ul style="
          margin-top:14px;
          padding-left:18px;
          line-height:1.9;
          font-size:14px;
        ">

          <li>🍚 ${menu?.pokok?.nama || '-'}</li>

          <li>🍗 ${menu?.hewani?.nama || '-'}</li>

          <li>🥜 ${menu?.nabati?.nama || '-'}</li>

          <li>🥬 ${menu?.sayur?.nama || '-'}</li>

          <li>🍎 ${menu?.buah?.nama || '-'}</li>

          ${menu?.susu ? `
            <li>🥛 ${menu?.susu?.nama}</li>
          ` : ''}

        </ul>

        <!-- SCORE -->
        <div class="score-box">

          <b>${rekomendasi?.label || 'AI Scoring'}</b><br>

          ${rekomendasi?.target || ''}

          <br><br>

          <small>
            Score:
            <b>${item?.hasil?.score || 0}</b>
          </small>

        </div>

        <!-- TOTAL NUTRISI -->
        <div style="font-size:13px; margin-top:12px">

          <b>Total Nutrisi:</b>

          <table style="
            width:100%;
            margin-top:8px;
            font-size:12px
          ">

            <tr>
              <td>Kalori</td>
              <td>${total?.kalori || 0}</td>
              <td>/ ${target?.kalori || 0}</td>
            </tr>

            <tr>
              <td>Protein</td>
              <td>${total?.protein || 0}</td>
              <td>/ ${target?.protein || 0}</td>
            </tr>

            <tr>
              <td>Karbohidrat</td>
              <td>${total?.karbohidrat || 0}</td>
              <td>/ ${target?.karbohidrat || 0}</td>
            </tr>

            <tr>
              <td>Lemak</td>
              <td>${total?.lemak || 0}</td>
              <td>/ ${target?.lemak || 0}</td>
            </tr>

            <tr>
              <td>Serat</td>
              <td>${total?.serat || 0}</td>
              <td>/ ${target?.serat || 0}</td>
            </tr>

          </table>

        </div>

      </div>

    `;

  }).join("");

  document.getElementById("menuBox").innerHTML = html;

  document.getElementById("infoText").innerHTML =
    "Rekomendasi menu MBG berdasarkan kebutuhan nutrisi pengguna.";

}
// =========================================
// 🟠 STUNTING
// =========================================
    else {

      function tampilkan(list) {

        return list.map(item => `

          <li style="margin-bottom:12px">

            <b>${item.nama}</b><br>

            <small>
              Protein: ${item.protein} g |
              Kalori: ${item.kalori}
            </small>

          </li>

        `).join("");
      }

      document.getElementById("menuBox").innerHTML = `

        <div class="card menu-card">

          <div class="menu-title">
            🌅 Sarapan
          </div>

          <ul>
            ${tampilkan(data.sarapan)}
          </ul>

        </div>


        <div class="card menu-card">

          <div class="menu-title">
            🍛 Makan Siang
          </div>

          <ul>
            ${tampilkan(data.siang)}
          </ul>

        </div>


        <div class="card menu-card">

          <div class="menu-title">
            🌙 Makan Malam
          </div>

          <ul>
            ${tampilkan(data.malam)}
          </ul>

        </div>


        <div class="card menu-card">

          <div class="menu-title">
            📊 Total Nutrisi Harian
          </div>

          <table style="width:100%; margin-top:10px; font-size:13px">

            <tr>
              <td><b>Protein</b></td>
              <td>${data.total.protein} g</td>
            </tr>

            <tr>
              <td><b>Kalori</b></td>
              <td>${data.total.kalori}</td>
            </tr>

            <tr>
              <td><b>Zat Besi</b></td>
              <td>${data.total.zat_besi} mg</td>
            </tr>

            <tr>
              <td><b>Kalsium</b></td>
              <td>${data.total.kalsium} mg</td>
            </tr>

          </table>

          <div class="score-box" style="margin-top:14px">
            <b>✅ Menu Tinggi Gizi</b><br>
            Direkomendasikan untuk pencegahan stunting
          </div>

        </div>

      `;

      document.getElementById("infoText").innerHTML =
        "Rekomendasi menu tinggi protein dan tinggi gizi untuk membantu pencegahan stunting.";
    }

    document.getElementById("menuBox").style.display = "grid";
    document.getElementById("infoBox").style.display = "block";

  } catch (error) {
    console.error(error);
    alert("Gagal ambil data dari Laravel");
  }
}


// ================================
// ✅ AUTO DISABLE STUNTING
// ================================
document.addEventListener("DOMContentLoaded", function () {

  const kategoriSelect = document.getElementById("kategori");
  const tujuanSelect = document.getElementById("tujuan");

  function handleKategoriChange() {
    let kategori = kategoriSelect.value;
    let optionStunting = tujuanSelect.querySelector('option[value="stunting"]');

    if (kategori !== "anak") {
      optionStunting.disabled = true;
      tujuanSelect.value = "mbg";
    } else {
      optionStunting.disabled = false;
    }
  }

  handleKategoriChange();
  kategoriSelect.addEventListener("change", handleKategoriChange);

});
</script>
@endpush