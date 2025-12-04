document.addEventListener('DOMContentLoaded', function () {

    /* ====================================
        MENU LATERAL (_menu.php)
    ==================================== */

    // Lógica para o dropdown do menu de Grupos
    const dropdownToggles = document.querySelectorAll('[data-toggle="dropdown"]');

    dropdownToggles.forEach(function (toggle) {
        const submenu = toggle.nextElementSibling;
        const chevron = toggle.querySelector('[data-chevron="true"]');

        // Define o estado inicial do chevron (classe + ícone)
        if (submenu && !submenu.classList.contains('hidden')) {
            if (chevron) {
                chevron.classList.add('rotate-180');
            }
        } else {
            if (chevron) {
                chevron.classList.remove('rotate-180');
            }
        }

        toggle.addEventListener('click', function () {
            const isHidden = submenu?.classList.toggle('hidden'); // true se agora está oculto
            if (chevron) {
                chevron.classList.toggle('rotate-180');
                // Ajusta o texto do ícone conforme estado atual do submenu
                chevron.textContent = isHidden ? 'expand_more' : 'expand_less';
            }
        });
    });

    const sidebar = document.getElementById('sidebar');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const sidebarCloseButton = document.getElementById('sidebar-close-button'); // O botão DENTRO do sidebar
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    function abrirMenu() {
        if (sidebar) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
        }
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('hidden');
            void sidebarOverlay.offsetWidth; // Força reflow para transição
            sidebarOverlay.classList.add('opacity-100');
        }
        // document.body.classList.add('overflow-hidden'); // Opcional: trava scroll do body
    }

    function fecharMenu() {
        if (sidebar) {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
        }
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('opacity-100');
            setTimeout(() => { // Espera a opacidade sumir antes de esconder
                sidebarOverlay.classList.add('hidden');
            }, 300); // Duração da transição de opacidade
        }
        // document.body.classList.remove('overflow-hidden');
    }

    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function (event) {
            event.stopPropagation();
            abrirMenu();
        });
    }

    if (sidebarCloseButton) {
        sidebarCloseButton.addEventListener('click', function (event) {
            event.stopPropagation();
            fecharMenu();
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            fecharMenu();
        });
    }

    document.addEventListener('keydown', function (event) {
        // Fecha apenas se o menu estiver visível (translate-x-0) e a tela for pequena (onde o overlay/toggle é relevante)
        if (event.key === 'Escape' && sidebar && sidebar.classList.contains('translate-x-0') && window.innerWidth < 1024) { // 1024px é o breakpoint 'lg' do Tailwind
            fecharMenu();
        }
        
    });

    document.getElementById('sidebar-button-close')?.addEventListener('click', function () {
        fecharMenu();
    });

    // Abre o menu sempre que a tela for redimensionada para um tamanho maior que o breakpoint 'lg'
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) {
            if (sidebar) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.add('hidden');
                sidebarOverlay.classList.remove('opacity-100');
            }
            // document.body.classList.remove('overflow-hidden');
        } else {
            if (sidebar) {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
            }
            // document.body.classList.remove('overflow-hidden');
        }
    });

    // Lógica de fechar alertas flash (o seu código jQuery aqui estava ok)
    document.querySelectorAll('#mensagem-flash').forEach(function(el) {
        setTimeout(function() {
            el.style.transition = "opacity 0.5s ease";
            el.style.opacity = 0;
            setTimeout(() => el.remove(), 500); // remove após o fade-out
        }, 5000); // 10 segundos
    });


    /* ====================================
        CABEÇALHO (_header.php)
    ==================================== */

    const userMenuButton = document.getElementById('user-menu-button');
    const userMenuDropdown = document.getElementById('user-menu-dropdown');

    if (!userMenuButton || !userMenuDropdown) return;

    userMenuButton.addEventListener('click', function (event) {
        event.stopPropagation();
        const isHidden = userMenuDropdown.classList.contains('hidden');

        if (isHidden) {
            // Abrir: remove hidden primeiro, depois anima
            userMenuDropdown.classList.remove('hidden');
            requestAnimationFrame(() => {
                userMenuDropdown.classList.remove('opacity-0', 'scale-95');
                userMenuDropdown.classList.add('opacity-100', 'scale-100');
            });
        } else {
            // Fechar: anima primeiro, depois adiciona hidden
            userMenuDropdown.classList.remove('opacity-100', 'scale-100');
            userMenuDropdown.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                userMenuDropdown.classList.add('hidden');
            }, 100); // Aguarda a duração da transição (duration-100 = 100ms)
        }
    });

    document.addEventListener('click', function (event) {
        const isClickInside = userMenuButton.contains(event.target) || userMenuDropdown.contains(event.target);
        if (!userMenuDropdown.classList.contains('hidden') && !isClickInside) {
            userMenuDropdown.classList.remove('opacity-100', 'scale-100');
            userMenuDropdown.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                userMenuDropdown.classList.add('hidden');
            }, 100);
        }
    });


    /** ====================================
        SAIR SEM SALVAR (Formulários)
     ===================================== */

     // Variável para controlar se o formulário foi modificado
    let formModificado = false;

    // Função para marcar o formulário como modificado
    function marcarFormularioComoModificado() {
        formModificado = true;
    }

    // Quando o DOM estiver carregado
    document.addEventListener('DOMContentLoaded', function() {
        // Encontra todos os formulários na página
        const formularios = document.getElementsByTagName('form');

        // Adiciona listeners para cada formulário
        for (const form of formularios) {
            // Adiciona listener para todos os inputs do formulário
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('change', marcarFormularioComoModificado);
                input.addEventListener('keyup', marcarFormularioComoModificado);
            });

            // Quando o formulário for enviado, não mostrar o aviso
            form.addEventListener('submit', function() {
                formModificado = false;
            });
        }

        // Adiciona o evento beforeunload na janela
        window.addEventListener('beforeunload', function(e) {
            if (formModificado) {
                // A especificação moderna recomenda apenas chamar preventDefault()
                // O navegador mostrará uma mensagem padrão de confirmação
                e.preventDefault();
            }
        });
    });
});