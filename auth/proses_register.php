<?php
include '../koneksi.php';

$nama = $_POST['nama'];
$kelas = $_POST['kelas'];
$username = $_POST['username'];
$password = $_POST['password'];

// cek username sudah ada atau belum
$cek = mysqli_query($conn,"SELECT * FROM user WHERE username='$username'");
if(mysqli_num_rows($cek) > 0){
    echo "Username sudah dipakai. <a href='register.php'>Kembali</a>";
    exit;
}

// simpan ke anggota
mysqli_query($conn,"INSERT INTO anggota VALUES(NULL,'$nama','$kelas')");
$id_anggota = mysqli_insert_id($conn);

// simpan ke user
mysqli_query($conn,"INSERT INTO user VALUES(NULL,'$username','$password','siswa','$id_anggota')");

echo "Registrasi berhasil. <a href='login.php'>Login sekarang</a>";
?>