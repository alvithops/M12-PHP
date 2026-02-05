<?php
// 1. Ambil data keranjang dari cookie
$barangPilih = isset($_COOKIE['keranjang']) ? $_COOKIE['keranjang'] : "0";

// 2. LOGIKA BATAL (Hanya jalan jika ada $_GET['id'] dan BUKAN sedang POST simpan)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $idHapus = $_GET['id'];
    $identitas = explode(",", $barangPilih);

    if (($key = array_search($idHapus, $identitas)) !== false) {
        unset($identitas[$key]);
    }

    $barangPilih = count($identitas) > 0 ? implode(",", $identitas) : "0";
    setcookie('keranjang', $barangPilih, time() + 3600);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 3. LOGIKA SIMPAN (POST)
$namacustErr = $emailErr = $notelpErr = $barangPilihErr = "";
$namacust = $email = $notelp = "";
$showTable = true; // Flag untuk mengontrol tampilan tabel

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namacust = htmlspecialchars($_POST['namacust']);
    $email = htmlspecialchars($_POST['email']);
    $notelp = htmlspecialchars($_POST['notelp']);

    if (empty($namacust))
        $namacustErr = "Nama belum diisi";
    if (empty($email))
        $emailErr = "Email belum diisi";
    if (empty($notelp))
        $notelpErr = "No. Telepon belum diisi";

    // Validasi keranjang
    if ($barangPilih == "0" || empty($barangPilih)) {
        $barangPilihErr = "<br><small>Keranjang belanja kosong!</small><br>";
    }

    if (empty($namacustErr) && empty($emailErr) && empty($notelpErr) && empty($barangPilihErr)) {
        echo "<div style='background:#d4edda; color:#155724; padding:15px; border-radius:5px;'>
                <h3>Sukses! Data pembeli <strong>$namacust</strong> telah direkam.</h3>
              </div>";

        // Hapus cookie di browser
        setcookie('keranjang', '', time() - 3600);
        // Reset variabel agar tabel di bawah ikut hilang
        $barangPilih = "0";
        $showTable = false;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Pembelian</title>
    <style>
        table,
        td,
        th {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        .tengah {
            width: 75%;
            margin: 20px auto;
            font-family: Arial, sans-serif;
        }

        small {
            color: red;
            font-style: italic;
        }

        .form-group {
            margin-bottom: 15px;
        }

        button {
            padding: 10px 20px;
            cursor: pointer;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="tengah">
        <?php if ($showTable): ?>
            <h2>DATA PEMBELI BARANG</h2>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="form-group">
                    Nama:<br>
                    <input type="text" name="namacust" value="<?php echo $namacust; ?>">
                    <?php if ($namacustErr)
                        echo "<br><small>$namacustErr</small>"; ?>
                </div>

                <div class="form-group">
                    Email:<br>
                    <input type="email" name="email" value="<?php echo $email; ?>">
                    <?php if ($emailErr)
                        echo "<br><small>$emailErr</small>"; ?>
                </div>

                <div class="form-group">
                    No. Telepon:<br>
                    <input type="text" name="notelp" value="<?php echo $notelp; ?>">
                    <?php if ($notelpErr)
                        echo "<br><small>$notelpErr</small>"; ?>
                </div>

                <button type="submit">Konfirmasi Pembelian</button>
            </form>
        <?php endif; ?>

        <?php if ($barangPilihErr)
            echo $barangPilihErr; ?>
        <hr>

        <?php
        require_once 'barang.php';

        $sql = "SELECT * FROM barang WHERE id IN ($barangPilih) ORDER BY id DESC";
        $hasils = ($barangPilih != "0") ? bacaBarang($sql) : [];

        echo "<h2>KERANJANG BELANJA</h2>";
        if (count($hasils) > 0) {
            echo "<table>
                <tr style='background:#f4f4f4'>
                    <th>Foto</th><th>Nama Barang</th><th>Harga</th><th>Stok</th><th>Operasi</th>
                </tr>";
            foreach ($hasils as $hasil) {
                echo "<tr>
                    <td><img src='gambar/{$hasil['foto']}' width='80'></td>
                    <td>{$hasil['nama']}</td>
                    <td>Rp " . number_format($hasil['harga'], 0, ',', '.') . "</td>
                    <td>{$hasil['stok']}</td>
                    <td><a href='{$_SERVER['PHP_SELF']}?id={$hasil['id']}' onclick='return confirm(\"Batalkan barang ini?\")'>Batal</a></td>
                  </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Tidak ada barang di keranjang. <a href='daftar_barang.php'>Kembali Belanja</a></p>";
        }
        ?>
    </div>
</body>

</html>