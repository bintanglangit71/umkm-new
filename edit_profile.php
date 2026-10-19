<?php
session_start();
include '../koneksi.php';

// Cek login
if(!isset($_SESSION['user_id'])){
    header('Location: login/login.php');
    exit();
}

$user_id   = $_SESSION['user_id'];
$nama      = $email = $username = $hp = $alamat = "";
$password  = "";
$err       = "";
$success   = "";

// Ambil data user terbaru dari DB
$query = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id = '$user_id'");
if($data = mysqli_fetch_array($query)){
    $nama     = $data['nama'] ?? "";
    $email    = $data['email'] ?? "";
    $username = $data['username'] ?? "";
    $hp       = $data['hp'] ?? "";
    $alamat   = $data['alamat'] ?? "";
    $password = $data['password'] ?? "";
}

if(isset($_POST['update'])){
    $new_nama     = trim($_POST['nama']);
    $new_email    = trim($_POST['email']);
    $new_hp       = trim($_POST['hp']);
    $new_alamat   = trim($_POST['alamat']);
    $new_password = $_POST['password'];

    if($new_nama == ""){
        $err = "Nama tidak boleh kosong.";
    } else {
        // Update password hanya jika diisi (tidak kosong)
        if(!empty($new_password)){
            $update = mysqli_query($koneksi, "UPDATE tb_user SET
                nama='$new_nama',
                email='$new_email',
                hp='$new_hp',
                alamat='$new_alamat',
                password='$new_password'
                WHERE id='$user_id'");
        } else {
            $update = mysqli_query($koneksi, "UPDATE tb_user SET
                nama='$new_nama',
                email='$new_email',
                hp='$new_hp',
                alamat='$new_alamat'
                WHERE id='$user_id'");
        }

        if($update){
            $success = "Profile berhasil diperbarui!";
            // Update session
            $_SESSION['user_nama'] = $new_nama;
            $_SESSION['user_email'] = $new_email;
            // Refresh data
            $nama = $new_nama;
            $email = $new_email;
            $hp = $new_hp;
            $alamat = $new_alamat;
        } else {
            $err = "Gagal memperbarui: " . mysqli_error($koneksi);
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profile — Téh Idaman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      :root{
        --hijau-tua:#0f3d2e;
        --hijau:#1a5c42;
        --krem:#f7f2e7;
        --emas:#c9a24b;
        --teks:#2b2b26;
      }
      *{margin:0;padding:0;box-sizing:border-box;}
      body{
        font-family:'Poppins',sans-serif;
        background:linear-gradient(rgba(15,61,46,.6),rgba(26,92,66,.7)), url('../images/hero.jpeg') center/cover no-repeat fixed;
        min-height:100vh;
        display:flex; align-items:center; justify-content:center;
        padding:40px 16px;
      }
      h1,h2,h3{font-family:'Fraunces',serif;}
      .profile-box{
        background:rgba(247,242,231,.12);
        backdrop-filter:blur(14px);
        -webkit-backdrop-filter:blur(14px);
        border:1px solid rgba(201,162,75,.35);
        box-shadow:0 12px 40px 0 rgba(0,0,0,.35);
        padding:42px 38px;
        border-radius:18px;
        width:100%; max-width:520px;
        color:var(--krem);
      }
      .profile-box .header{text-align:center; margin-bottom:28px;}
      .profile-box .avatar{
        width:80px; height:80px; border-radius:50%;
        background:var(--emas); color:var(--hijau-tua);
        display:inline-flex; align-items:center; justify-content:center;
        font-family:'Fraunces',serif; font-weight:700; font-size:36px;
        margin-bottom:14px;
        box-shadow:0 8px 20px rgba(201,162,75,.4);
      }
      .profile-box .header h1{font-size:26px; color:var(--krem); margin-bottom:4px;}
      .profile-box .header p{font-size:13px; opacity:.8; margin:0;}
      .form-floating label{color:rgba(247,242,231,.85);}
      .form-floating>.form-control,
      .form-floating>.form-select{
        background:rgba(247,242,231,.12);
        border:1px solid rgba(201,162,75,.35);
        color:var(--krem);
        padding-top:1.6rem;
      }
      .form-floating>.form-control:focus,
      .form-floating>.form-select:focus{
        background:rgba(247,242,231,.18);
        border-color:var(--emas);
        color:var(--krem);
        box-shadow:0 0 0 .2rem rgba(201,162,75,.25);
      }
      .form-floating>.form-control:focus~label,
      .form-floating>.form-control:not(:placeholder-shown)~label,
      .form-floating>.form-select:focus~label,
      .form-floating>.form-select:not(:placeholder-shown)~label{
        color:var(--emas);
        transform:scale(.85) translateY(-.8rem) translateX(.15rem);
      }
      .form-control:disabled{opacity:.7;}
      .form-select option{background:var(--hijau-tua); color:var(--krem);}
      .btn-idaman{
        background:var(--emas); color:var(--hijau-tua);
        font-weight:600; border:none; padding:.85rem 1rem;
        border-radius:30px; transition:.25s;
      }
      .btn-idaman:hover{background:var(--krem); color:var(--hijau-tua);}
      .btn-ghost{
        background:transparent; color:var(--krem);
        font-weight:600; border:1px solid var(--krem);
        padding:.85rem 1rem; border-radius:30px; transition:.25s;
      }
      .btn-ghost:hover{background:rgba(247,242,231,.12);}
      .alert-success-id{
        background:rgba(25,135,84,.3); border:1px solid rgba(25,135,84,.6);
        color:#d4edda; padding:12px 16px; border-radius:10px;
        font-size:14px; margin-bottom:20px;
      }
      .alert-err{
        background:rgba(220,53,69,.25); border:1px solid rgba(220,53,69,.5);
        color:#ffd5da; padding:12px 16px; border-radius:10px;
        font-size:14px; margin-bottom:20px;
      }
      .form-hint{
        font-size:12px; opacity:.7; margin-top:4px; margin-left:4px;
      }
      .back-home{
        position:fixed; top:20px; left:20px;
        color:var(--krem); font-size:14px;
        background:rgba(15,61,46,.6); padding:8px 16px; border-radius:20px;
        border:1px solid rgba(201,162,75,.35);
        transition:.2s; text-decoration:none;
      }
      .back-home:hover{background:var(--emas); color:var(--hijau-tua);}
    </style>
</head>
<body>
  <a href="../index.php" class="back-home">&larr; Beranda</a>

  <main class="m-auto w-100" style="max-width:520px;">
    <form method="post" class="profile-box">
      <div class="header">
        <div class="avatar">
          <?= strtoupper(substr($nama ?: 'U', 0, 1)) ?>
        </div>
        <h1>Edit Profile</h1>
        <p>Perbarui informasi akun Téh Idamanmu</p>
      </div>

      <?php if($success): ?>
        <div class="alert-success-id">
          <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>
      <?php if($err): ?>
        <div class="alert-err">
          <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($err) ?>
        </div>
      <?php endif; ?>

      <div class="form-floating mb-3">
        <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama Lengkap" value="<?= htmlspecialchars($nama) ?>" required>
        <label for="nama"><i class="bi bi-person"></i> Nama Lengkap</label>
      </div>

      <div class="form-floating mb-3">
        <input type="email" name="email" class="form-control" id="email" placeholder="email@contoh.com" value="<?= htmlspecialchars($email) ?>">
        <label for="email"><i class="bi bi-envelope"></i> Email</label>
      </div>

      <div class="form-floating mb-3">
        <input type="text" name="username" class="form-control" id="username" value="<?= htmlspecialchars($username) ?>" disabled>
        <label for="username"><i class="bi bi-at"></i> Username (tidak dapat diubah)</label>
      </div>

      <div class="form-floating mb-3">
        <input type="text" name="hp" class="form-control" id="hp" placeholder="08xx" value="<?= htmlspecialchars($hp) ?>">
        <label for="hp"><i class="bi bi-phone"></i> No. HP</label>
      </div>

      <div class="form-floating mb-3">
        <textarea name="alamat" class="form-control" id="alamat" placeholder="Alamat" style="height:90px; padding-top:1.6rem;"><?= htmlspecialchars($alamat) ?></textarea>
        <label for="alamat"><i class="bi bi-geo-alt"></i> Alamat</label>
      </div>

      <div class="form-floating mb-3">
        <input type="password" name="password" class="form-control" id="password" placeholder="Kosongkan jika tidak ingin ganti">
        <label for="password"><i class="bi bi-lock"></i> Password Baru</label>
      </div>
      <div class="form-hint mb-3">Kosongkan kolom password jika tidak ingin mengganti password.</div>

      <div class="d-flex gap-2">
        <button type="submit" name="update" class="btn btn-idaman flex-grow-1">
          <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
        </button>
        <a href="../index.php" class="btn btn-ghost">Batal</a>
      </div>

      <div class="text-center mt-4">
        <small style="opacity:.7;">Username: <b style="color:var(--emas);"><?= htmlspecialchars($username) ?></b></small>
      </div>
    </form>
  </main>

  <?php if($success): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: '<?= htmlspecialchars($success); ?>',
      confirmButtonColor: '#c9a24b'
    });
  </script>
  <?php endif; ?>

  <?php if($err): ?>
  <script>
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: '<?= htmlspecialchars($err); ?>',
      confirmButtonColor: '#dc3545'
    });
  </script>
  <?php endif; ?>
</body>
</html>