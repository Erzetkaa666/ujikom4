<?php
session_start();
include __DIR__ . '/../koneksi.php';

if($_SESSION['role']!='admin'){
    header("Location: ../auth/login.php");
    exit;
}

// Data untuk dropdown
$daftar_tingkat = [10, 11, 12];
$daftar_jurusan = ['RPL', 'DKV', 'TKJ', 'ANI', 'TKRO', 'TITL', 'AKL'];

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
    <title>Kelola Transaksi</title>
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
        .hidden-field {
            display: none;
        }
    </style>
</head>
<body>
<?php

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
    $where .= " AND (a.nama LIKE '%$search_safe%' OR b.judul LIKE '%$search_safe%' OR a.jurusan LIKE '%$search_safe%')";
}

if(isset($_POST['return'])){
    $id_transaksi = $_POST['id_transaksi'];
    $kondisi = mysqli_real_escape_string($koneksi, $_POST['kondisi']); // aman, robek, hilang
    $halaman_rusak = 0;
    $denda_kondisi = 0;
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan'] ?? '');

    $trx = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id='$id_transaksi'"));
    
    if($trx && $trx['status'] == 'pinjam'){
        $batas = $trx['batas_kembali'];
        $telat = floor((strtotime($today) - strtotime($batas)) / 86400);
        $telat = ($telat > 0) ? $telat : 0;
        $denda_terlambat = $telat * 2000;

        // Hitung denda berdasarkan kondisi
        if($kondisi == 'robek'){
            $halaman_rusak = (int)$_POST['halaman_rusak'];
            $denda_kondisi = $halaman_rusak * 5000;
        } elseif($kondisi == 'hilang'){
            $buku = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT harga FROM buku WHERE id='{$trx['id_buku']}'"));
            $denda_kondisi = $buku['harga'];
        }

        $total_denda = $denda_terlambat + $denda_kondisi;

        // Update dengan status pending (menunggu approval)
        $update_query = "UPDATE transaksi SET 
                        status='pending',
                        kondisi_buku='$kondisi',
                        halaman_rusak='$halaman_rusak',
                        denda='$denda_terlambat',
                        denda_kondisi='$denda_kondisi',
                        catatan_kondisi='$catatan',
                        tanggal_kembali='$today',
                        status_approval='pending'
                        WHERE id='$id_transaksi'";
        
        if(mysqli_query($koneksi, $update_query)){
            echo "<script>alert('Pengembalian buku diajukan untuk verifikasi');location='transaksi.php';</script>";
        }
        exit;
    }
}

$data = mysqli_query($koneksi, "SELECT t.id, a.nama, a.tingkat, a.jurusan, b.judul, b.harga, t.tanggal_pinjam, t.batas_kembali, t.tanggal_kembali, t.status, t.denda, t.kondisi_buku
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
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Cari nama / buku / jurusan">
        </div>
        <div class="col-auto">
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <option value="pinjam" <?= $status_filter=='pinjam' ? 'selected' : '' ?>>Dipinjam</option>
                <option value="pending" <?= $status_filter=='pending' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
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
                <th>Tingkat</th>
                <th>Jurusan</th>
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
                <td><span class="badge bg-secondary">Kelas <?= $d['tingkat'] ?></span></td>
                <td><span class="badge bg-<?= getBadgeColor($d['jurusan']) ?>"><?= htmlspecialchars($d['jurusan']) ?></span></td>
                <td><?= htmlspecialchars($d['judul']) ?></td>
                <td><?= $d['tanggal_pinjam'] ?></td>
                <td><?= $d['batas_kembali'] ?></td>
                <td><?= $d['tanggal_kembali'] ?: '-' ?></td>
                <td>
                    <?php if($d['status']=='pinjam'): ?>
                        <span class="badge bg-warning">Dipinjam</span>
                    <?php elseif($d['status']=='pending'): ?>
                        <span class="badge bg-info">Menunggu Verifikasi</span>
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
                            <div class="modal-dialog modal-lg">
                                <form method="POST" class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Proses Pengembalian Buku</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_transaksi" value="<?= $d['id'] ?>">

                                        <div class="mb-3">
                                            <p class="mb-1"><strong>Judul:</strong> <?= htmlspecialchars($d['judul']) ?></p>
                                            <p class="mb-1"><strong>Nama Siswa:</strong> <?= htmlspecialchars($d['nama']) ?></p>
                                            <p class="mb-3"><strong>Batas Kembali:</strong> <?= $d['batas_kembali'] ?></p>
                                            <?php 
                                                $telat = floor((strtotime($today) - strtotime($d['batas_kembali'])) / 86400);
                                                $telat = ($telat > 0) ? $telat : 0;
                                                if($telat > 0) {
                                                    echo '<p class="text-danger mb-0"><strong>⚠️ Terlambat:</strong> ' . $telat . ' hari → Denda Rp ' . number_format($telat * 2000) . '</p>';
                                                } else {
                                                    echo '<p class="text-success mb-0"><strong>✓ Tepat Waktu</strong></p>';
                                                }
                                            ?>
                                        </div>

                                        <hr>

                                        <!-- Kondisi Dropdown -->
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Kondisi Buku</strong></label>
                                            <select name="kondisi" class="form-select" id="kondisiSelect<?= $d['id'] ?>" required onchange="toggleFields(<?= $d['id'] ?>)">
                                                <option value="">-- Pilih Kondisi --</option>
                                                <option value="aman">✓ Aman (Tidak ada denda tambahan)</option>
                                                <option value="robek">⚠️ Robek Halaman (Rp 5.000/halaman)</option>
                                                <option value="hilang">❌ Hilang/Hilang Banyak Halaman (Rp <?= number_format($d['harga'] ?? 0) ?>)</option>
                                            </select>
                                        </div>

                                        <!-- Field untuk kondisi ROBEK -->
                                        <div class="mb-3 hidden-field" id="robekField<?= $d['id'] ?>">
                                            <label class="form-label">Berapa halaman yang robek?</label>
                                            <input type="number" name="halaman_rusak" class="form-control" min="1" placeholder="Jumlah halaman">
                                            <small class="text-muted">Denda per halaman: Rp 5.000</small>
                                            <div class="mt-2" id="dendaRobek<?= $d['id'] ?>"></div>
                                        </div>

                                        <!-- Field untuk kondisi HILANG -->
                                        <div class="mb-3 hidden-field" id="hilangField<?= $d['id'] ?>">
                                            <div class="alert alert-danger" role="alert">
                                                <strong>⚠️ Perhatian!</strong><br>
                                                Buku akan dikenai denda penuh sesuai harga buku
                                            </div>
                                        </div>

                                        <!-- Catatan tambahan -->
                                        <div class="mb-3">
                                            <label class="form-label">Catatan Tambahan (Opsional)</label>
                                            <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Halaman robek sejak..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="return" class="btn btn-success btn-sm">Ajukan Pengembalian</button>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFields(id) {
            const kondisi = document.getElementById('kondisiSelect' + id).value;
            const robekField = document.getElementById('robekField' + id);
            const hilangField = document.getElementById('hilangField' + id);
            
            robekField.classList.add('hidden-field');
            hilangField.classList.add('hidden-field');
            
            if (kondisi === 'robek') {
                robekField.classList.remove('hidden-field');
                
                const halamanInput = robekField.querySelector('input[name="halaman_rusak"]');
                halamanInput.addEventListener('input', function() {
                    const halaman = parseInt(this.value) || 0;
                    const denda = halaman * 5000;
                    document.getElementById('dendaRobek' + id).innerHTML = 
                        '<span class="text-danger"><strong>Total denda kondisi: Rp ' + denda.toLocaleString('id-ID') + '</strong></span>';
                });
            } else if (kondisi === 'hilang') {
                hilangField.classList.remove('hidden-field');
            }
        }
    </script>
</body>
</html>