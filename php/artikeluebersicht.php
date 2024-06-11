<?php
session_start();
include_once 'include/db_connection.php';

// Überprüfen, ob ein Suchbegriff eingegeben wurde
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']);
}

// Produkte aus der Datenbank abrufen
if ($searchTerm) {
    $stmt = $link->prepare("SELECT * FROM products WHERE name LIKE ?");
    $searchTermWithWildcards = "%$searchTerm%";
    $stmt->bind_param("s", $searchTermWithWildcards);
} else {
    $stmt = $link->prepare("SELECT * FROM products");
}

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
    <?php include '../php/include/headimport.php'; ?>
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container">
    <h1 class="mt-5">Artikelübersicht</h1>

    <!-- Suchformular -->
    <form id="search-form" method="get" action="artikeluebersicht.php" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" id="search-input" class="form-control" placeholder="Produkte suchen..." value="<?php echo htmlspecialchars($searchTerm); ?>">
        </div>
    </form>

    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="products.php?id=<?php echo htmlspecialchars($product['id']); ?>">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Produktbild">
                    </a>
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
        <?php else: ?>
            <p>Keine Produkte gefunden.</p>
        <?php endif; ?>
    </div>
</div>
<?php include "include/footimport.php"; ?>

<script>
    let searchTimeout;
    document.getElementById('search-input').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            document.getElementById('search-form').submit();
        }, 500); // Adjust the delay as needed (500 milliseconds in this example)
    });
</script>
</body>
</html>
