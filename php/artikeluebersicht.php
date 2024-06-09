<?php
session_start();
include_once 'include/db_connection.php';

// Produkte aus der Datenbank abrufen
$stmt = $link->prepare("SELECT * FROM products");
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$stmt->close();
$link->close();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikelübersicht</title>
    <?php include "include/headimport.php"; ?>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container">
    <h1 class="mt-5">Artikelübersicht</h1>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Produktbild">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p><?php echo htmlspecialchars($product['price']); ?>€</p>
                <?php if (isset($_SESSION['kunden_id'])): ?>
                    <form method="post" action="shopping_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                        <div class="quantity-wrapper">
                            <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                            <button type="submit" class="btn btn-primary">In den Warenkorb</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include "include/footimport.php"; ?>
</body>
</html>