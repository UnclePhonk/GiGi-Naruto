<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Подключение к базе данных
$mysqli = new mysqli("localhost", "root", "", "avoska");
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Получение данных из формы
$product_id = $_POST['product'];
$quantity = $_POST['quantity'];
$delivery_address = $_POST['delivery_address'];
$user_id = $_SESSION['id']; // Получение ID пользователя из сессии
$status = 'новый'; // Статус заказа по умолчанию

// Проверка на корректность введенных данных
if (is_numeric($quantity) && $quantity > 0) {
    // Подготовка SQL запроса для вставки данных
    $stmt = $mysqli->prepare("INSERT INTO orders (user_id, product_id, quantity, delivery_address, status) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die('Ошибка подготовки запроса: ' . $mysqli->error);
    }

    // Привязка переменных к параметрам запроса
    $stmt->bind_param("iiiss", $user_id, $product_id, $quantity, $delivery_address, $status);

    // Выполнение запроса
    if ($stmt->execute()) {
        echo "Заказ успешно создан.";
        // Перенаправление на страницу заказов
        header("Location: orders.php");
        exit();
    } else {
        echo "Ошибка при создании заказа: " . $stmt->error;
    }

    // Закрытие подготовленного запроса
    $stmt->close();
} else {
    echo "Количество должно быть положительным числом.";
}

// Закрытие соединения
$mysqli->close();
?>
