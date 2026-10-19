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

    // Convert selected image file to Base64 Data URL for direct database storage (bypasses PHP temporary storage folder)
    function convertImageToBase64(file, maxWidth = 1200, maxHeight = 1200, quality = 0.85) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (e) => {
                const img = new Image();
                img.src = e.target.result;
                img.onload = () => {
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth || height > maxHeight) {
                        if (width > height) {
                            height = Math.round((height * maxWidth) / width);
                            width = maxWidth;
                        } else {
                            width = Math.round((width * maxHeight) / height);
                            height = maxHeight;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    const dataUrl = canvas.toDataURL('image/jpeg', quality);
                    resolve(dataUrl);
                };
                img.onerror = () => reject(new Error('Gagal memuat gambar'));
            };
            reader.onerror = () => reject(new Error('Gagal membaca file gambar'));
        });
    }

    // Image preview and direct-to-database Base64 handler
    const imageInput = document.getElementById('image');
    const imageBase64Input = document.getElementById('imageBase64');
    const productForm = document.getElementById('productForm');
    let isProcessingImage = false;

    imageInput?.addEventListener('change', async function (event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('imagePlaceholder');

        if (!file) {
            return;
        }

        if (!file.type.startsWith('image/')) {
            showProductToast('File yang dipilih harus berupa gambar (JPG, PNG, atau WEBP).', true);
            event.target.value = '';
            if (imageBase64Input) {
                imageBase64Input.value = '';
            }
            return;
        }

        if (preview) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        }
        placeholder?.classList.add('hidden');

        try {
            isProcessingImage = true;
            showProductToast('Mengoptimasi gambar untuk disimpan langsung ke database...');
            const base64Data = await convertImageToBase64(file);
            if (imageBase64Input) {
                imageBase64Input.value = base64Data;
            }
            if (preview) {
                preview.src = base64Data;
            }
            showProductToast('Foto siap disimpan langsung ke database!');
        } catch (err) {
            console.error('Error converting image to Base64:', err);
            showProductToast('Gagal memproses gambar. Silakan coba file lain.', true);
        } finally {
            isProcessingImage = false;
        }
    });

    productForm?.addEventListener('submit', function (e) {
        if (isProcessingImage) {
            e.preventDefault();
            showProductToast('Mohon tunggu sejenak, foto sedang disiapkan...', true);
        }
    });

    // Toast notification helper
    function showProductToast(message, isError = false) {
        let container = document.getElementById('productToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'productToastContainer';
            container.className = 'fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-center gap-3 rounded-2xl ${
            isError ? 'bg-rose-900/95 border-rose-700' : 'bg-slate-900/95 border-slate-700'
        } text-white px-5 py-3.5 shadow-2xl backdrop-blur-md border text-xs sm:text-sm font-medium transition-all duration-300 translate-y-3 opacity-0`;
        toast.innerHTML = `
            <span class="flex h-6 w-6 items-center justify-center rounded-full ${
                isError ? 'bg-rose-500/20 text-rose-300' : 'bg-emerald-500/20 text-emerald-400'
            } font-bold shrink-0">${isError ? '✕' : '✓'}</span>
            <span>${message}</span>
        `;

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-3', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });

        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    // AJAX Product Deletion without page refresh or jumping to top
    document.querySelectorAll('.delete-product-form').forEach((form) => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const productName = form.dataset.productName || 'produk ini';
            const isConfirmed = confirm(`Apakah Anda yakin ingin menghapus produk "${productName}"?`);
            if (!isConfirmed) {
                return;
            }

            const button = form.querySelector('button[type="submit"]');
            const originalContent = button ? button.innerHTML : '';
            if (button) {
                button.disabled = true;
                button.classList.add('opacity-50', 'cursor-not-allowed');
                button.innerHTML = `
                    <svg class="animate-spin h-3.5 w-3.5 text-rose-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Hapus...</span>
                `;
            }

            const row = form.closest('tr');
            const tokenInput = form.querySelector('input[name="_token"]');
            const csrfToken = tokenInput ? tokenInput.value : '';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(form),
                });

                if (response.ok) {
                    const data = await response.json();

                    if (row) {
                        row.style.transition = 'all 0.3s ease-out';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(-20px)';
                        row.style.backgroundColor = '#fef2f2';

                        setTimeout(() => {
                            const productStock = parseInt(row.dataset.stock || '0', 10);
                            row.remove();

                            // Update stat counters in real time
                            const statTotal = document.getElementById('statTotalProducts');
                            if (statTotal) {
                                const currentTotal = parseInt(statTotal.textContent.replace(/\D/g, ''), 10) || 0;
                                statTotal.textContent = Math.max(0, currentTotal - 1);
                            }

                            const statStock = document.getElementById('statTotalStock');
                            if (statStock) {
                                const currentStock = parseInt(statStock.textContent.replace(/\D/g, ''), 10) || 0;
                                const newStock = Math.max(0, currentStock - productStock);
                                statStock.textContent = new Intl.NumberFormat('id-ID').format(newStock);
                            }

                            if (productStock <= 5) {
                                const statLow = document.getElementById('statLowStock');
                                if (statLow) {
                                    const currentLow = parseInt(statLow.textContent.replace(/\D/g, ''), 10) || 0;
                                    statLow.textContent = Math.max(0, currentLow - 1);
                                }
                            }

                            // Check if table is now empty
                            const tbody = document.getElementById('productsTableBody');
                            const remainingRows = tbody ? tbody.querySelectorAll('tr:not(#emptyProductRow)') : [];
                            if (remainingRows.length === 0) {
                                const emptyRow = document.getElementById('emptyProductRow');
                                if (emptyRow) {
                                    emptyRow.classList.remove('hidden');
                                }
                            }
                        }, 300);
                    }

                    showProductToast(data.message || `Produk "${productName}" berhasil dihapus.`);
                } else {
                    if (button) {
                        button.disabled = false;
                        button.classList.remove('opacity-50', 'cursor-not-allowed');
                        button.innerHTML = originalContent;
                    }
                    showProductToast('Gagal menghapus produk. Silakan coba lagi.', true);
                }
            } catch (error) {
                console.error('Error deleting product via AJAX:', error);
                if (button) {
                    button.disabled = false;
                    button.classList.remove('opacity-50', 'cursor-not-allowed');
                    button.innerHTML = originalContent;
                }
                // Fallback to standard form submission
                form.submit();
            }
        });
    });

    // Preserve scroll position across page reloads (e.g. stock adjustments, edit)
    const scrollKey = 'products_page_scroll_y';
    window.addEventListener('beforeunload', function () {
        sessionStorage.setItem(scrollKey, window.scrollY.toString());
    });

    const savedScroll = sessionStorage.getItem(scrollKey);
    if (savedScroll !== null) {
        sessionStorage.removeItem(scrollKey);
        if (!window.location.hash) {
            window.scrollTo({
                top: parseInt(savedScroll, 10),
                behavior: 'instant',
            });
        }
    }
});
