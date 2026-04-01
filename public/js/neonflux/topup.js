/* ==========================================================================
   NEON FLUX — Top-Up Page Interactions
   Amount selection, payment method selection, form validation
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    initAmountSelection();
    initPaymentSelection();
    initBuyButton();
});

/**
 * Amount Button Selection
 * Klik salah satu → highlight aktif, yang lain di-reset
 */
function initAmountSelection() {
    const amountButtons = document.querySelectorAll('.amount-btn');

    amountButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active from all
            amountButtons.forEach(b => {
                b.classList.remove('active');
                b.classList.remove('border-primary/50', 'shadow-neon-cyan');
                b.classList.add('border-white/10');
            });

            // Set active on clicked
            btn.classList.add('active');
            btn.classList.remove('border-white/10');
            btn.classList.add('border-primary/50', 'shadow-neon-cyan');
        });
    });
}

/**
 * Payment Method Selection
 * Auto-highlight border sesuai warna payment saat radio dipilih
 */
function initPaymentSelection() {
    const paymentOptions = document.querySelectorAll('.payment-option');

    paymentOptions.forEach(option => {
        const radio = option.querySelector('input[type="radio"]');
        if (!radio) return;

        option.addEventListener('click', () => {
            // Reset all
            paymentOptions.forEach(opt => {
                opt.classList.remove('active');
            });

            // Activate clicked
            option.classList.add('active');
            radio.checked = true;
        });

        // If already checked on load
        if (radio.checked) {
            option.classList.add('active');
        }
    });
}

/**
 * Buy Button — basic validation
 */
function initBuyButton() {
    const buyBtn = document.getElementById('btn-buy');
    if (!buyBtn) return;

    buyBtn.addEventListener('click', (e) => {
        e.preventDefault();

        const playerIdInput = document.getElementById('player-id');
        const selectedAmount = document.querySelector('.amount-btn.active');
        const selectedPayment = document.querySelector('input[name="payment"]:checked');

        // Validate player ID
        if (!playerIdInput || !playerIdInput.value.trim()) {
            shakeElement(playerIdInput?.closest('.relative'));
            return;
        }

        // Validate amount
        if (!selectedAmount) {
            const amountGrid = document.getElementById('amount-grid');
            if (amountGrid) shakeElement(amountGrid);
            return;
        }

        // Validate payment
        if (!selectedPayment) {
            const paymentSection = document.getElementById('payment-section');
            if (paymentSection) shakeElement(paymentSection);
            return;
        }

        // All valid — show confirmation (placeholder)
        const amount = selectedAmount.querySelector('.amount-price')?.textContent || '';
        const payment = selectedPayment.closest('.payment-option')?.querySelector('.text-sm')?.textContent || '';

        alert(`🎮 Konfirmasi Top-Up\n\nID: ${playerIdInput.value}\nJumlah: ${amount}\nPembayaran: ${payment}\n\n(Integrasi pembayaran segera hadir!)`);
    });
}

/**
 * Shake animation for validation feedback
 */
function shakeElement(el) {
    if (!el) return;
    el.classList.add('animate-shake');
    el.style.animation = 'shake 0.5s ease';
    setTimeout(() => {
        el.style.animation = '';
    }, 500);
}

// Add shake keyframes dynamically
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20% { transform: translateX(-8px); }
        40% { transform: translateX(8px); }
        60% { transform: translateX(-4px); }
        80% { transform: translateX(4px); }
    }
`;
document.head.appendChild(style);
