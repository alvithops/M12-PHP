<?php
// 1. Ambil data dari cookie, bersihkan dari spasi/karakter aneh
$barangPilih = isset($_COOKIE['keranjang']) ? trim($_COOKIE['keranjang'], ', ') : "";

if (isset($_GET['id'])) {
    $idHapus = $_GET['id'];

    // Pecah jadi array dan buang elemen kosong
    $identitas = array_filter(explode(",", $barangPilih));

    // Cari dan hapus ID target
    if (($key = array_search($idHapus, $identitas)) !== false) {
        unset($identitas[$key]);
    }

    // Gabungkan kembali
    $barangPilih = implode(",", $identitas);

    // Jika benar-benar kosong, hapus cookie sekalian agar bersih
    if (empty($barangPilih)) {
        setcookie('keranjang', '', time() - 3600); // Expire cookie
    } else {
        setcookie('keranjang', $barangPilih, time() + 3600);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 2. Filter SQL Final
// Pastikan hanya menggunakan ID yang valid (bukan 0 atau kosong)
$sqlFilter = (!empty($barangPilih) && $barangPilih !== "0") ? $barangPilih : "NULL";
?>