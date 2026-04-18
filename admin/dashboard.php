<?php
session_start();
include __DIR__ . '/../koneksi.php';

if($_SESSION['role']!='admin'){
    header("Location: ../auth/login.php");
    exit;
}

include '../config/header.php';

$hari_ini = date('Y-m-d');
$admin = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT username FROM user WHERE id='$_SESSION[id_user]'"));
$admin_name = $admin['username'] ?? 'Admin';

$peminjam_hari_ini = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE tanggal_pinjam='$hari_ini'"));
$belum_kembali = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE status='pinjam'"));
$terlambat = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE status='pinjam' AND batas_kembali < '$hari_ini'"));
$total_denda = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(denda) as total FROM transaksi"))['total'] ?? 0;

$data = mysqli_query($koneksi, "SELECT t.id, a.nama, b.judul, t.tanggal_pinjam, t.batas_kembali, t.status, t.denda, t.kondisi_buku
    FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id
    JOIN buku b ON t.id_buku = b.id
    ORDER BY t.id DESC
    LIMIT 8");
?>

<div class="container mt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h4 class="mb-1">Selamat Datang, <?= htmlspecialchars($admin_name) ?>!</h4>
                <p class="text-muted mb-0">Memantau transaksi, denda, dan pengembalian secara lengkap.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="buku.php" class="btn btn-outline-primary btn-sm">Kelola Buku</a>
                <a href="anggota.php" class="btn btn-outline-secondary btn-sm">Kelola User</a>
                <a href="transaksi.php" class="btn btn-outline-success btn-sm">Kelola Transaksi</a>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-secondary">Peminjam Hari Ini</h6>
                    <h3 class="text-primary mb-0"><?= $peminjam_hari_ini ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-secondary">Belum Kembali</h6>
                    <h3 class="text-warning mb-0"><?= $belum_kembali ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-secondary">Terlambat</h6>
                    <h3 class="text-danger mb-0"><?= $terlambat ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-secondary">Total Denda</h6>
                    <h3 class="text-success mb-0">Rp <?= number_format($total_denda) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            Aktivitas Peminjaman Terbaru
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover m-0 text-center align-middle">
                    <tr class="table-light">
                        <th>Nama</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Kondisi</th>
                    </tr>
                    <?php while($d=mysqli_fetch_assoc($data)){ ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nama']) ?></td>
                        <td><?= htmlspecialchars($d['judul']) ?></td>
                        <td><?= $d['tanggal_pinjam'] ?></td>
                        <td><?= $d['batas_kembali'] ?></td>
                        <td>
                            <?php if($d['status']=='pinjam'){ ?>
                                <span class="badge bg-warning">Dipinjam</span>
                            <?php } else { ?>
                                <span class="badge bg-success">Kembali</span>
                            <?php } ?>
                        </td>
                        <td>Rp <?= number_format($d['denda'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($d['kondisi_buku'] ?? '-') ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../config/footer.php'; ?>