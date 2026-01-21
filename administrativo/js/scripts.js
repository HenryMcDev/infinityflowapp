// ============================================
// InfinityFlow - Administrative Area Scripts
// ============================================

// Service Configuration
const services = {
    easypanel: {
        title: 'Easypanel',
        description: 'Gerenciamento de aplicações e containers',
        url: 'https://admin.infinityflowapp.com/',
        icon: 'layout-dashboard'
    },
    n8n: {
        title: 'n8n',
        description: 'Automação de workflows e integrações',
        url: 'https://n8n.infinityflowapp.com/',
        icon: 'workflow'
    },
    evolution: {
        title: 'Evolution API',
        description: 'API de mensageria e comunicação',
        url: 'https://api.infinityflowapp.com/manager',
        icon: 'message-square'
    },
    minio: {
        title: 'Minio',
        description: 'Armazenamento de objetos e arquivos',
        url: 'https://minio.infinityflowapp.com/',
        icon: 'database'
    },
    site: {
        title: 'Site InfinityFlow',
        description: 'Site institucional do InfinityFlow',
        url: 'https://infinityflowapp.com/',
        icon: 'globe'
    }
};

// ============================================
// Authentication Functions
// ============================================

function checkAuth() {
    // Check authentication status from PHP session
    // The page itself handles showing login vs dashboard
    // This function is now mainly for client-side state management
    const isAuthenticated = sessionStorage.getItem('infinityflow_auth');
    const loginView = document.getElementById('loginView');
    const dashboardView = document.getElementById('dashboardView');

    if (isAuthenticated === 'true') {
        loginView.classList.add('hidden');
        dashboardView.classList.remove('hidden');
    } else {
        loginView.classList.remove('hidden');
        dashboardView.classList.add('hidden');
    }
}

async function login(username, password) {
    try {
        const response = await fetch('/InfinityStore/assets/php/login-process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username: username,
                password: password
            })
        });

        const data = await response.json();

        if (data.success) {
            // Store in sessionStorage for client-side state
            sessionStorage.setItem('infinityflow_auth', 'true');
            sessionStorage.setItem('infinityflow_user', username);
            return { success: true, message: data.message };
        } else {
            return { success: false, message: data.message };
        }
    } catch (error) {
        console.error('Login error:', error);
        return { success: false, message: 'Erro ao conectar com o servidor' };
    }
}

function logout() {
    // Clear client-side storage
    sessionStorage.removeItem('infinityflow_auth');
    sessionStorage.removeItem('infinityflow_user');

    // Redirect to PHP logout to destroy server session
    window.location.href = '/InfinityStore/assets/php/logout.php';
}

// ============================================
// Content Management Functions
// ============================================

function renderServicePanel(serviceKey) {
    const service = services[serviceKey];

    if (!service) {
        console.error('Service not found:', serviceKey);
        return;
    }

    const contentArea = document.getElementById('contentArea');

    const html = `
        <div class="max-w-4xl mx-auto fade-in-up">
            <!-- Service Header -->
            <div class="text-center mb-12">
                <h1 class="text-6xl font-bold tracking-tight mb-6 bg-gradient-to-r from-[#C71A1D] via-red-500 to-[#ff4444] bg-clip-text text-transparent animate-pulse">
                    ${service.title}
                </h1>
                <p class="text-xl text-white/60 font-light tracking-wide">
                    ${service.description}
                </p>
            </div>
            
            <!-- Service Content Card -->
            <div class="glass border border-white/10 rounded-3xl p-16 shadow-2xl hover:shadow-[0_0_60px_rgba(199,26,29,0.15)] transition-all duration-500">
                <div class="text-center">
                    <a href="${service.url}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-[#C71A1D] via-red-600 to-[#ff4444] rounded-2xl text-lg font-semibold text-white shadow-xl hover:shadow-[0_0_50px_rgba(199,26,29,0.5)] hover:-translate-y-1 active:translate-y-0 transition-all duration-300 group">
                        <span class="tracking-wide">Acessar Painel</span>
                        <i data-lucide="external-link" class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1 group-hover:-translate-y-1"></i>
                    </a>
                    <p class="mt-8 text-white/40 text-sm tracking-wide">Clique para abrir em uma nova aba</p>
                </div>
            </div>
        </div>
    `;

    contentArea.innerHTML = html;

    // Reinitialize Lucide icons for the new content
    lucide.createIcons();
}

function setActiveMenuItem(serviceKey) {
    // Remove active classes from all items
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.classList.remove('!text-white', '!bg-[#C71A1D]/15', '!border-[#C71A1D]', 'shadow-[0_0_20px_rgba(199,26,29,0.2)]');
    });

    // Add active classes to selected item
    const selectedItem = document.querySelector(`[data-service="${serviceKey}"]`);
    if (selectedItem) {
        selectedItem.classList.add('!text-white', '!bg-[#C71A1D]/15', '!border-[#C71A1D]', 'shadow-[0_0_20px_rgba(199,26,29,0.2)]');
    }
}

// ============================================
// Event Listeners
// ============================================

document.addEventListener('DOMContentLoaded', function () {
    // Check authentication on page load
    checkAuth();

    // Login form submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('errorMessage');
            const submitBtn = this.querySelector('button[type="submit"]');

            // Disable submit button during request
            submitBtn.disabled = true;
            submitBtn.textContent = 'Entrando...';

            const result = await login(username, password);

            if (result.success) {
                errorMessage.textContent = '';
                errorMessage.classList.remove('text-[#C71A1D]');
                errorMessage.classList.add('text-green-500');
                errorMessage.textContent = result.message;

                // Wait a moment then check auth to show dashboard
                setTimeout(() => {
                    checkAuth();
                }, 500);
            } else {
                errorMessage.classList.remove('text-green-500');
                errorMessage.classList.add('text-[#C71A1D]');
                errorMessage.textContent = result.message;

                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Entrar';

                setTimeout(() => {
                    errorMessage.textContent = '';
                }, 3000);
            }
        });
    }

    // Logout button
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();
            logout();
        });
    }

    // Navigation menu items
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();

            const serviceKey = this.getAttribute('data-service');
            renderServicePanel(serviceKey);
            setActiveMenuItem(serviceKey);
        });
    });
});

// ============================================
// Utility Functions
// ============================================

// Prevent default behavior on Enter key in forms
document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
        const form = e.target.closest('form');
        if (form) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    }
});

// Initialize
console.log('InfinityFlow Administrative Area - Ready');
