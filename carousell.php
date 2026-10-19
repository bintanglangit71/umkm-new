<?php
session_start();
include_once 'koneksi.php';

// Ambil data user yang sedang login (jika ada)
$current_user = null;
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $q_user  = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id='$id' LIMIT 1");
    $current_user = mysqli_fetch_array($q_user);
}
?>
<!doctype html>
<html lang="id" data-bs-theme="auto">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta
      name="author"
      content="Mark Otto, Jacob Thornton, and Bootstrap contributors"
    />
    <meta name="generator" content="Astro v5.13.2" />
    <title>Carousel Template · Bootstrap v5.3</title>
    <link
      rel="canonical"
      href="https://getbootstrap.com/docs/5.3/examples/carousel/"
    />
    <script src="assets/js/color-modes.js"></script>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Tema hijau (sama seperti keranjang.php) -->
    <link href="theme-hijau.css" rel="stylesheet" />
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="theme-color" content="#0f3d2a" />
    <link href="carousel.css" rel="stylesheet" />
    <style>
      :root {
        --hijau-tua: #0f3d2a;
        --hijau-utama: #198754;
        --hijau-hover: #146c43;
        --hijau-aktif: #115c3a;
      }
      .bg-hijau-tua {
        background-color: var(--hijau-tua) !important;
      }

      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }
      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: #0000001a;
        border: solid rgba(0, 0, 0, 0.15);
        border-width: 1px 0;
        box-shadow:
          inset 0 0.5em 1.5em #0000001a,
          inset 0 0.125em 0.5em #00000026;
      }
      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }
      .bi {
        vertical-align: -0.125em;
        fill: currentColor;
      }
      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }
      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
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

      .carousel-item img {
        height: 500px;
      }

      .slide-poster-1 {
        object-fit: cover !important;
        object-position: center center !important; 
      }
      .slide-orang-2 {
        object-fit: cover !important;
        object-position: center 45% !important;
        transform: scale(1.08);
      }

      .slide-peta-3 {
        object-fit: cover !important;
        object-position: center 8% !important; 
      }

      .carousel-control-prev-icon,
      .carousel-control-next-icon {
        background-color: var(--hijau-utama);
        border-radius: 50%;
        padding: 12px;
        background-size: 50%;
      }

      .carousel-indicators [data-bs-target] {
        background-color: var(--hijau-utama);
        height: 4px;
      }

      .carousel-indicators .active {
        background-color: #ffffff;
      }

      footer a {
        text-decoration: none;
        transition: color 0.2s ease-in-out;
      }

      footer a:hover {
        color: #75d9a3 !important;
      }

      .footer-icon {
        width: 20px;
        height: 20px;
        fill: currentColor;
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
      <nav class="navbar navbar-expand-md navbar-dark fixed-top navbar-hijau">
        <div class="container-fluid">
          <a class="navbar-brand" href="carousell.php"><span>Teh Idaman</span></a>
          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarCollapse"
            aria-controls="navbarCollapse"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="#home">Home</a>
              </li>
              <li class="nav-item"><a class="nav-link" href="#menu">Menu</a></li>
              <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
            </ul>
            <form class="d-flex" role="search">
            </form>
                  <!-- KANAN: keranjang + profil -->
        <ul class="navbar-nav align-items-lg-center gap-lg-2">
                <!-- Keranjang -->
          <li class="nav-item">
            <a class="nav-link nav-cart" href="keranjang.php" title="Keranjang">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
              </svg>
              
            </a>
          </li>

          <!-- Profil (dropdown) -->
          <li class="nav-item dropdown">
            <a class="nav-link nav-profile dropdown-toggle d-flex align-items-center" href="#" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
              <?php if ($current_user && !empty($current_user['poto']) && file_exists("images/" . $current_user['poto'])): ?>
                <img src="images/<?= htmlspecialchars($current_user['poto']) ?>" class="rounded-circle me-2" width="28" height="28" style="object-fit:cover" alt="">
              <?php elseif ($current_user): ?>
                <span class="me-2 d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white" style="width:28px;height:28px;font-size:12px;font-weight:600;">
                  <?= strtoupper(substr($current_user['nama'] ?? 'U', 0, 1)) ?>
                </span>
              <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                </svg>
              <?php endif; ?>
              <?= $current_user ? htmlspecialchars($current_user['nama']) : 'Akun' ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <?php if ($current_user): ?>
                <li><a class="dropdown-item" href="profil.php">Profil Saya</a></li>
                <li><a class="dropdown-item" href="riwayat.php">Riwayat Pesanan</a></li>
                <?php if ($current_user['role'] == 'admin'): ?>
                  <li><a class="dropdown-item" href="dashboard/dashboard.php">Dashboard</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php" onclick="return confirmLogout(event)">Logout</a></li>
              <?php else: ?>
                <li><a class="dropdown-item" href="login/login.php">Login</a></li>
                <li><a class="dropdown-item" href="login/register.php">Daftar Akun</a></li>
              <?php endif; ?>
            </ul>
          </li>
        </ul>
          </div>
        </div>
      </nav>
    </header>
<main id="home">
      <div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel">
        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
          <button
            type="button"
            data-bs-target="#myCarousel"
            data-bs-slide-to="0"
            class="active"
            aria-current="true"
            aria-label="Slide 1"
          ></button>
          <button
            type="button"
            data-bs-target="#myCarousel"
            data-bs-slide-to="1"
            aria-label="Slide 2"
          ></button>
          <button
            type="button"
            data-bs-target="#myCarousel"
            data-bs-slide-to="2"
            aria-label="Slide 3"
          ></button>
        </div>


        <div class="carousel-inner">
          <!-- Slide 1 -->
          <div class="carousel-item active">
            <img src="images/cl4.jpg" class="d-block w-100 slide-poster-1" alt="Best Seller">
          </div>

          <!-- Slide 2 -->
          <div class="carousel-item overflow-hidden">
            <img src="images/cl2.jpg" class="d-block w-100 slide-orang-2" alt="Slide 2">
          </div>

          <!-- Slide 3 -->
          <div class="carousel-item">
            <img src="images/cl3.jpg" class="d-block w-100 slide-peta-3" alt="Peta Cabang">
          </div>
        </div>

        <!-- Carousel Controls -->
        <button
          class="carousel-control-prev"
          type="button"
          data-bs-target="#myCarousel"
          data-bs-slide="prev"
        >
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button
          class="carousel-control-next"
          type="button"
          data-bs-target="#myCarousel"
          data-bs-slide="next"
        >
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
      <!-- Marketing messaging and featurettes
  ================================================== -->
      <!-- Wrap the rest of the page in another container to center all the content. -->
        <!-- Three columns of text below the carousel -->
      <div class="album py-5 bg-body-tertiary" id="menu">
        <div class="container">
          <h1 class="mb-4">Tea Series</h1>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
            <?php
            include 'koneksi.php';
            $query = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE id_kategori='1'");
            while ($data = mysqli_fetch_array($query)) {?>

            <div class="col">
              <div class="card shadow-sm">
                <img src="images/<?= $data['poto']?>"
                  aria-label="Placeholder: Thumbnail"
                  class="bd-placeholder-img card-img-top"
                  height="400"
                  preserveAspectRatio="xMidYMid slice"
                  role="img"
                  width="80"
                  xmlns="http://www.w3.org/2000/svg"
                >
                </img>
                <div class="card-body">
                  <p class="card-text">
                    <b><?= $data['nama'] ?></b>
                  </p>
                  <p class="card-text">
                    <?= $data['deskripsi'] ?>
                  </p>
                  <p class="card-text">
                    Rp. <?= $data['harga'] ?>
                  </p>
                  <div
                    class="d-flex justify-content-between align-items-center"
                  >
                    <div class="btn-group">
                      <a href = "detail.php?id=<?= $data['id'] ?>"
                        type="button"
                        class="btn btn-sm btn-outline-success"
                      >
                        Detail
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
             <?php } ?>
            </div>
          </div>
        </div>
      </div>

      <div class="album py-5 bg-body-tertiary">
        <div class="container">
            <h2 class="mb-4">Thai Tea Series</h2>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
            <?php
            include 'koneksi.php';
            $query = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE id_kategori='2'");
            while ($data = mysqli_fetch_array($query)) {?>

            <div class="col">
              <div class="card shadow-sm">
                <img src="images/<?= $data['poto']?>"
                  aria-label="Placeholder: Thumbnail"
                  class="bd-placeholder-img card-img-top"
                  height="400"
                  preserveAspectRatio="xMidYMid slice"
                  role="img"
                  width="80"
                  xmlns="http://www.w3.org/2000/svg"
                >
                </img>
                <div class="card-body">
                  <p class="card-text">
                    <b><?= $data['nama'] ?></b>
                  </p>
                  <p class="card-text">
                    <?= $data['deskripsi'] ?>
                  </p>
                  <p class="card-text">
                    Rp. <?= $data['harga'] ?>
                  </p>
                  <div
                    class="d-flex justify-content-between align-items-center"
                  >
                    <div class="btn-group">
                      <a href = "detail.php?id=<?= $data['id'] ?>"
                        type="button"
                        class="btn btn-sm btn-outline-success"
                      >
                        Detail
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
             <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <div class="album py-5 bg-body-tertiary">
        <div class="container">
            <h2 class="mb-4">Squash Series</h2>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
            <?php
            include 'koneksi.php';
            $query = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE id_kategori='3'");
            while ($data = mysqli_fetch_array($query)) {?>

            <div class="col">
              <div class="card shadow-sm">
                <img src="images/<?= $data['poto']?>"
                  aria-label="Placeholder: Thumbnail"
                  class="bd-placeholder-img card-img-top"
                  height="400"
                  preserveAspectRatio="xMidYMid slice"
                  role="img"
                  width="80"
                  xmlns="http://www.w3.org/2000/svg"
                >
                </img>
                <div class="card-body">
                  <p class="card-text">
                    <b><?= $data['nama'] ?></b>
                  </p>
                  <p class="card-text">
                    <?= $data['deskripsi'] ?>
                  </p>
                  <p class="card-text">
                    Rp. <?= $data['harga'] ?>
                  </p>
                  <div
                    class="d-flex justify-content-between align-items-center"
                  >
                    <div class="btn-group">
                      <a href = "detail.php?id=<?= $data['id'] ?>"
                        type="button"
                        class="btn btn-sm btn-outline-success"
                      >
                        Detail
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
             <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <div class="album py-5 bg-body-tertiary">
        <div class="container">
            <h2 class="mb-4">Yakult Series</h2>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
            <?php
            include 'koneksi.php';
            $query = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE id_kategori='4'");
            while ($data = mysqli_fetch_array($query)) {?>

            <div class="col">
              <div class="card shadow-sm">
                <img src="images/<?= $data['poto']?>"
                  aria-label="Placeholder: Thumbnail"
                  class="bd-placeholder-img card-img-top"
                  height="400"
                  preserveAspectRatio="xMidYMid slice"
                  role="img"
                  width="80"
                  xmlns="http://www.w3.org/2000/svg"
                >
                </img>
                <div class="card-body">
                  <p class="card-text">
                    <b><?= $data['nama'] ?></b>
                  </p>
                  <p class="card-text">
                    <?= $data['deskripsi'] ?>
                  </p>
                  <p class="card-text">
                    Rp. <?= $data['harga'] ?>
                  </p>
                  <div
                    class="d-flex justify-content-between align-items-center"
                  >
                    <div class="btn-group">
                      <a href = "detail.php?id=<?= $data['id'] ?>"
                        type="button"
                        class="btn btn-sm btn-outline-success"
                      >
                        Detail
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
             <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <div class="album py-5 bg-body-tertiary">
        <div class="container">
            <h2 class="mb-4">Milky Series</h2>
          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
            <?php
            include 'koneksi.php';
            $query = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE id_kategori='5'");
            while ($data = mysqli_fetch_array($query)) {?>

            <div class="col">
              <div class="card shadow-sm">
                <img src="images/<?= $data['poto']?>"
                  aria-label="Placeholder: Thumbnail"
                  class="bd-placeholder-img card-img-top"
                  height="400"
                  preserveAspectRatio="xMidYMid slice"
                  role="img"
                  width="80"
                  xmlns="http://www.w3.org/2000/svg"
                >
                </img>
                <div class="card-body">
                  <p class="card-text">
                    <b><?= $data['nama'] ?></b>
                  </p>
                  <p class="card-text">
                    <?= $data['deskripsi'] ?>
                  </p>
                  <p class="card-text">
                    Rp. <?= $data['harga'] ?>
                  </p>
                  <div
                    class="d-flex justify-content-between align-items-center"
                  >
                    <div class="btn-group">
                      <a href = "detail.php?id=<?= $data['id'] ?>"
                        type="button"
                        class="btn btn-sm btn-outline-success"
                      >
                        Detail
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
             <?php } ?>
            </div>
          </div>
        </div>
      </div>
        </div>
        <!-- /.row -->
        <!-- START THE FEATURETTES -->
         <div class="container my-5" id="about">
        <hr class="featurette-divider" />
        <div class="row featurette">
          <div class="col-md-7">
            <h2 class="featurette-heading fw-normal lh-1 mb-1">
              About us
            </h2>
            <br>
            <h3 class="fw-normal lh-1 mb-3">
              Cita Rasa Teh Indonesia, Peluang Usaha Masa Kini
            </h3>
            <br>
            <p class="lead">
              Berawal dari Yogyakarta, Teh Idaman hadir membawa cita rasa teh otentik Indonesia yang diracik dari daun teh pilihan berkualitas, menghasilkan rasa yang khas, nikmat, dan mudah disukai berbagai kalangan.
            </p>
            <p class="lead">
              Menggabungkan kekayaan tradisi racikan teh lokal dengan konsep minuman modern, Teh Idaman hadir bukan hanya sebagai pilihan minuman kekinian, tetapi juga sebagai peluang bisnis yang menjanjikan.
           </p>
           <p class="lead">
              Dengan sistem manajemen yang teruji, konsep usaha yang menarik, serta dukungan penuh bagi para mitra, Teh Idaman siap menjadi partner bisnis bagi Anda yang ingin membangun usaha minuman dengan brand yang kuat dan berpotensi berkembang.
            </p>
            <p class="lead"> 
              Mari tumbuh bersama Teh Idaman dan hadirkan kesegaran cita rasa teh Indonesia di lebih banyak tempat.
            </p>
          </div>
          <div class="col-md-5">
            <img src="images/about1.png"
              aria-label="Placeholder: 500x500"
              class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto"
              height="500"
              preserveAspectRatio="xMidYMid slice"
              role="img"
              width="500"
              xmlns="http://www.w3.org/2000/svg"
            >
              </img>
          </div>
        </div>
        <hr class="featurette-divider" />
        <div class="row featurette">
          <div class="col-md-7 order-md-2">
            <h2 class="featurette-heading fw-normal lh-1">
              100% Gula Asli
            </h2>
            <br>
            <p class="lead">
              Setiap cup Teh Idaman diracik dari daun teh bermutu tinggi. Memberikan sensasi kesegaran alami yang sempurna untuk menemani waktu istirahat dan momen kumpul bersama keluarga.
            </p>
          </div>
          <div class="col-md-5 order-md-1">
            <img src = "images/gula.jpg"
              aria-label="Placeholder: 500x500"
              class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto"
              height="500"
              preserveAspectRatio="xMidYMid slice"
              role="img"
              width="500"
              xmlns="http://www.w3.org/2000/svg"
            >
              </img>
          </div>
        </div>
        <hr class="featurette-divider" />
        <div class="row featurette">
          <div class="col-md-7">
            <h2 class="featurette-heading fw-normal lh-1">
              100% Ga Bikin Sakit Tenggorokan
            </h2>
            <br>
            <p class="lead">
            Mood Lagi Turun? Jurus Ampuhnya Cuma IDAMAN GAESSSS" Segarnya Teh Idaman, bikin hari kamu kembali happy! Cobain Teh Idaman, rasa nikmat yang bikin ketagihan!
            </p>
          </div>
          <div class="col-md-5">
            <img src ="images/leher.jpg"
              aria-label="Placeholder: 500x500"
              class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto"
              height="500"
              preserveAspectRatio="xMidYMid slice"
              role="img"
              width="500"
              xmlns="http://www.w3.org/2000/svg"
            >
              </img>
          </div>
        </div>

        <!-- /END THE FEATURETTES -->
      </div>
      <!-- /.container -->
    </main>
      <!-- FOOTER & CONTACT US -->
      <footer class="footer-hijau bg-hijau-tua text-white pt-5 pb-4 border-top border-success">
        <div class="container">
          <div class="row g-4">
            
            <!-- Kolom 1: Tentang / Brand -->
            <div class="col-md-4 col-sm-12">
              <h5 class="fw-bold text-uppercase mb-3">Teh Idaman</h5>
              <p class="text-body-secondary small">
                Cita rasa teh otentik Indonesia dengan konsep minuman modern. Hadir dengan 700+ cabang di seluruh Indonesia, racikan nikmat 100% gula asli tanpa pemanis buatan.
              </p>
            </div>

            <!-- Kolom 2: Kontak Kemitraan & Layanan -->
            <div class="col-md-4 col-sm-6">
              <h5 class="fw-bold mb-3">Hubungi Kami</h5>
              <ul class="list-unstyled small text-body-secondary">
                <li class="mb-2 d-flex align-items-center gap-2">
                  <svg class="footer-icon footer-icon-wa" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592z"/>
                  </svg>
                  <span>Info Kemitraan (WA): <strong>0882-1640-2253</strong></span>
                </li>
                <li class="mb-2 d-flex align-items-center gap-2">
                  <svg class="footer-icon footer-icon-pusat" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.28.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"/>
                  </svg>
                  <span>Kantor Pusat: DI Yogyakarta</span>
                </li>
              </ul>
            </div>

            <!-- Kolom 3: Media Sosial & Navigasi -->
            <div class="col-md-4 col-sm-6">
              <h5 class="fw-bold mb-3">Media Sosial</h5>
              <div class="d-flex flex-column gap-2 small">
                <a href="https://www.instagram.com/tehidaman.anakbangsa" target="_blank" class="text-body-secondary d-flex align-items-center gap-2">
                  <svg class="footer-icon text-warning" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a3.11 3.11 0 1 0 0 6.22 3.11 3.11 0 0 0 0-6.22zm0 1.442a1.668 1.668 0 1 1 0 3.336 1.668 1.668 0 0 1 0-3.336z"/>
                  </svg>
                  <span>Instagram: @tehidaman.anakbangsa</span>
                </a>
                <a href="#home" class="text-body-secondary mt-2">Kembali ke atas &uarr;</a>
              </div>
            </div>

          </div>

          <hr class="my-4 border-success" />

          <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center small text-body-secondary">
            <p class="mb-0">&copy; <?= date('Y') ?> Teh Idaman Indonesia. All rights reserved.</p>
            <div class="d-flex gap-3 mt-2 mt-sm-0">
              <a href="#" class="text-body-secondary">Privacy Policy</a>
              <a href="#" class="text-body-secondary">Terms & Conditions</a>
            </div>
          </div>
        </div>
      </footer>
    <script
      src="assets/dist/js/bootstrap.bundle.min.js"
      class="astro-vvvwv3sm"
    ></script>
    <script>
    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan keluar dari sesi akun ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#198754',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        });
    }
    </script>
  </body>
</html>