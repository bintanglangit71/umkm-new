<?php
session_start();

// Hapus semua session
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #565656;
        }
    </style>
</head>
<body>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil Logout',
        text: 'Anda telah keluar dari sistem.',
        timer: 1800,
        showConfirmButton: false
      }).then(() => {
        window.location = 'login/login.php';
      });
    </script>
</body>
</html>