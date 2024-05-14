<?php
session_start();
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "avoska");
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

if (isset($_GET['order_id']) && isset($_GET['status'])) {
    $order_id = $mysqli->real_escape_string($_GET['order_id']);
    $status = $mysqli->real_escape_string($_GET['status']);

    // Обновляем статус заказа
    $query = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();
}

header('Location: admin_panel.php');
exit();
?>
