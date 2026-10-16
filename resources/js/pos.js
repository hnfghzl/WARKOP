function initializePaymentPreview() {
    const paymentMethod = document.getElementById('paymentMethod');
    const paymentInput = document.getElementById('paymentAmount');
    const changeAmount = document.getElementById('changeAmount');

    if (!paymentMethod || !paymentInput || !changeAmount) {
        return;
    }

    const totalAmount = Number(document.getElementById('posTotalAmount').dataset.total || 0);

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

document.addEventListener('DOMContentLoaded', function () {
    initializePaymentPreview();

    const checkoutSuccessModal = document.getElementById('checkoutSuccessModal');
    if (checkoutSuccessModal) {
        const closeCheckoutModal = function () {
            checkoutSuccessModal.remove();
        };

        checkoutSuccessModal.querySelector('[data-close-checkout-modal]').addEventListener('click', closeCheckoutModal);
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

    document.querySelectorAll('[data-cart-form]').forEach(function (form) {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const button = form.querySelector('button[type="submit"]');
            button.disabled = true;

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

                document.getElementById('cartPanel').outerHTML = updatedCart.outerHTML;
                initializePaymentPreview();
            } catch (error) {
                window.location.reload();
            }
        });
    });
});
