<?php
include "../koneksi.php";
session_start();
if(!isset($_SESSION['admin'])){
  header('Location:../login/login.php');
  exit();
}

$menu = isset($_GET['menu']) ? $_GET['menu'] : 'produk';
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : "";
$id   = isset($_GET['id']) ? $_GET['id'] : "";

// produk

// Variabel bawaan untuk form
$nama = $harga = $stok = $id_kategori = $deskripsi = $poto_lama = "";

if ($menu == 'produk' && $aksi == "edit" && !empty($id)) {
    $result = mysqli_query($koneksi, "SELECT * FROM tb_produk WHERE id = '$id'");
    if ($data = mysqli_fetch_array($result)) {
        $nama        = $data['nama'];
        $harga       = $data['harga'];
        $stok        = $data['stok'];
        $id_kategori = $data['id_kategori'];
        $deskripsi   = $data['deskripsi'];
        $poto_lama   = $data['poto'];
    }
}

if ($menu == 'produk' && isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];

    // Hapus file foto jika ada
    $get_foto = mysqli_query($koneksi, "SELECT poto FROM tb_produk WHERE id = '$id_hapus'");
    $data_foto = mysqli_fetch_array($get_foto);
    if (!empty($data_foto['poto']) && file_exists("../images/" . $data_foto['poto'])) {
        unlink("../images/" . $data_foto['poto']);
    }

    $hapus = mysqli_query($koneksi, "DELETE FROM tb_produk WHERE id = '$id_hapus'");
    if ($hapus) {
        header('Location: dashboard.php?menu=produk');
        exit();
    }
}

if ($menu == 'produk' && isset($_POST['tambah'])) {
    $nama        = $_POST['nama'];
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    $id_kategori = $_POST['id_kategori'];
    $deskripsi   = $_POST['deskripsi'];
    $poto_input  = $_FILES['poto']['name'];

    if ($aksi == 'edit') {
        // Jika mengupload foto baru
        if (!empty($poto_input)) {
            $path = "../images/" . $poto_input;
            move_uploaded_file($_FILES['poto']['tmp_name'], $path);
            $poto_fix = $poto_input;
        } else {
            $poto_fix = $poto_lama;
        }

        $simpan = mysqli_query($koneksi, "UPDATE tb_produk SET
                    nama='$nama',
                    harga='$harga',
                    stok='$stok',
                    id_kategori='$id_kategori',
                    poto='$poto_fix',
                    deskripsi='$deskripsi'
                  WHERE id='$id'");

        if ($simpan) {
            header('Location: dashboard.php?menu=produk');
            exit();
        }
    } else {
        // Proses Tambah Baru
        if (!empty($poto_input)) {
            $path = "../images/" . $poto_input;
            move_uploaded_file($_FILES['poto']['tmp_name'], $path);
        }

        $simpan = mysqli_query($koneksi, "INSERT INTO tb_produk (nama, harga, stok, poto, id_kategori, deskripsi)
                  VALUES ('$nama', '$harga', '$stok', '$poto_input', '$id_kategori', '$deskripsi')");

        if ($simpan) {
            header('Location: dashboard.php?menu=produk');
            exit();
        }
    }
}

// reset password

if ($menu == 'pelanggan' && isset($_GET['reset'])) {
    $id_reset = intval($_GET['reset']);

    $cek_user = mysqli_query($koneksi, "SELECT username FROM tb_user WHERE id='$id_reset' AND role='pelanggan'");
    if ($user_reset = mysqli_fetch_array($cek_user)) {
        // Password direset kembali menjadi sama dengan username pelanggan
        $password_baru = mysqli_real_escape_string($koneksi, $user_reset['username']);
        mysqli_query($koneksi, "UPDATE tb_user SET password='$password_baru' WHERE id='$id_reset'");
        header('Location: dashboard.php?menu=pelanggan&reset_success=1');
        exit();
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head><script src="../assets/js/color-modes.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.122.0">
    <title>Dashboard · Teh Idaman</title>
    <meta name="theme-color" content="#0f3d2a">

    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/dashboard/">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">

<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../theme-hijau.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
      :root { --header-h: 56px; }
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
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
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

      .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
      }

      /* ===== Tema hijau tua: header, sidebar & komponen ===== */
      :root { --hijau-sedang: #1b5e43; }

      .navbar-hijau-ds {
        min-height: var(--header-h);
        background: linear-gradient(90deg, #0a2a1c 0%, #0f3d2a 55%, #146c43 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      }
      .navbar-hijau-ds .navbar-brand {
        font-weight: 700;
        letter-spacing: 0.5px;
      }

      .sidebar,
      .sidebar .offcanvas-md {
        background: linear-gradient(180deg, #0f3d2a 0%, #0a2a1c 100%) !important;
      }
      @media (min-width: 768px) {
        .sidebar .offcanvas-md { background: transparent !important; }
      }
      .sidebar.border { border-color: rgba(255, 255, 255, 0.08) !important; }
      .sidebar .nav .nav-item .nav-link {
        color: rgba(255, 255, 255, 0.82);
        border-radius: 10px;
        margin: 2px 12px;
        padding: 0.55rem 0.85rem;
        transition: background-color 0.2s ease, color 0.2s ease;
      }
      .sidebar .nav .nav-item .nav-link:hover {
        color: #ffffff;
        background-color: rgba(255, 255, 255, 0.1);
      }
      .sidebar .nav .nav-item .nav-link.active {
        color: #ffffff;
        background-color: rgba(255, 255, 255, 0.14);
      }
      .sidebar hr { color: #ffffff; }
      .sidebar .offcanvas-title { color: #ffffff; font-weight: 700; }
      .sidebar .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

      .table thead th {
        background-color: var(--hijau-tua);
        color: #ffffff;
        border-bottom: 0;
      }
      .modal-header {
        background: linear-gradient(135deg, #0f3d2a 0%, #146c43 100%);
        color: #ffffff;
      }
      .modal-header .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

      .form-control:focus, .form-select:focus, .form-check-input:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
      }
      .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
      }
      .table-responsive {
        overflow-x: auto !important;
        overflow-y: hidden !important;
        -webkit-overflow-scrolling: touch;
      }
      .table-responsive > .table {
        min-width: 640px;
      }
      .table td:last-child {
        white-space: nowrap;
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">
  </head>
  <body>
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="check2" viewBox="0 0 16 16">
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
        <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
      </symbol>
    </svg>

    <div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
      <button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center"
              id="bd-theme"
              type="button"
              aria-expanded="false"
              data-bs-toggle="dropdown"
              aria-label="Toggle theme (auto)">
        <svg class="bi my-1 theme-icon-active" width="1em" height="1em"><use href="#circle-half"></use></svg>
        <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
        <li>
          <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
            <svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#sun-fill"></use></svg>
            Light
            <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
            <svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#moon-stars-fill"></use></svg>
            Dark
            <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
            <svg class="bi me-2 opacity-50" width="1em" height="1em"><use href="#circle-half"></use></svg>
            Auto
            <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
          </button>
        </li>
      </ul>
    </div>

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="aperture" viewBox="0 0 16 16">
    <circle cx="8" cy="8" r="8"/>
  </symbol>
  <symbol id="cart" viewBox="0 0 16 16">
    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
  <symbol id="chevron-right" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
  </symbol>
  <symbol id="door-closed" viewBox="0 0 16 16">
    <path d="M3 2a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v13h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2zm1 13h8V2H4v13z"/>
    <path d="M9 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
  </symbol>
  <symbol id="file-earmark" viewBox="0 0 16 16">
    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
  </symbol>
  <symbol id="file-earmark-text" viewBox="0 0 16 16">
    <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
    <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
  </symbol>
  <symbol id="house-fill" viewBox="0 0 16 16">
    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5z"/>
    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6Z"/>
  </symbol>
  <symbol id="list" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
  </symbol>
  <symbol id="people" viewBox="0 0 16 16">
    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.72C2.312 10.629 3.282 10 5 10c.242 0 .473.013.694.038-.135.213-.25.438-.344.672zM6 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm1-2a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
  </symbol>
  <symbol id="plus-circle" viewBox="0 0 16 16">
    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
  </symbol>
  <symbol id="search" viewBox="0 0 16 16">
    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
  </symbol>
</svg>

<header class="navbar sticky-top navbar-hijau-ds flex-md-nowrap p-0 shadow" data-bs-theme="dark">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="#">Admin Dashboard</a>

  <ul class="navbar-nav flex-row d-md-none">
    <li class="nav-item text-nowrap">
      <button class="nav-link px-3 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSearch" aria-controls="navbarSearch" aria-expanded="false" aria-label="Toggle search">
        <svg class="bi"><use href="#search"/></svg>
      </button>
    </li>
    <li class="nav-item text-nowrap">
      <button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <svg class="bi"><use href="#list"/></svg>
      </button>
    </li>
  </ul>

  <div id="navbarSearch" class="navbar-search w-100 collapse">
    <input class="form-control w-100 rounded-0 border-0" type="text" placeholder="Search" aria-label="Search">
  </div>
</header>

<div class="container-fluid">
  <div class="row">

    <!-- SIDEBAR -->
    <div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
      <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="sidebarMenuLabel">Teh Idaman</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2 <?= ($menu == 'produk') ? 'active' : '' ?>" <?= ($menu == 'produk') ? 'aria-current="page"' : '' ?> href="dashboard.php?menu=produk">
                <svg class="bi"><use href="#cart"/></svg>
                Produk
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2 <?= ($menu == 'pelanggan') ? 'active' : '' ?>" <?= ($menu == 'pelanggan') ? 'aria-current="page"' : '' ?> href="dashboard.php?menu=pelanggan">
                <svg class="bi"><use href="#people"/></svg>
                Pelanggan
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2 <?= ($menu == 'transaksi') ? 'active' : '' ?>" <?= ($menu == 'transaksi') ? 'aria-current="page"' : '' ?> href="dashboard.php?menu=transaksi">
                <svg class="bi"><use href="#file-earmark-text"/></svg>
                Transaksi
              </a>
            </li>
          </ul>

          <hr class="my-3">

          <ul class="nav flex-column mb-auto">
            <li class="nav-item">
              <a class="nav-link d-flex align-items-center gap-2" href="../carousell.php">
                <svg class="bi"><use href="#door-closed"/></svg>
                Sign out
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <h2>Halo <?php echo $_SESSION['username']; ?></h2>

<?php if ($menu == 'produk'): ?>

      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><?= ($aksi == 'edit') ? 'Edit Produk' : 'Tambah Produk'; ?></h1>
      </div>

      <!-- FORM PRODUK -->
      <form class="row g-3" method="POST" enctype="multipart/form-data" action="dashboard.php?menu=produk<?= ($aksi=='edit') ? '&aksi=edit&id='.$id : '' ?>">
        <div class="col-md-6">
          <label class="form-label">Nama Produk</label>
          <input type="text" class="form-control" name="nama" value="<?= $nama; ?>" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Stok</label>
          <input type="number" class="form-control" name="stok" value="<?= $stok; ?>" required>
        </div>

        <div class="col-12">
          <label class="form-label">Deskripsi</label>
          <input type="text" class="form-control" name="deskripsi" value="<?= $deskripsi; ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Harga</label>
          <div class="input-group">
            <span class="input-group-text">Rp.</span>
            <input type="number" class="form-control" name="harga" value="<?= $harga; ?>" required>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Kategori</label>
          <select class="form-select" name="id_kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php
            $result_kat = mysqli_query($koneksi, "SELECT * FROM tb_kategori");
            while($list = mysqli_fetch_array($result_kat)) {
                $selected = ($list['id_kategori'] == $id_kategori) ? "selected" : "";
            ?>
              <option value="<?= $list['id_kategori']; ?>" <?= $selected; ?>>
                <?= $list['nama_kategori']; ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Foto Produk</label>
          <input type="file" class="form-control" name="poto">
          <?php if($aksi == 'edit' && !empty($poto_lama)): ?>
            <small class="text-muted">Foto saat ini: <?= $poto_lama; ?></small>
          <?php endif; ?>
        </div>

        <div class="col-12 mb-4">
          <button type="submit" class="btn btn-success" name="tambah">
            <?= ($aksi == 'edit') ? 'Update Produk' : 'Tambah Produk'; ?>
          </button>
          <?php if($aksi == 'edit'): ?>
            <a href="dashboard.php?menu=produk" class="btn btn-secondary">Batal</a>
          <?php endif; ?>
        </div>
      </form>

      <h2>Daftar Produk</h2>
      <div class="table-responsive small">
        <table class="table table-striped table-sm align-middle">
          <thead>
            <tr>
              <th scope="col">No</th>
              <th scope="col">Nama</th>
              <th scope="col">Harga</th>
              <th scope="col">Stok</th>
              <th scope="col">Foto</th>
              <th scope="col">Kategori</th>
              <th scope="col">Deskripsi</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $hasil = mysqli_query($koneksi, "SELECT p.*, k.nama_kategori FROM tb_produk p LEFT JOIN tb_kategori k ON p.id_kategori = k.id_kategori");
            while($data = mysqli_fetch_array($hasil)) { ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= $data['nama'] ?></td>
                <td>Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
                <td><?= $data['stok'] ?></td>
                <td>
                  <?php if(!empty($data['poto'])): ?>
                    <img src="../images/<?= $data['poto'] ?>" width="60" height="60" style="object-fit:cover; border-radius:6px;">
                  <?php else: ?>
                    <span class="badge bg-secondary">Tidak ada foto</span>
                  <?php endif; ?>
                </td>
                <td><?= $data['nama_kategori'] ?></td>
                <td><?= $data['deskripsi'] ?></td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="dashboard.php?menu=produk&aksi=edit&id=<?= $data['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="dashboard.php?menu=produk&hapus=<?= $data['id'] ?>" onclick="return confirmHapusProduk(event, this.href)" class="btn btn-sm btn-danger">Hapus</a>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

<?php elseif ($menu == 'pelanggan'): ?>

      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Data Pelanggan</h1>
      </div>

      <?php if (isset($_GET['reset_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Password pelanggan berhasil direset (password baru = username pelanggan).
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="table-responsive small">
        <table class="table table-striped table-sm align-middle">
          <thead>
            <tr>
              <th scope="col">No</th>
              <th scope="col">Nama</th>
              <th scope="col">Email</th>
              <th scope="col">Username</th>
              <th scope="col">No HP</th>
              <th scope="col">Alamat</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $hasil_pelanggan = mysqli_query($koneksi, "SELECT id, nama, email, username, hp, alamat FROM tb_user WHERE role = 'pelanggan'");
            while ($p = mysqli_fetch_array($hasil_pelanggan)) { ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= !empty($p['nama']) ? $p['nama'] : '-' ?></td>
                <td><?= !empty($p['email']) ? $p['email'] : '-' ?></td>
                <td><?= $p['username'] ?></td>
                <td><?= !empty($p['hp']) ? $p['hp'] : '-' ?></td>
                <td><?= !empty($p['alamat']) ? $p['alamat'] : '-' ?></td>
                <td>
                  <a href="dashboard.php?menu=pelanggan&reset=<?= $p['id'] ?>"
                     onclick="return confirmResetPassword(event, this.href, '<?= htmlspecialchars($p['username']) ?>')"
                     class="btn btn-sm btn-warning">
                    Reset Password
                  </a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

<?php elseif ($menu == 'transaksi'): ?>

      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Riwayat Transaksi</h1>
      </div>

      <?php
      // Ambil semua transaksi dulu. Modal detail dirender SETELAH tabel selesai,
      // karena <div> tidak boleh berada di dalam <tbody> (itu yang merusak tampilan tabel).
      $daftar_transaksi = [];
      $hasil_transaksi = mysqli_query($koneksi, "SELECT t.*, u.nama AS nama_pelanggan, u.username
                                                   FROM tb_transaksi t
                                                   LEFT JOIN tb_user u ON t.id_pelanggan = u.id
                                                   ORDER BY t.tanggal DESC, t.id_transaksi DESC");
      while ($t = mysqli_fetch_assoc($hasil_transaksi)) {
          $t['nama_tampil'] = !empty($t['nama_pelanggan']) ? $t['nama_pelanggan'] : $t['username'];
          $id_t = (int)$t['id_transaksi'];
          $t['items'] = [];
          $hasil_detail = mysqli_query($koneksi, "SELECT d.jumlah, p.nama, p.harga
                                                   FROM tb_detail d
                                                   LEFT JOIN tb_produk p ON d.id_produk = p.id
                                                   WHERE d.id_transaksi = $id_t");
          while ($d = mysqli_fetch_assoc($hasil_detail)) {
              $t['items'][] = $d;
          }
          $daftar_transaksi[] = $t;
      }
      ?>

      <div class="table-responsive small">
        <table class="table table-striped table-sm align-middle">
          <thead>
            <tr>
              <th scope="col">No</th>
              <th scope="col">ID Transaksi</th>
              <th scope="col">Pelanggan</th>
              <th scope="col">Tanggal</th>
              <th scope="col">Total Harga</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($daftar_transaksi)): ?>
              <tr><td colspan="6" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
            <?php endif; ?>
            <?php $no = 1; foreach ($daftar_transaksi as $t): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td>#<?= $t['id_transaksi'] ?></td>
                <td><?= htmlspecialchars($t['nama_tampil']) ?></td>
                <td><?= date('d-m-Y', strtotime($t['tanggal'])) ?></td>
                <td>Rp <?= number_format($t['total_harga'], 0, ',', '.') ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $t['id_transaksi'] ?>">
                    Lihat Detail
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Modal Detail Transaksi (di luar tabel) -->
      <?php foreach ($daftar_transaksi as $t): ?>
        <div class="modal fade" id="modalDetail<?= $t['id_transaksi'] ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi #<?= $t['id_transaksi'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p class="mb-3">
                  <strong>Pelanggan:</strong> <?= htmlspecialchars($t['nama_tampil']) ?><br>
                  <strong>Tanggal:</strong> <?= date('d-m-Y', strtotime($t['tanggal'])) ?>
                </p>
                <div class="table-responsive">
                  <table class="table table-sm table-bordered align-middle">
                    <thead>
                      <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($t['items'] as $d): ?>
                        <tr>
                          <td><?= !empty($d['nama']) ? htmlspecialchars($d['nama']) : '-' ?></td>
                          <td>Rp <?= number_format($d['harga'], 0, ',', '.') ?></td>
                          <td><?= $d['jumlah'] ?></td>
                          <td>Rp <?= number_format($d['harga'] * $d['jumlah'], 0, ',', '.') ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th>Rp <?= number_format($t['total_harga'], 0, ',', '.') ?></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

<?php endif; ?>

    </main>
  </div>
</div>

<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmHapusProduk(e, url) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus Produk?',
        text: "Yakin ingin menghapus produk ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#198754',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
    return false;
}

function confirmResetPassword(e, url, username) {
    e.preventDefault();
    Swal.fire({
        title: 'Reset Password?',
        text: 'Reset password pelanggan "' + username + '"? Password akan dikembalikan menjadi sama dengan username.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, reset!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
    return false;
}
</script>
</body>
</html>