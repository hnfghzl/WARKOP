document.addEventListener('DOMContentLoaded', function () {
    const formContainer = document.getElementById('productFormContainer');
    const btnToggle = document.getElementById('btnToggleProductForm');
    const btnToggleText = document.getElementById('btnToggleProductFormText');
    const btnToggleIcon = document.getElementById('btnToggleProductFormIcon');
    const isEditing = formContainer?.dataset.isEditing === 'true';

    function openForm() {
        if (!formContainer) return;
        formContainer.classList.remove('hidden');

        if (btnToggleText && !isEditing) {
            btnToggleText.textContent = 'Tutup Form';
        }
        if (btnToggleIcon && !isEditing) {
            btnToggleIcon.classList.add('rotate-45');
        }

        formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });

        const nameInput = document.getElementById('name');
        if (nameInput) {
            setTimeout(() => nameInput.focus(), 250);
        }
    }

    function closeForm() {
        if (!formContainer) return;
        formContainer.classList.add('hidden');

        if (btnToggleText && !isEditing) {
            btnToggleText.textContent = 'Tambah Produk';
        }
        if (btnToggleIcon && !isEditing) {
            btnToggleIcon.classList.remove('rotate-45');
        }
    }

    function toggleForm() {
        if (!formContainer) return;
        if (formContainer.classList.contains('hidden')) {
            openForm();
        } else {
            closeForm();
        }
    }

    btnToggle?.addEventListener('click', toggleForm);

    document.querySelectorAll('[data-action="open-product-form"]').forEach((btn) => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            openForm();
        });
    });

    document.querySelectorAll('[data-action="close-product-form"]').forEach((btn) => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            closeForm();
        });
    });

    // Auto open if URL has #tambah or #tambah-produk
    if (window.location.hash === '#tambah' || window.location.hash === '#tambah-produk') {
        openForm();
    }

    // Image preview handler
    document.getElementById('image')?.addEventListener('change', function (event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('imagePlaceholder');

        if (!file || !preview) {
            return;
        }

        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        placeholder?.classList.add('hidden');
    });
});
