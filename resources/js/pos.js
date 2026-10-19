function initializePaymentPreview() {
    const paymentMethod = document.getElementById('paymentMethod');
    const paymentInput = document.getElementById('paymentAmount');
    const changeAmount = document.getElementById('changeAmount');

    if (!paymentMethod || !paymentInput || !changeAmount) {
        return;
    }

    const totalEl = document.getElementById('posTotalAmount');
    const totalAmount = Number((totalEl && totalEl.dataset.total) || 0);

    function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(value);
    }

    function updateChange() {
        const paymentValue = Number.parseInt((paymentInput.value || '').replace(/[^0-9]/g, ''), 10) || 0;
        const isCash = paymentMethod.value === 'cash';
        const change = isCash ? Math.max(paymentValue - totalAmount, 0) : 0;
        changeAmount.textContent = formatRupiah(change);
    }

    paymentInput.addEventListener('input', updateChange);
    paymentMethod.addEventListener('change', updateChange);
    updateChange();
}

function syncMobileCartBar() {
    const cartPanel = document.getElementById('cartPanel');
    const stickyBar = document.getElementById('mobileCartStickyBar');
    const badge = document.getElementById('mobileCartBadge');
    const totalEl = document.getElementById('mobileCartTotal');

    if (!cartPanel || !stickyBar) {
        return;
    }

    const count = parseInt(cartPanel.dataset.cartCount || '0', 10);
    const total = cartPanel.dataset.cartTotal || '0';

    if (badge) {
        badge.textContent = count;
    }
    if (totalEl) {
        totalEl.textContent = 'Rp ' + total;
    }

    if (count > 0) {
        stickyBar.classList.remove('hidden');
    } else {
        stickyBar.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    initializePaymentPreview();
    syncMobileCartBar();

    const checkoutSuccessModal = document.getElementById('checkoutSuccessModal');
    if (checkoutSuccessModal) {
        const closeCheckoutModal = function () {
            checkoutSuccessModal.remove();
        };

        const closeBtn = checkoutSuccessModal.querySelector('[data-close-checkout-modal]');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeCheckoutModal);
        }

        checkoutSuccessModal.addEventListener('click', function (event) {
            if (event.target === checkoutSuccessModal) {
                closeCheckoutModal();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeCheckoutModal();
            }
        });
    }

    // Use event delegation for all [data-cart-form] submissions
    document.addEventListener('submit', async function (event) {
        const form = event.target.closest('[data-cart-form]');
        if (!form) {
            return;
        }

        event.preventDefault();

        const button = form.querySelector('button[type="submit"]');
        if (button) {
            button.disabled = true;
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'text/html',
                },
            });

            if (!response.ok) {
                throw new Error('Gagal memperbarui keranjang.');
            }

            const html = await response.text();
            const documentFragment = new DOMParser().parseFromString(html, 'text/html');
            const updatedCart = documentFragment.querySelector('#cartPanel');

            if (!updatedCart) {
                throw new Error('Panel keranjang tidak ditemukan.');
            }

            const existingCart = document.getElementById('cartPanel');
            if (existingCart) {
                existingCart.outerHTML = updatedCart.outerHTML;
            }

            initializePaymentPreview();
            syncMobileCartBar();
        } catch (error) {
            window.location.reload();
        } finally {
            if (button) {
                button.disabled = false;
            }
        }
    });
});
