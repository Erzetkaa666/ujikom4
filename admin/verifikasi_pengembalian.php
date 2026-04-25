<?php
session_start();
include __DIR__ . '/../koneksi.php';

if($_SESSION['role']!='admin'){
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pengembalian Buku</title>
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

// PROSES APPROVAL
if(isset($_POST['action'])){
    $id_transaksi = $_POST['id_transaksi'];
    $action = $_POST['action']; // 'approved' atau 'rejected'
    
    $trx = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT * FROM transaksi WHERE id='$id_transaksi'"));
    
    if($action == 'approved'){
        // Set status menjadi kembali dan status_approval approved
        $update = "UPDATE transaksi SET 
                    status='kembali', 
                    status_approval='approved' 
                    WHERE id='$id_transaksi'";
        
        // Kembalikan stok buku (jika kondisi bukan hilang)
        if($trx['kondisi_buku'] != 'hilang'){
            mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id='{$trx['id_buku']}'");
        }
        
        $message = "Pengembalian buku disetujui!";
    } else {
        // Reject - kembali ke status pinjam
        $update = "UPDATE transaksi SET 
                    status='pinjam', 
                    status_approval='rejected',
                    tanggal_kembali=NULL,
                    kondisi_buku=NULL,
                    halaman_rusak=0,
                    denda_kondisi=0,
                    catatan_kondisi=NULL
                    WHERE id='$id_transaksi'";
        
        $message = "Pengembalian buku ditolak, status kembali ke dipinjam!";
    }
    
    if(mysqli_query($koneksi, $update)){
        echo "<script>alert('$message');location='verifikasi_pengembalian.php';</script>";
    }
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

// Ambil data pending return
$data = mysqli_query($koneksi,"
    SELECT t.id, a.nama, a.tingkat, a.jurusan, b.judul, b.harga, t.tanggal_pinjam, t.batas_kembali, t.tanggal_kembali,
           t.kondisi_buku, t.halaman_rusak, t.denda, t.denda_kondisi, t.catatan_kondisi, t.status_approval
    FROM transaksi t
    JOIN anggota a ON t.id_anggota = a.id
    JOIN buku b ON t.id_buku = b.id
    WHERE t.status='pending'
    ORDER BY t.tanggal_kembali DESC
");
$jumlah_pending = mysqli_num_rows($data);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Verifikasi Pengembalian Buku</h4>
        <a href="dashboard.php" class="btn btn-secondary btn-sm">← Dashboard</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">Menunggu Verification</h6>
                    <h3 class="text-primary"><?= $jumlah_pending ?></h3>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-0">Verifikasi setiap pengembalian buku dari siswa</p>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <tr class="table-light text-center">
                <th>No</th>
                <th>Nama / Kelas</th>
                <th>Judul Buku</th>
                <th>Tgl Pinjam - Kembali</th>
                <th>Kondisi</th>
                <th>Denda (Terlambat + Kondisi)</th>
                <th>Catatan</th>
                <th>Aksi</th>
            </tr>

            <?php 
            $no = 1;
            $data_array = [];
            while($d = mysqli_fetch_assoc($data)){
                $data_array[] = $d;
            }
            
            if(count($data_array) == 0){
                echo '<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada pengembalian buku yang menunggu verifikasi</td></tr>';
            } else {
                foreach($data_array as $d){
                    $total_denda = $d['denda'] + $d['denda_kondisi'];
                    $kondisi_badge = '';
                    if($d['kondisi_buku'] == 'aman'){
                        $kondisi_badge = '<span class="badge bg-success">✓ Aman</span>';
                    } elseif($d['kondisi_buku'] == 'robek'){
                        $kondisi_badge = '<span class="badge bg-warning">⚠️ Robek (' . $d['halaman_rusak'] . ' hal)</span>';
                    } elseif($d['kondisi_buku'] == 'hilang'){
                        $kondisi_badge = '<span class="badge bg-danger">❌ Hilang</span>';
                    }
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td>
                    <strong><?= htmlspecialchars($d['nama']) ?></strong><br>
                    <small class="text-muted">
                        Kelas <?= $d['tingkat'] ?> 
                        <span class="badge bg-<?= getBadgeColor($d['jurusan']) ?>">
                            <?= htmlspecialchars($d['jurusan']) ?>
                        </span>
                    </small>
                </td>
                <td><?= htmlspecialchars($d['judul']) ?></td>
                <td class="text-center">
                    <small><?= $d['tanggal_pinjam'] ?> s/d<br><?= $d['tanggal_kembali'] ?></small>
                </td>
                <td class="text-center">
                    <?= $kondisi_badge ?>
                </td>
                <td class="text-center">
                    <div>Terlambat: <strong>Rp <?= number_format($d['denda']) ?></strong></div>
                    <?php if($d['denda_kondisi'] > 0){ ?>
                        <div>Kondisi: <strong class="text-danger">Rp <?= number_format($d['denda_kondisi']) ?></strong></div>
                    <?php } ?>
                    <div class="mt-2 pt-2 border-top">
                        <strong>Total: Rp <?= number_format($total_denda) ?></strong>
                    </div>
                </td>
                <td>
                    <small><?= htmlspecialchars($d['catatan_kondisi']) ?></small>
                </td>
                <td class="text-center">
                    <!-- Tombol Terima -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_transaksi" value="<?= $d['id'] ?>">
                        <input type="hidden" name="action" value="approved">
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Setujui pengembalian buku ini?')">
                            ✓ Terima
                        </button>
                    </form>

                    <!-- Tombol Tolak -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_transaksi" value="<?= $d['id'] ?>">
                        <input type="hidden" name="action" value="rejected">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tolak pengembalian? Status akan kembali ke dipinjam.')">
                            ✗ Tolak
                        </button>
                    </form>
                </td>
            </tr>
            <?php } ?>
            <?php } ?>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
