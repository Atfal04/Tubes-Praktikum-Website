<?php
session_start();
include './lib/koneksi.php';

if (isset($_SESSION['kasir_id']) == false) {
    header("Location: index.php");
    exit;
}

    $filter_waktu = $_GET['filter_waktu'] ?? 'semua';
    $kondisi_waktu = "1=1";
    $label_laporan = "Semua Waktu";

    if ($filter_waktu === 'hari_ini') {
        $kondisi_waktu = "DATE(transaksi.waktu_transaksi) = CURDATE()";
        $label_laporan = "Hari Ini";
    } elseif ($filter_waktu === 'kemarin') {
        $kondisi_waktu = "DATE(transaksi.waktu_transaksi) = CURDATE() - INTERVAL 1 DAY";
        $label_laporan = "Kemarin";
    } elseif ($filter_waktu === '7_hari') {
        $kondisi_waktu = "DATE(transaksi.waktu_transaksi) >= CURDATE() - INTERVAL 7 DAY";
        $label_laporan = "7 Hari Terakhir";
    } elseif ($filter_waktu === 'bulan_ini') {
        $kondisi_waktu = "MONTH(transaksi.waktu_transaksi) = MONTH(CURDATE()) AND YEAR(transaksi.waktu_transaksi) = YEAR(CURDATE())";
        $label_laporan = "Bulan Ini";
    } elseif ($filter_waktu === 'tahun_ini') {
        $kondisi_waktu = "YEAR(transaksi.waktu_transaksi) = YEAR(CURDATE())";
        $label_laporan = "Tahun Ini";
    }

    $perintah_ambil_riwayat = mysqli_query($conn, "SELECT transaksi.*, users.username FROM transaksi JOIN users ON transaksi.kasir_id = users.id WHERE $kondisi_waktu ORDER BY transaksi.waktu_transaksi DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat - TepatKasir</title>
    <link rel="stylesheet" href="./style/style.css?v=4">
</head>

<body>

    <div class="navbar">
        <a href="#" class="navbar-brand">TepatKasir</a>
        <div class="navbar-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="kasir.php">Kasir</a>
            <a href="inventory.php">Inventory</a>
            <a href="riwayat.php" class="aktif">Riwayat</a>
            <span style="border-left:2px solid #000; padding-left:15px; margin-left:5px; font-weight:900;">👋 Halo, <?= $_SESSION['username'] ?></span>
            <a href="logout.php" class="btn-merah">Keluar</a>
        </div>
    </div>

    <div class="container">
        <div class="box-putih">
            <div class="hide-on-print" style="display:flex; justify-content:space-between; align-items:center; border-bottom: 2px solid #000; padding-bottom:10px; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                <form method="GET" style="display:flex; align-items:center; gap:15px; flex-wrap:wrap;">
                    <h2 style="margin:0;">📜 Laporan Transaksi</h2>
                    <select name="filter_waktu" class="kolom-ketik" onchange="this.form.submit()" style="margin-bottom:0; padding:8px 12px; font-size:15px; width:auto; cursor:pointer;">
                        <option value="semua" <?= $filter_waktu=='semua' ? 'selected':'' ?>>Semua Waktu</option>
                        <option value="hari_ini" <?= $filter_waktu=='hari_ini' ? 'selected':'' ?>>Hari Ini</option>
                        <option value="kemarin" <?= $filter_waktu=='kemarin' ? 'selected':'' ?>>Kemarin</option>
                        <option value="7_hari" <?= $filter_waktu=='7_hari' ? 'selected':'' ?>>7 Hari Terakhir</option>
                        <option value="bulan_ini" <?= $filter_waktu=='bulan_ini' ? 'selected':'' ?>>Bulan Ini</option>
                        <option value="tahun_ini" <?= $filter_waktu=='tahun_ini' ? 'selected':'' ?>>Tahun Ini</option>
                    </select>
                </form>
                <button onclick="window.print()" class="btn-biru" style="font-size:16px; border:2px solid #000; box-shadow: 2px 2px 0px #000; cursor:pointer; font-weight:800;">🖨️ Cetak Laporan</button>
            </div>

            <div class="print-header" style="display:none; text-align:center; margin-bottom:30px;">
                <h2 style="font-size:28px; text-transform:uppercase; text-decoration:underline; font-weight:900; margin-bottom:5px;">Laporan Transaksi TepatKasir</h2>
                <p style="font-size:16px; font-weight:800;">Periode: <?= $label_laporan ?></p>
            </div>

            <div class="wadah-tabel">
                <table class="tabel-brutal">
                    <tr>
                        <th style="background-color: #FBBC04; color: #000;">No. Nota</th>
                        <th style="background-color: #FBBC04; color: #000;">Waktu</th>
                        <th style="background-color: #FBBC04; color: #000;">Nama Kasir</th>
                        <th style="background-color: #FBBC04; color: #000;">Total Belanja</th>
                        <th style="background-color: #FBBC04; color: #000;">Metode</th>
                        <th style="background-color: #FBBC04; color: #000;">Bayar</th>
                        <th style="background-color: #FBBC04; color: #000;">Kembali</th>
                    </tr>
                    <?php 
                        $sum_total = 0;
                        $sum_bayar = 0;
                        $sum_kembali = 0;
                        while ($data_riwayat = mysqli_fetch_assoc($perintah_ambil_riwayat)): 
                            $sum_total += $data_riwayat['total_belanja'];
                            $sum_bayar += $data_riwayat['uang_bayar'];
                            $sum_kembali += $data_riwayat['kembalian'];
                    ?>
                        <tr>
                            <td style="font-weight:900;">TRX-<?= $data_riwayat['id'] ?></td>
                            <td style="font-weight:700;"><?= date('d M Y - H:i', strtotime($data_riwayat['waktu_transaksi'])) ?></td>
                            <td style="font-weight:900; color:#4285F4;"><?= $data_riwayat['username'] ?></td>
                            <td style="font-weight:900; color:#34A853; font-size:18px;">Rp <?= number_format($data_riwayat['total_belanja'], 0, ',', '.') ?></td>
                            <?php 
                                $tipe_tr = $data_riwayat['tipe_pembayaran'] ?? 'Tunai';
                                $warna_tipe = '#34A853'; 
                                if(strtolower($tipe_tr) == 'transfer') $warna_tipe = '#4285F4'; 
                                if(strtolower($tipe_tr) == 'qris') $warna_tipe = '#FBBC04';
                                $warna_teks_tipe = (strtolower($tipe_tr) == 'qris') ? '#000' : '#fff';
                            ?>
                            <td><span style="background-color:<?= $warna_tipe ?>; color:<?= $warna_teks_tipe ?>; padding:4px 8px; border-radius:3px; font-weight:900; font-size:12px; border:2px solid #000; box-shadow:1px 1px 0px #000; text-transform:uppercase;"><?= $tipe_tr ?></span></td>
                            <td style="font-weight:700;">Rp <?= number_format($data_riwayat['uang_bayar'], 0, ',', '.') ?></td>
                            <td style="font-weight:700; color:#EA4335;">Rp <?= number_format($data_riwayat['kembalian'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr style="background-color: #fbbc04; color: #000; border-top: 4px solid #000;">
                        <td colspan="3" style="text-align:right; font-weight:900; font-size:16px; text-transform:uppercase;">Total Pendapatan:</td>
                        <td style="font-weight:900; font-size:18px;">Rp <?= number_format($sum_total, 0, ',', '.') ?></td>
                        <td>-</td>
                        <td style="font-weight:900; font-size:16px;">Rp <?= number_format($sum_bayar, 0, ',', '.') ?></td>
                        <td style="font-weight:900; font-size:16px;">Rp <?= number_format($sum_kembali, 0, ',', '.') ?></td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</body>

</html>