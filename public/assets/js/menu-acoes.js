class MenuAcoes {
    constructor(config = {}) {
        // Configurações padrão
        this.config = {
            onEditar: (id) => console.log(`Editar item ${id}`),
            onVisualizar: (id) => console.log(`Visualizar item ${id}`),
            onExcluir: (id) => console.log(`Excluir item ${id}`),
            ...config
        };

        // Estado do menu
        this.menuAberto = null;

        // Inicializar
        this.inicializarEventos();
    }

    inicializarEventos() {
        // Fechar menu ao clicar fora
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#menu-button-acoes') && !e.target.closest('#menu-acoes')) {
                this.fecharMenu();
            }
        });

        // Manipular cliques nos botões do menu
        document.addEventListener('click', (e) => {
            const botaoMenu = e.target.closest('#menu-button-acoes');
            if (botaoMenu) {
                e.stopPropagation();
                const id = botaoMenu.closest('tr')?.dataset.id || botaoMenu.dataset.id;
                this.toggleMenu(botaoMenu, id);
            }

            const acaoMenu = e.target.closest('[data-action]');
            if (acaoMenu) {
                e.preventDefault();
                const acao = acaoMenu.dataset.action;
                const id = this.menuAberto.dataset.id;
                this.executarAcao(acao, id);
                this.fecharMenu();
            }
        });
    }

    toggleMenu(botao, id) {
        const menu = botao.nextElementSibling;

        // Se já existe um menu aberto, fecha ele primeiro
        if (this.menuAberto && this.menuAberto !== menu) {
            this.fecharMenu();
        }

        // Toggle do menu atual
        if (menu.classList.contains('hidden')) {
            this.abrirMenu(menu, id);
        } else {
            this.fecharMenu();
        }
    }

    abrirMenu(menu, id) {
        // Remove classe hidden de todos os menus
        menu.classList.remove('hidden');
        menu.dataset.id = id;
        this.menuAberto = menu;

        // Posicionar o menu
        const rect = menu.previousElementSibling.getBoundingClientRect();
        menu.style.top = `${rect.bottom + window.scrollY}px`;
        menu.style.left = `${rect.left + window.scrollX - menu.offsetWidth + rect.width}px`;
    }

    fecharMenu() {
        if (this.menuAberto) {
            this.menuAberto.classList.add('hidden');
            this.menuAberto = null;
        }
    }

    executarAcao(acao, id) {
        switch(acao) {
            case 'editar':
                this.config.onEditar(id);
                break;
            case 'visualizar':
                this.config.onVisualizar(id);
                break;
            case 'excluir':
                if (confirm('Tem certeza que deseja excluir este item?')) {
                    this.config.onExcluir(id);
                }
                break;
        }
    }
}

/*
// Estado global do menu
let menuAbertoAtual = null;
let portalMenu = null;

// Funções de manipulação do menu
function criarPortalMenu() {
    if (!portalMenu) {
        portalMenu = document.createElement('div');
        portalMenu.id = 'tabela-menu-portal';
        document.body.appendChild(portalMenu);
    }
    return portalMenu;
}

function renderizarMenu(btnMenuAcao, idLinha) {
    const portal = criarPortalMenu();

    const menuHtml = `
        <div class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-gray-400/70 py-1">
            <a href="#" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100" data-action="editar">Editar</a>
            <a href="#" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100" data-action="visualizar">Visualizar</a>
            <a href="#" class="block px-4 py-2 text-sm font-semibold text-red-700 hover:bg-gray-100" data-action="excluir">Excluir</a>
        </div>
    `;

    portal.innerHTML = menuHtml;
    const menuElement = portal.querySelector('div');

    posicionarMenu(menuElement, btnMenuAcao);
    configurarEventosMenu(menuElement, idLinha);

    menuAbertoAtual = menuElement;
    portal.classList.remove('hidden');
}

function posicionarMenu(menuElement, btnMenuAcao) {
    const rect = btnMenuAcao.getBoundingClientRect();
    menuElement.style.position = 'absolute';
    menuElement.style.top = `${rect.bottom + window.scrollY}px`;
    menuElement.style.left = `${rect.left + window.scrollX + rect.width - menuElement.offsetWidth}px`;
    menuElement.style.zIndex = '9999';
}

function configurarEventosMenu(menuElement, idLinha) {
    menuElement.querySelectorAll('a[data-action]').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const action = e.target.dataset.action;
            executarAcao(action, idLinha);
            fecharMenu();
        });
    });
}

function fecharMenu() {
    if (portalMenu) {
        portalMenu.classList.add('hidden');
        menuAbertoAtual = null;
    }
}

function toggleMenu(botao, idLinha) {
    if (menuAbertoAtual) {
        fecharMenu();
    }
    renderizarMenu(botao, idLinha);
}

// Funções de ação
function executarAcao(acao, id) {
    const acoes = {
        editar: () => editarItem(id),
        visualizar: () => visualizarItem(id),
        excluir: () => excluirItem(id)
    };

    if (acoes[acao]) {
        acoes[acao]();
    } else {
        console.warn(`Nenhuma função auxiliar definida para a ação: "${acao}"`);
    }
}

// Implementações das ações
function editarItem(id) {
    console.log(`Editando item ${id}`);
    // Implemente sua lógica de edição aqui
}

function visualizarItem(id) {
    console.log(`Visualizando item ${id}`);
    // Implemente sua lógica de visualização aqui
}

function excluirItem(id) {
    if (confirm('Tem certeza que deseja excluir este item?')) {
        console.log(`Excluindo item ${id}`);
        // Implemente sua lógica de exclusão aqui
    }
}

// Inicialização e eventos globais
document.addEventListener('DOMContentLoaded', () => {
    // Evento para fechar o menu ao clicar fora
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#menu-button-acoes') && !e.target.closest('#tabela-menu-portal')) {
            fecharMenu();
        }
    });

    // Evento para abrir o menu
    document.addEventListener('click', (e) => {
        const botaoMenu = e.target.closest('#menu-button-acoes');
        if (botaoMenu) {
            e.stopPropagation();
            const id = botaoMenu.closest('tr')?.dataset.id || botaoMenu.dataset.id;
            toggleMenu(botaoMenu, id);
        }
    });
});
 */