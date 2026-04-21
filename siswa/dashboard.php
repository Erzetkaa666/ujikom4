<?php
session_start();
include '../koneksi.php';

if($_SESSION['role']!='siswa'){
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
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
<?php

$id_anggota = $_SESSION['id_anggota'];
$siswa = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT nama FROM anggota WHERE id='$id_anggota'"));
$nama_siswa = $siswa['nama'];

// RIWAYAT yang sedang dipinjam
$dipinjam = mysqli_query($koneksi,"
    SELECT b.judul, t.tanggal_pinjam
    FROM transaksi t
    JOIN buku b ON t.id_buku=b.id
    WHERE t.id_anggota='$id_anggota' AND t.status='pinjam'
    ORDER BY t.id DESC
");

// RIWAYAT terakhir
$riwayat = mysqli_query($koneksi,"
    SELECT b.judul, t.tanggal_pinjam, t.status
    FROM transaksi t
    JOIN buku b ON t.id_buku=b.id
    WHERE t.id_anggota='$id_anggota'
    ORDER BY t.id DESC
    LIMIT 5
");

$hari_ini = date('Y-m-d');
$total_transaksi = mysqli_num_rows(mysqli_query($koneksi,"
    SELECT * FROM transaksi WHERE id_anggota='$id_anggota'
"));
$terlambat = mysqli_num_rows(mysqli_query($koneksi,"
    SELECT * FROM transaksi
    WHERE id_anggota='$id_anggota' AND status='pinjam' AND batas_kembali < '$hari_ini'
"));
$denda = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT SUM(denda) as total FROM transaksi
    WHERE id_anggota='$id_anggota'
"))['total'] ?? 0;
?>

<div class="container mt-4">

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h4 class="mb-2">Selamat Datang, <?= $nama_siswa ?>!</h4>
                    <p class="text-muted mb-0">Pilih buku, cek status, dan lihat riwayat terbaru dengan mudah.</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="pinjam.php" class="btn btn-primary btn-sm">Pinjam Buku</a>
                    <a href="kembali.php" class="btn btn-outline-primary btn-sm">Kembalikan Buku</a>
                    <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin logout?')">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-secondary">Sedang Dipinjam</h6>
                    <h3 class="text-primary mb-0"><?= mysqli_num_rows($dipinjam) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-secondary">Riwayat Terakhir</h6>
                    <h3 class="text-success mb-0"><?= mysqli_num_rows($riwayat) ?></h3>
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
                    <h3 class="text-success mb-0">Rp <?= number_format($denda) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row gx-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light"><strong>Sedang Dipinjam</strong></div>
                <div class="card-body">
                    <?php if(mysqli_num_rows($dipinjam)>0){ ?>
                        <ul class="list-unstyled mb-0">
                        <?php while($d=mysqli_fetch_assoc($dipinjam)){ ?>
                            <li class="mb-3 pb-2 border-bottom border-200">
                                <strong><?= $d['judul'] ?></strong><br>
                                <small class="text-muted">Dipinjam sejak <?= $d['tanggal_pinjam'] ?></small>
                            </li>
                        <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <div class="text-center text-muted py-4">Tidak ada buku yang sedang dipinjam.</div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light"><strong>Riwayat Terakhir</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover m-0 text-center">
                            <tr class="table-light">
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                            <?php while($r=mysqli_fetch_assoc($riwayat)){ ?>
                            <tr>
                                <td><?= $r['judul'] ?></td>
                                <td><?= $r['tanggal_pinjam'] ?></td>
                                <td>
                                    <?php if($r['status'] == 'pinjam'){ ?>
                                        <span class="badge bg-warning">Dipinjam</span>
                                    <?php } else { ?>
                                        <span class="badge bg-success">Kembali</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>