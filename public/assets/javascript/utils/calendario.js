class Calendario {

    constructor(containerId = 'calendario-container') {
        this.containerId = containerId;
        this.data = new Date();
        this.hoje = new Date(); // Guarda a data atual para comparação
        this.mesAtual = {};
        this.mesAnterior = {};
        this.anoAtual = this.data.getFullYear();

        this.atualizarDatas();
        this.iniciar();
    }

    /**
     * Atualiza as informações do mês atual e anterior com base na data da instância.
     */
    atualizarDatas() {
        this.anoAtual = this.data.getFullYear();
        const numeroMesAtual = this.data.getMonth();

        this.mesAtual = {
            numero: numeroMesAtual,
            nome: this.data.toLocaleString('pt-BR', { month: 'long' }),
            quantidadeDias: new Date(this.anoAtual, numeroMesAtual + 1, 0).getDate(),
            primeiroDiaDaSemana: new Date(this.anoAtual, numeroMesAtual, 1).getDay() === 0 ? 7 : new Date(this.anoAtual, numeroMesAtual, 1).getDay()
        };
        this.mesAtual.nome = this.mesAtual.nome.charAt(0).toUpperCase() + this.mesAtual.nome.slice(1);

        const dataMesAnterior = new Date(this.anoAtual, numeroMesAtual, 0);
        this.mesAnterior = {
            quantidadeDias: dataMesAnterior.getDate()
        };
    }

    /**
     * Cria a estrutura HTML base do calendário e anexa os eventos de clique.
     */
    iniciar() {
        const html = `
                <div class="flex items-center text-gray-900">
                    <button type="button" id="calendario-mes-anterior" class="-m-1.5 flex flex-none items-center justify-center p-1.5 text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Mês anterior</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="flex-auto text-center text-sm font-semibold">
                        <span id="calendario-mes-atual-nome">${this.mesAtual.nome.toLowerCase()}</span> 
                        <span class="text-sky-600" id="calendario-ano-atual">${this.anoAtual}</span>
                    </div>
                    <button type="button" id="calendario-mes-seguinte" class="-m-1.5 flex flex-none items-center justify-center p-1.5 text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Próximo mês</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                
                <div class="mt-6 grid grid-cols-7 text-center text-xs leading-6 text-gray-500">
                    <div>D</div>
                    <div>S</div>
                    <div>T</div>
                    <div>Q</div>
                    <div>Q</div>
                    <div>S</div>
                    <div>S</div>
                </div>

                <div id="calendario-grid" class="isolate mt-2 grid grid-cols-7 gap-px rounded-lg bg-gray-200 text-sm ring-1 ring-gray-200" style="grid-auto-rows: minmax(40px, auto);"></div>
        `;

        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error(`Container com ID #${this.containerId} não foi encontrado.`);
            return;
        }
        container.innerHTML = html;

        document.getElementById('calendario-mes-anterior').addEventListener('click', () => this.retrocederMes());
        document.getElementById('calendario-mes-seguinte').addEventListener('click', () => this.avancarMes());
        
        this.renderizar();
    }

    /**
     * Renderiza a grade de dias do calendário.
     */
    renderizar() {
        // Atualiza o cabeçalho
        document.getElementById('calendario-mes-atual-nome').innerText = this.mesAtual.nome.toLowerCase();
        document.getElementById('calendario-ano-atual').innerText = this.anoAtual;

        const calendarioGrid = document.getElementById('calendario-grid');
        calendarioGrid.innerHTML = ''; // Limpa a grade antes de renderizar

        const diasParaMostrarMesAnterior = this.mesAtual.primeiroDiaDaSemana - 1;
        const dataMesAnterior = new Date(this.anoAtual, this.mesAtual.numero, 0);
        const anoMesAnterior = dataMesAnterior.getFullYear();
        const mesMesAnterior = dataMesAnterior.getMonth() + 1;

        // 1. Exibe os dias do mês anterior
        for (let i = 0; i < diasParaMostrarMesAnterior; i++) {
            const dia = this.mesAnterior.quantidadeDias - (diasParaMostrarMesAnterior - 1 - i);
            const classeCanto = (i === 0) ? 'rounded-tl-lg' : '';
            const dataCompleta = `${anoMesAnterior}-${String(mesMesAnterior).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;

            calendarioGrid.innerHTML += `
                <button type="button" class="${classeCanto} bg-gray-50 py-1.5 text-gray-400 hover:bg-gray-100 focus:z-10">
                    <time datetime="${dataCompleta}" class="mx-auto flex h-7 w-7 items-center justify-center rounded-full">${dia}</time>
                </button>
            `;
        }

        // 2. Exibe os dias do mês atual
        for (let dia = 1; dia <= this.mesAtual.quantidadeDias; dia++) {
            const isToday = (
                dia === this.hoje.getDate() &&
                this.mesAtual.numero === this.hoje.getMonth() &&
                this.anoAtual === this.hoje.getFullYear()
            );

            let buttonClasses = isToday
                ? 'bg-sky-100 font-semibold text-sky-600'
                : 'bg-white text-gray-900 hover:bg-gray-100';
            
            buttonClasses += ' py-1.5 focus:z-10';

            // Adiciona classe para o canto superior direito
            if ((diasParaMostrarMesAnterior + dia) === 7) {
                buttonClasses += ' rounded-tr-lg';
            }
            
            const dataCompleta = `${this.anoAtual}-${String(this.mesAtual.numero + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;

            calendarioGrid.innerHTML += `
                <button type="button" class="${buttonClasses}">
                    <time datetime="${dataCompleta}" class="mx-auto flex h-7 w-7 items-center justify-center rounded-full">${dia}</time>
                </button>
            `;
        }
        
        // 3. Exibe os dias do próximo mês
        const totalDiasExibidos = diasParaMostrarMesAnterior + this.mesAtual.quantidadeDias;
        const diasProximoMes = (totalDiasExibidos % 7 === 0) ? 0 : 7 - (totalDiasExibidos % 7);
        const dataProximoMes = new Date(this.anoAtual, this.mesAtual.numero + 1, 1);
        const anoProximoMes = dataProximoMes.getFullYear();
        const mesProximoMes = dataProximoMes.getMonth() + 1;

        for (let dia = 1; dia <= diasProximoMes; dia++) {
            let classeCanto = '';
            // Adiciona classe para o canto inferior direito
            if (dia === diasProximoMes) {
                classeCanto = 'rounded-br-lg';
            }
            const dataCompleta = `${anoProximoMes}-${String(mesProximoMes).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;

             calendarioGrid.innerHTML += `
                <button type="button" class="${classeCanto} bg-gray-50 py-1.5 text-gray-400 hover:bg-gray-100 focus:z-10">
                    <time datetime="${dataCompleta}" class="mx-auto flex h-7 w-7 items-center justify-center rounded-full">${dia}</time>
                </button>
            `;
        }
    }

    /**
     * Avança para o próximo mês.
     */
    avancarMes() {
        this.data.setMonth(this.data.getMonth() + 1);
        this.atualizarDatas();
        this.renderizar();
    }
    
    /**
     * Retrocede para o mês anterior.
     */
    retrocederMes() {
        this.data.setMonth(this.data.getMonth() - 1);
        this.atualizarDatas();
        this.renderizar();
    }

    obterEventos() {
        
        $.ajax({
            url: '/eventos/listar',
            method: 'GET',
            data: {
                mes: this.mesAtual.numero + 1,
                ano: this.anoAtual
            },
            dataType: 'json',
            success: (response) => {
                if (response.status === 'sucesso' && Array.isArray(response.eventos)) {
                    renderizarEventos(response.eventos);
                }
            },
            error: (xhr, status, error) => {
                console.error('Erro ao obter eventos:', error);
            }

        });
    }

    renderizarEventos(eventos) {
        // TODO: Inserir os eventos no calendário
    }
}