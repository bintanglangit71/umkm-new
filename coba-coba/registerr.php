<?php
include '../koneksi.php';

if(isset($_POST['register'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $register = mysqli_query($koneksi, "INSERT INTO tb_user (username, password, email, role) VALUES ('$username','$password','$email','pelanggan')");

    if($register) {
      echo"<script>
      alert('Registrasi berhasil! Silahkan login');
      window.location='loginn.php';
      </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>daftar</title>
    <style>
         body { 
            background: linear-gradient( rgba(15, 61, 46, 0.5),rgba(26, 92, 66, 0.5)), url('../hero.jpeg');
            background-size: cover;
            background-position: center;
            background-color: #565656;
            font-family: Arial;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            padding: 40px;
            border-radius: 10px;
            width: 300px;
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ffffff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group input { width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #fff;
            box-sizing: border-box;
            border-radius: 5px;
            display: block;
            margin-bottom: 5px;
        }
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #008d54;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #01693f;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Daftar</h2>
    <form action="" method="post">
        <div class="form-group">
    Email: <input type="email" name="email">
    <br>
    Username: <input type="text" name="username">
    <br>
    Password: <input type="password" name="password">
        </div>
    <br>
    <input type="submit" name="register" class="btn">
    </form>
</div>
</body>
</html>
