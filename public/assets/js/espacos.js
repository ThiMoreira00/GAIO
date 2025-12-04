/**
 * @file espacos.js
 * @description Funcionalidades JavaScript para gerenciamento de espaços
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

class GerenciadorEspacos {
    constructor() {
        this.init();
    }

    /**
     * Inicializa o gerenciador
     */
    init() {
        this.bindEvents();
        this.initializeFilters();
        this.initializeSearch();
        this.initializeViewToggle();
        this.initializeModal();
        this.initializeForm();
        this.initializeCounters();
    }

    /**
     * Vincula eventos aos elementos
     */
    bindEvents() {
        // Botões de ação
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-modal-trigger]')) {
                this.openModal(e.target.dataset.modalTrigger);
            }
            
            if (e.target.matches('[data-modal-close]') || e.target.closest('[data-modal-close]')) {
                this.closeAllModals();
            }
            
            if (e.target.matches('[data-modal-backdrop]')) {
                this.closeAllModals();
            }

            if (e.target.matches('.btn-editar') || e.target.closest('.btn-editar')) {
                const btn = e.target.matches('.btn-editar') ? e.target : e.target.closest('.btn-editar');
                this.editarEspaco(btn.dataset.espacoId);
            }

            if (e.target.matches('.btn-excluir') || e.target.closest('.btn-excluir')) {
                const btn = e.target.matches('.btn-excluir') ? e.target : e.target.closest('.btn-excluir');
                this.confirmarExclusao(btn.dataset.espacoId, btn.dataset.espacoNome);
            }

            if (e.target.matches('.filtro-status')) {
                this.aplicarFiltroStatus(e.target.dataset.status);
            }

            if (e.target.matches('.view-toggle')) {
                this.alterarVisualizacao(e.target.dataset.view);
            }
        });

        // Formulário
        document.addEventListener('submit', (e) => {
            if (e.target.id === 'form-espaco') {
                e.preventDefault();
                this.salvarEspaco();
            }
        });

        // Busca em tempo real
        document.addEventListener('input', (e) => {
            if (e.target.id === 'busca-espacos') {
                this.debounce(() => this.buscarEspacos(e.target.value), 300)();
            }

            if (e.target.matches('[data-counter]')) {
                this.updateCounter(e.target);
            }
        });

        // Teclas de atalho
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }

            // Ctrl+N para novo espaço
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                this.openModal('modal-espaco-form');
            }
        });
    }

    /**
     * Inicializa os filtros
     */
    initializeFilters() {
        // Aplicar filtro ativo por padrão se houver parâmetro na URL
        const urlParams = new URLSearchParams(window.location.search);
        const statusFilter = urlParams.get('status');
        
        if (statusFilter) {
            this.aplicarFiltroStatus(statusFilter);
        }
    }

    /**
     * Inicializa a busca
     */
    initializeSearch() {
        const searchInput = document.getElementById('busca-espacos');
        if (searchInput && searchInput.value) {
            this.destacarTermoBusca(searchInput.value);
        }
    }

    /**
     * Inicializa o toggle de visualização
     */
    initializeViewToggle() {
        const savedView = localStorage.getItem('espacos-view') || 'cards';
        this.alterarVisualizacao(savedView);
    }

    /**
     * Inicializa o modal
     */
    initializeModal() {
        // Implementação será expandida conforme necessário
    }

    /**
     * Inicializa o formulário
     */
    initializeForm() {
        // Validação em tempo real
        const form = document.getElementById('form-espaco');
        if (form) {
            const inputs = form.querySelectorAll('input[required], select[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
                input.addEventListener('input', () => this.clearFieldError(input));
            });
        }
    }

    /**
     * Inicializa os contadores de caracteres
     */
    initializeCounters() {
        const textareas = document.querySelectorAll('textarea[maxlength]');
        textareas.forEach(textarea => {
            this.updateCounter(textarea);
        });
    }

    /**
     * Aplica filtro por status
     */
    aplicarFiltroStatus(status) {
        // Atualizar botões de filtro
        document.querySelectorAll('.filtro-status').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.status === status) {
                btn.classList.add('active');
            }
        });

        // Filtrar elementos
        const elementos = document.querySelectorAll('[data-status]');
        elementos.forEach(el => {
            const shouldShow = !status || el.dataset.status === status;
            el.style.display = shouldShow ? '' : 'none';
        });

        // Atualizar contador
        this.atualizarContador();

        // Atualizar URL sem recarregar
        const url = new URL(window.location);
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        history.replaceState(null, '', url);
    }

    /**
     * Altera a visualização entre cards e tabela
     */
    alterarVisualizacao(view) {
        const cardsView = document.getElementById('cards-view');
        const tableView = document.getElementById('table-view');
        const viewButtons = document.querySelectorAll('.view-toggle');

        // Atualizar botões
        viewButtons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            }
        });

        // Alternar visualizações
        if (view === 'table') {
            cardsView?.classList.add('hidden');
            tableView?.classList.remove('hidden');
        } else {
            cardsView?.classList.remove('hidden');
            tableView?.classList.add('hidden');
        }

        // Salvar preferência
        localStorage.setItem('espacos-view', view);
    }

    /**
     * Busca espaços
     */
    buscarEspacos(termo) {
        if (termo.length < 2 && termo.length > 0) {
            return; // Não buscar com menos de 2 caracteres
        }

        // Implementar busca via AJAX ou filtro local
        const elementos = document.querySelectorAll('.espaco-card, [data-status]');
        
        elementos.forEach(el => {
            const codigo = el.querySelector('[data-codigo]')?.textContent || '';
            const nome = el.querySelector('[data-nome]')?.textContent || '';
            const texto = (codigo + ' ' + nome).toLowerCase();
            
            const matches = !termo || texto.includes(termo.toLowerCase());
            el.style.display = matches ? '' : 'none';
        });

        this.destacarTermoBusca(termo);
        this.atualizarContador();
    }

    /**
     * Destaca o termo de busca nos resultados
     */
    destacarTermoBusca(termo) {
        if (!termo) return;

        // Implementar destaque visual do termo de busca
        // Esta é uma implementação básica
        const elementos = document.querySelectorAll('[data-codigo], [data-nome]');
        elementos.forEach(el => {
            const texto = el.textContent;
            const regex = new RegExp(`(${termo})`, 'gi');
            el.innerHTML = texto.replace(regex, '<mark>$1</mark>');
        });
    }

    /**
     * Atualiza o contador de resultados
     */
    atualizarContador() {
        const visibleElements = document.querySelectorAll('.espaco-card:not([style*="display: none"]), tbody tr:not([style*="display: none"])');
        const counter = document.querySelector('[data-results-count]');
        
        if (counter) {
            counter.textContent = visibleElements.length;
        }
    }

    /**
     * Abre modal
     */
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        if (modalId === 'modal-espaco-form') {
            this.carregarFormularioEspaco();
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        // Focar no primeiro elemento focável
        setTimeout(() => {
            const firstFocusable = modal.querySelector('input, select, textarea, button:not([data-modal-close])');
            firstFocusable?.focus();
        }, 100);
    }

    /**
     * Fecha todos os modais
     */
    closeAllModals() {
        const modals = document.querySelectorAll('[id^="modal-"]');
        modals.forEach(modal => {
            modal.classList.add('hidden');
        });
        document.body.classList.remove('overflow-hidden');

        // Limpar formulário
        this.limparFormulario();
    }

    /**
     * Carrega o formulário do espaço
     */
    async carregarFormularioEspaco() {
        const modalContent = document.getElementById('modal-content');
        if (!modalContent) return;

        try {
            // Aqui você carregaria o conteúdo via AJAX se necessário
            // Para este exemplo, assumimos que o formulário já está na página
            const formTemplate = document.querySelector('[data-form-template]');
            if (formTemplate) {
                modalContent.innerHTML = formTemplate.innerHTML;
            }
        } catch (error) {
            console.error('Erro ao carregar formulário:', error);
            this.showError('Erro ao carregar o formulário.');
        }
    }

    /**
     * Edita um espaço
     */
    async editarEspaco(espacoId) {
        try {
            // Carregar dados do espaço via AJAX
            const response = await fetch(`/espacos/${espacoId}/dados`);
            const espaco = await response.json();

            if (espaco.status === 'erro') {
                throw new Error(espaco.mensagem);
            }

            // Preencher formulário com os dados
            this.preencherFormulario(espaco.dados);
            
            // Alterar título do modal
            document.getElementById('modal-title-text').textContent = 'Editar Espaço';
            document.getElementById('btn-salvar-text').textContent = 'Atualizar Espaço';
            
            // Abrir modal
            this.openModal('modal-espaco-form');

        } catch (error) {
            console.error('Erro ao editar espaço:', error);
            this.showError('Erro ao carregar dados do espaço.');
        }
    }

    /**
     * Confirma exclusão de espaço
     */
    confirmarExclusao(espacoId, espacoNome) {
        document.getElementById('espaco-nome-exclusao').textContent = espacoNome;
        
        const btnConfirmar = document.getElementById('btn-confirmar-exclusao');
        btnConfirmar.onclick = () => this.excluirEspaco(espacoId);
        
        this.openModal('modal-confirmar-exclusao');
    }

    /**
     * Exclui um espaço
     */
    async excluirEspaco(espacoId) {
        try {
            const response = await fetch(`/espacos/${espacoId}/excluir`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status === 'sucesso') {
                this.closeAllModals();
                this.showSuccess('Espaço excluído com sucesso!');
                
                // Remover elemento da página
                const elemento = document.querySelector(`[data-espaco-id="${espacoId}"]`)?.closest('.espaco-card, tr');
                elemento?.remove();
                
                this.atualizarContador();
            } else {
                throw new Error(result.mensagem);
            }

        } catch (error) {
            console.error('Erro ao excluir espaço:', error);
            this.showError('Erro ao excluir o espaço.');
        }
    }

    /**
     * Salva um espaço (criar ou editar)
     */
    async salvarEspaco() {
        const form = document.getElementById('form-espaco');
        if (!form) return;

        if (!this.validarFormulario(form)) {
            return;
        }

        const formData = new FormData(form);
        const espacoId = formData.get('id');
        const url = espacoId ? `/espacos/${espacoId}/editar` : '/espacos/criar';
        const method = espacoId ? 'PUT' : 'POST';

        try {
            this.setLoading(true);

            const response = await fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status === 'sucesso') {
                this.closeAllModals();
                this.showSuccess(result.mensagem || 'Espaço salvo com sucesso!');
                
                // Recarregar página ou atualizar lista
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.mensagem);
            }

        } catch (error) {
            console.error('Erro ao salvar espaço:', error);
            this.showFormError('Erro ao salvar o espaço: ' + error.message);
        } finally {
            this.setLoading(false);
        }
    }

    /**
     * Valida o formulário
     */
    validarFormulario(form) {
        let isValid = true;
        const errors = [];

        // Limpar erros anteriores
        this.clearFormErrors();

        // Validações obrigatórias
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.setFieldError(field, 'Este campo é obrigatório.');
                isValid = false;
            }
        });

        // Validações específicas
        const codigo = form.querySelector('#espaco-codigo');
        if (codigo?.value && !/^[A-Za-z0-9\-_]{1,20}$/.test(codigo.value)) {
            this.setFieldError(codigo, 'Código deve conter apenas letras, números, hífen e underscore.');
            isValid = false;
        }

        const capacidade = form.querySelector('#espaco-capacidade');
        if (capacidade?.value && (parseInt(capacidade.value) < 1 || parseInt(capacidade.value) > 9999)) {
            this.setFieldError(capacidade, 'Capacidade deve estar entre 1 e 9999.');
            isValid = false;
        }

        if (!isValid) {
            this.showFormError('Por favor, corrija os erros no formulário.');
        }

        return isValid;
    }

    /**
     * Valida um campo individual
     */
    validateField(field) {
        if (field.hasAttribute('required') && !field.value.trim()) {
            this.setFieldError(field, 'Este campo é obrigatório.');
            return false;
        }

        this.clearFieldError(field);
        return true;
    }

    /**
     * Define erro em um campo
     */
    setFieldError(field, message) {
        field.classList.add('error');
        
        const errorId = field.id + '-error';
        const errorElement = document.getElementById(errorId);
        
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
    }

    /**
     * Limpa erro de um campo
     */
    clearFieldError(field) {
        field.classList.remove('error');
        field.classList.add('success');
        
        const errorId = field.id + '-error';
        const errorElement = document.getElementById(errorId);
        
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    }

    /**
     * Limpa todos os erros do formulário
     */
    clearFormErrors() {
        const formErrors = document.getElementById('form-errors');
        if (formErrors) {
            formErrors.classList.add('hidden');
        }

        const fieldErrors = document.querySelectorAll('[id$="-error"]');
        fieldErrors.forEach(error => error.classList.add('hidden'));

        const errorFields = document.querySelectorAll('.error');
        errorFields.forEach(field => field.classList.remove('error'));
    }

    /**
     * Mostra erro geral do formulário
     */
    showFormError(message) {
        const formErrors = document.getElementById('form-errors');
        const errorsList = document.getElementById('form-errors-list');
        
        if (formErrors && errorsList) {
            errorsList.innerHTML = `<li>${message}</li>`;
            formErrors.classList.remove('hidden');
        }
    }

    /**
     * Preenche o formulário com dados
     */
    preencherFormulario(dados) {
        const form = document.getElementById('form-espaco');
        if (!form) return;

        Object.keys(dados).forEach(key => {
            const field = form.querySelector(`[name="${key}"]`);
            if (field) {
                field.value = dados[key] || '';
            }
        });

        // Atualizar contadores
        this.initializeCounters();
    }

    /**
     * Limpa o formulário
     */
    limparFormulario() {
        const form = document.getElementById('form-espaco');
        if (form) {
            form.reset();
            this.clearFormErrors();
            
            // Resetar título do modal
            const titleText = document.getElementById('modal-title-text');
            const btnText = document.getElementById('btn-salvar-text');
            
            if (titleText) titleText.textContent = 'Adicionar Espaço';
            if (btnText) btnText.textContent = 'Salvar Espaço';
        }
    }

    /**
     * Atualiza contador de caracteres
     */
    updateCounter(textarea) {
        const maxLength = parseInt(textarea.getAttribute('maxlength'));
        const currentLength = textarea.value.length;
        
        const counterId = textarea.id + '-count';
        const counter = document.getElementById(counterId);
        
        if (counter) {
            counter.textContent = currentLength;
            
            // Mudar cor baseado na proximidade do limite
            const percentage = (currentLength / maxLength) * 100;
            const counterContainer = counter.closest('.character-counter') || counter.parentElement;
            
            counterContainer.classList.remove('warning', 'error');
            
            if (percentage > 90) {
                counterContainer.classList.add('error');
            } else if (percentage > 75) {
                counterContainer.classList.add('warning');
            }
        }
    }

    /**
     * Define estado de loading
     */
    setLoading(loading) {
        const btn = document.getElementById('btn-salvar');
        const btnText = document.getElementById('btn-salvar-text');
        const btnLoading = document.getElementById('btn-salvar-loading');
        
        if (btn) {
            btn.disabled = loading;
            btn.classList.toggle('btn-loading', loading);
        }
        
        if (btnText && btnLoading) {
            btnText.style.display = loading ? 'none' : '';
            btnLoading.style.display = loading ? '' : 'none';
        }
    }

    /**
     * Mostra mensagem de sucesso
     */
    showSuccess(message) {
        // Implementar usando o sistema de notificações existente
        if (typeof NotificadorFlash !== 'undefined') {
            new NotificadorFlash().sucesso(message);
        } else {
            alert(message); // Fallback
        }
    }

    /**
     * Mostra mensagem de erro
     */
    showError(message) {
        // Implementar usando o sistema de notificações existente
        if (typeof NotificadorFlash !== 'undefined') {
            new NotificadorFlash().erro(message);
        } else {
            alert(message); // Fallback
        }
    }

    /**
     * Função debounce para otimizar busca
     */
    debounce(func, wait) {
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
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    new GerenciadorEspacos();
});

// Exportar para uso global se necessário
window.GerenciadorEspacos = GerenciadorEspacos;