document.addEventListener('DOMContentLoaded', () => {
    const updateCart = () => {
        const usePoints = document.getElementById('use-points-checkbox').checked;
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ use_points: usePoints })
        })
            .then(response => response.json())
            .then(data => {
                if (typeof data.total === 'number') {
                    document.getElementById('total-price').innerText = data.total.toFixed(2) + '€';
                    document.getElementById('total-discount').innerText = data.total_discount;
                    document.getElementById('points-discount').innerText = data.points_discount;
                }
                data.cart_items.forEach(item => {
                    const productId = item.id;
                    document.querySelector(`.quantity-display[data-product-id="${productId}"]`).innerText = item.quantity;
                    document.querySelector(`.product-total[data-product-id="${productId}"]`).innerText = item.product_total.toFixed(2) + '€';
                    document.querySelector(`.product-discount[data-product-id="${productId}"]`).innerText = item.discount;
                });
            })
            .catch(error => console.error('Error:', error));
    };

    const applyPoints = () => {
        const usePoints = document.getElementById('use-points-checkbox').checked;
        fetch('apply_points.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ use_points: usePoints })
        })
            .then(response => response.json())
            .then(data => {
                if (typeof data.total === 'number') {
                    document.getElementById('total-price').innerText = data.total.toFixed(2) + '€';
                    document.getElementById('points-discount').innerText = data.points_discount;
                }
            })
            .catch(error => console.error('Error:', error));
    };

    const updateQuantity = (productId, quantity) => {
        const usePoints = document.getElementById('use-points-checkbox').checked;
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ product_id: productId, quantity: quantity, use_points: usePoints })
        })
            .then(response => response.json())
            .then(data => {
                if (typeof data.total === 'number') {
                    document.getElementById('total-price').innerText = data.total.toFixed(2) + '€';
                    document.getElementById('total-discount').innerText = data.total_discount;
                    document.getElementById('points-discount').innerText = data.points_discount;
                }
                data.cart_items.forEach(item => {
                    const productId = item.id;
                    document.querySelector(`.quantity-display[data-product-id="${productId}"]`).innerText = item.quantity;
                    document.querySelector(`.product-total[data-product-id="${productId}"]`).innerText = item.product_total.toFixed(2) + '€';
                    document.querySelector(`.product-discount[data-product-id="${productId}"]`).innerText = item.discount;
                });
            })
            .catch(error => console.error('Error:', error));
    };

    const removeFromCart = (productId) => {
        const usePoints = document.getElementById('use-points-checkbox').checked;
        fetch('remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ product_id: productId, use_points: usePoints })
        })
            .then(response => response.json())
            .then(data => {
                if (typeof data.total === 'number') {
                    document.getElementById('total-price').innerText = data.total.toFixed(2) + '€';
                    document.getElementById('total-discount').innerText = data.total_discount;
                    document.getElementById('points-discount').innerText = data.points_discount;
                }
                document.querySelector(`tr[data-product-id="${productId}"]`).remove();
            })
            .catch(error => console.error('Error:', error));
    };

    document.querySelectorAll('.quantity-button').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.productId;
            const display = document.querySelector(`.quantity-display[data-product-id="${productId}"]`);
            let quantity = parseInt(display.innerText);

            if (button.classList.contains('increase')) {
                quantity += 1;
            } else if (button.classList.contains('decrease')) {
                quantity -= 1;
                if (quantity < 1) {
                    quantity = 0;
                }
            }

            updateQuantity(productId, quantity);
        });
    });

    document.querySelectorAll('.remove-button').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.productId;
            removeFromCart(productId);
        });
    });

    document.getElementById('use-points-checkbox').addEventListener('change', () => {
        applyPoints();
        updateCart();
    });

    // Initial cart update
    updateCart();
});
