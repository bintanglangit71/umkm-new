<?php
session_start();
include '../koneksi.php';

$error_login = false;

if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $login = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username' LIMIT 1");
    
    if (mysqli_num_rows($login) > 0) {
        $data = mysqli_fetch_array($login);

        // ===== Cek password (support plain-text & hash) =====
        $password_ok = false;
        if (!empty($data['password'])) {
            if (preg_match('/^\$2[aby]\$.+/', $data['password'])) {
                $password_ok = password_verify($password, $data['password']);
            } else {
                $password_ok = ($password === $data['password']);
            }
        }

        if ($password_ok) {
            // ===== Set session untuk SEMUA role =====
            $_SESSION['id']       = $data['id'];       
            $_SESSION['username'] = $data['username'];
            $_SESSION['role']     = $data['role'];

            if ($data['role'] == 'admin') {
                $_SESSION['admin'] = $username;
                header('Location: ../dashboard/dashboard.php');
                exit;
            } elseif ($data['role'] == 'pelanggan') {
                header('Location: ../carousell.php');
                exit;
            } else {
                header('Location: ../carousell.php');
                exit;
            }
        } else {
            $error_login = true;
        }
    } else {
        $error_login = true;
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
    <title>Signin</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/sign-in/" />
    <script src="../assets/js/color-modes.js"></script>
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- SweetAlert2 CDN -->
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
        <!-- Logo di Tengah -->
        <img
          class="mb-4 mx-auto d-block"
          src="../images/logo-bg.jpeg"
          alt="Logo"
          width="72"
          height="72"
        />
        <h1 class="h3 mb-3 fw-normal text-start">Please sign in</h1>
        <div class="form-floating mb-2">
          <input
            type="text"
            name="username"
            class="form-control"
            id="floatingInput"
            placeholder="username"
            required
          />
          <label for="floatingInput">Username</label>
        </div>
        <div class="form-floating">
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
        <div class="form-check text-start my-3">
          <input
            class="form-check-input"
            type="checkbox"
            value="remember-me"
            id="checkDefault"
          />
          <label class="form-check-label" for="checkDefault">
            Remember me
          </label>
        </div>
        <button class="btn btn-success w-100 py-2" type="submit" name="login">
          Sign in
        </button>
        <div class="mt-3 text-start">
          <a href="register.php" class="text-white">Belum punya akun? Daftar</a>
        </div>
        <p class="mt-4 mb-3 text-body-secondary">&copy; 2017–2026</p>
      </form>
    </main>

    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>

    <?php if($error_login): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Gagal Login',
        text: 'Username atau password salah!',
        confirmButtonColor: '#198754'
      });
    </script>
    <?php endif; ?>
  </body>
</html>