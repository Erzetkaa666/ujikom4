<?php
session_start();
include __DIR__ . '/../koneksi.php';

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// LOGIKA SEARCH
$keyword = "";
if (isset($_GET['search'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['search']);
    $query = "SELECT * FROM buku 
              WHERE judul LIKE '%$keyword%' 
              OR pengarang LIKE '%$keyword%' 
              OR tahun_terbit LIKE '%$keyword%' 
              OR jenis LIKE '%$keyword%' 
              ORDER BY id DESC";
} else {
    $query = "SELECT * FROM buku ORDER BY id DESC";
}

$data = mysqli_query($koneksi, $query);

include '../config/header.php';
?>

<div class="container mt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h4 class="mb-1">Data Buku</h4>
                <p class="text-muted mb-0">Kelola koleksi buku, lihat stok, dan edit data dengan tampilan yang seragam.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="dashboard.php" class="btn btn-secondary btn-sm">← Dashboard</a>
                <a href="tambah.php" class="btn btn-primary btn-sm">+ Tambah Buku</a>
            </div>
        </div>
    </div>

    <form class="row g-2 align-items-center mb-4" method="GET">
        <div class="col-auto">
            <input type="text" name="search" value="<?= htmlspecialchars($keyword) ?>" class="form-control form-control-sm" placeholder="Cari buku...">
        </div>
        <div class="col-auto">
            <button class="btn btn-dark btn-sm">Cari</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <tr class="text-center table-light">
                <th style="width:60px">No</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th style="width:90px">Tahun</th>
                <th style="width:120px">Jenis</th>
                <th style="width:80px">Stok</th>
                <th style="width:150px">Aksi</th>
            </tr>

            <?php 
            $no = 1;
            while ($d = mysqli_fetch_assoc($data)) :
                $stok_label = $d['stok'] <= 2 ? '<span class="badge bg-danger">'.$d['stok'].'</span>' : $d['stok'];
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($d['judul']) ?></td>
                <td class="text-center"><?= htmlspecialchars($d['pengarang']) ?></td>
                <td class="text-center"><?= htmlspecialchars($d['tahun_terbit']) ?></td>
                <td class="text-center"><?= htmlspecialchars($d['jenis']) ?></td>
                <td class="text-center"><?= $stok_label ?></td>
                <td class="text-center">
                    <a href="edit.php?id=<?= $d['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="hapus.php?id=<?= $d['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus buku ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<?php include '../config/footer.php'; ?>