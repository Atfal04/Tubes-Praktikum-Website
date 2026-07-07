<?php
$hostname = "localhost";
$username = "root";
$password = "";
$db   = "tepatkasir";

$conn = mysqli_connect($hostname, $username, $password, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek dan tambah kolom 'satuan' di tabel produk
$cek_satuan = mysqli_query($conn, "SHOW COLUMNS FROM produk LIKE 'satuan'");
if ($cek_satuan && mysqli_num_rows($cek_satuan) == 0) {
    mysqli_query($conn, "ALTER TABLE produk ADD COLUMN satuan VARCHAR(50) DEFAULT 'Pcs'");
}

// Cek dan tambah kolom 'tipe_pembayaran' di tabel transaksi
$cek_tipe = mysqli_query($conn, "SHOW COLUMNS FROM transaksi LIKE 'tipe_pembayaran'");
if ($cek_tipe && mysqli_num_rows($cek_tipe) == 0) {
    mysqli_query($conn, "ALTER TABLE transaksi ADD COLUMN tipe_pembayaran VARCHAR(50) DEFAULT 'Tunai'");
}

// Ubah Engine ke InnoDB untuk mendukung Relasi Foreign Key (FK)
mysqli_query($conn, "ALTER TABLE users ENGINE=InnoDB");
mysqli_query($conn, "ALTER TABLE transaksi ENGINE=InnoDB");
mysqli_query($conn, "ALTER TABLE produk ENGINE=InnoDB");

// Tambahkan Relasi Foreign Key (FK) 
try {
    $cek_fk = mysqli_query($conn, "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = 'tepatkasir' AND TABLE_NAME = 'transaksi' AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'fk_transaksi_kasir'");
    if ($cek_fk && mysqli_num_rows($cek_fk) == 0) {
        // Amankan data yatim (orphan): Jika ada kasir_id di transaksi yang usernya sudah dihapus,
        // alihkan sementara ke user pertama agar tidak menyebabkan error Foreign Key dan data penjualan tidak hilang.
        mysqli_query($conn, "UPDATE transaksi SET kasir_id = (SELECT id FROM users LIMIT 1) WHERE kasir_id NOT IN (SELECT id FROM users)");
        
        // Buat relasi FK
        mysqli_query($conn, "ALTER TABLE transaksi ADD CONSTRAINT fk_transaksi_kasir FOREIGN KEY (kasir_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE");
    }
} catch (\Throwable $e) {
}

// Buat tabel transaksi_detail untuk merinci barang yang dibeli
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS transaksi_detail (
        id INT AUTO_INCREMENT PRIMARY KEY,
        transaksi_id INT,
        produk_id INT,
        jumlah INT,
        harga_satuan INT,
        FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE=InnoDB
");
?>