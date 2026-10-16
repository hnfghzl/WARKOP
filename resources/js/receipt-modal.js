document.addEventListener('DOMContentLoaded', function () {
    const receiptModal = document.getElementById('receiptModal');

    if (!receiptModal) {
        return;
    }

    const closeReceiptModal = function () {
        receiptModal.classList.add('hidden');
        receiptModal.classList.remove('flex');
    };

    document.querySelectorAll('[data-open-receipt-modal]').forEach(function (button) {
        button.addEventListener('click', function () {
            receiptModal.classList.remove('hidden');
            receiptModal.classList.add('flex');
        });
    });

    receiptModal.querySelector('[data-close-receipt-modal]').addEventListener('click', closeReceiptModal);
    receiptModal.addEventListener('click', function (event) {
        if (event.target === receiptModal) {
            closeReceiptModal();
        }
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeReceiptModal();
        }
    });
});
