<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
include 'config.php';

// ✅ Tangani permintaan POST dari menu.html (JavaScript)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || !isset($data['cart']) || !isset($data['total']) || !is_array($data['cart'])) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
        exit;
    }

    $cart = $data['cart'];
    $total = (int)$data['total'];
    $metode = $data['metode'] ?? 'Tunai';
    $waktu = date("Y-m-d H:i:s");

    // Simpan ke tabel orders
    $queryOrder = "INSERT INTO orders (total, metode, waktu) VALUES ($total, '$metode', '$waktu')";
    if (mysqli_query($conn, $queryOrder)) {
        $order_id = mysqli_insert_id($conn);

        foreach ($cart as $item) {
            $name = mysqli_real_escape_string($conn, $item['name']);
            $qty = (int)$item['qty'];
            $price = (int)$item['price'];

            // Simpan item pesanan ke order_items
            mysqli_query($conn, "INSERT INTO order_items (order_id, name, price, quantity, created_at)
                                 VALUES ($order_id, '$name', $price, $qty, '$waktu')");

            // Kurangi stok menu
            mysqli_query($conn, "UPDATE menu SET stok = stok - $qty WHERE nama_menu = '$name'");
        }

        echo json_encode(["status" => "success", "message" => "Pesanan berhasil disimpan"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan pesanan"]);
    }
    exit;
}

// ✅ Validasi login untuk akses dashboard
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Admin';

// ✅ Ambil total penghasilan dalam 24 jam terakhir
$query = "SELECT SUM(price * quantity) AS total 
          FROM order_items 
          WHERE created_at >= NOW() - INTERVAL 1 DAY";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$total_penghasilan = $data['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Penghasilan - MIAGO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-container {
            max-width: 800px;
            margin: 80px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .dashboard-header {
            margin-bottom: 30px;
        }
        .total-box {
            background-color: #e6f4ea;
            border-left: 6px solid #28a745;
            padding: 20px;
            border-radius: 12px;
        }
        .total-box h3 {
            font-size: 2rem;
            margin: 0;
        }
        .total-box i {
            font-size: 2rem;
            color: #28a745;
            margin-right: 10px;
        }
        .btn-group {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="dashboard-header text-center">
        <h2>Dashboard Penghasilan</h2>
        <p>Selamat datang, <strong><?= htmlspecialchars($username) ?></strong></p>
    </div>

    <div class="total-box d-flex align-items-center">
        <i class="fas fa-wallet"></i>
        <div>
            <p class="mb-1">Total Penghasilan (24 jam terakhir)</p>
            <h3 class="text-success">Rp<?= number_format($total_penghasilan, 0, ',', '.') ?></h3>
        </div>
    </div>

    <div class="btn-group d-flex justify-content-center">
        <a href="menu_admin.php" class="btn btn-outline-primary mt-4 me-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
        <a href="logout.php" class="btn btn-outline-danger mt-4">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

</body>
</html>
