<?php
session_start();
include __DIR__ . '/../koneksi.php';

if($_SESSION['role']!='admin'){
    header("Location: ../auth/login.php");
    exit;
}

include '../config/header.php';

$today = date('Y-m-d');
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$where = "";

if($status_filter){
    $status_filter = mysqli_real_escape_string($koneksi, $status_filter);
    $where .= " AND t.status='$status_filter'";
}

if($search){
    $search_safe = mysqli_real_escape_string($koneksi, $search);
    $where .= " AND (a.nama LIKE '%$search_safe%' OR b.judul LIKE '%$search_safe%' OR a.kelas LIKE '%$search_safe%')";
}

if(isset($_POST['return'])){
    $id_transaksi = $_POST['id_transaksi'];
    $kondisi = mysqli_real_escape_string($koneksi, $_POST['kondisi']);

    $trx = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id='$id_transaksi'"));
    if($trx && $trx['status'] == 'pinjam'){
        $batas = $trx['batas_kembali'];
        $telat = floor((strtotime($today) - strtotime($batas)) / 86400);
        $telat = ($telat > 0) ? $telat : 0;
        $denda = $telat * 2000;

        mysqli_query($koneksi, "UPDATE transaksi SET status='kembali', tanggal_kembali='$today', denda='$denda', kondisi_buku='$kondisi' WHERE id='$id_transaksi'");
        mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id='{$trx['id_buku']}'");
        header('Location: transaksi.php');
        exit;
    }
}

$data = mysqli_query($koneksi, "SELECT t.id, a.nama, a.kelas, b.judul, t.tanggal_pinjam, t.batas_kembali, t.tanggal_kembali, t.status, t.denda, t.kondisi_buku
    FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id
    JOIN buku b ON t.id_buku = b.id
    WHERE 1=1 $where
    ORDER BY t.id DESC");
?>

<div class="container mt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h4 class="mb-1">Kelola Transaksi</h4>
                <p class="text-muted mb-0">Lihat semua peminjaman, status, denda, dan proses pengembalian langsung dari admin.</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary btn-sm">← Kembali Dashboard</a>
        </div>
    </div>

    <form class="row g-2 align-items-center mb-4" method="GET">
        <div class="col-auto">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Cari nama / buku / kelas">
        </div>
        <div class="col-auto">
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <option value="pinjam" <?= $status_filter=='pinjam' ? 'selected' : '' ?>>Dipinjam</option>
                <option value="kembali" <?= $status_filter=='kembali' ? 'selected' : '' ?>>Kembali</option>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary btn-sm">Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <tr class="table-light text-center">
                <th>No</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Buku</th>
                <th>Tgl Pinjam</th>
                <th>Batas Kembali</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th>Denda</th>
                <th>Kondisi</th>
                <th>Aksi</th>
            </tr>
            <?php $no=1; while($d=mysqli_fetch_assoc($data)): ?>
            <tr class="text-center">
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($d['nama']) ?></td>
                <td><?= htmlspecialchars($d['kelas']) ?></td>
                <td><?= htmlspecialchars($d['judul']) ?></td>
                <td><?= $d['tanggal_pinjam'] ?></td>
                <td><?= $d['batas_kembali'] ?></td>
                <td><?= $d['tanggal_kembali'] ?: '-' ?></td>
                <td>
                    <?php if($d['status']=='pinjam'): ?>
                        <span class="badge bg-warning">Dipinjam</span>
                    <?php else: ?>
                        <span class="badge bg-success">Kembali</span>
                    <?php endif; ?>
                </td>
                <td>Rp <?= number_format($d['denda'] ?? 0) ?></td>
                <td><?= htmlspecialchars($d['kondisi_buku'] ?? '-') ?></td>
                <td>
                    <?php if($d['status']=='pinjam'): ?>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#return<?= $d['id'] ?>">Return</button>

                        <div class="modal fade" id="return<?= $d['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST" class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Proses Pengembalian</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_transaksi" value="<?= $d['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Kondisi buku</label>
                                            <textarea name="kondisi" class="form-control" rows="3" required placeholder="Contoh: Aman, rusak, hilang halaman"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="return" class="btn btn-success btn-sm">Konfirmasi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<?php include '../config/footer.php'; ?>