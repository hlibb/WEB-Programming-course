<?php
session_start();
include_once 'include/db_connection.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $link->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    $stmt->execute();

    $result = $stmt->get_result();

    // Überprüfen, ob Daten zurückgegeben wurden
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $productName = $row["name"];
        $productDescription = $row["description"];
        $productPrice = $row["price"];
        $productImage = $row["image_url"];
    } else {
        echo "0 results";
        exit();
    }

    $stmt->close();
} else {
    echo "Ungültige ID";
    exit();
}

$link->close();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Artikelseite</title>
    <?php include '../php/include/headimport.php' ?>
</head>
<body>
<?php include "include/navimport.php"; ?>
<div class="container">
    <div class="row mt-5">
        <div class="col-md-6">
            <img
                src="<?php echo htmlspecialchars($productImage); ?>"
                class="img-fluid"
                alt="Artikelbild"
            />
        </div>
        <div class="col-md-6">
            <h1 class="mb-4"><?php echo htmlspecialchars($productName); ?></h1>
            <p class="lead mb-4"><?php echo htmlspecialchars($productDescription); ?></p>
            <p><strong>Preis:</strong> <?php echo htmlspecialchars($productPrice); ?>€</p>
            <form method="post" action="shopping_cart.php">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($id); ?>">
                <label for="quantity">Menge:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="99" class="form-control mb-3">
                <button type="submit" class="btn btn-primary">In den Warenkorb legen</button>
            </form>
        </div>
    </div>
</div>
<?php include "include/footimport.php"; ?>
</body>
</html>
