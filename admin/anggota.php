<?php
session_start();
include __DIR__ . '/../koneksi.php';

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Data untuk dropdown
$daftar_tingkat = [10, 11, 12];
$daftar_jurusan = ['RPL', 'DKV', 'TKJ', 'ANI', 'TKRO', 'TITL', 'AKL'];

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

// Function untuk label kelas (tingkat + jurusan)
function getKelasLabel($tingkat, $jurusan) {
    return "Kelas " . $tingkat . " " . $jurusan;
}

$message = "";
$message_type = "";

// TAMBAH
if (isset($_POST['tambah'])) {
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tingkat = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);

    $result = mysqli_query($koneksi, "INSERT INTO anggota (nama, tingkat, jurusan) 
                            VALUES ('$nama', '$tingkat', '$jurusan')");
    
    if ($result) {
        $message = "✓ Anggota berhasil ditambahkan";
        $message_type = "success";
        // Clear form
        $_POST = array();
    } else {
        $message = "✗ Gagal menambahkan anggota: " . mysqli_error($koneksi);
        $message_type = "danger";
    }
}

// EDIT
if (isset($_POST['edit'])) {
    $id      = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tingkat = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);

    $result = mysqli_query($koneksi, "UPDATE anggota SET
                            nama='$nama',
                            tingkat='$tingkat',
                            jurusan='$jurusan'
                            WHERE id='$id'");
    
    if ($result) {
        $message = "✓ Anggota berhasil diupdate";
        $message_type = "success";
        // Clear form and edit
        $_POST = array();
        $_GET['edit'] = null;
    } else {
        $message = "✗ Gagal update anggota: " . mysqli_error($koneksi);
        $message_type = "danger";
    }
}

// HAPUS
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Hapus user account yang terhubung dengan anggota ini
    mysqli_query($koneksi, "DELETE FROM user WHERE id_anggota='$id'");
    
    // Kemudian hapus data anggota
    $result = mysqli_query($koneksi, "DELETE FROM anggota WHERE id='$id'");
    
    if ($result) {
        $message = "✓ Anggota berhasil dihapus";
        $message_type = "success";
    } else {
        $message = "✗ Gagal menghapus anggota";
        $message_type = "danger";
    }
}

// DATA EDIT
$edit = null;
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $q  = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id='$id'");
    $edit = mysqli_fetch_assoc($q);
    
    // Jika kolom tidak ada, gunakan default
    if ($edit && !isset($edit['tingkat'])) {
        $edit['tingkat'] = 10;
    }
    if ($edit && !isset($edit['jurusan'])) {
        $edit['jurusan'] = 'RPL';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #eef2f7;
            color: #1f2937;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .card {
            border-radius: 18px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
            border: none;
        }
        .btn {
            border-radius: 999px;
        }
        .btn-sm {
            padding: 0.5rem 0.95rem;
        }
        .table th {
            background: #f8fafc;
            border-bottom: 2px solid #e9ecef;
        }
        .table td,
        .table th {
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background: #f3f6ff;
        }
        .alert {
            border-radius: 14px;
        }
        h3, h4, h5, h6 {
            color: #111827;
        }
        .badge {
            font-size: 0.82rem;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4>Kelola Data Anggota</h4>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola informasi data siswa (nama dan kelas)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="user.php" class="btn btn-info btn-sm">➕ Kelola User/Login</a>
            <a href="dashboard.php" class="btn btn-secondary btn-sm">← Dashboard</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php 
    // Cek apakah database sudah di-update dengan kolom tingkat dan jurusan
    $check_column = mysqli_query($koneksi, "SHOW COLUMNS FROM anggota LIKE 'tingkat'");
    $column_exists = mysqli_num_rows($check_column) > 0;
    
    if (!$column_exists) {
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>⚠️ Perhatian!</strong> Database belum di-update dengan struktur kelas dan jurusan yang baru.
            <br>Silakan jalankan file <code>database_update.sql</code> terlebih dahulu sebelum menambah/edit anggota.
            <br><small>Buka phpMyAdmin → Select ujikom4 → Tab SQL → Copy-paste isi file `database_update.sql` → Execute</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    }
    ?>

    <div class="card p-4 mb-4">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Anggota</label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Ahmad Rizki"
                           value="<?= htmlspecialchars($edit['nama'] ?? $_POST['nama'] ?? '') ?>" required>
                    <?php if ($edit): ?>
                        <small class="text-muted d-block mt-1">Admin bisa mengubah nama siswa</small>
                    <?php endif; ?>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tingkat Kelas</label>
                    <select name="tingkat" class="form-select" required>
                        <option value="">-- Pilih Tingkat --</option>
                        <?php foreach ($daftar_tingkat as $tkt): ?>
                            <option value="<?= $tkt ?>" <?= (isset($edit['tingkat']) && $edit['tingkat'] == $tkt) || ($tkt == 10 && !isset($edit)) ? 'selected' : '' ?>>
                                Kelas <?= $tkt ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jurusan</label>
                    <select name="jurusan" class="form-select" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php foreach ($daftar_jurusan as $jr): ?>
                            <option value="<?= $jr ?>" <?= (isset($edit['jurusan']) && $edit['jurusan'] == $jr) || ($jr == 'RPL' && !isset($edit)) ? 'selected' : '' ?>>
                                <?= $jr ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-12 d-flex align-items-end gap-2">
                    <?php if ($edit) { ?>
                        <button name="edit" type="submit" class="btn btn-warning">✏️ Update Data</button>
                        <a href="anggota.php" class="btn btn-secondary">Batal</a>
                    <?php } else { ?>
                        <button name="tambah" type="submit" class="btn btn-success">➕ Tambah Anggota</button>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <h6 class="mb-0">Daftar Anggota</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Nama Anggota</th>
                        <th>Tingkat Kelas</th>
                        <th>Jurusan</th>
                        <th>Tanggal Daftar</th>
                        <th style="width:170px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $a = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY tingkat DESC, jurusan ASC, nama ASC");
                    if (mysqli_num_rows($a) > 0) {
                        while ($d = mysqli_fetch_assoc($a)) {
                    ?>
                    <tr>
                        <td class="text-center"><strong><?= $no++ ?></strong></td>
                        <td><?= htmlspecialchars($d['nama']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-secondary">Kelas <?= $d['tingkat'] ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-<?= getBadgeColor($d['jurusan']) ?>">
                                <?= htmlspecialchars($d['jurusan']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($d['created_at'])) ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="?edit=<?= $d['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="?hapus=<?= $d['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Yakin hapus anggota ini?\n\nData user yang terhubung juga akan dihapus!')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php }} else { ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada data anggota</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>