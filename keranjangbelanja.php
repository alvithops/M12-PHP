<?php
$barangPilih = "0"; // Default 0 agar query IN (0) tidak error
if (isset($_COOKIE['keranjang'])) {
    $barangPilih = $_COOKIE['keranjang'];
}

if (isset($_GET['id'])) {
    $idHapus = $_GET['id'];

    // Pecah string menjadi array agar mudah dikelola
    $identitas = explode(",", $barangPilih);

    // Cari posisi ID yang mau dihapus, jika ada maka buang dari array
    if (($key = array_search($idHapus, $identitas)) !== false) {
        unset($identitas[$key]);
    }

    // Gabungkan kembali menjadi string koma
    $barangPilih = implode(",", $identitas);

    // Jika kosong setelah dihapus, kembalikan ke "0"
    if (empty($barangPilih))
        $barangPilih = "0";

    setcookie('keranjang', $barangPilih, time() + 3600);

    // Redirect agar perubahan langsung terlihat
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Keranjang Belanja</title>
    <style>
        table,
        td,
        th {
            border: 1px solid gray;
            padding: 10px;
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

        // SQL hanya akan mengambil data jika ID ada di cookie
        $sql = "SELECT * FROM barang WHERE id IN ($barangPilih) ORDER BY id DESC";

        echo "<code>Debug SQL: " . $sql . "</code><br>";

        $hasils = bacaBarang($sql);
        echo "<h2>KERANJANG BELANJA ANDA</h2>";

        if (count($hasils) > 0 && $barangPilih != "0") {
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
                echo "<td><a href='{$_SERVER['PHP_SELF']}?id={$hasil['id']}'>Batal Beli</a></td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<br><a href='daftar_barang.php'>Tambah Barang Lagi</a>";
        } else {
            echo "<p>Keranjang kosong. <a href='daftar_barang.php'>Lihat Barang</a></p>";
        }
        ?>
    </div>
</body>

</html>