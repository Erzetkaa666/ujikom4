<?php
session_start();
include __DIR__ . '/../koneksi.php';

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
    <title>Pengembalian Buku</title>
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

$id_anggota = $_SESSION['id_anggota'];
$today = date('Y-m-d');

// PROSES KEMBALI - INSERT DENGAN STATUS PENDING
if(isset($_POST['kembali'])){
    $id_transaksi = $_POST['id_transaksi'];
    $kondisi = $_POST['kondisi']; // aman, robek, hilang
    $halaman_rusak = 0;
    $denda_kondisi = 0;
    $catatan = $_POST['catatan'] ?? '';

    // Get transaksi data
    $trx = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT * FROM transaksi WHERE id='$id_transaksi'"));
    
    if(!$trx){
        echo "<script>alert('Data transaksi tidak ditemukan');location='kembali.php';</script>";
        exit;
    }

    // Hitung denda keterlambatan
    $batas = $trx['batas_kembali'];
    $telat = (strtotime($today) - strtotime($batas)) / 86400;
    $telat = ($telat > 0) ? $telat : 0;
    $denda_terlambat = $telat * 2000;

    // Hitung denda berdasarkan kondisi buku
    if($kondisi == 'robek'){
        $halaman_rusak = (int)$_POST['halaman_rusak'];
        $denda_kondisi = $halaman_rusak * 5000; // Rp 5000 per halaman
    } elseif($kondisi == 'hilang'){
        // Ambil harga dari buku
        $buku = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT harga FROM buku WHERE id='{$trx['id_buku']}'"));
        $denda_kondisi = $buku['harga'];
    }

    // Insert dengan status PENDING (menunggu approval admin)
    $total_denda = $denda_terlambat + $denda_kondisi;
    
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
        echo "<script>alert('Buku berhasil diajukan untuk pengembalian. Menunggu persetujuan admin.');location='kembali.php';</script>";
    } else {
        echo "<script>alert('Error: '.mysqli_error(\$koneksi));location='kembali.php';</script>";
    }
    exit;
}

// Ambil buku yang masih dipinjam (status pinjam)
$data = mysqli_query($koneksi,"
    SELECT t.id, b.judul, b.harga, t.tanggal_pinjam, t.batas_kembali, t.status
    FROM transaksi t
    JOIN buku b ON t.id_buku=b.id
    WHERE t.status='pinjam' AND t.id_anggota='$id_anggota'
");
$jumlah_pinjam = mysqli_num_rows($data);

// Ambil buku yang sedang pending
$pending = mysqli_query($koneksi,"
    SELECT t.id, b.judul, t.status_approval, t.kondisi_buku, t.denda, t.denda_kondisi
    FROM transaksi t
    JOIN buku b ON t.id_buku=b.id
    WHERE t.status='pending' AND t.id_anggota='$id_anggota'
");
$jumlah_pending = mysqli_num_rows($pending);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Pengembalian Buku</h4>
        <a href="dashboard.php" class="btn btn-secondary btn-sm">← Kembali Dashboard</a>
    </div>

    <!-- Status Badge -->
    <div class="row gap-2 mb-4">
        <div class="col-auto">
            <span class="badge bg-primary">Belum dikembalikan: <?= $jumlah_pinjam ?></span>
        </div>
        <div class="col-auto">
            <span class="badge bg-warning">Menunggu persetujuan: <?= $jumlah_pending ?></span>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <strong>📋 Informasi Penting:</strong>
            <ul class="mb-0 mt-2" style="font-size: 0.95rem;">
                <li>Denda keterlambatan: <strong>Rp 2.000/hari</strong></li>
                <li>Buku <strong>Aman</strong>: Tidak ada denda tambahan</li>
                <li>Buku <strong>Robek</strong>: Denda <strong>Rp 5.000/halaman</strong></li>
                <li>Buku <strong>Hilang</strong>: Denda sesuai harga buku</li>
                <li>Setelah approval admin, buku akan masuk ke sistem</li>
            </ul>
        </div>
    </div>

    <!-- Buku yang Sedang Pending -->
    <?php if($jumlah_pending > 0){ ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning">
            <strong>⏳ Status Pengembalian Menunggu Approval</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered m-0 align-middle text-center">
                    <tr class="table-light">
                        <th>Judul</th>
                        <th>Kondisi</th>
                        <th>Denda</th>
                        <th>Status</th>
                    </tr>
                    <?php while($p=mysqli_fetch_assoc($pending)){ 
                        $total = $p['denda'] + $p['denda_kondisi'];
                        $status_badge = $p['status_approval'] == 'approved' ? 'bg-success' : ($p['status_approval'] == 'rejected' ? 'bg-danger' : 'bg-warning');
                        $status_text = $p['status_approval'] == 'approved' ? 'Disetujui' : ($p['status_approval'] == 'rejected' ? 'Ditolak' : 'Menunggu');
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($p['judul']) ?></td>
                        <td><span class="badge bg-info"><?= ucfirst($p['kondisi_buku']) ?></span></td>
                        <td>Rp <?= number_format($total) ?></td>
                        <td><span class="badge <?= $status_badge ?>"><?= $status_text ?></span></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>

    <!-- Buku yang Belum Dikembalikan -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong>📚 Buku yang Belum Dikembalikan (<?= $jumlah_pinjam ?>)</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover m-0 text-center align-middle">
                    <tr class="table-light">
                        <th>No</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Aksi</th>
                    </tr>

                    <?php 
                    $no=1;
                    $data = mysqli_query($koneksi,"
                        SELECT t.id, b.judul, b.harga, t.tanggal_pinjam, t.batas_kembali
                        FROM transaksi t
                        JOIN buku b ON t.id_buku=b.id
                        WHERE t.status='pinjam' AND t.id_anggota='$id_anggota'
                    ");

                    while($d=mysqli_fetch_assoc($data)) :
                        $telat = floor((strtotime($today) - strtotime($d['batas_kembali'])) / 86400);
                        $telat = ($telat > 0) ? $telat : 0;
                        $denda_telat = $telat * 2000;
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($d['judul']) ?></td>
                        <td><?= $d['tanggal_pinjam'] ?></td>
                        <td>
                            <?= $d['batas_kembali'] ?>
                            <?php if($telat > 0){ ?>
                                <br><span class="text-danger small">⏰ Terlambat <?= $telat ?> hari<br>Rp <?= number_format($denda_telat) ?></span>
                            <?php } else { ?>
                                <br><span class="text-success small">✓ Tepat waktu</span>
                            <?php } ?>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#kembali<?= $d['id'] ?>">
                                Kembalikan
                            </button>

                            <!-- Modal Return Book -->
                            <div class="modal fade" id="kembali<?= $d['id'] ?>" tabindex="-1">
                              <div class="modal-dialog modal-lg">
                                <form method="POST" class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Proses Pengembalian Buku</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>

                                  <div class="modal-body">
                                      <input type="hidden" name="id_transaksi" value="<?= $d['id'] ?>">

                                      <div class="mb-3">
                                        <p class="mb-2"><strong>Judul:</strong> <?= htmlspecialchars($d['judul']) ?></p>
                                        <p class="mb-2"><strong>Harga Buku:</strong> Rp <?= number_format($d['harga']) ?></p>
                                        <p class="mb-2"><strong>Batas Kembali:</strong> <?= $d['batas_kembali'] ?></p>
                                        <?php if($telat > 0){ ?>
                                            <p class="text-danger mb-0"><strong>⚠️ Terlambat:</strong> <?= $telat ?> hari → Denda Rp <?= number_format($denda_telat) ?></p>
                                        <?php } else { ?>
                                            <p class="text-success mb-0"><strong>✓ Tepat Waktu</strong></p>
                                        <?php } ?>
                                      </div>

                                      <hr>

                                      <!-- Kondisi Dropdown -->
                                      <div class="mb-3">
                                        <label class="form-label"><strong>Kondisi Buku</strong></label>
                                        <select name="kondisi" class="form-select" id="kondisiSelect<?= $d['id'] ?>" required onchange="toggleFields(<?= $d['id'] ?>)">
                                            <option value="">-- Pilih Kondisi --</option>
                                            <option value="aman">✓ Aman (Tidak ada denda tambahan)</option>
                                            <option value="robek">⚠️ Robek Halaman (Rp 5.000/halaman)</option>
                                            <option value="hilang">❌ Hilang/Hilang Banyak Halaman (Rp <?= number_format($d['harga']) ?>)</option>
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
                                            Buku akan dikenai denda penuh sebesar <strong>Rp <?= number_format($d['harga']) ?></strong>
                                        </div>
                                      </div>

                                      <!-- Catatan tambahan -->
                                      <div class="mb-3">
                                        <label class="form-label">Catatan Tambahan (Opsional)</label>
                                        <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Halaman robek sejak pertemuan..."></textarea>
                                      </div>
                                  </div>

                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" name="kembali" class="btn btn-success btn-sm">Ajukan Pengembalian</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFields(id) {
    const kondisi = document.getElementById('kondisiSelect' + id).value;
    const robekField = document.getElementById('robekField' + id);
    const hilangField = document.getElementById('hilangField' + id);
    
    robekField.classList.add('hidden-field');
    hilangField.classList.add('hidden-field');
    
    if (kondisi === 'robek') {
        robekField.classList.remove('hidden-field');
        
        // Add event listener untuk hitung denda robek
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
