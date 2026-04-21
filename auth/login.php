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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .card {
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: none;
        }
        .btn {
            border-radius: 999px;
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>

<div class="container" style="max-width: 500px;">
    <div class="card">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h3 class="mb-2">Login</h3>
            </div>

            <?php if(isset($error)) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100 mb-3">
                    Login
                </button>

                <div class="text-center">
                    <p class="text-muted mb-0">Belum punya akun? <a href="register.php" class="text-decoration-none">Daftar di sini</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>