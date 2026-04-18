<?php
session_start();
include __DIR__ . '/../koneksi.php';

if(isset($_POST['daftar'])){
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas    = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);

    // simpan ke anggota
    mysqli_query($koneksi, "INSERT INTO anggota (nama, kelas) 
                            VALUES ('$nama','$kelas')");
    $id_anggota = mysqli_insert_id($koneksi);

    // simpan ke user
    mysqli_query($koneksi, "INSERT INTO user (username,password,role,id_anggota) 
                            VALUES ('$username','$password','siswa','$id_anggota')");

    echo "<script>alert('Register berhasil, silakan login');location='login.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5" style="max-width:600px;">
    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Register Siswa</h4>
                <a href="login.php" class="btn btn-secondary btn-sm">
                    ← Kembali Login
                </a>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Kelas</label>
                    <input type="text" name="kelas" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" name="daftar" class="btn btn-success w-100">
                    Daftar
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>