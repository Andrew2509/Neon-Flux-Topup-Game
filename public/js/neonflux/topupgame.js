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
    const summaryFee = document.getElementById('summary-fee');
    const summaryBasePrice = document.getElementById('summary-base-price');
    const playerNicknameInput = document.getElementById('player_nickname_input');

    /** true saat request /api/check-id sedang berjalan — updateSummary tidak boleh menimpa teks loading / hasil. */
    let playerLookupInFlight = false;

    /** Nama dari respons API sukses terakhir, dikunci ke pasangan User ID + Zone (hidden input dikosongkan lagi saat cek berjalan). */
    let playerNickCache = { user: '', zone: '', nick: '' };

    function clearPlayerNickCache() {
        playerNickCache = { user: '', zone: '', nick: '' };
    }

    function setPlayerNickCache(user, zone, nick) {
        const n = (nick || '').trim().slice(0, 128);
        if (!n) {
            clearPlayerNickCache();
            return;
        }
        playerNickCache = {
            user: String(user || ''),
            zone: String(zone || ''),
            nick: n,
        };
    }

    function isPlaceholderNicknameText(t) {
        if (!t) return true;
        if (t === 'Mengecek...') return true;
        if (t === '—' || t === '-' || t === '–') return true;
        if (t.startsWith('Mencari')) return true;
        if (t.startsWith('Isi Zone ID')) return true;
        if (t.startsWith('Lengkapi Zone')) return true;
        return false;
    }

    function resolveGameSlug() {
        const fromData = userIdInput && userIdInput.dataset.gameSlug
            ? String(userIdInput.dataset.gameSlug).trim()
            : '';
        if (fromData) return fromData;
        const parts = window.location.pathname.split('/').filter(Boolean);
        const topIdx = parts.indexOf('topup');
        return (topIdx !== -1 && parts[topIdx + 1]) ? parts[topIdx + 1] : '';
    }

    function checkIdEndpoint() {
        return new URL('/api/check-id', window.location.origin).href;
    }

    function getSelectedProductCode() {
        let code = '';
        productRadios.forEach((r) => {
            if (r.checked) code = String(r.value || '').trim();
        });
        return code;
    }

    function forEachSummaryPlayerNameEl(callback) {
        const nodes = document.querySelectorAll('.js-summary-player-name');
        if (nodes.length) {
            nodes.forEach(callback);
            return;
        }
        const leg = document.getElementById('summary-player-name');
        if (leg) {
            callback(leg);
        }
    }

    function applyNicknameToSummary(nick) {
        const n = (nick || '').trim().slice(0, 128);
        forEachSummaryPlayerNameEl((sp) => {
            if (sp.getAttribute('data-sticky-summary') === '1') {
                sp.textContent = n ? ('Nama: ' + n) : '';
                sp.classList.toggle('hidden', !n);
            } else {
                sp.textContent = n || '—';
            }
        });
    }

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

        if (summaryWhatsapp && customerWhatsappInput) {
            const w = (customerWhatsappInput.value || '').trim();
            summaryWhatsapp.textContent = w || 'Belum Diisi';
        }

        reflowSummaryPlayerName();

        let selectedProduct2 = null;
        let selectedPayment2 = null;
        productRadios.forEach(r => {
            if (r.checked) selectedProduct2 = r;
        });
        paymentRadios.forEach(r => {
            if (r.checked) selectedPayment2 = r;
        });
        if (selectedProduct2 && summaryTotal) {
            let basePrice = parseInt(selectedProduct2.dataset.price.replace(/\./g, ''), 10);
            let feeAmount = 0;
            
            // First Purchase Discount (10%)
            const configEl = document.getElementById('first-purchase-config');
            const isEligible = configEl && configEl.dataset.eligible === '1';
            let discountAmount = 0;

            if (isEligible) {
                discountAmount = Math.floor(basePrice * 0.10);
            }

            const discountedBase = basePrice - discountAmount;

            if (selectedPayment2) {
                const feeStr = (selectedPayment2.dataset.fee || '0').toString().replace(/\s/g, '');
                
                // Mendukung format hybrid seperti "2.5%+2000"
                const components = feeStr.split('+');
                components.forEach(comp => {
                    if (comp.includes('%')) {
                        const feePercent = parseFloat(comp.replace('%', ''));
                        // Fees are usually applied to the discounted base price OR original base price.
                        // We'll apply to discounted base for user benefit.
                        feeAmount += discountedBase * (feePercent / 100);
                    } else {
                        feeAmount += parseInt(comp.replace(/[^\d]/g, ''), 10) || 0;
                    }
                });
            }

            const total = discountedBase + feeAmount;

            if (summaryBasePrice) {
                summaryBasePrice.textContent = 'Rp ' + basePrice.toLocaleString('id-ID');
            }

            // Sync Discount Row
            const discountRow = document.getElementById('discount-row');
            const summaryDiscount = document.getElementById('summary-discount');
            if (discountRow && summaryDiscount) {
                if (discountAmount > 0) {
                    discountRow.classList.remove('hidden');
                    discountRow.classList.add('flex');
                    summaryDiscount.textContent = '-Rp ' + discountAmount.toLocaleString('id-ID');
                } else {
                    discountRow.classList.add('hidden');
                    discountRow.classList.remove('flex');
                }
            }

            if (summaryFee) {
                summaryFee.textContent = 'Rp ' + Math.ceil(feeAmount).toLocaleString('id-ID');
            }

            // Sync visible display elements if they exist
            const displayBasePrice = document.getElementById('display-base-price');
            const displayFee = document.getElementById('display-fee');
            if (displayBasePrice) displayBasePrice.textContent = 'Rp ' + basePrice.toLocaleString('id-ID');
            if (displayFee) displayFee.textContent = 'Rp ' + Math.ceil(feeAmount).toLocaleString('id-ID');

            summaryTotal.textContent = 'Rp ' + Math.ceil(total).toLocaleString('id-ID');
        } else if (summaryTotal) {
            if (summaryBasePrice) summaryBasePrice.textContent = 'Rp 0';
            if (summaryFee) summaryFee.textContent = 'Rp 0';

            const discountRow = document.getElementById('discount-row');
            if (discountRow) discountRow.classList.add('hidden');
            
            const displayBasePrice = document.getElementById('display-base-price');
            const displayFee = document.getElementById('display-fee');
            if (displayBasePrice) displayBasePrice.textContent = 'Rp 0';
            if (displayFee) displayFee.textContent = 'Rp 0';

            summaryTotal.textContent = 'Rp 0';
        }
    }

    function readVerifiedNicknameFromForm() {
        const u = userIdInput ? userIdInput.value.trim() : '';
        const z = zoneIdInput ? zoneIdInput.value.trim() : '';

        if (playerNickCache.nick && playerNickCache.user === u && playerNickCache.zone === z) {
            return playerNickCache.nick;
        }

        const nickEl = document.querySelector('#nickname-area .js-player-nick')
            || document.getElementById('player-nickname');
        if (nickEl && !nickEl.classList.contains('text-red-500')) {
            const t = nickEl.textContent.trim();
            if (t && !isPlaceholderNicknameText(t)) {
                return t.slice(0, 128);
            }
        }
        if (playerNicknameInput && playerNicknameInput.value.trim()) {
            return playerNicknameInput.value.trim().slice(0, 128);
        }
        return '';
    }

    function reflowSummaryPlayerName() {
        let hasTarget = false;
        forEachSummaryPlayerNameEl(() => {
            hasTarget = true;
        });
        if (!hasTarget) {
            return;
        }
        const nick = readVerifiedNicknameFromForm();
        /* Jangan timpa "Mencari…" hanya jika request masih jalan DAN nama belum tampil di form */
        if (playerLookupInFlight && !nick) {
            return;
        }
        /* Jika #player-nickname sudah terisi tapi flag msih true (race), tetap sinkronkan ringkasan */
        if (nick && playerNicknameInput && !playerNicknameInput.value.trim()) {
            playerNicknameInput.value = nick;
        }
        forEachSummaryPlayerNameEl((sp) => {
            if (sp.getAttribute('data-sticky-summary') === '1') {
                sp.textContent = nick ? ('Nama: ' + nick) : '';
                sp.classList.toggle('hidden', !nick);
            } else {
                sp.textContent = nick || '—';
            }
            sp.classList.remove('animate-pulse', 'opacity-80');
        });
    }

    function paintSummaryPlayerLoading() {
        const msg = 'Mencari nama pemain...';
        forEachSummaryPlayerNameEl((sp) => {
            if (sp.getAttribute('data-sticky-summary') === '1') {
                sp.textContent = 'Nama: ' + msg;
                sp.classList.remove('hidden');
            } else {
                sp.textContent = msg;
            }
            sp.classList.add('animate-pulse', 'opacity-80');
        });
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
    const paymentSection = document.getElementById('payment-section');

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

    function updatePaymentSectionState() {
        if (!paymentSection) return;
        const userId = (userIdInput ? userIdInput.value.trim() : '');
        const hasNominal = getSelectedProductCode() !== '';
        
        // Locked if nominal section is locked OR no nominal is selected
        if (whatsappDigitsOk() && userId.length >= 3 && hasNominal) {
            paymentSection.classList.remove('opacity-50', 'is-locked');
            paymentSection.classList.add('transition-opacity', 'duration-500');
            // Remove pointer-events-none if we were using it, 
            // but we're handling clicks in JS for better feedback
        } else {
            paymentSection.classList.add('opacity-50', 'is-locked');
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

    if (paymentSection) {
        paymentSection.addEventListener('click', (e) => {
            if (paymentSection.classList.contains('is-locked')) {
                // Prevent interaction if locked
                e.preventDefault();
                e.stopPropagation();
                
                const hasNominal = getSelectedProductCode() !== '';
                let title = 'Silahkan pilih nominal terlebih dahulu';
                let targetEl = null;

                if (!whatsappDigitsOk()) {
                    title = 'Silahkan isi nomor WhatsApp';
                    targetEl = customerWhatsappInput;
                } else if ((userIdInput ? userIdInput.value.trim().length : 0) < 3) {
                    title = 'Silahkan isi data akun';
                    targetEl = userIdInput;
                } else if (!hasNominal) {
                    title = 'Silahkan pilih nominal top-up';
                    targetEl = nominalSection;
                }

                // Show toast message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        icon: 'warning',
                        title: title,
                        background: document.documentElement.classList.contains('dark') ? '#0a0a15' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    });
                }

                if (targetEl) {
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    if (targetEl.focus) targetEl.focus();
                }
            }
        }, true);
    }


    paymentRadios.forEach(r => r.addEventListener('change', updateSummary));
    if (customerWhatsappInput) {
        customerWhatsappInput.addEventListener('input', () => {
            updateSummary();
            updateNominalSectionState();
            updatePaymentSectionState();
        });
    }
    if (userIdInput) {
        userIdInput.addEventListener('input', () => {
            updateSummary();
            updateNominalSectionState();
            updatePaymentSectionState();
        });
    }
    if (zoneIdInput) {
        zoneIdInput.addEventListener('input', () => {
            updateSummary();
            updateNominalSectionState();
            updatePaymentSectionState();
        });
    }

    // Initial update
    updateSummary();
    updatePaymentPrices();
    updateNominalSectionState();
    updatePaymentSectionState();

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

    let checkIdGeneration = 0;
    let checkIdAbort = null;
    /** Reset saat User/Zone berubah; membatasi auto-retry saat Codashop rate limit. */
    let checkIdRateLimitKey = '';
    let checkIdRateLimitRetries = 0;

    async function checkPlayerId() {
        const userId = userIdInput ? userIdInput.value.trim() : '';
        const zoneId = zoneIdInput ? zoneIdInput.value.trim() : '';

        const operatorId = userIdInput ? (userIdInput.dataset.operatorId || '') : '';
        const gameSlug = resolveGameSlug();

        if (userId.length < 3) {
            checkIdGeneration += 1;
            checkIdRateLimitKey = '';
            checkIdRateLimitRetries = 0;
            playerLookupInFlight = false;
            clearPlayerNickCache();
            if (checkIdAbort) {
                checkIdAbort.abort();
                checkIdAbort = null;
            }
            if (playerNicknameInput) {
                playerNicknameInput.value = '';
            }
            if (nicknameArea) {
                nicknameArea.classList.add('hidden');
            }
            applyNicknameToSummary('');
            updateSummary();
            return;
        }

        if (zoneIdInput && zoneId.length < 1) {
            checkIdGeneration += 1;
            checkIdRateLimitKey = '';
            checkIdRateLimitRetries = 0;
            playerLookupInFlight = false;
            clearPlayerNickCache();
            if (checkIdAbort) {
                checkIdAbort.abort();
                checkIdAbort = null;
            }
            if (playerNicknameInput) {
                playerNicknameInput.value = '';
            }
            if (nicknameArea && playerNickname) {
                nicknameArea.classList.remove('hidden');
                nicknameArea.classList.add('flex');
                nicknameArea.classList.remove('animate-pulse');
                playerNickname.textContent = 'Isi Zone ID dulu untuk cek nama pemain';
                playerNickname.classList.remove('text-red-500');
            }
            applyNicknameToSummary('');
            updateSummary();
            return;
        }

        checkIdGeneration += 1;
        const myGen = checkIdGeneration;
        const rateKey = userId + '\0' + zoneId + '\0' + (getSelectedProductCode() || '');
        if (rateKey !== checkIdRateLimitKey) {
            checkIdRateLimitKey = rateKey;
            checkIdRateLimitRetries = 0;
        }
        if (checkIdAbort) {
            checkIdAbort.abort();
        }
        checkIdAbort = new AbortController();

        playerLookupInFlight = true;
        paintSummaryPlayerLoading();

        if (playerNicknameInput) {
            playerNicknameInput.value = '';
        }

        const hasNickUi = nicknameArea && playerNickname;
        if (hasNickUi) {
            nicknameArea.classList.remove('hidden');
            nicknameArea.classList.add('flex', 'animate-pulse');
            playerNickname.textContent = 'Mencari nama pemain...';
            playerNickname.classList.remove('text-red-500');
        }
        updateSummary();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');

            const response = await fetch(checkIdEndpoint(), {
                method: 'POST',
                signal: checkIdAbort.signal,
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
                    game_slug: gameSlug,
                    product_code: getSelectedProductCode()
                })
            });

            if (myGen !== checkIdGeneration) {
                return;
            }

            if (hasNickUi) {
                nicknameArea.classList.remove('animate-pulse');
            }

            const rawText = await response.text();
            let data = null;
            try {
                data = rawText ? JSON.parse(rawText) : null;
            } catch (parseErr) {
                if (myGen !== checkIdGeneration) return;
                clearPlayerNickCache();
                if (hasNickUi) {
                    playerNickname.textContent = 'Server mengembalikan data bukan JSON. Muat ulang halaman atau cek koneksi.';
                    playerNickname.classList.add('text-red-500');
                }
                if (playerNicknameInput) {
                    playerNicknameInput.value = '';
                }
                return;
            }

            if (!response.ok) {
                if (myGen !== checkIdGeneration) return;
                clearPlayerNickCache();
                if (hasNickUi) {
                    playerNickname.textContent = (data && data.message) ? data.message : ('Gagal mengecek ID (HTTP ' + response.status + ')');
                    playerNickname.classList.add('text-red-500');
                }
                if (playerNicknameInput) {
                    playerNicknameInput.value = '';
                }
                return;
            }

            if (data && (data.success === true || data.success === 1 || data.success === '1')) {
                if (myGen !== checkIdGeneration) return;
                checkIdRateLimitRetries = 0;
                const n = data.nickname != null ? String(data.nickname).trim().slice(0, 128) : '';
                if (hasNickUi) {
                    playerNickname.textContent = n || '—';
                    playerNickname.classList.remove('text-red-500');
                }
                if (playerNicknameInput && n) {
                    playerNicknameInput.value = n;
                }
                if (n) {
                    setPlayerNickCache(userId, zoneId, n);
                    applyNicknameToSummary(n);
                } else {
                    clearPlayerNickCache();
                }
            } else if (data && data.rate_limited) {
                if (myGen !== checkIdGeneration) return;
                clearPlayerNickCache();
                if (hasNickUi) {
                    playerNickname.textContent = '—';
                    playerNickname.classList.remove('text-red-500');
                }
                if (playerNicknameInput) {
                    playerNicknameInput.value = '';
                }
                applyNicknameToSummary('');
                if (checkIdRateLimitRetries < 2) {
                    checkIdRateLimitRetries += 1;
                    setTimeout(() => debouncedCheck(), 5600);
                }
            } else {
                if (myGen !== checkIdGeneration) return;
                clearPlayerNickCache();
                if (hasNickUi) {
                    playerNickname.textContent = (data && data.message) ? data.message : 'Invalid ID';
                    playerNickname.classList.add('text-red-500');
                }
                if (playerNicknameInput) {
                    playerNicknameInput.value = '';
                }
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }
            console.error('Error auto check ID:', error);
            if (myGen !== checkIdGeneration) return;
            clearPlayerNickCache();
            if (hasNickUi) {
                playerNickname.textContent = 'Masalah koneksi: ' + (error.message || '');
                playerNickname.classList.add('text-red-500');
            }
            if (playerNicknameInput) {
                playerNicknameInput.value = '';
            }
        } finally {
            if (myGen === checkIdGeneration) {
                playerLookupInFlight = false;
                const synced = readVerifiedNicknameFromForm();
                if (synced && playerNicknameInput && !playerNicknameInput.value.trim()) {
                    playerNicknameInput.value = synced;
                }
                updateSummary();
            }
        }
    }

    const debouncedCheck = debounce(checkPlayerId, 700);
    const debouncedCheckProduct = debounce(checkPlayerId, 400);

    productRadios.forEach(r => r.addEventListener('change', () => {
        updateSummary();
        updatePaymentPrices();
        updatePaymentSectionState();
        debouncedCheckProduct();
    }));

    if (userIdInput) {
        userIdInput.addEventListener('input', debouncedCheck);
        userIdInput.addEventListener('change', debouncedCheck);
        userIdInput.addEventListener('blur', debouncedCheck);
    }
    if (zoneIdInput) {
        zoneIdInput.addEventListener('input', debouncedCheck);
        zoneIdInput.addEventListener('change', debouncedCheck);
        zoneIdInput.addEventListener('blur', debouncedCheck);
    }

    requestAnimationFrame(() => {
        debouncedCheck();
    });

    // --- Form Submission Handling ---
    const topupForm = document.getElementById('topup-form');
    if (topupForm) {
        topupForm.addEventListener('submit', (e) => {
            const hid = document.getElementById('player_nickname_input');
            const nickEl = document.getElementById('player-nickname');
            if (hid && nickEl && !hid.value.trim()) {
                const t = nickEl.textContent.trim();
                if (t && t !== 'Mengecek...' && !nickEl.classList.contains('text-red-500')) {
                    hid.value = t.slice(0, 128);
                }
            }
            updateSummary();

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
