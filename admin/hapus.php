<?php
include __DIR__ . '/../koneksi.php';
mysqli_query($koneksi,"DELETE FROM buku WHERE id='$_GET[id]'");
header("Location: buku.php");
?>