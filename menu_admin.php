<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
include 'config.php';

// Tambah menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama = $_POST['nama_menu'] ?? $_POST['nama'];
    $harga = $_POST['harga'];
    $gambar = $_POST['gambar'];
    $stok = $_POST['stok'];
    $kategori = $_POST['kategori'];

    $result = mysqli_query($conn, "INSERT INTO menu (nama_menu, harga, gambar, stok, kategori) 
        VALUES ('$nama', $harga, '$gambar', $stok, '$kategori')");

    if ($result) {
        header("Location: menu_admin.php");
        exit();
    } else {
        echo "Gagal menambahkan menu: " . mysqli_error($conn);
    }
}

// Update menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama_menu = $_POST['nama'];
    $harga = $_POST['harga'];
    $gambar = $_POST['gambar'];
    $stok = $_POST['stok'];
    $kategori = $_POST['kategori'];

    mysqli_query($conn, "UPDATE menu SET nama_menu='$nama_menu', harga=$harga, gambar='$gambar', stok=$stok, kategori='$kategori' WHERE id=$id");
    header("Location: menu_admin.php");
    exit();
}

// Hapus menu
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM menu WHERE id=$id");
    header("Location: menu_admin.php");
    exit();
}

$menus = mysqli_query($conn, "SELECT * FROM menu");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - MIAGO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color:rgb(0, 199, 80);
            padding-top: 50px;
            font-family: Arial;
        }
        .dashboard-box {
            max-width: 950px;
            margin: auto;
            background: #c1ff72;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px #ccc;
        }
        .btn {
            margin: 10px 5px;
        }
        table {
            width: 100%;
            margin-top: 20px;
        }
        input[type="text"], input[type="number"], select {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="dashboard-box">
    <h2>Selamat Datang, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Anda berhasil login ke MIAGO Dashboard.</p>

    <form method="POST" class="mb-3">
        <input type="hidden" name="id" id="id">
        <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama Menu" required>
        <input type="number" name="harga" id="harga" class="form-control" placeholder="Harga" required>
        <input type="text" name="gambar" id="gambar" class="form-control" placeholder="Nama File Gambar (contoh: miago.png)" required>
        <input type="number" name="stok" id="stok" class="form-control" placeholder="Stok" min="0" required>
        <select name="kategori" id="kategori" class="form-control" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="Makanan">Makanan</option>
            <option value="Minuman">Minuman</option>
        </select>
        <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
        <button type="submit" name="update" class="btn btn-warning">Update</button>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Nama</th>
                <th>Harga</th>
                <th>Gambar</th>
                <th>Stok</th>
                <th>Kategori</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($menus)): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama_menu']) ?></td>
                <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                <td><?= htmlspecialchars($row['gambar']) ?></td>
                <td><?= (int)$row['stok'] ?></td>
                <td><?= htmlspecialchars($row['kategori']) ?></td>
                <td>
                    <button class="btn btn-sm btn-info" onclick='editMenu(<?= json_encode($row) ?>)'>Edit</button>
                    <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus menu ini?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="menu.html" class="btn btn-primary">Kembali ke Menu</a>
    <a href="total_penghasilan.php" class="btn btn-success">Total Penghasilan</a>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>

<script>
function editMenu(menu) {
    document.getElementById('id').value = menu.id;
    document.getElementById('nama').value = menu.nama_menu;
    document.getElementById('harga').value = menu.harga;
    document.getElementById('gambar').value = menu.gambar;
    document.getElementById('stok').value = menu.stok;
    document.getElementById('kategori').value = menu.kategori;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

</body>
</html>
