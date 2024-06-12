$(document).ready(function () {
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
                                $('#cart-badge').text(response.cart_count);
                                updateArticleTotal(articleId, quantity);
                                updateDiscounts();
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
        var price = parseFloat($('button[data-article-id="' + articleId + '"]').closest('tr').find('td:eq(1)').text().replace(' €', ''));
        var total = (price * quantity).toFixed(2);
        $('button[data-article-id="' + articleId + '"]').closest('tr').find('.article-total').text(total + ' €');
    }

    function updateDiscounts() {
        $('.quantity-controls').each(function () {
            var articleId = $(this).find('.minus-btn').data('article-id');
            var quantity = $(this).find('#quantity-display-' + articleId).text();

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
        var quantityDisplay = $('#quantity-display-' + articleId);
        var newQuantity = parseInt(quantityDisplay.text()) + 1;
        quantityDisplay.text(newQuantity);
        updateQuantity(articleId, newQuantity);
    });

    $('.minus-btn').on('click', function () {
        var articleId = $(this).data('article-id');
        var quantityDisplay = $('#quantity-display-' + articleId);
        var newQuantity = parseInt(quantityDisplay.text()) - 1;
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
                                quantityDisplay.closest('tr').remove();
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
                    quantityDisplay.text(1);
                    updateQuantity(articleId, 1);
                }
            });
        } else if (newQuantity > 0) {
            quantityDisplay.text(newQuantity);
            updateQuantity(articleId, newQuantity);
        }
    });

    $('.remove-from-cart').on('click', function () {
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
            }
        });
    });

    $('#apply-points').on('click', function () {
        var usePoints = $('#use_points').is(':checked') ? 1 : 0;

        $.ajax({
            type: 'POST',
            url: 'apply_points.php',
            data: {
                use_points: usePoints
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    updateCartTotal();
                    console.log('Punkterabatt angewendet.');
                } else {
                    console.error('Fehler beim Anwenden des Punkterabatts.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Fehler beim Anwenden des Punkterabatts:', error);
            }
        });
    });

    updateCartTotal();
    updateDiscounts();
});
