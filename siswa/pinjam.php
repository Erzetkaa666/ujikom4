<?php
session_start();
include __DIR__ . '/../koneksi.php';

if($_SESSION['role']!='siswa'){
    header("Location: ../auth/login.php");
    exit;
}

$id_anggota = $_SESSION['id_anggota'];
$hari_ini = date('Y-m-d');
$max_batas = date('Y-m-d', strtotime('+7 days'));

// PROSES PINJAM
if(isset($_POST['pinjam'])){
    $id_buku = $_POST['pinjam'];
    $batas_kembali = $_POST['batas_kembali'] ?? '';

    if(!$batas_kembali){
        echo "<script>alert('Silakan pilih tanggal pengembalian.');location='pinjam.php';</script>";
        exit;
    }

    if($batas_kembali < $hari_ini || $batas_kembali > $max_batas){
        echo "<script>alert('Tanggal kembali harus antara $hari_ini dan $max_batas.');location='pinjam.php';</script>";
        exit;
    }

    $cek = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT stok FROM buku WHERE id='$id_buku'"));

    if($cek['stok'] > 0){
        mysqli_query($koneksi,"INSERT INTO transaksi
            (id_anggota, id_buku, tanggal_pinjam, batas_kembali, status)
            VALUES
            ('$id_anggota', '$id_buku', NOW(), '$batas_kembali', 'pinjam')");

        mysqli_query($koneksi,"UPDATE buku 
            SET stok = stok - 1 WHERE id='$id_buku'");

        echo "<script>alert('Buku berhasil dipinjam hingga $batas_kembali');location='pinjam.php';</script>";
        exit;
    }else{
        echo "<script>alert('Stok buku habis');location='pinjam.php';</script>";
        exit;
    }
}

// SEARCH
$cari = $_GET['cari'] ?? '';
if($cari != ''){
    $safeCari = mysqli_real_escape_string($koneksi, $cari);
    $data = mysqli_query($koneksi,"SELECT * FROM buku WHERE 
        judul LIKE '%$safeCari%' OR
        pengarang LIKE '%$safeCari%' OR
        tahun_terbit LIKE '%$safeCari%' OR
        jenis LIKE '%$safeCari%' 
        ORDER BY id DESC");
}else{
    $data = mysqli_query($koneksi,"SELECT * FROM buku ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Buku</title>
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
?>

<div class="container mt-4">
    <div class="row align-items-center mb-3">
        <div class="col-md-8">
            <h3 class="mb-1">Pinjam Buku</h3>
            <p class="text-muted mb-0">Pilih buku dan tentukan tanggal kembali hingga maksimal 7 hari dari sekarang.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="dashboard.php" class="btn btn-secondary btn-sm">← Kembali Dashboard</a>
        </div>
    </div>

    <form class="d-flex flex-wrap gap-2 align-items-center mb-3" method="GET">
        <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>"
               class="form-control form-control-sm" style="max-width:260px;"
               placeholder="Cari buku...">
        <button class="btn btn-primary btn-sm">Search</button>
    </form>

    <div class="alert alert-info p-3 mb-4">
        Pilih tanggal pengembalian maksimal 7 hari dari hari ini. Denda keterlambatan Rp 2.000/hari.
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <tr class="text-center">
                <th style="width:60px">No</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th style="width:90px">Tahun</th>
                <th style="width:120px">Jenis</th>
                <th style="width:80px">Stok</th>
                <th style="width:220px">Pilih Tanggal Kembali</th>
                <th style="width:140px">Aksi</th>
            </tr>

            <?php 
            $no = 1;
            while ($d = mysqli_fetch_assoc($data)) :
            ?>
            <tr class="text-center">
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($d['judul']) ?></td>
                <td><?= htmlspecialchars($d['pengarang']) ?></td>
                <td><?= htmlspecialchars($d['tahun_terbit']) ?></td>
                <td><?= htmlspecialchars($d['jenis']) ?></td>
                <td><?= $d['stok'] ?></td>
                <td>
                    <?php if($d['stok'] > 0){ ?>
                        <small class="text-muted">Antara <?= $hari_ini ?> sampai <?= $max_batas ?></small>
                    <?php } else { ?>
                        -
                    <?php } ?>
                </td>
                <td>
                    <?php if($d['stok'] > 0){ ?>
                        <form method="POST" class="d-flex flex-column gap-2 align-items-center">
                            <input type="hidden" name="pinjam" value="<?= $d['id'] ?>">
                            <input type="date" name="batas_kembali" class="form-control form-control-sm"
                                min="<?= $hari_ini ?>" max="<?= $max_batas ?>" value="<?= $max_batas ?>" required>
                            <button type="submit" class="btn btn-success btn-sm w-100">Pinjam</button>
                        </form>
                    <?php } else { ?>
                        <button class="btn btn-secondary btn-sm" disabled>Habis</button>
                    <?php } ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>