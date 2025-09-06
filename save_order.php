<?php
require 'config.php';
header('Content-Type: application/json');

// Ambil data JSON dari request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['cart']) || !isset($data['paymentMethod']) || !isset($data['total'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak lengkap',
        'received' => $data
    ]);
    exit;
}

$cart = $data['cart'];
$paymentMethod = $data['paymentMethod'];
$total = $data['total'];
$tanggal = date('Y-m-d H:i:s');

// Simpan ke database
try {
    // Simpan ke tabel pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan (tanggal, metode_pembayaran, total) VALUES (?, ?, ?)");
    $stmt->execute([$tanggal, $paymentMethod, $total]);
    $orderId = $conn->lastInsertId();

    // Simpan ke tabel detail pesanan
    $stmtItem = $conn->prepare("INSERT INTO pesanan_detail (id_pesanan, nama_menu, jumlah, harga) VALUES (?, ?, ?, ?)");

    foreach ($cart as $item) {
        $stmtItem->execute([$orderId, $item['name'], $item['qty'], $item['price']]);

        // Update stok menu - sesuaikan dengan kolom `nama_menu`
        $stmtStok = $conn->prepare("UPDATE menu SET stok = stok - ? WHERE nama_menu = ?");
        $stmtStok->execute([$item['qty'], $item['name']]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Pesanan berhasil disimpan']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
