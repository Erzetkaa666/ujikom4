<?php
session_start();
include __DIR__ . '/../koneksi.php';

// Enable error display untuk debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
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

$message = null;
$message_type = "";

// Cek jika ada message dari redirect
if (isset($_GET['message'])) {
    if ($_GET['message'] == 'updated') {
        $message = "✓ User berhasil diupdate!";
        $message_type = "success";
    } elseif ($_GET['message'] == 'added') {
        $message = "✓ User berhasil ditambahkan!";
        $message_type = "success";
    }
}

// TAMBAH USER
if (isset($_POST['tambah_user'])) {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tingkat   = mysqli_real_escape_string($koneksi, $_POST['tingkat'] ?? '');
    $jurusan   = mysqli_real_escape_string($koneksi, $_POST['jurusan'] ?? '');
    $username  = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password  = md5($_POST['password']); // Hash password dengan MD5
    $role      = mysqli_real_escape_string($koneksi, $_POST['role']);

    // Cek username sudah ada atau belum
    $cek = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $message = "✗ Username <code>$username</code> sudah terdaftar!";
        $message_type = "danger";
    } else {
        $id_anggota = NULL; // Default NULL untuk admin
        
        // Jika role adalah siswa, cek/buat anggota
        if ($role == 'siswa') {
            // Validasi tingkat dan jurusan untuk siswa
            if (empty($tingkat) || empty($jurusan)) {
                $message = "✗ Untuk siswa, tingkat dan jurusan harus diisi!";
                $message_type = "danger";
            } else {
                // Cari anggota berdasarkan nama, tingkat dan jurusan
                $cek_anggota = mysqli_query($koneksi, "SELECT id FROM anggota WHERE nama='$nama' AND tingkat='$tingkat' AND jurusan='$jurusan' LIMIT 1");
                
                if (mysqli_num_rows($cek_anggota) > 0) {
                    $anggota = mysqli_fetch_assoc($cek_anggota);
                    $id_anggota = $anggota['id'];
                } else {
                    // Jika anggota belum ada, buat anggota baru
                    $insert_anggota = mysqli_query($koneksi, "INSERT INTO anggota (nama, tingkat, jurusan) VALUES ('$nama', '$tingkat', '$jurusan')");
                    if (!$insert_anggota) {
                        $message = "✗ Gagal membuat data anggota: " . mysqli_error($koneksi);
                        $message_type = "danger";
                        $id_anggota = null;
                    } else {
                        $id_anggota = mysqli_insert_id($koneksi);
                    }
                }
            }
        }

        // Insert user baru jika tidak ada error
        if (!isset($message)) {
            $result = mysqli_query($koneksi, "INSERT INTO user (username, password, role, id_anggota) 
                                             VALUES ('$username', '$password', '$role', " . ($id_anggota ? "'$id_anggota'" : "NULL") . ")");

            if ($result) {
                $message = "✓ User <code>" . htmlspecialchars($username) . "</code> berhasil ditambahkan!";
                $message_type = "success";
                // Redirect untuk clear form
                header("Location: user.php?message=added");
                exit;
            } else {
                $message = "✗ Gagal menambahkan user: " . mysqli_error($koneksi);
                $message_type = "danger";
            }
        }
    }
}

// EDIT USER
$debug_edit = array();
if (isset($_POST['edit_user'])) {
    $id_user   = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $username  = mysqli_real_escape_string($koneksi, $_POST['username']);
    $role      = mysqli_real_escape_string($koneksi, $_POST['role']);
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tingkat   = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    $jurusan   = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    
    $debug_edit[] = "1. Input: ID=$id_user, Username=$username, Role=$role";
    $debug_edit[] = "2. Data: Nama=$nama, Tingkat=$tingkat, Jurusan=$jurusan";
    
    // Cek user ada atau tidak
    $query_check = "SELECT username, id_anggota FROM user WHERE id='$id_user'";
    $debug_edit[] = "3. Query Check: $query_check";
    $result_check = mysqli_query($koneksi, $query_check);
    
    if (!$result_check) {
        $message = "✗ Database error: " . mysqli_error($koneksi);
        $message_type = "danger";
        $debug_edit[] = "ERROR at query check: " . mysqli_error($koneksi);
    } else {
        $edit_user_check = mysqli_fetch_assoc($result_check);
        if (!$edit_user_check) {
            $message = "✗ User tidak ditemukan!";
            $message_type = "danger";
            $debug_edit[] = "ERROR: User ID $id_user tidak ditemukan di database";
        } else {
            $debug_edit[] = "4. User ditemukan - ID Anggota: " . ($edit_user_check['id_anggota'] ?? 'NULL');
            $debug_edit[] = "4a. Old username: " . $edit_user_check['username'];
            $debug_edit[] = "4b. New username: " . $username;
            $debug_edit[] = "4c. Are they different? " . ($edit_user_check['username'] != $username ? "YES" : "NO");
            
            // Cek username duplikat
            if ($edit_user_check['username'] != $username) {
                $debug_edit[] = "4d. Username berbeda, checking duplikat...";
                $cek_username = mysqli_query($koneksi, "SELECT id FROM user WHERE username='$username'");
                $debug_edit[] = "4e. Duplikat query result: " . ($cek_username ? "OK" : "FAILED");
                
                if ($cek_username && mysqli_num_rows($cek_username) > 0) {
                    $message = "✗ Username <code>$username</code> sudah terdaftar oleh user lain!";
                    $message_type = "danger";
                    $debug_edit[] = "4f. Duplikat FOUND!";
                } else {
                    $debug_edit[] = "4f. Duplikat not found, OK to proceed";
                }
            } else {
                $debug_edit[] = "4d. Username sama, skip duplikat check";
            }
            
            $debug_edit[] = "4g. After username check - message set? " . (isset($message) ? "YES" : "NO");
            
            if (!isset($message)) {
                // Update data user (username, password, role)
                $debug_edit[] = "5a. Checking message status: " . (isset($message) ? "SET" : "NOT SET");
                
                if (!empty($_POST['password'])) {
                    $password = md5($_POST['password']);
                    $query_user = "UPDATE user SET username='$username', password='$password', role='$role' WHERE id='$id_user'";
                    $debug_edit[] = "5b. Query Update User (with pass): $query_user";
                } else {
                    $query_user = "UPDATE user SET username='$username', role='$role' WHERE id='$id_user'";
                    $debug_edit[] = "5b. Query Update User (no pass): $query_user";
                }
                
                $debug_edit[] = "5c. About to execute query...";
                $result = mysqli_query($koneksi, $query_user);
                $debug_edit[] = "5d. Query executed, result: " . ($result ? "TRUE" : "FALSE");
                
                if (!$result) {
                    $affected = 0;
                    $error = mysqli_error($koneksi);
                    $debug_edit[] = "5e. Query failed with error: $error";
                    $message = "✗ Gagal update user: $error";
                    $message_type = "danger";
                } else {
                    $affected = mysqli_affected_rows($koneksi);
                    $debug_edit[] = "5e. Query success - rows affected: $affected";
                    $result = true;
                }
                
                // Update data anggota jika ada (nama, tingkat, jurusan)
                $debug_edit[] = "6a. Checking if need to update anggota...";
                $debug_edit[] = "6b. result=$result, id_anggota=" . ($edit_user_check['id_anggota'] ?? 'NULL') . ", nama=$nama";
                
                if ($result && $edit_user_check['id_anggota'] && !empty($nama)) {
                    $id_anggota = $edit_user_check['id_anggota'];
                    $query_anggota = "UPDATE anggota SET nama='$nama', tingkat='$tingkat', jurusan='$jurusan' WHERE id='$id_anggota'";
                    $debug_edit[] = "6c. Query Update Anggota: $query_anggota";
                    
                    $result_anggota = mysqli_query($koneksi, $query_anggota);
                    $affected_anggota = mysqli_affected_rows($koneksi);
                    
                    $debug_edit[] = "6d. Anggota query result: " . ($result_anggota ? "TRUE" : "FALSE");
                    
                    if (!$result_anggota) {
                        $error = mysqli_error($koneksi);
                        $message = "✗ Gagal update data anggota: $error";
                        $message_type = "danger";
                        $debug_edit[] = "6e. ERROR Update Anggota: $error";
                        $result = false;
                    } else {
                        $debug_edit[] = "6e. Update Anggota SUCCESS (rows affected: $affected_anggota)";
                    }
                } else {
                    if ($result) {
                        $debug_edit[] = "6c. Skip update anggota";
                    }
                }
                
                $debug_edit[] = "7a. Final check - isset(message)=" . (isset($message) ? "TRUE" : "FALSE") . ", result=$result";
                
                if ($result && !isset($message)) {
                    $message = "✓ User <code>" . htmlspecialchars($username) . "</code> berhasil diupdate!";
                    $message_type = "success";
                    $debug_edit[] = "7b. FINAL RESULT: SUCCESS!";
                    // Redirect untuk clear form dan $_POST
                    header("Location: user.php?message=updated");
                    exit;
                } else {
                    $debug_edit[] = "7b. FINAL RESULT: INCOMPLETE - result=$result, message set=" . (isset($message) ? "yes" : "no");
                }
            }
        }
    }
}

// HAPUS USER
if (isset($_GET['hapus'])) {
    $id_user = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // Jangan hapus admin pertama
    $cek_admin = mysqli_query($koneksi, "SELECT * FROM user WHERE id='$id_user' AND username='admin'");
    if (mysqli_num_rows($cek_admin) > 0) {
        $message = "✗ Tidak bisa menghapus user admin utama!";
        $message_type = "danger";
    } else {
        $result = mysqli_query($koneksi, "DELETE FROM user WHERE id='$id_user'");
        if ($result) {
            $message = "✓ User berhasil dihapus";
            $message_type = "success";
        } else {
            $message = "✗ Gagal menghapus user";
            $message_type = "danger";
        }
    }
}

// DATA EDIT
$edit = null;
if (isset($_GET['edit'])) {
    $id_user = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $q = mysqli_query($koneksi, "SELECT u.*, a.nama as nama_anggota, a.tingkat, a.jurusan 
                                  FROM user u 
                                  LEFT JOIN anggota a ON u.id_anggota = a.id 
                                  WHERE u.id='$id_user'");
    $edit = mysqli_fetch_assoc($q);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User/Login</title>
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
        .form-section {
            border-left: 4px solid #3b82f6;
            padding-left: 16px;
        }
        .password-note {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4>Kelola User / Akun Login</h4>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Tambah, edit, atau hapus akun login untuk admin dan siswa</p>
        </div>
        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-secondary btn-sm">← Dashboard</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Debug: Tampilkan status edit -->
    <?php if ($edit): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Mode Edit</strong> - Mengubah akun: <code><?= htmlspecialchars($edit['username']) ?></code>
            <a href="user.php" class="ms-2">← Kembali ke daftar</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card p-4 mb-4">
        <div class="form-section">
            <h5 class="mb-3">
                <?= $edit ? 'Edit Akun Login' : 'Tambah Akun Login Baru' ?>
            </h5>
            
            <form method="POST">
                <input type="hidden" name="<?= $edit ? 'edit_user' : 'tambah_user' ?>" value="1">
                <?php if ($edit): ?>
                    <input type="hidden" name="id_user" value="<?= $edit['id'] ?>">
                <?php endif; ?>

                <?php if (!$edit): ?>
                <!-- FORM TAMBAH USER -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label"><strong>Role / Peran</strong> <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" id="role_select" required onchange="updateFieldsForRole()">
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="siswa" <?= (isset($_POST['role']) && $_POST['role'] == 'siswa') ? 'selected' : '' ?>>Siswa</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" 
                               placeholder="Contoh: Ahmad Rizki"
                               value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-3" id="tingkat_field" style="display:none;">
                        <label class="form-label">Tingkat Kelas <span class="text-danger" id="tingkat_required">*</span></label>
                        <select name="tingkat" class="form-select" id="tingkat_select">
                            <option value="">-- Pilih Tingkat --</option>
                            <?php foreach ($daftar_tingkat as $tkt): ?>
                                <option value="<?= $tkt ?>" <?= (isset($_POST['tingkat']) && $_POST['tingkat'] == $tkt) ? 'selected' : '' ?>>
                                    Kelas <?= $tkt ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3" id="jurusan_field" style="display:none;">
                        <label class="form-label">Jurusan <span class="text-danger" id="jurusan_required">*</span></label>
                        <select name="jurusan" class="form-select" id="jurusan_select">
                            <option value="">-- Pilih Jurusan --</option>
                            <?php foreach ($daftar_jurusan as $jr): ?>
                                <option value="<?= $jr ?>" <?= (isset($_POST['jurusan']) && $_POST['jurusan'] == $jr) ? 'selected' : '' ?>>
                                    <?= $jr ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" 
                               placeholder="Contoh: ahmad_rizki"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">Username harus unik</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" 
                               placeholder="Minimum 6 karakter" required>
                        <div class="password-note">
                            🔐 Password akan di-hash otomatis
                        </div>
                    </div>
                </div>

                <?php else: ?>
                <!-- FORM EDIT USER - Bisa edit nama, tingkat, jurusan, username, role -->
                <div class="row g-3">
                    <?php if ($edit['id_anggota']): ?>
                    <!-- User adalah Siswa - Tampilkan field data anggota -->
                    <div class="col-md-6">
                        <label class="form-label">👤 Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" 
                               placeholder="Contoh: Ahmad Rizki"
                               value="<?= htmlspecialchars($edit['nama_anggota'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">🏫 Tingkat Kelas <span class="text-danger">*</span></label>
                        <select name="tingkat" class="form-select" required>
                            <option value="">-- Pilih Tingkat --</option>
                            <?php foreach ($daftar_tingkat as $tkt): ?>
                                <option value="<?= $tkt ?>" <?= ($edit['tingkat'] == $tkt) ? 'selected' : '' ?>>
                                    Kelas <?= $tkt ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">📚 Jurusan <span class="text-danger">*</span></label>
                        <select name="jurusan" class="form-select" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <?php foreach ($daftar_jurusan as $jr): ?>
                                <option value="<?= $jr ?>" <?= ($edit['jurusan'] == $jr) ? 'selected' : '' ?>>
                                    <?= $jr ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <!-- User adalah Admin - Hidden fields untuk nama/tingkat/jurusan -->
                    <input type="hidden" name="nama" value="">
                    <input type="hidden" name="tingkat" value="">
                    <input type="hidden" name="jurusan" value="">
                    <div class="col-md-12">
                        <p class="text-muted"><small>👤 Ini adalah akun Admin (tidak ada data siswa yang terkait)</small></p>
                    </div>
                    <?php endif; ?>

                    <div class="col-md-4">
                        <label class="form-label"><strong>Username</strong> <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" 
                               placeholder="Contoh: ahmad_rizki"
                               value="<?= htmlspecialchars($edit['username'] ?? '') ?>" required>
                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">Username harus unik</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><strong>Role / Peran</strong> <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="admin" <?= ($edit && $edit['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="siswa" <?= ($edit && $edit['role'] == 'siswa') ? 'selected' : '' ?>>Siswa</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">🔑 Password (Kosongkan jika tidak ingin ubah)</label>
                        <input type="password" name="password" class="form-control" 
                               placeholder="Biarkan kosong jika tidak ingin ubah password">
                        <div class="password-note">
                            🔐 Password akan di-hash otomatis jika diisi
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mt-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-<?= $edit ? 'warning' : 'success' ?>">
                            <?= $edit ? 'Update Akun' : '➕ Tambah Akun' ?>
                        </button>
                        <?php if ($edit): ?>
                            <a href="user.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <h6 class="mb-0">Daftar User / Akun Login</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Username</th>
                        <th>Nama Anggota</th>
                        <th>Tingkat Kelas</th>
                        <th>Jurusan</th>
                        <th>Role</th>
                        <th>Tanggal Join</th>
                        <th style="width:150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    // Query dengan sorting: Admin di atas, kemudian berdasarkan nama abjad
                    $query = mysqli_query($koneksi, "SELECT u.*, a.nama as nama_anggota, a.tingkat, a.jurusan 
                                                    FROM user u 
                                                    LEFT JOIN anggota a ON u.id_anggota = a.id 
                                                    ORDER BY 
                                                        CASE WHEN u.role = 'admin' THEN 0 ELSE 1 END ASC,
                                                        COALESCE(a.nama, u.username) ASC");
                    if (mysqli_num_rows($query) > 0) {
                        while ($d = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td class="text-center"><strong><?= $no++ ?></strong></td>
                        <td>
                            <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($d['username']) ?>
                            </code>
                        </td>
                        <td><?= $d['nama_anggota'] ?? '<em class="text-muted">-</em>' ?></td>
                        <td>
                            <?php if ($d['tingkat']): ?>
                                <span class="badge bg-secondary">Kelas <?= $d['tingkat'] ?></span>
                            <?php else: ?>
                                <em class="text-muted">-</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($d['jurusan']): ?>
                                <span class="badge bg-<?= getBadgeColor($d['jurusan']) ?>">
                                    <?= htmlspecialchars($d['jurusan']) ?>
                                </span>
                            <?php else: ?>
                                <em class="text-muted">-</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $d['role'] == 'admin' ? 'bg-danger' : 'bg-success' ?>">
                                <?= ucfirst($d['role']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($d['created_at'])) ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="?edit=<?= $d['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <?php if ($d['username'] != 'admin'): // Jangan bisa hapus admin utama ?>
                                    <a href="?hapus=<?= $d['id'] ?>" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin hapus user <?= htmlspecialchars($d['username']) ?>?')">Hapus</a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-secondary" disabled title="Tidak bisa hapus admin utama">Hapus</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php }} else { ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada user</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Update field visibility dan requirement berdasarkan role
function updateFieldsForRole() {
    const role = document.getElementById('role_select').value;
    const tingkatField = document.getElementById('tingkat_field');
    const jurusanField = document.getElementById('jurusan_field');
    const tingkatSelect = document.getElementById('tingkat_select');
    const jurusanSelect = document.getElementById('jurusan_select');
    
    if (role === 'siswa') {
        // Tampilkan field dan set required
        tingkatField.style.display = 'block';
        jurusanField.style.display = 'block';
        tingkatSelect.required = true;
        jurusanSelect.required = true;
    } else if (role === 'admin') {
        // Sembunyikan field dan remove required
        tingkatField.style.display = 'none';
        jurusanField.style.display = 'none';
        tingkatSelect.required = false;
        jurusanSelect.required = false;
        tingkatSelect.value = '';
        jurusanSelect.value = '';
    }
}

// Jalankan saat halaman load untuk restore state jika form sudah terisi
document.addEventListener('DOMContentLoaded', function() {
    updateFieldsForRole();
});

// Konfirmasi logout
function confirmLogout(event) {
    if (!confirm('Apakah Anda yakin ingin logout?')) {
        event.preventDefault();
    }
}
</script>
</body>
</html>
