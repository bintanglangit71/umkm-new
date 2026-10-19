<?php
include 'koneksi.php';

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $login = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username' AND password='$password'");
    if (mysqli_num_rows($login) > 0) {
        $data = mysqli_fetch_array($login);
        if($data['role'] == 'admin') {
            header('Location: dashboard.php');
        } elseif($data['role'] == 'pelanggan'){
            header('Location: index.php');
        }
    } else {
        echo"<script>alert('Username atau password salah')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>
<body>
    <form action="" method="post">
    username: <input type="text" name="username">
    <br><br>
    password: <input type="password" name="password">
    <input type="submit" name="login">
    </form>
</body>
</html>