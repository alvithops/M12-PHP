<?php
$barangPilih = 0;
if(isset($_COOKIE['keranjang'])){
$barangPilih = $_COOKIE['keranjang'];
}
if(isset($_GET['id'])){
$id = $_GET['id'];
$barangPilih = str_replace((",".$id),"",$barangPilih);
setcookie('keranjang', $barangPilih, time()+3600);
}
$namacustErr = "";
$emailErr = "";
$notelpErr = "";
$barangPilihErr = "";
$namacust = "";
$email = "";
7
$notelp = "";
$request_method = strtoupper($_SERVER['REQUEST_METHOD']);
// Jika diklik tombol Simpan
if ($request_method === 'POST'){
$namacust = htmlspecialchars($_POST['namacust']);
if(empty($namacust)){
$namacustErr = "Nama belum diisi";
}
$email = htmlspecialchars($_POST['email']);
if(empty($email)){
$emailErr = "Email belum diisi";
}
$notelp = htmlspecialchars($_POST['notelp']);
if(empty($notelp)){
$notelpErr = "No. Telepon belum diisi";
}
$tanggal = date("Y-m-d");
if(!isset($_COOKIE['keranjang'])){
$barangPilihErr = "<br><small>Keranjang belanja
kosong</small><br>";
}
// jika tidak ada error data siap disimpan
if(empty($namacustErr) && empty($emailErr)
&& empty($notelpErr) && empty($barangPilihErr)){
echo "<h3>Data siap disimpan.</h3>";
setcookie('keranjang',$barangPilih,time()-3600);
}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Pembelian</title>
<style>
table, td, th {
border: 1px solid gray;
}
table {
border-collapse: collapse;
}
8
.tengah{
width: 75%;
margin: auto;
}
small{
color: red;
}
</style>
</head>
<body>
<div class="tengah">
<h2>DATA PEMBELI BARANG</h2>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])
?>" method="post">
<label>
Nama:<br>
<input type="text" name="namacust" value="<?php echo
$namacust;?>"><br>
</label>
<?php if(!empty($namacustErr)) echo
"<small>$namacustErr</small><br>"; ?>
<label>
<br>Email:<br>
<input type="email" name="email" value="<?php echo
$email;?>"><br>
</label>
<?php if(!empty($emailErr)) echo "<small>$emailErr</small><br>";
?>
<label>
<br>No. Telepon:<br>
<input type="text" name="notelp" value="<?php echo
$notelp;?>"><br>
</label>
<?php if(!empty($notelpErr)) echo
"<small>$notelpErr</small><br>"; ?>
<br><button type="submit">Simpan</button>
</form>
<?php
if(!empty($barangPilihErr)){
echo $barangPilihErr;
}
?>
<?php
// tampilkan keranjang belanja
require_once 'barang.php';
9
$sql = "select * from barang where id in (".$barangPilih.")
order by id desc";
echo $sql."<br>"; // cek
echo "<hr>";
$hasils = bacaBarang($sql);
echo "<h2>KERANJANG BELANJA</h2>";
if(count($hasils) > 0){
echo "<table>";
echo "<tr>
<th>Foto</th>
<th>Nama Barang</th>
<th>Harga</th>
<th>Stok</th>
<th>Operasi</th>
</tr>";
foreach($hasils as $hasil){
echo "<tr>";
echo "<td><img src='gambar/{$hasil['foto']}'
width='100'></td>";
echo "<td>{$hasil['nama']}</td>";
echo "<td>{$hasil['harga']}</td>";
echo "<td>{$hasil['stok']}</td>";
echo "<td><a
href='$_SERVER[PHP_SELF]?id={$hasil['id']}'>Batal</a></td>";
echo "</tr>\n";
}
echo "</table>";
}
?>
</div>
</body>
</html>