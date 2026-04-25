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
    <title>Tambah Buku</title>
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

if(isset($_POST['simpan'])){
    mysqli_query($koneksi,"INSERT INTO buku 
        (judul,pengarang,tahun_terbit,jenis,stok)
        VALUES
        ('$_POST[judul]','$_POST[pengarang]',
         '$_POST[tahun]','$_POST[jenis]','$_POST[stok]')");
    
    echo "<script>location='buku.php';</script>";
}
?>

<h4 class="mb-4">Tambah Buku</h4>

<form method="post">
    <div class="mb-2">
        <label>Judul</label>
        <input type="text" name="judul" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Pengarang</label>
        <input type="text" name="pengarang" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Tahun Terbit</label>
        <input type="number" name="tahun" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Jenis Buku</label>
        <input type="text" name="jenis" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Stok</label>
        <input type="number" name="stok" class="form-control" required>
    </div>

    <button name="simpan" class="btn btn-success">Simpan</button>
    <a href="buku.php" class="btn btn-secondary">Kembali</a>
</form>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>