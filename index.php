<?php
session_start();
include 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $db_username, $db_password);
        $stmt->fetch();

        // Untuk sementara menggunakan perbandingan biasa (plaintext)
        if ($password === $db_password) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $db_username;
            header("Location: menu_admin.php");
            exit();
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Username tidak ditemukan.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - MIAGO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-image: url('warung.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: rgba(246, 255, 209, 0.95);
            padding: 30px 35px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgb(0 0 0 / 0.15);
            max-width: 360px;
            width: 100%;
            text-align: center;
        }
        .judul-miago {
            color: #00ff15;
            font-weight: 700;
            margin-bottom: 25px;
            font-size: 1.9rem;
            /* text-shadow: 0 0 6px #00ff15; */
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 1rem;
            box-shadow: inset 0 1px 3px rgb(0 0 0 / 0.1);
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 6px #28a745;
            outline: none;
        }
        input[type="submit"] {
            background-color: #28a745;
            border: none;
            padding: 12px 0;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 15px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .error {
            color: #d63333;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .back-btn a {
            display: inline-block;
            margin-top: 20px;
            color: #fff;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .back-btn a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h1 class="judul-miago">MIAGO SUPER HOT & BAKSO Pak Tri</h1>

    <h2 class="mb-4">Login</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="text" name="username" class="form-control" placeholder="Username" required autofocus />
        <input type="password" name="password" class="form-control mt-3" placeholder="Password" required />
        <input type="submit" value="Login" />
    </form>

    <div class="back-btn">
        <a href="menu.html">← Kembali ke Menu</a>
    </div>
</div>

</body>
</html>
