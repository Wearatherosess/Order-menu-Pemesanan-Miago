<?php
header('Content-Type: application/json');

$koneksi = new mysqli("localhost", "root", "", "miago_warung");

if ($koneksi->connect_error) {
    echo json_encode(["error" => "Koneksi gagal"]);
    exit;
}

$query = "SELECT id, nama_menu, harga, stok, gambar, kategori FROM menu";
$result = $koneksi->query($query);

$menuList = [];

while ($row = $result->fetch_assoc()) {
    $menuList[] = [
        "id" => $row["id"],
        "nama_menu" => $row["nama_menu"],
        "harga" => (int) $row["harga"],
        "stok" => (int) $row["stok"],
        "gambar" => $row["gambar"],
        "kategori" => $row["kategori"]
    ];
}

echo json_encode($menuList);
?>
