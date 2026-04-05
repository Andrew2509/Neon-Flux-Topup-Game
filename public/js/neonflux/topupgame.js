/* ==========================================================================
   NEON FLUX — Top-Up Game Detail Page Scripts
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    console.log('Top-up Game scripts active');

    const productRadios = document.querySelectorAll('input[name="product_code"]');
    const paymentRadios = document.querySelectorAll('input[name="payment"]');
    const userIdInput = document.getElementById('user_id_input');
    const zoneIdInput = document.getElementById('zone_id_input');
    const customerWhatsappInput = document.getElementById('customer_whatsapp_input');

    const summaryNominal = document.getElementById('summary-nominal');
    const summaryPayment = document.getElementById('summary-payment');
    const summaryUserId = document.getElementById('summary-userid');
    const summaryWhatsapp = document.getElementById('summary-whatsapp');
    const summaryTotal = document.getElementById('summary-total');

    function whatsappDigitsOk() {
        if (!customerWhatsappInput) return true;
        const d = (customerWhatsappInput.value || '').replace(/\D/g, '');
        return d.length >= 10 && d.length <= 15;
    }

    function updateSummary() {
        let selectedProduct = null;
        let selectedPayment = null;

        productRadios.forEach(r => {
            if (r.checked) selectedProduct = r;
        });

        paymentRadios.forEach(r => {
            if (r.checked) selectedPayment = r;
        });

        // Update Product info
        if (selectedProduct && summaryNominal) {
            summaryNominal.textContent = selectedProduct.dataset.name;
        }

        // Update Payment info
        if (selectedPayment && summaryPayment) {
            summaryPayment.textContent = selectedPayment.dataset.name;
        }

        // Update User ID info
        if (summaryUserId) {
            const userId = userIdInput ? userIdInput.value : '';
            const zoneId = zoneIdInput ? zoneIdInput.value : '';

            if (userId) {
                summaryUserId.textContent = zoneId ? `${userId} (${zoneId})` : userId;
            } else {
                summaryUserId.textContent = 'Belum Diisi';
            }
        }

        // Update Total Price
        if (selectedProduct && summaryTotal) {
            let basePrice = parseInt(selectedProduct.dataset.price.replace(/\./g, ''));
            let total = basePrice;

            if (selectedPayment) {
                const feeStr = selectedPayment.dataset.fee;
                if (feeStr.includes('%')) {
                    const feePercent = parseFloat(feeStr.replace('%', ''));
                    total += basePrice * (feePercent / 100);
                } else {
                    total += parseInt(feeStr.replace(/[^\d]/g, '')) || 0;
                }
            }

            summaryTotal.textContent = 'Rp ' + Math.ceil(total).toLocaleString('id-ID');
        } else if (summaryTotal) {
            summaryTotal.textContent = 'Rp 0';
        }
    }

    function updatePaymentPrices() {
        let selectedProduct = null;
        productRadios.forEach(r => {
            if (r.checked) selectedProduct = r;
        });

        document.querySelectorAll('.method-card').forEach(radio => {
            const card = radio.closest('label');
            const totalEl = card ? card.querySelector('.payment-total') : null;
            if (!totalEl) return;

            if (!selectedProduct) {
                totalEl.classList.add('hidden');
                totalEl.textContent = '';
                return;
            }

            const basePrice = parseInt(selectedProduct.dataset.price.replace(/\./g, ''));
            const feeStr = radio.dataset.fee;
            let total = basePrice;

            if (feeStr.includes('%')) {
                const feePercent = parseFloat(feeStr.replace('%', ''));
                total += basePrice * (feePercent / 100);
            } else {
                total += parseInt(feeStr.replace(/[^\d]/g, '')) || 0;
            }

            totalEl.textContent = 'Rp ' + Math.ceil(total).toLocaleString('id-ID');
            totalEl.classList.remove('hidden');
        });
    }

    const nominalSection = document.getElementById('nominal-section');

    function updateNominalSectionState() {
        if (!nominalSection) return;
        const userId = userIdInput ? userIdInput.value.trim() : '';
        if (whatsappDigitsOk() && userId.length >= 3) {
            nominalSection.classList.remove('opacity-50', 'is-locked');
            nominalSection.classList.add('transition-opacity', 'duration-500');
        } else {
            nominalSection.classList.add('opacity-50', 'is-locked');
        }
    }

    if (nominalSection) {
        nominalSection.addEventListener('click', (e) => {
            if (nominalSection.classList.contains('is-locked')) {
                // Check if the click target or its parent is a category tab button
                const isTabBtn = e.target.closest('.tab-btn') || 
                                 e.target.closest('[role="tab"]') || 
                                 e.target.closest('.category-tab'); // Common classes in the UI
                
                if (isTabBtn) return; // Allow interaction with tabs

                // Prevent all other child interactions if locked
                e.preventDefault();
                e.stopPropagation();
                
                // Show toast message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        icon: 'warning',
                        title: 'Silahkan isi akun terlebih dahulu',
                        background: document.documentElement.classList.contains('dark') ? '#0a0a15' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    });
                }
                
                const userId = userIdInput ? userIdInput.value.trim() : '';
                let targetInput = null;
                if (!whatsappDigitsOk() && customerWhatsappInput) {
                    targetInput = customerWhatsappInput;
                } else if (userId.length < 3) {
                    targetInput = userIdInput;
                } else if (zoneIdInput && zoneIdInput.required && !(zoneIdInput.value || '').trim()) {
                    targetInput = zoneIdInput;
                }

                if (targetInput) {
                    targetInput.focus();
                    targetInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    targetInput.classList.add('ring-2', 'ring-primary', 'animate-pulse');
                    setTimeout(() => {
                        targetInput.classList.remove('ring-2', 'ring-primary', 'animate-pulse');
                    }, 2000);
                }
            }
        }, true); // Use capture phase to intercept before children
    }


    productRadios.forEach(r => r.addEventListener('change', () => {
        updateSummary();
        updatePaymentPrices();
    }));
    paymentRadios.forEach(r => r.addEventListener('change', updateSummary));
    if (customerWhatsappInput) {
        customerWhatsappInput.addEventListener('input', () => {
            updateSummary();
            updateNominalSectionState();
        });
    }
    if (userIdInput) {
        userIdInput.addEventListener('input', () => {
            updateSummary();
            updateNominalSectionState();
        });
    }
    if (zoneIdInput) {
        zoneIdInput.addEventListener('input', () => {
            updateSummary();
            updateNominalSectionState();
        });
    }

    // Initial update
    updateSummary();
    updatePaymentPrices();
    updateNominalSectionState();

    // --- Debounce Helper ---
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // --- Cek ID Logic (Automated) ---
    const playerNickname = document.getElementById('player-nickname');
    const nicknameArea = document.getElementById('nickname-area');

    async function checkPlayerId() {
        const userId = userIdInput ? userIdInput.value.trim() : '';
        const zoneId = zoneIdInput ? zoneIdInput.value.trim() : '';
        
        // Find operatorId from input or any element that has it
        const operatorId = userIdInput ? userIdInput.dataset.operatorId : '';

        // Only check if userId has minimum length (e.g. 3 chars)
        if (userId.length < 3) {
            nicknameArea.classList.add('hidden');
            return;
        }

        // Loading state
        nicknameArea.classList.remove('hidden');
        nicknameArea.classList.add('flex', 'animate-pulse');
        playerNickname.textContent = 'Mengecek...';
        playerNickname.classList.remove('text-red-500');

        try {
            // Robust slug extraction: find the part after /topup/
            const parts = window.location.pathname.split('/');
            const topupIndex = parts.indexOf('topup');
            const gameSlug = (topupIndex !== -1 && parts[topupIndex + 1]) ? parts[topupIndex + 1] : '';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');

            const response = await fetch('/api/check-id', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    user_id: userId,
                    zone_id: zoneId,
                    operator_id: operatorId,
                    game_slug: gameSlug
                })
            });

            nicknameArea.classList.remove('animate-pulse');

            const rawText = await response.text();
            let data = null;
            try {
                data = rawText ? JSON.parse(rawText) : null;
            } catch (parseErr) {
                playerNickname.textContent = 'Server mengembalikan data bukan JSON. Muat ulang halaman atau cek koneksi.';
                playerNickname.classList.add('text-red-500');
                return;
            }
            
            if (!response.ok) {
                playerNickname.textContent = (data && data.message) ? data.message : ('Gagal mengecek ID (HTTP ' + response.status + ')');
                playerNickname.classList.add('text-red-500');
                return;
            }

            if (data.success) {
                playerNickname.textContent = data.nickname;
                playerNickname.classList.remove('text-red-500');
            } else {
                playerNickname.textContent = data.message || 'Invalid ID';
                playerNickname.classList.add('text-red-500');
            }
        } catch (error) {
            console.error('Error auto check ID:', error);
            playerNickname.textContent = 'Masalah koneksi: ' + (error.message || '');
            playerNickname.classList.add('text-red-500');
        }
    }

    const debouncedCheck = debounce(checkPlayerId, 1000);

    if (userIdInput) userIdInput.addEventListener('input', debouncedCheck);
    if (zoneIdInput) zoneIdInput.addEventListener('input', debouncedCheck);

    // --- Form Submission Handling ---
    const topupForm = document.getElementById('topup-form');
    if (topupForm) {
        topupForm.addEventListener('submit', (e) => {
            const userId = userIdInput ? userIdInput.value.trim() : '';
            const product = document.querySelector('input[name="product_code"]:checked');
            const payment = document.querySelector('input[name="payment"]:checked');
            const submitBtn = topupForm.querySelector('button[type="submit"]');

            if (customerWhatsappInput) {
                const d = (customerWhatsappInput.value || '').replace(/\D/g, '');
                if (d.length < 10 || d.length > 15) {
                    e.preventDefault();
                    alert('Masukkan nomor WhatsApp yang valid (10–15 digit angka).');
                    customerWhatsappInput.focus();
                    return;
                }
            }

            if (!userId) {
                e.preventDefault();
                alert('Silakan masukkan ID Pemain terlebih dahulu.');
                if (userIdInput) userIdInput.focus();
                return;
            }

            if (!product) {
                e.preventDefault();
                alert('Silakan pilih nominal produk.');
                return;
            }

            if (!payment) {
                e.preventDefault();
                alert('Silakan pilih metode pembayaran.');
                return;
            }

            // Show loading state
            if (submitBtn) {
                const originalContent = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="animate-spin material-icons-round text-sm">sync</span> Memproses...';
            }
        });
    }
});
