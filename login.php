<?php
session_start(); // Начало сессии
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mysqli = new mysqli("localhost", "root", "", "avoska");

    // Проверка соединения
    if ($mysqli->connect_error) {
        die("Ошибка подключения: " . $mysqli->connect_error);
    }

    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Жестко заданные учетные данные администратора
    $adminUsername = 'sklad';
    $adminPassword = '123qwe'; // В реальных условиях пароль должен быть захеширован

    // Проверка на администратора
    if ($username === $adminUsername && $password === $adminPassword) {
        // Установка сессии для администратора
        $_SESSION['isAdmin'] = true;
        $_SESSION['username'] = $adminUsername;
        header("Location: admin_panel.php"); // Перенаправление на панель администратора
        exit;
    } else {
        // Подготовленный запрос для избежания SQL инъекций
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Проверка наличия пользователя в базе данных и проверка пароля
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Авторизация успешна, сохранение данных пользователя в сессии
                $_SESSION['id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['phone'] = $row['phone'];
                $_SESSION['email'] = $row['email'];
                header("Location: orders.php"); // Перенаправление на страницу товаров
                exit;
            } else {
                $errorMessage = "Неверный логин или пароль";
            }
        } else {
            $errorMessage = "Пользователь не найден";
        }
    }
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
</head>
<body>
    <h2>Авторизация</h2>
    <?php if (!empty($errorMessage)) echo "<p style='color: red;'>$errorMessage</p>"; ?>
    <form action="login.php" method="post">
        <a href='register.php'>Еще нет аккаунта?</a><br>
        <label for="username">Логин:</label><br>
        <input type="text" id="username" name="username" required><br>
        
        <label for="password">Пароль:</label><br>
        <input type="password" id="password" name="password" required><br>
        
        <input type="submit" value="Войти">
    </form>
</body>
</html>
