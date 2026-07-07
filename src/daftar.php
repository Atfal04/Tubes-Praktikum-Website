<?php
session_start();
include './lib/koneksi.php';

// Kalau sudah masuk, langsung ke dashboard
if (isset($_SESSION['kasir_id'])) {
    header("Location: dashboard.php");
    exit;
}

$pesan_sukses = "";
$pesan_error = "";

if (isset($_POST['tombol_daftar'])) {
    $nama_baru = $_POST['username'];
    $sandi_baru = md5($_POST['password']);

    // Cek apakah username sudah dipakai
    $perintah_cari = mysqli_query($conn, "SELECT * FROM users WHERE username='$nama_baru'");

    if (mysqli_num_rows($perintah_cari) > 0) {
        $pesan_error = "Maaf, nama tersebut sudah dipakai orang lain.";
    } else {
        // Jika belum dipakai, simpan ke database
        mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('$nama_baru', '$sandi_baru')");
        $pesan_sukses = "Pendaftaran berhasil! Silakan ke halaman MASUK.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - TepatKasir</title>
    <link rel="stylesheet" href="./style/style.css">
</head>

<body class="layar-login">

    <div class="kotak-login">
        <h1 style="font-size: 34px; font-weight: 900; color: #202124; margin-bottom: 10px; text-transform: uppercase;">AKUN BARU</h1>
        <p style="font-weight: 600; color: #636e72; margin-bottom: 30px;">Buat akun untuk bisa masuk ke sistem</p>

        <?php if ($pesan_error != "") {
            echo "<div style='background:#EA4335; color:white; padding:10px; border:2px solid #000; font-weight:bold; margin-bottom:20px; box-shadow: 0.2rem 0.2rem 0 #000;'>$pesan_error</div>";
        } ?>
        <?php if ($pesan_sukses != "") {
            echo "<div style='background:#34A853; color:white; padding:10px; border:2px solid #000; font-weight:bold; margin-bottom:20px; box-shadow: 0.2rem 0.2rem 0 #000;'>$pesan_sukses</div>";
        } ?>

        <form method="POST">
            <input type="text" name="username" class="kolom-ketik" placeholder="Buat Username Baru" required>
            <input type="password" name="password" class="kolom-ketik" placeholder="Buat Password Baru" required>
            <button type="submit" name="tombol_daftar" class="btn-hijau" style="font-size: 18px; padding: 15px; margin-bottom: 25px;">DAFTAR SEKARANG</button>
        </form>

        <div style="margin-top: 10px;">
            <p style="font-weight: bold; color: #202124; margin-bottom: 10px;">Sudah punya akun?</p>

            <a href="index.php" class="btn-biru" style="display: block; width: 100%; text-align: center; padding: 15px; font-size: 16px; box-sizing: border-box;">Kembali ke halaman MASUK</a>
        </div>
    </div>

</body>

</html>   