<?php
include "koneksi.php";

$id = isset($_GET['id']) ? $_GET['id'] : '';
$nama = '';
$harga = 0;
$deskripsi = '';
$poto = '';

$hasil = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE id='$id'");
if ($data = mysqli_fetch_array($hasil)){
  $nama = $data['nama'];
  $harga = $data['harga'];
  $deskripsi = $data['deskripsi'];
  $poto = $data['poto'];
}
?>

<!doctype html>
<html lang="id" data-bs-theme="auto">
  <head>
    <meta charset="utf-8" />
    <meta name="theme-color" content="#0f3d2a" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detail Produk - <?= htmlspecialchars($nama) ?></title>
    <script src="assets/js/color-modes.js"></script>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="theme-hijau.css" rel="stylesheet" />
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        user-select: none;
      }
      .product-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        overflow: hidden;
      }
      .product-img {
        width: 100%;
        max-height: 450px;
        object-fit: contain;
        border-radius: 12px;
      }
      .price-tag {
        font-size: 1.75rem;
        font-weight: 700;
        color: #198754;
      }
      :root {
        --hijau-tua: #0f3d2a;
        --hijau-utama: #198754;
        --hijau-hover: #146c43;
        --hijau-aktif: #115c3a;
      }
      .bg-hijau-tua {
        background-color: var(--hijau-tua) !important;
      }

      .bi {
        vertical-align: -0.125em;
        fill: currentColor;
      }
      .btn-bd-primary {
        --bd-violet-bg: var(--hijau-utama);
        --bd-violet-rgb: 25, 135, 84;
        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--hijau-utama);
        --bs-btn-border-color: var(--hijau-utama);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: var(--hijau-hover);
        --bs-btn-hover-border-color: var(--hijau-hover);
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: var(--hijau-aktif);
        --bs-btn-active-border-color: var(--hijau-aktif);
      }
      .bd-mode-toggle {
        z-index: 1500;
      }
      .bd-mode-toggle .bi {
        width: 1em;
        height: 1em;
      }
      .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
      }

      /* ===== Layout: footer selalu menempel di bawah, tanpa area putih ===== */
      html {
        background-color: var(--hijau-tua);
      }
      body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background-color: var(--bs-body-bg);
      }
      body > main {
        flex: 1 0 auto;
      }
      body > footer {
        margin-top: auto;
      }

      /* ===== Footer: teks & ikon terbaca di latar hijau tua ===== */
      .footer-hijau {
        color: #ffffff;
      }
      .footer-hijau h5 {
        color: #ffffff;
      }
      .footer-hijau .text-body-secondary {
        color: rgba(255, 255, 255, 0.78) !important;
      }
      .footer-hijau a.text-body-secondary:hover {
        color: #75d9a3 !important;
      }
      .footer-icon-wa {
        color: #75d9a3;
      }
      .footer-icon-pusat {
        color: #ff9b9b;
      }

      /* ===== Navbar ===== */
      .navbar-hijau {
        background: linear-gradient(90deg, #0a2a1c 0%, #0f3d2a 55%, #146c43 100%);
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding-top: 0.6rem;
        padding-bottom: 0.6rem;
      }
      .navbar-hijau .navbar-brand {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-weight: 700;
        font-size: 1.3rem;
        letter-spacing: 0.5px;
        color: #ffffff;
      }
      .navbar-hijau .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.82);
        font-weight: 500;
        border-radius: 999px;
        padding: 0.45rem 1.1rem;
        transition: background-color 0.2s ease, color 0.2s ease;
      }
      .navbar-hijau .navbar-nav .nav-link:hover,
      .navbar-hijau .navbar-nav .nav-link:focus {
        color: #ffffff;
        background-color: rgba(255, 255, 255, 0.12);
      }
      .navbar-hijau .navbar-nav .nav-link.active {
        color: #ffffff;
        background-color: var(--hijau-utama);
      }
      .navbar-hijau .navbar-nav .nav-link.nav-cart {
        width: 42px;
        height: 42px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.12);
      }
      .navbar-hijau .navbar-nav .nav-link.nav-cart:hover {
        background-color: var(--hijau-utama);
      }
      .navbar-hijau .navbar-nav .nav-link.nav-profile {
        padding: 0.3rem 1rem 0.3rem 0.4rem;
        border: 1px solid rgba(255, 255, 255, 0.25);
      }
      .navbar-hijau .navbar-nav .nav-link.nav-profile:hover,
      .navbar-hijau .navbar-nav .nav-link.nav-profile.show {
        background-color: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 255, 255, 0.45);
      }
      .navbar-hijau .dropdown-menu {
        border: 0;
        border-radius: 14px;
        padding: 0.5rem;
        margin-top: 0.6rem;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
      }
      .navbar-hijau .dropdown-item {
        border-radius: 8px;
        padding: 0.5rem 0.9rem;
        font-weight: 500;
      }
      .navbar-hijau .dropdown-item:hover {
        background-color: rgba(25, 135, 84, 0.25);
      }
      .navbar-hijau .dropdown-item.text-danger:hover {
        background-color: rgba(220, 53, 69, 0.18);
      }
      .navbar-hijau .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.35);
      }
      .navbar-hijau .navbar-toggler:focus {
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.45);
      }
      @media (max-width: 767.98px) {
        .navbar-hijau .navbar-nav {
          padding: 0.5rem 0;
        }
      }

      .form-control:focus, .form-select:focus, .form-check-input:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
      }
      .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
      }
        </style>
  </head>
  <body>
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="check2" viewBox="0 0 16 16">
        <path
          d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"
        ></path>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path
          d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"
        ></path>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path
          d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"
        ></path>
        <path
          d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"
        ></path>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path
          d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"
        ></path>
      </symbol>
    </svg>
    <div
      class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle"
    >
      <button
        class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center"
        id="bd-theme"
        type="button"
        aria-expanded="false"
        data-bs-toggle="dropdown"
        aria-label="Toggle theme (auto)"
      >
        <svg class="bi my-1 theme-icon-active" aria-hidden="true">
          <use href="#circle-half"></use>
        </svg>
        <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
      </button>
      <ul
        class="dropdown-menu dropdown-menu-end shadow"
        aria-labelledby="bd-theme-text"
      >
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center"
            data-bs-theme-value="light"
            aria-pressed="false"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#sun-fill"></use>
            </svg>
            Light
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center"
            data-bs-theme-value="dark"
            aria-pressed="false"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#moon-stars-fill"></use>
            </svg>
            Dark
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
        <li>
          <button
            type="button"
            class="dropdown-item d-flex align-items-center active"
            data-bs-theme-value="auto"
            aria-pressed="true"
          >
            <svg class="bi me-2 opacity-50" aria-hidden="true">
              <use href="#circle-half"></use>
            </svg>
            Auto
            <svg class="bi ms-auto d-none" aria-hidden="true">
              <use href="#check2"></use>
            </svg>
          </button>
        </li>
      </ul>
    </div>
    <header data-bs-theme="dark">
      <div class="collapse bg-hijau-tua text-white" id="navbarHeader">
        <div class="container">
          <div class="row">
            <div class="col-sm-8 col-md-7 py-4">
              <h4>About</h4>
              <p class="text-body-secondary">
                Teh Idaman lahir dari kecintaan kami terhadap tradisi teh Nusantara yang kaya dan otentik. Kami menghadirkan racikan daun teh pilihan segar dari perkebunan lokal terbaik.
              </p>
            </div>
            <div class="col-sm-4 offset-md-1 py-4">
              <h4>Contact</h4>
              <ul class="list-unstyled">
                <li><a href="https://www.threads.com/@tehidaman.anakbangsa" class="text-white">Follow on Threads</a></li>
                <li><a href="https://www.facebook.com/371247936070633" class="text-white">Like on Facebook</a></li>
                <li><a href="https://www.instagram.com/tehidaman.anakbangsa/#" class="text-white">Follow on Instagram</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="navbar navbar-dark navbar-hijau shadow-sm">
        <div class="container">
          <a href="carousell.php" class="navbar-brand d-flex align-items-center gap-2">
            <span>Teh Idaman</span>
          </a>
          <div class="d-flex align-items-center gap-3">
            <button
              class="navbar-toggler"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#navbarHeader"
              aria-controls="navbarHeader"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
              <span class="navbar-toggler-icon"></span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="py-5">
      <div class="container">
        <!-- Tombol Kembali -->
        <a href="carousell.php" class="btn btn-outline-success rounded-pill px-3 mb-4">&larr; Kembali ke Menu</a>

        <div class="card product-card bg-body-tertiary p-4">
          <div class="row g-4 align-items-center">
            <!-- Gambar Produk -->
            <div class="col-md-5 text-center">
              <img 
                src="images/<?= htmlspecialchars($poto) ?>" 
                alt="<?= htmlspecialchars($nama) ?>" 
                class="img-fluid product-img shadow-sm"
              >
            </div>

            <!-- Detail Produk -->
            <div class="col-md-7">
              <h1 class="display-6 fw-bold mb-2"><?= htmlspecialchars($nama) ?></h1>
              <div class="price-tag mb-3">
                Rp <?= number_format($harga, 0, ',', '.') ?>
              </div>
              <p class="lead text-body-secondary fs-6 mb-4">
                <?= nl2br(htmlspecialchars($deskripsi)) ?>
              </p>

              <!-- Form Tambah ke Keranjang -->
              <form method="post" action="keranjang.php" class="mt-4">
                <input type="hidden" name="id_produk" value="<?= htmlspecialchars($id) ?>">
                <input type="hidden" name="hidden_foto" value="<?= htmlspecialchars($poto) ?>">
                <input type="hidden" name="hidden_nama" value="<?= htmlspecialchars($nama) ?>">
                <input type="hidden" name="hidden_harga" value="<?= htmlspecialchars($harga) ?>">

                <div class="row g-3 align-items-center">
                  <div class="col-auto">
                    <label for="jumlah" class="col-form-label fw-semibold">Jumlah:</label>
                  </div>
                  <div class="col-auto">
                    <input 
                      type="number" 
                      id="jumlah" 
                      name="jumlah" 
                      value="1" 
                      min="1" 
                      class="form-control text-center" 
                      style="width: 80px;"
                    >
                  </div>
                  <div class="col-auto">
                    <button type="submit" name="add" class="btn btn-success px-4 py-2 fw-semibold">
                      + Tambah ke Keranjang
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>

      <div class="container mt-auto pb-4">
      <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center pt-3 small text-body-secondary text-center">
        <p class="mb-0">&copy; <?= date('Y') ?> Teh Idaman Indonesia. All rights reserved.</p>
      </div>
    </div>

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>