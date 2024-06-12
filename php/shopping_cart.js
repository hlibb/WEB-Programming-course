$(document).ready(function () {
    // Funktion zum Aktualisieren der Anzahl und des Gesamtpreises
    function updateQuantity(articleId, quantity) {
        console.log('Updating quantity for article ' + articleId + ' to ' + quantity);

        $.ajax({
            type: 'POST',
            url: 'check_availability.php',
            data: {
                article_id: articleId,
                quantity: quantity
            },
            dataType: 'json',
            success: function (response) {
                if (response.success && response.available) {
                    // Verfügbarkeit geprüft, aktualisiere die Menge
                    $.ajax({
                        type: 'POST',
                        url: 'update_cart.php',
                        data: {
                            article_id: articleId,
                            quantity: quantity
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                // Erfolgreich aktualisiert - aktualisiere das Warenkorbbadge
                                $('#cart-badge').text(response.cart_count);
                                updateArticleTotal(articleId, quantity);
                                updateDiscounts(); // Aktualisiere Rabatte nach Änderung
                                updateCartTotal();
                                console.log('Anzahl und Gesamtpreis aktualisiert.');
                            } else {
                                console.error('Fehler beim Aktualisieren der Anzahl und des Gesamtpreises.');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Fehler beim Aktualisieren der Anzahl und des Gesamtpreises:', error);
                        }
                    });
                } else {
                    console.error('Die angeforderte Menge ist nicht verfügbar.');
                    Swal.fire({
                        title: 'Menge überschreitet den verfügbaren Bestand',
                        text: 'Die gewünschte Menge ist nicht verfügbar. Die Menge wurde auf den maximal verfügbaren Bestand zurückgesetzt.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        quantity = response.available_quantity;
                        $('#quantity-input-' + articleId).val(quantity);
                        $('#quantity-display-' + articleId).text(quantity);
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('Fehler bei der Verfügbarkeitsprüfung:', error);
            }
        });
    }

    function updateArticleTotal(articleId, quantity) {
        var price = parseFloat($('button[data-article-id="' + articleId + '"]').closest('tr').find('td:eq(3)').text().replace(' €', ''));
        var total = (price * quantity).toFixed(2);
        $('button[data-article-id="' + articleId + '"]').closest('tr').find('.article-total').text(total + ' €');
    }

    function updateDiscounts() {
        $('.quantity').each(function () {
            var articleId = $(this).closest('tr').find('.remove-from-cart').data('article-id');
            var quantity = $(this).val();

            $.ajax({
                type: 'POST',
                url: 'check_discount.php',
                data: {
                    article_id: articleId,
                    quantity: quantity
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        var discount = response.discount;
                        var discountDisplay = discount > 0 ? discount.toFixed(2) + ' € Rabatt' : 'Kein Rabatt';
                        $('button[data-article-id="' + articleId + '"]').closest('tr').find('.article-discount').text(discountDisplay);
                    } else {
                        console.error('Fehler beim Abrufen des Rabatts.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Fehler beim Abrufen des Rabatts:', error);
                }
            });
        });
    }

    function updateCartTotal() {
        var total = 0;
        $('.article-total').each(function () {
            total += parseFloat($(this).text().replace(' €', ''));
        });
        $('#cart-total').text(total.toFixed(2) + ' €');
    }

    $('.plus-btn').on('click', function () {
        var articleId = $(this).data('article-id');
        var quantityInput = $(this).parent().find('.quantity');
        var newQuantity = parseInt(quantityInput.val()) + 1;
        quantityInput.val(newQuantity);
        updateQuantity(articleId, newQuantity);
    });

    $('.minus-btn').on('click', function () {
        var articleId = $(this).data('article-id');
        var quantityInput = $(this).parent().find('.quantity');
        var newQuantity = parseInt(quantityInput.val()) - 1;
        if (newQuantity == 0) {
            Swal.fire({
                title: 'Sind Sie sicher?',
                text: 'Möchten Sie diesen Artikel wirklich entfernen?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, entfernen',
                cancelButtonText: 'Nein, behalten'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: 'remove_from_cart.php',
                        data: {
                            article_id: articleId
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                $('#cart-badge').text(response.cart_count);
                                quantityInput.closest('tr').remove();
                                updateCartTotal();
                                console.log('Artikel erfolgreich entfernt.');
                            } else {
                                console.error('Fehler beim Entfernen des Artikels.');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Fehler beim Entfernen des Artikels:', error);
                        }
                    });
                } else {
                    quantityInput.val(1);
                    updateQuantity(articleId, 1);
                }
            });
        } else if (newQuantity > 0) {
            quantityInput.val(newQuantity);
            updateQuantity(articleId, newQuantity);
        }
    });

    $('.quantity').on('input', function () {
        var articleId = $(this).closest('tr').find('.remove-from-cart').data('article-id');
        var quantity = $(this).val();
        if (quantity == 0) {
            Swal.fire({
                title: 'Sind Sie sicher?',
                text: 'Möchten Sie diesen Artikel wirklich entfernen?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, entfernen',
                cancelButtonText: 'Nein, behalten'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: 'remove_from_cart.php',
                        data: {
                            article_id: articleId
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                $('#cart-badge').text(response.cart_count);
                                $(this).closest('tr').remove();
                                updateCartTotal();
                                console.log('Artikel erfolgreich entfernt.');
                            } else {
                                console.error('Fehler beim Entfernen des Artikels.');
                            }
                        }.bind(this),
                        error: function (xhr, status, error) {
                            console.error('Fehler beim Entfernen des Artikels:', error);
                        }
                    });
                } else {
                    $(this).val(1);
                    updateQuantity(articleId, 1);
                }
            });
        } else {
            updateQuantity(articleId, quantity);
        }
    });

    $('.remove-from-cart').on('click', function (event) {
        event.preventDefault();

        var articleId = $(this).data('article-id');
        var $this = $(this);

        Swal.fire({
            title: 'Sind Sie sicher?',
            text: 'Möchten Sie diesen Artikel wirklich entfernen?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, entfernen',
            cancelButtonText: 'Nein, behalten'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'remove_from_cart.php',
                    data: {
                        article_id: articleId
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('#cart-badge').text(response.cart_count);
                            $this.closest('tr').remove();
                            updateCartTotal();
                            console.log('Artikel erfolgreich entfernt.');
                        } else {
                            console.error('Fehler beim Entfernen des Artikels.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Fehler beim Entfernen des Artikels:', error);
                    }
                });
            } else {
                console.log('Entfernen des Artikels abgebrochen.');
            }
        });
    });

    updateCartTotal();
    updateDiscounts();
});
