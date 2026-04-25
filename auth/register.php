<?php
session_start();
include __DIR__ . '/../koneksi.php';

// Data untuk dropdown
$daftar_tingkat = [10, 11, 12];
$daftar_jurusan = [
    'RPL' => 'RPL (Rekayasa Perangkat Lunak)',
    'DKV' => 'DKV (Desain Komunikasi Visual)',
    'TKJ' => 'TKJ (Teknik Komputer dan Jaringan)',
    'ANI' => 'ANI (Animasi)',
    'TKRO' => 'TKRO (Teknik Kendaraan Ringan Otomotif)',
    'TITL' => 'TITL (Teknik Instalasi Tenaga Listrik)',
    'AKL' => 'AKL (Akuntansi dan Keuangan Lembaga)'
];

// Function untuk warna badge berdasarkan jurusan
function getBadgeColor($jurusan) {
    $colors = [
        'RPL' => 'primary',      // Biru
        'DKV' => 'danger',       // Merah
        'TKJ' => 'success',      // Hijau
        'ANI' => 'warning',      // Kuning/Orange
        'TKRO' => 'info',        // Cyan
        'TITL' => 'secondary',   // Abu-abu
        'AKL' => 'dark'          // Hitam
    ];
    return $colors[$jurusan] ?? 'secondary';
}

if(isset($_POST['daftar'])){
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tingkat  = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    $jurusan  = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);

    // Cek username sudah ada
    $cek = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah terdaftar!";
    } else {
        // simpan ke anggota
        $result_anggota = mysqli_query($koneksi, "INSERT INTO anggota (nama, tingkat, jurusan) 
                                VALUES ('$nama', '$tingkat', '$jurusan')");
        
        if ($result_anggota) {
            $id_anggota = mysqli_insert_id($koneksi);

            // simpan ke user
            $result_user = mysqli_query($koneksi, "INSERT INTO user (username, password, role, id_anggota) 
                                        VALUES ('$username', '$password', 'siswa', '$id_anggota')");
            
            if ($result_user) {
                echo "<script>alert('Register berhasil, silakan login');location='login.php';</script>";
            } else {
                $error = "Gagal mendaftarkan user: " . mysqli_error($koneksi);
            }
        } else {
            $error = "Gagal menyimpan data siswa: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Siswa</title>
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
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>

<div class="container" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h3 class="mb-2">📚 Register Siswa</h3>
                <p class="text-muted">Daftar akun untuk akses perpustakaan digital</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Ahmad Rizki" required
                           value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tingkat Kelas</label>
                        <select name="tingkat" class="form-select" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <?php foreach ($daftar_tingkat as $tkt): ?>
                                <option value="<?= $tkt ?>" <?= (isset($_POST['tingkat']) && $_POST['tingkat'] == $tkt) ? 'selected' : '' ?>>
                                    Kelas <?= $tkt ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jurusan</label>
                        <select name="jurusan" class="form-select" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <?php foreach ($daftar_jurusan as $kode => $label): ?>
                                <option value="<?= $kode ?>" <?= (isset($_POST['jurusan']) && $_POST['jurusan'] == $kode) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Contoh: ahmad_rizki" required
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    <small class="text-muted d-block mt-1">Username harus unik dan tidak boleh dipakai orang lain</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    <small class="text-muted d-block mt-1">🔐 Password akan di-hash otomatis</small>
                </div>

                <button type="submit" name="daftar" class="btn btn-primary w-100 mb-3">
                    ✓ Daftar Sekarang
                </button>

                <div class="text-center">
                    <p class="mb-0">Sudah punya akun? <a href="login.php" class="text-primary fw-bold">Login di sini</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>