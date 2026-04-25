<?php
include __DIR__ . '/koneksi.php';
session_start();

// Hanya admin yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die('Access denied');
}

echo "<h3>Database Diagnostic</h3>";

// 1. Cek struktur tabel user
echo "<h4>1. Struktur Tabel USER:</h4>";
$structure = mysqli_query($koneksi, "DESCRIBE user");
echo "<table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th><th>Key</th></tr>";
while ($row = mysqli_fetch_assoc($structure)) {
    echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Key']}</td></tr>";
}
echo "</table>";

// 2. Cek struktur tabel anggota
echo "<h4>2. Struktur Tabel ANGGOTA:</h4>";
$structure = mysqli_query($koneksi, "DESCRIBE anggota");
echo "<table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th><th>Key</th></tr>";
while ($row = mysqli_fetch_assoc($structure)) {
    echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Key']}</td></tr>";
}
echo "</table>";

// 3. Cek data user
echo "<h4>3. Data User di Database:</h4>";
$users = mysqli_query($koneksi, "SELECT u.id, u.username, u.role, u.id_anggota, a.nama, a.tingkat, a.jurusan 
                                 FROM user u 
                                 LEFT JOIN anggota a ON u.id_anggota = a.id 
                                 LIMIT 5");
echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Username</th><th>Role</th><th>ID Anggota</th><th>Nama</th><th>Tingkat</th><th>Jurusan</th></tr>";
while ($row = mysqli_fetch_assoc($users)) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['username']}</td>";
    echo "<td>{$row['role']}</td>";
    echo "<td>{$row['id_anggota']}</td>";
    echo "<td>{$row['nama']}</td>";
    echo "<td>{$row['tingkat']}</td>";
    echo "<td>{$row['jurusan']}</td>";
    echo "</tr>";
}
echo "</table>";

// 4. Test query edit
if (isset($_GET['test_edit_id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['test_edit_id']);
    echo "<h4>4. Test Query Edit untuk ID: $id</h4>";
    $q = mysqli_query($koneksi, "SELECT u.*, a.nama as nama_anggota, a.tingkat, a.jurusan 
                                  FROM user u 
                                  LEFT JOIN anggota a ON u.id_anggota = a.id 
                                  WHERE u.id='$id'");
    
    if ($q && mysqli_num_rows($q) > 0) {
        $edit = mysqli_fetch_assoc($q);
        echo "<pre>";
        print_r($edit);
        echo "</pre>";
    } else {
        echo "Query error atau data tidak ditemukan";
    }
}

echo "<p><a href='admin/user.php'>← Kembali ke User Management</a></p>";
?>
