<?php
session_start();
include 'koneksi.php';

$id_user = $_SESSION['id_user'] ?? $_SESSION['id'] ?? null;

if (empty($id_user)) {
    header("Location: login.php?pesan=harus_login");
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT id_transaksi, tanggal, total_harga
                                   FROM tb_transaksi
                                   WHERE id_pelanggan = ?
                                   ORDER BY tanggal DESC, id_transaksi DESC");
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$riwayat = mysqli_stmt_get_result($stmt);

$jumlah_keranjang = isset($_SESSION['cart']) ? count($_SESSION['cart']) : (isset($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0);
$sudah_login      = !empty($id_user);
$nama_user        = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Pengguna';
?>
<!doctype html>
<html lang="id" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Pesanan · Teh Idaman</title>

    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="theme-hijau.css" rel="stylesheet">
    <script src="assets/js/color-modes.js"></script>
    <meta name="theme-color" content="#0f3d2a">
    <link href="carousel.css" rel="stylesheet">
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

        .ti-riwayat-wrap { padding: 3rem 0 6rem; }
        .ti-riwayat-card {
            background-color: var(--bs-body-bg);
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid var(--bs-border-color);
        }
        .ti-riwayat-table { margin-bottom: 0; }
        .ti-riwayat-table thead th {
            background-color: rgba(25, 135, 84, 0.12);
            color: #198754;
            border: none;
            font-weight: 700;
            padding: 1rem 0.75rem;
        }
        .ti-riwayat-table td {
            vertical-align: middle;
            padding: 0.9rem 0.75rem;
        }
        .ti-riwayat-id {
            font-weight: 700;
            color: #198754;
        }
        .btn-ti-outline {
            border: 2px solid #198754;
            color: #198754;
            border-radius: 50rem;
            font-weight: 600;
            padding: 0.35rem 1.1rem;
            background: transparent;
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-block;
            transition: all 0.2s ease-in-out;
        }
        .btn-ti-outline:hover { 
            background-color: #198754; 
            color: #fff; 
        }
        .btn-ti-primary {
            background-color: #198754;
            color: #fff;
            border: none;
        }
        .btn-ti-primary:hover {
            background-color: #146c43;
            color: #fff;
        }
        .ti-empty-riwayat {
            text-align: center;
            padding: 4rem 1rem;
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
      .navbar-hijau .brand-icon {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.14);
        font-size: 1.1rem;
        line-height: 1;
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
          <button class="navbar-toggler" type="button"
                  data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                  aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
              <li class="nav-item"><a class="nav-link" href="carousell.php#home">Home</a></li>
              <li class="nav-item"><a class="nav-link" href="carousell.php#menu">Menu</a></li>
              <li class="nav-item"><a class="nav-link" href="carousell.php#about">About Us</a></li>
            </ul>
            <ul class="navbar-nav align-items-lg-center gap-lg-2">
              <li class="nav-item">
                <a class="nav-link nav-cart position-relative" href="keranjang.php" title="Keranjang">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                  </svg>
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link nav-profile dropdown-toggle d-flex align-items-center active" href="#" role="button"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                  </svg>
                  <?= $sudah_login ? htmlspecialchars($nama_user) : 'Akun' ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <?php if ($sudah_login) { ?>
                    <li><a class="dropdown-item" href="profil.php">Profil Saya</a></li>
                    <li><a class="dropdown-item active bg-success" href="riwayat.php">Riwayat Pesanan</a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                      <li><a class="dropdown-item" href="dashboard/dashboard.php">Dashboard</a></li>
                    <?php } ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                  <?php } else { ?>
                    <li><a class="dropdown-item" href="login/login.php">Login</a></li>
                    <li><a class="dropdown-item" href="login/register.php">Daftar Akun</a></li>
                  <?php } ?>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <main class="mt-5 pt-3">
        <div class="container ti-riwayat-wrap">
            <h2 class="fw-bold mb-4">Riwayat Pesanan Saya</h2>

            <?php if ($riwayat && mysqli_num_rows($riwayat) > 0) { ?>
                <div class="ti-riwayat-card">
                    <table class="table ti-riwayat-table align-middle">
                        <thead>
                            <tr class="text-center">
                                <th class="text-start">No. Struk</th>
                                <th>Tanggal</th>
                                <th>Total Bayar</th>
                                <th>Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($trx = mysqli_fetch_assoc($riwayat)) {
                                $no_struk = 'TI-' . str_pad($trx['id_transaksi'], 5, '0', STR_PAD_LEFT);
                            ?>
                                <tr>
                                    <td class="ti-riwayat-id">#<?= htmlspecialchars($no_struk) ?></td>
                                    <td class="text-center"><?= date('d F Y', strtotime($trx['tanggal'])) ?></td>
                                    <td class="text-center fw-semibold">Rp <?= number_format($trx['total_harga'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="cetak.php?id=<?= (int) $trx['id_transaksi'] ?>" class="btn-ti-outline" target="_blank">
                                            Lihat &amp; Cetak
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="ti-empty-riwayat">
                    <p class="lead mb-3">Belum ada riwayat pesanan.</p>
                    <a href="carousell.php#menu" class="btn btn-ti-primary rounded-pill px-4">Yuk Pesan Teh Idaman</a>
                </div>
            <?php } ?>

        </div>
      <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center pt-3 small text-body-secondary text-center">
          <p class="mb-0">&copy; <?= date('Y') ?> Teh Idaman Indonesia. All rights reserved.</p>
      </div>
    </main>

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>