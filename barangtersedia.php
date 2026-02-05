<?php
// 1. Inisialisasi keranjang dari cookie jika ada
$barangPilih = "";
if (isset($_COOKIE['keranjang'])) {
    $barangPilih = $_COOKIE['keranjang'];
}

// 2. Logika penambahan barang ke keranjang
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($barangPilih == "") {
        $barangPilih = $id; // Barang pertama
    } else {
        // Cek agar ID yang sama tidak masuk berulang kali (opsional tapi disarankan)
        $ids = explode(",", $barangPilih);
        if (!in_array($id, $ids)) {
            $barangPilih = $barangPilih . "," . $id;
        }
    }
    setcookie('keranjang', $barangPilih, time() + 3600);
}

// 3. Menyiapkan variabel untuk SQL
// Jika kosong, berikan nilai 0 agar query "NOT IN (0)" tetap valid
$sqlFilter = ($barangPilih == "") ? "0" : $barangPilih;
?>
<!DOCTYPE html>
<html>

<head>
    <title>Barang Tersedia</title>
    <style>
        table,
        td,
        th {
            border: 1px solid gray;
            padding: 8px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .tengah {
            width: 75%;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="tengah">
        <?php
        require_once 'barang.php';

        // Gunakan $sqlFilter agar tidak error saat keranjang kosong
        $sql = "SELECT * FROM barang WHERE id NOT IN ($sqlFilter) AND stok > 0 ORDER BY id DESC";

        echo "<code>Debug SQL: " . $sql . "</code><br>";

        $hasils = bacaBarang($sql);
        echo "<h2>DAFTAR BARANG TERSEDIA</h2>";

        if (count($hasils) > 0) {
            echo "<table>";
            echo "<tr>
                <th>Foto</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Operasi</th>
              </tr>";
            foreach ($hasils as $hasil) {
                echo "<tr>";
                echo "<td><img src='gambar/{$hasil['foto']}' width='100'></td>";
                echo "<td>{$hasil['nama']}</td>";
                echo "<td>" . number_format($hasil['harga'], 0, ',', '.') . "</td>";
                echo "<td>{$hasil['stok']}</td>";
                echo "<td><a href='{$_SERVER['PHP_SELF']}?id={$hasil['id']}'>Beli</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Semua barang sudah masuk keranjang atau stok habis.";
        }
        ?>
    </div>
</body>

</html>