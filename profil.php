<?php
session_start();
include 'koneksi.php';

// Akses hanya untuk user yang sudah login
if (!isset($_SESSION['id'])) {
    header("Location: login/login.php");
    exit;
}

$id = $_SESSION['id'];

// Helper: cek password (support plain-text lama & hash baru)
function verify_password($input, $stored) {
    if (empty($stored)) return false;
    // Deteksi hash bcrypt ($2y$, $2a$, $2b$)
    if (preg_match('/^\$2[aby]\$.+/', $stored)) {
        return password_verify($input, $stored);
    }
    // Plain-text (cara lama)
    return ($input === $stored);
}

// ============================================================
// 1. HANDLE POST (update profil)
// ============================================================
$success = '';
$error   = '';
$old     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($_POST['nama'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $hp      = trim($_POST['hp'] ?? '');
    $alamat     = trim($_POST['alamat'] ?? '');
    $pass_lama  = $_POST['password_lama'] ?? '';
    $pass_baru  = $_POST['password_baru'] ?? '';
    $pass_konf  = $_POST['password_konfirmasi'] ?? '';
    $hapus_poto  = isset($_POST['hapus_poto']) ? (int)$_POST['hapus_poto'] : 0;

    $old = compact('nama', 'email', 'hp', 'alamat');

    // Ambil data user lama
    $q_user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id='$id' LIMIT 1");
    $user   = mysqli_fetch_array($q_user);

    if (!$user) {
        $error = "User tidak ditemukan.";
    } elseif ($nama === '') {
        $error = "Nama lengkap wajib diisi.";
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        // Cek email unik (jika diisi)
        if ($email !== '') {
            $cek = mysqli_query($koneksi, "SELECT id FROM tb_user WHERE email='" . mysqli_real_escape_string($koneksi, $email) . "' AND id <> '$id' LIMIT 1");
            if (mysqli_num_rows($cek) > 0) {
                $error = "Email sudah digunakan user lain.";
            }
        }
    }

    // Handle password (opsional)
    $update_password = '';
    if (!$error && ($pass_lama !== '' || $pass_baru !== '' || $pass_konf !== '')) {
        if ($pass_lama === '' || $pass_baru === '' || $pass_konf === '') {
            $error = "Untuk ganti password, ketiga kolom password wajib diisi.";
        } elseif (!verify_password($pass_lama, $user['password'])) {
            $error = "Password lama salah.";
        } elseif (strlen($pass_baru) < 6) {
            $error = "Password baru minimal 6 karakter.";
        } elseif ($pass_baru !== $pass_konf) {
            $error = "Konfirmasi password baru tidak cocok.";
        } else {
            // Simpan sebagai hash bcrypt (upgrade dari plain-text otomatis)
            $hash_baru       = password_hash($pass_baru, PASSWORD_DEFAULT);
            $update_password = ", password='" . mysqli_real_escape_string($koneksi, $hash_baru) . "'";
        }
    }

    // Handle foto profil
    $nama_poto_baru = $user['poto'] ?? '';

    if (!$error && $hapus_poto === 1 && !empty($user['poto'])) {
        $path_lama = "images/" . $user['poto'];
        if (file_exists($path_lama)) @unlink($path_lama);
        $nama_poto_baru = '';
    }

    if (!$error && isset($_FILES['poto']) && $_FILES['poto']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['poto'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if ($file['size'] > $max_size) {
            $error = "Ukuran foto melebihi 2MB.";
        } else {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
            $finfo   = finfo_open(FILEINFO_MIME_TYPE);
            $mime    = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!array_key_exists($mime, $allowed)) {
                $error = "Format foto tidak didukung. Gunakan JPG/PNG/GIF/WEBP.";
            } else {
                $ext            = $allowed[$mime];
                $nama_poto_baru = 'user_' . $id . '_' . time() . '.' . $ext;
                $tujuan         = "images/" . $nama_poto_baru;

                if (!is_dir("images")) @mkdir("images", 0777, true);

                if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
                    $error = "Gagal mengunggah foto. Coba lagi.";
                } elseif (!empty($user['poto']) && $user['poto'] !== $nama_poto_baru) {
                    $path_lama = "images/" . $user['poto'];
                    if (file_exists($path_lama)) @unlink($path_lama);
                }
            }
        }
    }

    // Eksekusi UPDATE
    if (!$error) {
        $nama_e   = mysqli_real_escape_string($koneksi, $nama);
        $email_e  = mysqli_real_escape_string($koneksi, $email);
        $hp_e  = mysqli_real_escape_string($koneksi, $hp);
        $alamat_e = mysqli_real_escape_string($koneksi, $alamat);
        $foto_e   = mysqli_real_escape_string($koneksi, $nama_poto_baru);

        $sql = "UPDATE tb_user SET
                    nama   = '$nama_e',
                    email  = '$email_e',
                    hp  = '$hp_e',
                    alamat = '$alamat_e',
                    poto   = '$foto_e'
                    $update_password
                WHERE id = '$id'";

        if (mysqli_query($koneksi, $sql)) {
            $success = "Profil berhasil diperbarui.";
            $old     = [];
        } else {
            $error = "Gagal menyimpan perubahan: " . mysqli_error($koneksi);
        }
    }
}

// ============================================================
// 2. AMBIL DATA USER (selalu fresh setelah update)
// ============================================================
$q_user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id='$id' LIMIT 1");
$user   = mysqli_fetch_array($q_user);

if (!$user) {
    header("Location: logout.php");
    exit;
}

// Nilai form: pakai input lama (jika ada error) atau data DB
$f_nama   = $old['nama']   ?? $user['nama']   ?? '';
$f_email  = $old['email']  ?? $user['email']  ?? '';
$f_hp  = $old['hp']  ?? $user['hp']  ?? '';
$f_alamat = $old['alamat'] ?? $user['alamat'] ?? '';

$foto_url = (!empty($user['poto']) && file_exists("images/" . $user['poto']))
    ? 'images/' . htmlspecialchars($user['poto'])
    : 'https://placehold.co/160x160?text=Foto';
?>
<!doctype html>
<html lang="id" data-bs-theme="auto">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profil Saya · Teh Idaman</title>
    <script src="assets/js/color-modes.js"></script>
    <meta name="theme-color" content="#0f3d2a" />
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="theme-hijau.css" rel="stylesheet" />
    <link href="carousel.css" rel="stylesheet" />
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .profile-avatar {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--hijau-utama);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        }
        .profile-form {
            max-width: 760px;
            margin: 0 auto;
        }
        .card-header {
            background-color: rgba(25, 135, 84, 0.12);
            border-bottom: 1px solid rgba(25, 135, 84, 0.25);
        }
        .card-header h5 { color: var(--hijau-utama); font-weight: 700; }
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
                <a class="nav-link" href="carousell.php#home">Home</a>
              </li>
              <li class="nav-item"><a class="nav-link" href="carousell.php#menu">Menu</a></li>
              <li class="nav-item"><a class="nav-link" href="carousell.php#about">About Us</a></li>
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
            <a class="nav-link nav-profile dropdown-toggle d-flex align-items-center active" href="#" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
              <?php if ($user && !empty($user['poto']) && file_exists("images/" . $user['poto'])): ?>
                <img src="images/<?= htmlspecialchars($user['poto']) ?>" class="rounded-circle me-2" width="28" height="28" style="object-fit:cover" alt="">
              <?php elseif ($user): ?>
                <span class="me-2 d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white" style="width:28px;height:28px;font-size:12px;font-weight:600;">
                  <?= strtoupper(substr($user['nama'] ?? 'U', 0, 1)) ?>
                </span>
              <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                </svg>
              <?php endif; ?>
              <?= $user ? htmlspecialchars($user['nama']) : 'Akun' ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <?php if ($user): ?>
                <li><a class="dropdown-item active bg-success" href="profil.php">Profil Saya</a></li>
                <li><a class="dropdown-item" href="riwayat.php">Riwayat Pesanan</a></li>
                <?php if ($user['role'] == 'admin'): ?>
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

<main class="py-5" style="margin-top: 72px;">
    <div class="container">
        <div class="profile-form">

            <h2 class="mb-4">Profil Saya</h2>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Avatar + identitas singkat -->
            <div class="text-center mb-4">
                <img id="avatarPreview" src="<?= $foto_url ?>" class="profile-avatar" alt="Foto Profil">
                <h3 class="mt-3 mb-0"><?= htmlspecialchars($user['nama']) ?></h3>
                <p class="text-muted mb-0">@<?= htmlspecialchars($user['username']) ?></p>
            </div>

            <!-- Form edit (inline di halaman yang sama) -->
            <form action="profil.php" method="POST" enctype="multipart/form-data" novalidate>

                <!-- Foto Profil -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Foto Profil</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="poto" class="form-label">Upload Foto Baru</label>
                            <input type="file" class="form-control" id="poto" name="poto" accept="image/*">
                            <div class="form-text">Format: JPG, JPEG, PNG, GIF, WEBP. Maks 2MB.</div>
                        </div>
                        <?php if (!empty($user['poto'])): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="hapus_poto" name="hapus_poto" value="1">
                                <label class="form-check-label text-danger" for="hapus_poto">
                                    Hapus foto profil saat ini
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Data Profil -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Profil</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" required
                                   value="<?= htmlspecialchars($f_nama) ?>"
                                   placeholder="Masukkan nama lengkap">
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                            <div class="form-text">Username tidak dapat diubah.</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($f_email) ?>"
                                   placeholder="nama@email.com">
                        </div>

                        <div class="mb-3">
                            <label for="hp" class="form-label">No. HP</label>
                            <input type="tel" class="form-control" id="hp" name="hp"
                                   value="<?= htmlspecialchars($f_hp) ?>"
                                   placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap"><?= htmlspecialchars($f_alamat) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Ganti Password (opsional) -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Keamanan <small class="text-muted fw-normal">(opsional)</small></h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="password_lama" class="form-label">Password Lama</label>
                            <input type="password" class="form-control" id="password_lama" name="password_lama" placeholder="Kosongkan jika tidak ganti password">
                        </div>
                        <div class="mb-3">
                            <label for="password_baru" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password_baru" name="password_baru" placeholder="Minimal 6 karakter">
                        </div>
                        <div class="mb-3">
                            <label for="password_konfirmasi" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_konfirmasi" name="password_konfirmasi" placeholder="Ulangi password baru">
                        </div>
                        <div class="form-text text-muted">
                            Isi ketiga kolom di atas hanya jika ingin mengganti password.
                            Password baru akan disimpan dalam bentuk hash (lebih aman).
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="carousell.php" class="btn btn-outline-success rounded-pill px-4">Kembali</a>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>

        </div>
    </div>
</main>

      <div class="container mt-auto pb-4">
      <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center pt-3 small text-body-secondary text-center">
        <p class="mb-0">&copy; <?= date('Y') ?> Teh Idaman Indonesia. All rights reserved.</p>
      </div>
    </div>

<script src="assets/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Preview foto sebelum upload
document.getElementById('poto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('avatarPreview').src = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php if ($success): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= htmlspecialchars($success); ?>',
    confirmButtonColor: '#198754'
  });
</script>
<?php endif; ?>

<?php if ($error): ?>
<script>
  Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '<?= htmlspecialchars($error); ?>',
    confirmButtonColor: '#d33'
  });
</script>
<?php endif; ?>
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