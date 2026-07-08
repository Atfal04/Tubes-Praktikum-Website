<?php
include './lib/koneksi.php';
session_start();

// Cek apakah user sudah login sebelumnya. Kalau sudah, langsung arahkan ke Dashboard.
if (isset($_SESSION['kasir_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Menyiapkan variabel kosong untuk pesan error
$pesan_error = "";

// JIKA TOMBOL "MASUK" DITEKAN
if (isset($_POST['tombol_login'])) {
    $username_diketik = $_POST['username'];
    $password_diketik = md5($_POST['password']);
    $cari_user = mysqli_query($conn, "SELECT * FROM users WHERE username='$username_diketik' AND password='$password_diketik'");

    // Jika datanya ditemukan (jumlah baris lebih dari 0)
    if (mysqli_num_rows($cari_user) > 0) {
        $data_user = mysqli_fetch_assoc($cari_user);
        $_SESSION['kasir_id'] = $data_user['id'];
        $_SESSION['username'] = $data_user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $pesan_error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TepatKasir</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style/style.css">
</head>

<body class="layar-login">

    <div class="kotak-login">
        <h1 style="font-size: 36px; font-weight: 800; color: #202124; margin-bottom: 10px;">TEPATKASIR</h1>
        <p style="font-weight: 600; color: #636e72; margin-bottom: 30px;">Silakan masuk ke sistem</p>

        <?php if ($pesan_error != ""): ?>
            <div style="background: #EA4335; color: white; padding: 10px; border: 2px solid #000; font-weight: bold; margin-bottom: 20px; box-shadow: 0.2rem 0.2rem 0 #000;">
                <?= $pesan_error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" class="kolom-ketik" placeholder="Masukkan Username" required>
            <input type="password" name="password" class="kolom-ketik" placeholder="Masukkan Password" required>

            <button type="submit" name="tombol_login" class="btn-biru" style="width: 100%; font-size: 18px; padding: 15px; margin-bottom: 20px;">MASUK</button>
        </form>

        <div style="border-top: 2px solid #000; padding-top: 20px; margin-top: 10px;">
            <p style="font-weight: bold; color: #202124; margin-bottom: 15px;">
                Belum punya akun?
            </p>
            <a href="daftar.php" class="btn-kuning" style="width: 100%; display: block; text-align: center; padding: 15px; font-size: 16px; box-sizing: border-box;">DAFTAR KASIR BARU</a>
        </div>

    </div>

</body>

</html>