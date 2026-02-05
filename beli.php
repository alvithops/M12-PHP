<?php
// 1. Inisialisasi awal & Sinkronisasi Cookie
$barangPilih = isset($_COOKIE['keranjang']) ? $_COOKIE['keranjang'] : "0";

// 2. Logika Hapus Barang (Batal)
if (isset($_GET['id'])) {
    $idHapus = $_GET['id'];
    $identitas = explode(",", $barangPilih);
    
    // Cari dan buang ID yang dipilih
    if (($key = array_search($idHapus, $identitas)) !== false) {
        unset($identitas[$key]);
    }
    
    $barangPilih = count($identitas) > 0 ? implode(",", $identitas) : "0";
    setcookie('keranjang', $barangPilih, time() + 3600);
    
    // Redirect agar daftar belanja langsung terupdate tanpa lag
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 3. Inisialisasi Error Handling
$namacustErr = $emailErr = $notelpErr = $barangPilihErr = "";
$namacust = $email = $notelp = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namacust = htmlspecialchars($_POST['namacust']);
    if (empty($namacust)) $namacustErr = "Nama belum diisi";

    $email = htmlspecialchars($_POST['email']);
    if (empty($email)) $emailErr = "Email belum diisi";

    $notelp = htmlspecialchars($_POST['notelp']);
    if (empty($notelp)) $notelpErr = "No. Telepon belum diisi";

    // Validasi apakah keranjang kosong (hanya berisi "0" atau tidak ada cookie)
    if ($barangPilih == "0") {
        $barangPilihErr = "<br><small>Keranjang belanja kosong</small><br>";
    }

    // Jika semua valid, simpan data
    if (empty($namacustErr) && empty($emailErr) && empty($notelpErr) && empty($barangPilihErr)) {
        echo "<h3 style='color:green;'>Data pembeli $namacust siap disimpan ke database.</h3>";
        // Hapus cookie setelah checkout sukses
        setcookie('keranjang', '', time() - 3600);
        $barangPilih = "0"; 
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pembelian</title>
    <style>
        table, td, th { border: 1px solid gray; padding: 8px; }
        table { border-collapse: collapse; width: 100%; }
        .tengah { width: 75%; margin: auto; }
        small { color: red; }
    </style>
</head>
<body>
<div class="tengah">
    <h2>DATA PEMBELI BARANG</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        Nama:<br>
        <input type="text" name="namacust" value="<?php echo $namacust; ?>">
        <?php if ($namacustErr) echo "<br><small>$namacustErr</small>"; ?>
        
        <br><br>Email:<br>
        <input type="email" name="email" value="<?php echo $email; ?>">
        <?php if ($emailErr) echo "<br><small>$emailErr</small>"; ?>
        
        <br><br>No. Telepon:<br>
        <input type="text" name="notelp" value="<?php echo $notelp; ?>">
        <?php if ($notelpErr) echo "<br><small>$notelpErr</small>"; ?>
        
        <br><br><button type="submit">Simpan Pembelian</button>
    </form>

    <?php if ($barangPilihErr) echo $barangPilihErr; ?>
    <hr>

    <?php
    require_once 'barang.php';
    
    // Pastikan SQL tidak error jika kosong dengan filter ID > 0
    $sql = "SELECT * FROM barang WHERE id IN ($barangPilih) ORDER BY id DESC";
    $hasils = bacaBarang($sql);
    
    echo "<h2>KERANJANG BELANJA</h2>";
    if (count($hasils) > 0 && $barangPilih != "0") {
        echo "<table>
                <tr>
                    <th>Foto</th><th>Nama Barang</th><th>Harga</th><th>Stok</th><th>Operasi</th>
                </tr>";
        foreach ($hasils as $hasil) {
            echo "<tr>
                    <td><img src='gambar/{$hasil['foto']}' width='100'></td>
                    <td>{$hasil['nama']}</td>
                    <td>" . number_format($hasil['harga'], 0, ',', '.') . "</td>
                    <td>{$hasil['stok']}</td>
                    <td><a href='{$_SERVER['PHP_SELF']}?id={$hasil['id']}'>Batal</a></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Keranjang Anda kosong.</p>";
    }
    ?>
</div>
</body>
</html>