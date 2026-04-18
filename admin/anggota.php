<?php
session_start();
include __DIR__ . '/../koneksi.php';

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/header.php';

// TAMBAH
if (isset($_POST['tambah'])) {
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);

    mysqli_query($koneksi, "INSERT INTO anggota (nama, kelas) 
                            VALUES ('$nama', '$kelas')");
}

// EDIT
if (isset($_POST['edit'])) {
    $id    = $_POST['id'];
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);

    mysqli_query($koneksi, "UPDATE anggota SET
                            nama='$nama',
                            kelas='$kelas'
                            WHERE id='$id'");
}

// HAPUS
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM anggota WHERE id='$id'");
}

// DATA EDIT
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q  = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id='$id'");
    $edit = mysqli_fetch_assoc($q);
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Kelola Anggota / User</h4>
        <a href="dashboard.php" class="btn btn-secondary btn-sm">
            ← Kembali ke Dashboard
        </a>
    </div>

    <form method="POST" class="row g-2">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

        <div class="col-md-5">
            <input name="nama" class="form-control" placeholder="Nama"
                   value="<?= $edit['nama'] ?? '' ?>" required>
        </div>

        <div class="col-md-5">
            <input name="kelas" class="form-control" placeholder="Kelas"
                   value="<?= $edit['kelas'] ?? '' ?>" required>
        </div>

        <div class="col-md-2">
            <?php if ($edit) { ?>
                <button name="edit" class="btn btn-warning w-100">Update</button>
            <?php } else { ?>
                <button name="tambah" class="btn btn-success w-100">Tambah</button>
            <?php } ?>
        </div>
    </form>

    <hr>

    <table class="table table-bordered table-hover">
        <tr class="text-center">
            <th style="width:60px">No</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th style="width:170px">Aksi</th>
        </tr>

        <?php
        $no = 1;
        $a = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY nama ASC");
        while ($d = mysqli_fetch_assoc($a)) {
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= $d['nama'] ?></td>
            <td class="text-center"><?= $d['kelas'] ?></td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                    <a href="?edit=<?= $d['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="?hapus=<?= $d['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Yakin hapus anggota ini?')">Hapus</a>
                </div>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php include '../config/footer.php'; ?>