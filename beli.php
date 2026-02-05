<?php
/* ===============================
   KONEKSI DATABASE
   =============================== */
$koneksi = mysqli_connect("localhost", "root", "", "tokoabcdefghijklmnopqrstuvw");
if (!$koneksi) {
    die("Koneksi database gagal");
}

/* ===============================
   AMBIL KERANJANG DARI COOKIE
   =============================== */
$barangPilih = isset($_COOKIE['keranjang']) ? $_COOKIE['keranjang'] : "0";

/* ===============================
   LOGIKA HAPUS BARANG (GET)
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $idHapus = (int) $_GET['id'];
    $identitas = array_map('intval', explode(",", $barangPilih));

    if (($key = array_search($idHapus, $identitas, true)) !== false) {
        unset($identitas[$key]);
    }

    $identitas = array_filter($identitas);
    $barangPilih = count($identitas) ? implode(",", $identitas) : "0";

    setcookie("keranjang", $barangPilih, time() + 3600, "/");
    header("Location: beli.php");
    exit;
}

/* ===============================
   VALIDASI FORM (POST)
   =============================== */
$namacust = $email = $notelp = "";
$namacustErr = $emailErr = $notelpErr = $barangPilihErr = "";
$showForm = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namacust = trim($_POST['namacust']);
    $email = trim($_POST['email']);
    $notelp = trim($_POST['notelp']);

    if ($namacust === "")
        $namacustErr = "Nama wajib diisi";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $emailErr = "Email tidak valid";
    if ($notelp === "")
        $notelpErr = "No telepon wajib diisi";
    if ($barangPilih === "0")
        $barangPilihErr = "Keranjang masih kosong";

    if (!$namacustErr && !$emailErr && !$notelpErr && !$barangPilihErr) {
        echo "<div style='background:#d4edda;padding:15px;margin-bottom:10px'>
                <strong>Sukses!</strong> Pembelian atas nama <b>$namacust</b> berhasil disimpan.
              </div>";

        setcookie("keranjang", "", time() - 3600, "/");
        $barangPilih = "0";
        $showForm = false;
    }
}

/* ===============================
   AMANKAN ID UNTUK QUERY
   =============================== */
$ids = array_filter(array_map('intval', explode(",", $barangPilih)), fn($v) => $v > 0);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pembelian</title>
    <style>
        body {
            font-family: Arial
        }

        .container {
            width: 75%;
            margin: auto
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px
        }

        small {
            color: red
        }

        .btn {
            padding: 8px 15px;
            background: #28a745;
            color: white;
            border: none
        }

        .hapus {
            color: red;
            text-decoration: none
        }
    </style>
</head>

<body>

    <div class="container">

        <?php if ($showForm): ?>
            <h2>DATA PEMBELI</h2>
            <form method="post" action="beli.php">
                Nama<br>
                <input type="text" name="namacust" value="<?= htmlspecialchars($namacust) ?>"><br>
                <small><?= $namacustErr ?></small><br><br>

                Email<br>
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>"><br>
                <small><?= $emailErr ?></small><br><br>

                No Telp<br>
                <input type="text" name="notelp" value="<?= htmlspecialchars($notelp) ?>"><br>
                <small><?= $notelpErr ?></small><br><br>

                <small><?= $barangPilihErr ?></small><br>
                <button class="btn">Simpan Pembelian</button>
            </form>
        <?php endif; ?>

        <hr>

        <h2>KERANJANG BELANJA</h2>

        <?php
        if (count($ids) > 0) {
            $idList = implode(",", $ids);
            $query = mysqli_query($koneksi, "SELECT * FROM barang WHERE id IN ($idList)");

            echo "<table>
            <tr>
                <th>Foto</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>";

            while ($row = mysqli_fetch_assoc($query)) {
                echo "<tr>
                <td><img src='gambar/{$row['foto']}' width='70'></td>
                <td>{$row['nama']}</td>
                <td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                <td>{$row['stok']}</td>
                <td>
                    <a class='hapus' href='beli.php?id={$row['id']}'
                    onclick='return confirm(\"Hapus barang?\")'>Batal</a>
                </td>
              </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>keranjang kosong.</p>";
        }
        ?>

    </div>
</body>

</html>