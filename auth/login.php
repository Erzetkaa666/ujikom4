<?php
session_start();
include __DIR__ . '/../koneksi.php';

if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);

    $data = mysqli_query($koneksi, "SELECT * FROM user 
            WHERE username='$username' AND password='$password'");

    $cek  = mysqli_num_rows($data);
    $user = mysqli_fetch_assoc($data);

    if($cek > 0){
        $_SESSION['id_user']    = $user['id'];
        $_SESSION['role']       = $user['role'];
        $_SESSION['id_anggota'] = $user['id_anggota'];

        if($user['role']=='admin'){
            header("Location: ../admin/dashboard.php");
        }else{
            header("Location: ../siswa/dashboard.php");
        }
        exit;
    }else{
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5" style="max-width:600px;">
    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Login Perpustakaan</h4>
                <a href="register.php" class="btn btn-secondary btn-sm">
                    Daftar Siswa
                </a>
            </div>

            <?php if(isset($error)) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100">
                    Login
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>