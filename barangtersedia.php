<?php
// 1. Inisialisasi awal
$barangPilih = "";
if (isset($_COOKIE['keranjang'])) {
    $barangPilih = $_COOKIE['keranjang'];
}

// 2. Logika penambahan barang (DIPERBAIKI)
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($barangPilih == "") {
        $barangPilih = $id;
    } else {
        $ids = explode(",", $barangPilih);
        if (!in_array($id, $ids)) {
            $barangPilih = $barangPilih . "," . $id;
        }
    }

    // Kirim ke browser user
    setcookie('keranjang', $barangPilih, time() + 3600);

    /* TROUBLE FIX: 
       Setelah setcookie, kita harus redirect atau memastikan 
       variabel $barangPilih yang digunakan di SQL sudah update.
       Cara paling bersih adalah redirect agar header cookie terbaca sempurna.
    */
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 3. Menyiapkan filter SQL
$sqlFilter = ($barangPilih == "") ? "0" : $barangPilih;
?>