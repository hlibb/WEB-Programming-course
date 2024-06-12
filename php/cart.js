$(document).ready(function () {
    // Funktion zum Aktualisieren der Anzahl und des Gesamtpreises
    function updateQuantity(articleId, quantity) {
        $.ajax({
            type: 'POST',
            url: 'update_cart.php',
            data: {
                product_id: articleId,
                quantity: quantity
            },
            success: function (response) {
                // Erfolgreich aktualisiert - aktualisiere das Warenkorbbadge und den Gesamtpreis
                $('#cart-total').text(response.newTotal.toFixed(2) + ' €');
                $('#cart-badge').text(response.cartCount);
            },
            error: function (xhr, status, error) {
                console.error('Fehler beim Aktualisieren der Anzahl und des Gesamtpreises:', error);
            }
        });
    }

    // Plus-Button-Click-Event
    $('.plus-btn').on('click', function (e) {
        e.preventDefault(); // Standardaktion verhindern
        var articleId = $(this).data('article-id');
        var quantityInput = $(this).closest('tr').find('span');
        var newQuantity = parseInt(quantityInput.text()) + 1;
        quantityInput.text(newQuantity); // Anzahl in der Anzeige aktualisieren
        updateQuantity(articleId, newQuantity);
    });

    // Minus-Button-Click-Event
    $('.minus-btn').on('click', function (e) {
        e.preventDefault(); // Standardaktion verhindern
        var articleId = $(this).data('article-id');
        var quantityInput = $(this).closest('tr').find('span');
        var newQuantity = parseInt(quantityInput.text()) - 1;
        if (newQuantity > 0) {
            quantityInput.text(newQuantity); // Anzahl in der Anzeige aktualisieren
            updateQuantity(articleId, newQuantity);
        } else {
            // Artikel entfernen, wenn die Anzahl 0 erreicht
            $(this).closest('tr').remove();
            updateQuantity(articleId, 0);
        }
    });

    // Entfernen-Button-Click-Event
    $('.remove-from-cart').on('click', function (e) {
        e.preventDefault(); // Standardaktion verhindern
        var articleId = $(this).data('article-id');
        $.ajax({
            type: 'POST',
            url: 'remove_from_cart.php',
            data: {
                product_id: articleId
            },
            success: function () {
                // Erfolgreich entfernt - aktualisiere das Warenkorbbadge
                location.reload(); // Seite neu laden, um die Änderungen anzuzeigen
            },
            error: function (xhr, status, error) {
                console.error('Fehler beim Entfernen des Artikels:', error);
            }
        });
    });

    // Punkte verwenden-Checkbox-Event
    $('#use_points').on('change', function () {
        $.ajax({
            type: 'POST',
            url: 'apply_points.php',
            data: {
                use_points: $(this).is(':checked') ? 1 : 0
            },
            success: function (response) {
                if (response.success) {
                    location.reload(); // Seite neu laden, um die Änderungen anzuzeigen
                } else {
                    console.error('Fehler beim Anwenden der Punkte.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Fehler beim Anwenden der Punkte:', error);
            }
        });
    });
});
