<?php
session_start();
include 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ambil data JSON dari request POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['cart']) || !isset($data['paymentMethod']) || !isset($data['total'])) {
  echo json_encode(['success' => false, 'message' => 'Data pesanan tidak valid']);
  exit;
}

$cart = $data['cart'];
$paymentMethod = $data['paymentMethod'];
$total = $data['total'];

// Simpan ke tabel orders
$stmt = $conn->prepare("INSERT INTO orders (order_time, total) VALUES (NOW(), ?)");
$stmt->bind_param("i", $total);

if ($stmt->execute()) {
  $order_id = $stmt->insert_id;

  // Simpan detail ke order_items
  $stmt_detail = $conn->prepare("INSERT INTO order_items (order_id, name, quantity, price) VALUES (?, ?, ?, ?)");
  
  foreach ($cart as $item) {
    $name = $item['name'];
    $quantity = $item['qty']; // gunakan 'qty' sesuai JavaScript
    $price = $item['price'];

    $stmt_detail->bind_param("isii", $order_id, $name, $quantity, $price);
    $stmt_detail->execute();
  }
  $stmt_detail->close();

  echo json_encode(['success' => true, 'order_id' => $order_id]);
} else {
  echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pesanan']);
}

$stmt->close();
$conn->close();
exit;
?>
