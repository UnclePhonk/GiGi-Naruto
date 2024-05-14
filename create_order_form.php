<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "avoska");
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("SELECT product_id, product_name, description, price FROM products");
if ($stmt === false) {
    die('Ошибка подготовки запроса: ' . $mysqli->error);
}
$stmt->execute();
$products = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сформировать новый заказ</title>
    <script>
        function updatePrice() {
            var select = document.getElementById('product');
            var quantity = document.getElementById('quantity').value;
            var price = select.options[select.selectedIndex].getAttribute('data-price');
            var total = quantity * price;
            document.getElementById('totalPrice').textContent = 'Общая стоимость: ' + total.toFixed(2) + ' руб.';
        }
    </script>
</head>
<body>
    <h2>Сформировать новый заказ</h2>
    <form action="create_order.php" method="post">
        <label for="product">Товар:</label>
        <select name="product" id="product" onchange="updatePrice()">
            <?php while ($product = $products->fetch_assoc()): ?>
                <option value="<?php echo $product['product_id']; ?>" data-price="<?php echo $product['price']; ?>">
                    <?php echo htmlspecialchars($product['product_name']); ?> - <?php echo htmlspecialchars($product['description']); ?> - <?php echo $product['price']; ?> руб.
                </option>
            <?php endwhile; ?>
        </select><br>
        <label for="quantity">Количество:</label>
        <input type="number" name="quantity" id="quantity" value="1" min="1" required oninput="updatePrice()"><br>
        <div id="totalPrice"></div>
        <label for="delivery_address">Адрес доставки:</label>
        <input type="text" name="delivery_address" id="delivery_address" required><br>
        <input type="submit" value="Создать заказ">
    </form>
    <script>updatePrice(); // Инициализируем начальную цену при загрузке</script>
</body>
</html>

<?php
$mysqli->close();
?>
