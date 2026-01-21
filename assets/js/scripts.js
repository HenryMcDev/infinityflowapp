/**
 * InfinityFlow - JavaScript Logic
 * =================================
 * Handles Tailwind configuration, animations, form submission, and UI interactions
 */

// ==================== TAILWIND CSS CONFIGURATION ====================
tailwind.config = {
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
            },
            fontFamily: {
                sans: ['Montserrat', 'sans-serif'],
            },
        }
    }
};

// ==================== INITIALIZE LIBRARIES ====================
document.addEventListener('DOMContentLoaded', () => {
    // Initialize Lucide Icons
    lucide.createIcons();

    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });
});

// ==================== MOBILE MENU TOGGLE ====================
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const mobileMenu = document.getElementById('mobile-menu');

if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
}

// ==================== ACCORDION FUNCTIONALITY ====================
function toggleAccordion(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('i[data-lucide="chevron-down"]');

    content.classList.toggle('active');

    if (icon) {
        icon.style.transform = content.classList.contains('active')
            ? 'rotate(180deg)'
            : 'rotate(0deg)';
    }
}

// ==================== FORM SUBMISSION HANDLER ====================
const form = document.getElementById('infinityForm');
const btn = document.getElementById('submitBtn');
const btnText = document.getElementById('btnText');

if (form && btn && btnText) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Disable button and show loading state
        btn.disabled = true;
        btnText.innerText = "Processando informações...";

        const formData = new FormData(form);

        try {
            const response = await fetch('index.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                alert('🚀 Dados enviados! O Flow já está processando sua solicitação e o n8n iniciou o fluxo. Entraremos em contato em breve.');
                form.reset();
            } else {
                alert('❌ Erro ao entrar em contato com o Flow. Por favor, tente pelo botão do WhatsApp.');
            }
        } catch (error) {
            console.error('Erro de submissão:', error);
            alert('⚠️ Erro de conexão. Verifique se sua internet está ativa ou use o suporte via WhatsApp.');
        } finally {
            // Re-enable button
            btn.disabled = false;
            btnText.innerText = "Enviar para Análise do Flow";
        }
    });
}
