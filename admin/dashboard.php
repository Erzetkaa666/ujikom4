<?php
session_start();
include __DIR__ . '/../koneksi.php';

if($_SESSION['role']!='admin'){
    header("Location: ../auth/login.php");
    exit;
}

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

$hari_ini = date('Y-m-d');
$admin = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT username FROM user WHERE id='$_SESSION[id_user]'"));
$admin_name = $admin['username'] ?? 'Admin';

$peminjam_hari_ini = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE tanggal_pinjam='$hari_ini'"));
$belum_kembali = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE status='pinjam'"));
$terlambat = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE status='pinjam' AND batas_kembali < '$hari_ini'"));
$pending_approval = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE status='pending'"));
$total_denda = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(denda) as total FROM transaksi"))['total'] ?? 0;

$data = mysqli_query($koneksi, "SELECT t.id, a.nama, a.tingkat, a.jurusan, b.judul, t.tanggal_pinjam, t.batas_kembali, t.status, t.denda, t.denda_kondisi, t.kondisi_buku
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
                <a href="user.php" class="btn btn-outline-info btn-sm">Kelola User/Login</a>
                <a href="transaksi.php" class="btn btn-outline-success btn-sm">Kelola Transaksi</a>
                <a href="verifikasi_pengembalian.php" class="btn btn-outline-warning btn-sm">Verifikasi Return</a>
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
            <a href="verifikasi_pengembalian.php" style="text-decoration: none;">
                <div class="card shadow-sm text-center bg-light" style="cursor: pointer; border: 2px solid #ffc107;">
                    <div class="card-body">
                        <h6 class="text-secondary">Pending Approval</h6>
                        <h3 class="text-warning mb-0"><?= $pending_approval ?></h3>
                        <small class="text-muted">Klik untuk verifikasi</small>
                    </div>
                </div>
            </a>
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
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Kondisi</th>
                    </tr>
                    <?php while($d=mysqli_fetch_assoc($data)){ 
                        $total_denda = ($d['denda'] ?? 0) + ($d['denda_kondisi'] ?? 0);
                        $kondisi_badge = '';
                        if($d['status'] == 'pending' || $d['status'] == 'kembali'){
                            if($d['kondisi_buku'] == 'aman'){
                                $kondisi_badge = '<span class="badge bg-success">✓ Aman</span>';
                            } elseif($d['kondisi_buku'] == 'robek'){
                                $kondisi_badge = '<span class="badge bg-warning">⚠️ Robek</span>';
                            } elseif($d['kondisi_buku'] == 'hilang'){
                                $kondisi_badge = '<span class="badge bg-danger">❌ Hilang</span>';
                            }
                        } else {
                            $kondisi_badge = '<span class="text-muted">-</span>';
                        }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nama']) ?></td>
                        <td><span class="badge bg-secondary">Kelas <?= $d['tingkat'] ?></span></td>
                        <td><span class="badge bg-<?= getBadgeColor($d['jurusan']) ?>"><?= htmlspecialchars($d['jurusan']) ?></span></td>
                        <td><?= htmlspecialchars($d['judul']) ?></td>
                        <td><?= $d['tanggal_pinjam'] ?></td>
                        <td><?= $d['batas_kembali'] ?></td>
                        <td>
                            <?php if($d['status']=='pinjam'){ ?>
                                <span class="badge bg-warning">Dipinjam</span>
                            <?php } elseif($d['status']=='pending'){ ?>
                                <span class="badge bg-info">Menunggu Verifikasi</span>
                            <?php } else { ?>
                                <span class="badge bg-success">Kembali</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div><small>Terlambat: Rp <?= number_format($d['denda'] ?? 0) ?></small></div>
                            <?php if(($d['denda_kondisi'] ?? 0) > 0){ ?>
                                <div><small class="text-danger">Kondisi: Rp <?= number_format($d['denda_kondisi']) ?></small></div>
                                <div><strong>Total: Rp <?= number_format($total_denda) ?></strong></div>
                            <?php } ?>
                        </td>
                        <td><?= $kondisi_badge ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>