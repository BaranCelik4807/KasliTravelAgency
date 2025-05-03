
<?php
require_once('db_connection.php');

session_start();

// Başlangıçta hata mesajı yok olarak ayarla
$error = '';

// Form submit edildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kullanıcı adı ve parolayı al
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kullanıcı adı ve parolayı veritabanından kontrol et
    $sql = "SELECT * FROM Admins WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Kullanıcı bulundu, parolayı kontrol et
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Parola doğru, giriş başarılı
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header("Location: x2xx12adminadmistor0748xza.php"); // Admin paneline yönlendir
            exit();
        } else {
            // Parola yanlış
            $error = "Hatalı kullanıcı adı veya parola";
        }
    } else {
        // Kullanıcı bulunamadı
        $error = "Hatalı kullanıcı adı veya parola";
    }
}

// Veritabanı bağlantısını kapat
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Giriş</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
            background-color: #f2f2f2;
        }
        .login-box {
            width: 160px;
            padding: 100px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .login-box h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .login-box label {
            font-weight: bold;
        }
        .textbox {
            margin-bottom: 20px;
        }
        .textbox label {
            display: block;
            margin-bottom: 5px;
        }
        .textbox input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-box button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }
        .login-box button:hover {
            background-color: #0056b3;
        }
        .login-box p {
            margin-top: 15px;
            text-align: center;
            color: red; /* Hata mesajı rengi */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>Admin Panel Giriş</h2>
            <?php if (!empty($error)) { echo "<p>$error</p>"; } ?>
            <form method="POST">
                <div class="textbox">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="textbox">
                    <label for="password">Parola</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Giriş Yap</button>
            </form>
        </div>
    </div>
</body>
</html>
