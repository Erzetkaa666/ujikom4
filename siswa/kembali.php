<?php
session_start();
include __DIR__ . '/../koneksi.php';

if($_SESSION['role']!='siswa'){
    header("Location: ../auth/login.php");
    exit;
}

include '../config/header.php';

$id_anggota = $_SESSION['id_anggota'];
$today = date('Y-m-d');

// PROSES KEMBALI
if(isset($_POST['kembali'])){
    $id_transaksi = $_POST['id_transaksi'];
    $kondisi = $_POST['kondisi'];

    $trx = mysqli_fetch_assoc(mysqli_query($koneksi,"
        SELECT * FROM transaksi WHERE id='$id_transaksi'
    "));

    $batas = $trx['batas_kembali'];

    // Hitung keterlambatan
    $telat = (strtotime($today) - strtotime($batas)) / 86400;
    $telat = ($telat > 0) ? $telat : 0;
    $denda = $telat * 2000;

    // Update transaksi
    mysqli_query($koneksi,"
        UPDATE transaksi SET
            status='kembali',
            tanggal_kembali='$today',
            denda='$denda',
            kondisi_buku='$kondisi'
        WHERE id='$id_transaksi'
    ");

    // Kembalikan stok buku
    mysqli_query($koneksi,"
        UPDATE buku SET stok = stok + 1
        WHERE id='{$trx['id_buku']}'
    ");

    echo "<script>alert('Buku berhasil dikembalikan');location='kembali.php';</script>";
}

// Ambil buku yang masih dipinjam
$data = mysqli_query($koneksi,"
    SELECT t.id, b.judul, t.tanggal_pinjam, t.batas_kembali
    FROM transaksi t
    JOIN buku b ON t.id_buku=b.id
    WHERE t.status='pinjam' AND t.id_anggota='$id_anggota'
");
$jumlah_pinjam = mysqli_num_rows($data);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Pengembalian Buku</h4>
        <a href="dashboard.php" class="btn btn-secondary btn-sm">← Kembali Dashboard</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <p class="text-muted mb-0">Pilih buku yang ingin dikembalikan dan isi catatan kondisi agar proses lebih jelas.</p>
            </div>
            <div class="text-md-end">
                <span class="badge bg-primary">Belum kembali: <?= $jumlah_pinjam ?></span>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body p-3">
            <strong>Info:</strong> Jika melewati batas kembali, denda akan dihitung Rp 2.000/hari. Isi catatan kondisi buku dengan jelas, misalnya rusak, hilang halaman, atau cover lepas.
        </div>
    </div>

    <div class="card shadow-sm">
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
                                <br><span class="text-danger small">Terlambat <?= $telat ?> hari<br>Rp <?= number_format($denda_telat) ?></span>
                            <?php } else { ?>
                                <br><span class="text-success small">Tepat waktu</span>
                            <?php } ?>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#kembali<?= $d['id'] ?>">
                                Kembalikan
                            </button>

                            <div class="modal fade" id="kembali<?= $d['id'] ?>">
                              <div class="modal-dialog">
                                <form method="POST" class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Kondisi Buku</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                  </div>

                                  <div class="modal-body">
                                      <input type="hidden" name="id_transaksi" value="<?= $d['id'] ?>">

                                      <p class="mb-2"><strong>Batas kembali:</strong> <?= $d['batas_kembali'] ?></p>
                                      <?php if($telat > 0){ ?>
                                          <p class="text-danger mb-3">Terlambat <?= $telat ?> hari, denda Rp <?= number_format($denda_telat) ?></p>
                                      <?php } else { ?>
                                          <p class="text-success mb-3">Masih dalam batas waktu pengembalian.</p>
                                      <?php } ?>

                                      <label class="form-label">Catatan kondisi buku saat dikembalikan</label>
                                      <textarea name="kondisi" class="form-control" required rows="3"
                                        placeholder="Contoh: Aman, robek halaman 5, cover lepas, hilang halaman"></textarea>
                                  </div>

                                  <div class="modal-footer">
                                    <button class="btn btn-success" name="kembali">Simpan & Kembalikan</button>
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

<?php include '../config/footer.php'; ?>