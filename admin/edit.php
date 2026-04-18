<?php
session_start();
include __DIR__ . '/../koneksi.php';
if($_SESSION['role']!='admin') header("Location: ../auth/login.php");
include '../config/header.php';

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

<?php include '../config/footer.php'; ?>