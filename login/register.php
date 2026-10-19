<?php
include '../koneksi.php';

$register_sukses = false;
$error = '';

if (isset($_POST['register'])) {
    $nama    = $_POST['nama'];
    $email    = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $cek = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username'");

    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $register = mysqli_query($koneksi, "INSERT INTO tb_user (nama, email, username, password, role) VALUES ('$nama','$email','$username','$password','pelanggan')");

        if ($register) {
            $register_sukses = true;
        }
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors" />
    <meta name="generator" content="Astro v5.13.2" />
    <title>Register</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/sign-in/" />
    <script src="../assets/js/color-modes.js"></script>
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="theme-color" content="#712cf9" />
    <link href="sign-in.css" rel="stylesheet" />
    <style>
      body{
        background: linear-gradient( rgba(15, 61, 46, 0.5),rgba(26, 92, 66, 0.5)), url('../images/hero.jpeg');
        background-size: cover;
        background-position: center;
        background-color: #565656;
      }
      .login-box {
        background: rgba(10, 42, 28, 0.62);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        padding: 40px;
        border-radius: 10px;
        width: 400px;
        margin: 0 auto;
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
        box-shadow: inset 0 0.5em 1.5em #0000001a, inset 0 0.125em 0.5em #00000026;
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
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;
        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }
      .login-box h1 { color: #ffffff; }
      .login-box a { color: #ffffff; }
      .login-box a:hover { color: #75d9a3; }
      .login-box .form-check-label { color: #ffffff; }
      .login-box .text-body-secondary { color: rgba(255, 255, 255, 0.75) !important; }
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
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
    </style>
  </head>
  <body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="m-auto">
      <form method="post" class="login-box text-center">
        <img
          class="mb-4 mx-auto d-block"
          src="../images/logo-bg.jpeg"
          alt="Logo"
          width="72"
          height="72"
        />
        <h1 class="h3 mb-3 fw-normal text-start">Register</h1>
        <div class="form-floating mb-2">
          <input
            type="text"
            name="nama"
            class="form-control"
            id="floatingInputUsername"
            placeholder="nama"
            value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>"
            required
          />
          <label for="floatingInputUsername">Nama</label>
        </div>
        <div class="form-floating mb-2">
          <input
            type="email"
            name="email"
            class="form-control"
            id="floatingInputEmail"
            placeholder="email"
            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
            required
          />
          <label for="floatingInputEmail">Email</label>
        </div>
        <div class="form-floating mb-2">
          <input
            type="text"
            name="username"
            class="form-control"
            id="floatingInputUsername"
            placeholder="username"
            value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
            required
          />
          <label for="floatingInputUsername">Username</label>
        </div>
        <div class="form-floating mb-3">
          <input
            type="password"
            name="password"
            class="form-control"
            id="floatingPassword"
            placeholder="Password"
            required
          />
          <label for="floatingPassword">Password</label>
        </div>
        <button class="btn btn-success w-100 py-2" type="submit" name="register">
          Register
        </button>
        <div class="mt-3 text-start">
          <a href="../carousell.php" class="text-white">Kembali ke Beranda</a>
        </div>
        <p class="mt-4 mb-3 text-body-secondary">&copy; 2017–2026</p>
      </form>
    </main>

    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($register_sukses): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Registrasi Berhasil!',
        text: 'Silahkan login dengan akun Anda.',
        confirmButtonColor: '#198754'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location = 'login.php';
        }
      });
    </script>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Registrasi Gagal!',
        text: '<?= htmlspecialchars($error) ?>',
        confirmButtonColor: '#d33'
      });
    </script>
    <?php endif; ?>
  </body>
</html>