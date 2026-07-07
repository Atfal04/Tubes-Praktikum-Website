<?php
include './lib/koneksi.php';

$res = mysqli_query($conn, "DESCRIBE produk");
echo "Table: produk\n";
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

echo "\nTable: transaksi\n";
$res2 = mysqli_query($conn, "DESCRIBE transaksi");
while ($row = mysqli_fetch_assoc($res2)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
