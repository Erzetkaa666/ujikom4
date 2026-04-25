<?php
session_start();
include __DIR__ . '/../koneksi.php';
if($_SESSION['role']!='admin') header("Location: ../auth/login.php");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>
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
<?php

$id = $_GET['id'];
$data = mysqli_fetch_array(mysqli_query($koneksi,"SELECT * FROM buku WHERE id='$id'"));

if(isset($_POST['update'])){
    mysqli_query($koneksi,"UPDATE buku SET
        judul='$_POST[judul]',
        pengarang='$_POST[pengarang]',
        tahun_terbit='$_POST[tahun]',
        jenis='$_POST[jenis]',
        stok='$_POST[stok]'
        WHERE id='$id'");

    echo "<script>location='buku.php';</script>";
}
?>

<h4 class="mb-4">Edit Buku</h4>

<form method="post">
    <div class="mb-2">
        <label>Judul</label>
        <input type="text" name="judul" value="<?= $data['judul'] ?>" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Pengarang</label>
        <input type="text" name="pengarang" value="<?= $data['pengarang'] ?>" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Tahun Terbit</label>
        <input type="number" name="tahun" value="<?= $data['tahun_terbit'] ?>" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Jenis Buku</label>
        <input type="text" name="jenis" value="<?= $data['jenis'] ?>" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Stok</label>
        <input type="number" name="stok" value="<?= $data['stok'] ?>" class="form-control" required>
    </div>

    <button name="update" class="btn btn-warning">Update</button>
    <a href="buku.php" class="btn btn-secondary">Kembali</a>
</form>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>