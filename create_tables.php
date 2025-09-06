<?php
include 'config.php';

$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_menu VARCHAR(100) NOT NULL,
    kategori ENUM('makanan', 'minuman') NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    stok INT DEFAULT 0,
    status ENUM('tersedia', 'habis') DEFAULT 'tersedia'
);


CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    total INT
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    name VARCHAR(255),
    price INT,
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

CREATE TABLE IF NOT EXISTS dapur_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT,
    menu_id INT,
    dapur_id INT,
    waktu_mulai DATETIME,
    waktu_selesai DATETIME,
    status ENUM('antri', 'diproses', 'selesai') DEFAULT 'antri',
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE,
    FOREIGN KEY (dapur_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS menu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_menu VARCHAR(100) NOT NULL,
  harga INT NOT NULL,
  gambar VARCHAR(100) NOT NULL
);
";

if ($conn->multi_query($sql) === TRUE) {
    echo "Tabel berhasil dibuat.";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
