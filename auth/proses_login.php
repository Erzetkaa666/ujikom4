<?php
session_start();
include __DIR__ . '/../koneksi.php';

$username = $_POST['username'];
$password = md5($_POST['password']);

$query = mysqli_query($koneksi, "SELECT * FROM user 
        WHERE username='$username' AND password='$password'");

$cek = mysqli_num_rows($query);

if($cek > 0){
    $user = mysqli_fetch_assoc($query);

    $_SESSION['id_user'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    // hanya siswa yang punya id_anggota
    if($user['role'] == 'siswa'){
        $_SESSION['id_anggota'] = $user['id_anggota'];
    }

    if($user['role'] == 'admin'){
        header("Location: ../admin/dashboard.php");
        exit;
    }else{
        header("Location: ../siswa/dashboard.php");
        exit;
    }

}else{
    echo "<script>alert('Login gagal! Username / Password salah');location='login.php';</script>";
}
?>