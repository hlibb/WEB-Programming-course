$(document).ready(function () {
    function updateQuantity(cartItemId, quantity) {
        console.log('Updating quantity for cart item ' + cartItemId + ' to ' + quantity);

        $.ajax({
            type: 'POST',
            url: 'check_availability.php',
            data: {
                cart_item_id: cartItemId,
                quantity: quantity
            },
            dataType: 'json',
            success: function (response) {
                if (response.success && response.available) {
                    $.ajax({
                        type: 'POST',
                        url: 'update_cart.php',
                        data: {
                            cart_item_id: cartItemId,
                            quantity: quantity
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                $('#cart-badge').text(response.cart_count);
                                updateArticleTotal(cartItemId, quantity);
                                updateCartTotal();
                                console.log('Quantity and total price updated.');
                            } else {
                                console.error('Error updating quantity and total price.');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error updating quantity and total price:', error);
                        }
                    });
                } else {
                    console.error('Requested quantity is not available.');
                    quantity = response.available_quantity;
                    $('#quantity-input-' + cartItemId).val(quantity);
                    $('#quantity-display-' + cartItemId).text(quantity);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error checking availability:', error);
            }
        });
    }

    function updateArticleTotal(cartItemId, quantity) {
        var price = parseFloat($('button[data-article-id="' + cartItemId + '"]').closest('tr').find('td:eq(1)').text().replace(' €', ''));
        var total = (price * quantity).toFixed(2);
        $('button[data-article-id="' + cartItemId + '"]').closest('tr').find('.article-total').text(total + ' €');
    }

    function updateCartTotal() {
        var total = 0;
        $('.article-total').each(function () {
            total += parseFloat($(this).text().replace(' €', ''));
        });
        $('#cart-total').text(total.toFixed(2) + ' €');
    }

    $('.plus-btn').on('click', function () {
        var cartItemId = $(this).data('article-id');
        var quantity = parseInt($(this).siblings('.quantity').text()) + 1;
        $(this).siblings('.quantity').text(quantity);
        updateQuantity(cartItemId, quantity);
    });

    $('.minus-btn').on('click', function () {
        var cartItemId = $(this).data('article-id');
        var quantity = parseInt($(this).siblings('.quantity').text()) - 1;
        if (quantity > 0) {
            $(this).siblings('.quantity').text(quantity);
            updateQuantity(cartItemId, quantity);
        } else {
            $.ajax({
                type: 'POST',
                url: 'remove_from_cart.php',
                data: {
                    cart_item_id: cartItemId
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#cart-badge').text(response.cart_count);
                        $('button[data-article-id="' + cartItemId + '"]').closest('tr').remove();
                        updateCartTotal();
                        console.log('Article successfully removed.');
                    } else {
                        console.error('Error removing article.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error removing article:', error);
                }
            });
        }
    });

    $('.remove-from-cart').on('click', function () {
        var cartItemId = $(this).data('article-id');
        $.ajax({
            type: 'POST',
            url: 'remove_from_cart.php',
            data: {
                cart_item_id: cartItemId
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#cart-badge').text(response.cart_count);
                    $(this).closest('tr').remove();
                    updateCartTotal();
                    console.log('Article successfully removed.');
                } else {
                    console.error('Error removing article.');
                }
            }.bind(this),
            error: function (xhr, status, error) {
                console.error('Error removing article:', error);
            }
        });
    });

    updateCartTotal();
});
