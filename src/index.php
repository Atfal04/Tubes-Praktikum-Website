<?php
include './lib/koneksi.php';
session_start();

// 1. CEK COOKIE (Jika session kosong tapi cookie ada, otomatis login-kan)
if (!isset($_SESSION['kasir_id']) && isset($_COOKIE['ingat_kasir_id']) && isset($_COOKIE['ingat_username'])) {
    // Dekode atau ambil langsung data dari cookie
    $cookie_id = $_COOKIE['ingat_kasir_id'];
    $cookie_user = $_COOKIE['ingat_username'];

    // Validasi ulang ke database demi keamanan
    $cek_cookie_db = mysqli_query($conn, "SELECT * FROM users WHERE id='$cookie_id' AND username='$cookie_user'");
    if (mysqli_num_rows($cek_cookie_db) > 0) {
        $data_user = mysqli_fetch_assoc($cek_cookie_db);
        $_SESSION['kasir_id'] = $data_user['id'];
        $_SESSION['username'] = $data_user['username'];
    }
}

// 2. CEK SESSION (Kalau sudah login, langsung lempar ke Dashboard)
if (isset($_SESSION['kasir_id'])) {
    header("Location: dashboard.php");
    exit;
}

$pesan_error = "";

// 3. JIKA TOMBOL "MASUK" DITEKAN
if (isset($_POST['tombol_login'])) {
    $username_diketik = $_POST['username'];
    $password_diketik = md5($_POST['password']);
    $cari_user = mysqli_query($conn, "SELECT * FROM users WHERE username='$username_diketik' AND password='$password_diketik'");

    if (mysqli_num_rows($cari_user) > 0) {
        $data_user = mysqli_fetch_assoc($cari_user);
        
        // Set Session Utama
        $_SESSION['kasir_id'] = $data_user['id'];
        $_SESSION['username'] = $data_user['username'];

        // JIKA "INGAT SAYA" DICENTANG, BUAT COOKIE (Berlaku 30 Hari)
        if (isset($_POST['ingat_saya'])) {
            setcookie('ingat_kasir_id', $data_user['id'], time() + (30 * 24 * 60 * 60), "/");
            setcookie('ingat_username', $data_user['username'], time() + (30 * 24 * 60 * 60), "/");
        }

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

            <div style="text-align: left; margin-bottom: 20px; font-weight: 700;">
                <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="ingat_saya" style="width: 18px; height: 18px; accent-color: #202124;"> Ingat Saya (30 Hari)
                </label>
            </div>

            <button type="submit" name="tombol_login" class="btn-biru" style="width: 100%; font-size: 18px; padding: 15px; margin-bottom: 20px;">MASUK</button>
        </form>

        <div style="border-top: 2px solid #000; padding-top: 20px; margin-top: 10px;">
            <p style="font-weight: bold; color: #202124; margin-bottom: 15px;">Belum punya akun?</p>
            <a href="daftar.php" class="btn-kuning" style="width: 100%; display: block; text-align: center; padding: 15px; font-size: 16px; box-sizing: border-box;">DAFTAR KASIR BARU</a>
        </div>
    </div>

</body>
</html>