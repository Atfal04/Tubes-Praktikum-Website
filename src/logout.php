<?php
session_start();

// 1. Hancurkan semua data Session
session_destroy();

// 2. Hapus Cookie yang pernah dibuat (set waktu mundur ke -3600)
if (isset($_COOKIE['ingat_kasir_id'])) {
    setcookie('ingat_kasir_id', '', time() - 3600, "/");
}
if (isset($_COOKIE['ingat_username'])) {
    setcookie('ingat_username', '', time() - 3600, "/");
}

// 3. Kembalikan ke halaman login
header("Location: index.php");
exit;
?>