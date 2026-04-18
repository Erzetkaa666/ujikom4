<?php
session_start();
include __DIR__ . '/../koneksi.php';
if($_SESSION['role']!='admin') header("Location: ../auth/login.php");
include '../config/header.php';

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

<?php include '../config/footer.php'; ?>