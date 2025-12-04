document.addEventListener('DOMContentLoaded', function() {

    // Inicializa o DataGrid para listar ofertas (página index)
    try {
        if (document.getElementById('container-inscricoes')) {
            const datagrid = new DataGrid({
                endpoint: '/inscricoes/filtrar',
                container: '#container-inscricoes',
                template: '#template-lista-item-inscricao',
                campos: {
                    busca: '#busca-inscricao',
                    turno: '#turno-input'
                },
                metodo: 'GET',
                itensPorPagina: 30
            });

            datagrid.carregar();

            // Ações nos itens
            document.getElementById('container-inscricoes').addEventListener('click', function(event) {
                const btnSolicitar = event.target.closest('button[data-action="solicitar"]');
                const btnCancelar = event.target.closest('button[data-action="cancelar"]');
                const btnVisualizar = event.target.closest('button[data-action="visualizar"]');

                if (btnVisualizar) return; // link já trata

                const item = event.target.closest('.inscricao-item');
                if (!item) return;
                const id = item.getAttribute('data-id');

                if (btnSolicitar) {
                    event.preventDefault();
                    const template = document.getElementById('template-inscricao-modal-deferir');
                    if (!template) return;
                    const clone = template.content.cloneNode(true);
                    document.body.appendChild(clone);
                    const modalEl = document.getElementById('inscricao-modal-deferir');
                    const form = document.getElementById('inscricao-form-deferir');
                    form.action = `/inscricoes/${id}/solicitar`;
                    form.querySelector('input[name="inscricao_id"]')?.setAttribute('value', id);
                    var modal = new Modal('#inscricao-modal-deferir');
                    modal.abrir();

                    // inicializa submit com Formulario se existir
                    var formulario = new Formulario('#inscricao-form-deferir', {
                        onSuccess: function(resp) {
                            modal.fechar();
                            if (typeof notificador !== 'undefined') notificador.sucesso(resp.mensagem || 'Solicitação enviada.');
                            datagrid.recarregar();
                        }
                    });
                }

                if (btnCancelar) {
                    event.preventDefault();
                    const template = document.getElementById('template-inscricao-modal-indeferir');
                    if (!template) return;
                    const clone = template.content.cloneNode(true);
                    document.body.appendChild(clone);
                    const modalEl = document.getElementById('inscricao-modal-indeferir');
                    const form = document.getElementById('inscricao-form-indeferir');
                    form.action = `/inscricoes/${id}/cancelar`;
                    form.querySelector('input[name="inscricao_id"]')?.setAttribute('value', id);
                    var modal = new Modal('#inscricao-modal-indeferir');
                    modal.abrir();

                    var formulario = new Formulario('#inscricao-form-indeferir', {
                        onSuccess: function(resp) {
                            modal.fechar();
                            if (typeof notificador !== 'undefined') notificador.sucesso(resp.mensagem || 'Solicitação cancelada.');
                            datagrid.recarregar();
                        }
                    });
                }
            });
        }
    } catch (e) {
        console.error('[Inscricoes] Erro inicializando datagrid:', e);
    }

    // Carregar calendário de horários (dia/semana)
    try {
        const calendario = document.getElementById('calendario-conteudo');
        const btnDay = document.getElementById('view-day');
        const btnWeek = document.getElementById('view-week');
        const btnMonth = document.getElementById('view-month');

        function buildGrid(data, view = 'week') {
            // days order and short labels
            const days = [
                { full: 'Segunda-feira', short: 'Seg' },
                { full: 'Terça-feira', short: 'Ter' },
                { full: 'Quarta-feira', short: 'Qua' },
                { full: 'Quinta-feira', short: 'Qui' },
                { full: 'Sexta-feira', short: 'Sex' },
                { full: 'Sábado', short: 'Sáb' }
            ];

            // coletar intervalos únicos (inicio-fim)
            const slotsMap = {};
            data.forEach(item => {
                const key = `${item.inicio}-${item.fim}`;
                slotsMap[key] = { inicio: item.inicio, fim: item.fim };
            });

            const slots = Object.values(slotsMap).sort((a,b) => a.inicio.localeCompare(b.inicio));

            // construir tabela
            const table = document.createElement('div');
            table.className = 'w-full overflow-x-auto';

            // header
            const header = document.createElement('div');
            header.className = 'grid grid-cols-7 border-t border-gray-200 sticky top-0 left-0 w-full bg-white z-10';
            const empty = document.createElement('div'); empty.className = 'p-3.5 flex items-center justify-center text-sm font-medium text-gray-900'; empty.textContent = 'Horário'; header.appendChild(empty);
            days.forEach(d => {
                const h = document.createElement('div');
                h.className = 'p-3.5 flex items-center justify-center text-sm font-medium text-gray-900';
                h.textContent = d.short;
                h.setAttribute('title', d.full);
                header.appendChild(h);
            });
            table.appendChild(header);

            // rows
            const grid = document.createElement('div');
            grid.className = 'grid grid-cols-7 w-full';

            slots.forEach(slot => {
                // time cell
                const timeCell = document.createElement('div');
                timeCell.className = 'h-12 p-2 border-t border-r border-gray-200 flex items-start';
                timeCell.innerHTML = `<span class="text-xs font-semibold text-gray-400">${slot.inicio}</span>`;
                grid.appendChild(timeCell);

                // day cells
                days.forEach(dayObj => {
                    const day = dayObj.full;
                    const cell = document.createElement('div');
                    cell.className = 'h-12 p-2 border-t border-r border-gray-200 transition-all hover:bg-stone-100';

                    // encontrar turmas que batem nesse dia e horário
                    const matches = data.filter(it => (it.dia_semana === day) && it.inicio === slot.inicio && it.fim === slot.fim);
                    if (matches.length === 0) {
                        // placeholder para horário livre
                        const placeholder = document.createElement('div');
                        placeholder.className = 'text-xs text-gray-400 italic';
                        placeholder.textContent = 'Livre';
                        cell.appendChild(placeholder);
                    } else {
                        matches.forEach(m => {
                            if (!m.turma_id) {
                                const placeholder = document.createElement('div');
                                placeholder.className = 'text-xs text-gray-500 italic mb-1';
                                placeholder.textContent = m.disciplina || '—';
                                cell.appendChild(placeholder);
                                return;
                            }

                            const card = document.createElement('div');
                            card.className = 'rounded p-2 border-l-2 bg-indigo-50 mb-1';
                            card.dataset.turmaId = m.turma_id;

                            const title = document.createElement('p');
                            title.className = 'text-xs font-normal text-gray-900 mb-1';
                            title.textContent = m.disciplina || 'Turma';

                            const time = document.createElement('p');
                            time.className = 'text-xs font-semibold text-indigo-600';
                            time.textContent = `${m.inicio} - ${m.fim}`;

                            const vagas = document.createElement('p');
                            vagas.className = 'text-xs text-gray-600 mt-1';
                            vagas.textContent = `Vagas: ${m.vagas_disponiveis ?? '-'} livre(s) — ${m.ocupadas ?? 0} ocupada(s)`;

                            const actions = document.createElement('div');
                            actions.className = 'mt-2 flex gap-2';

                            const btn = document.createElement('button');
                            btn.className = 'px-2 py-1 text-xs rounded bg-white border';
                            btn.type = 'button';
                            if (m.solicitado_pelo_usuario) {
                                btn.textContent = 'Cancelar';
                                btn.dataset.action = 'cancelar';
                            } else {
                                btn.textContent = 'Solicitar';
                                btn.dataset.action = 'solicitar';
                            }

                            // clique no card abre detalhe simples
                            card.addEventListener('click', function(e) {
                                e.stopPropagation();
                                const html = `${m.disciplina || 'Turma'} (${m.codigo || ''})\nProfessor: ${m.professor || 'N/A'}\n${m.inicio} - ${m.fim}`;
                                // substituir por modal no futuro
                                alert(html);
                            });

                            // ação do botão (solicitar / cancelar)
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();

                                if (btn.dataset.action === 'solicitar') {
                                    const template = document.getElementById('template-inscricao-modal-deferir');
                                    if (!template) return;
                                    document.body.appendChild(template.content.cloneNode(true));
                                    const modal = new Modal('#inscricao-modal-deferir');
                                    const form = document.getElementById('inscricao-form-deferir');
                                    form.action = `/inscricoes/${m.turma_id}/solicitar`;
                                    form.querySelector('input[name="inscricao_id"]')?.setAttribute('value', m.turma_id);
                                    modal.abrir();
                                    new Formulario('inscricao-form-deferir', {
                                        onSuccess: function(resp) {
                                            modal.fechar();
                                            if (typeof notificador !== 'undefined') notificador.sucesso(resp.mensagem || 'Solicitação enviada.');
                                            // recarregar a página para atualizar calendário / lista
                                            window.location.reload();
                                        }
                                    });
                                } else {
                                    // cancelar solicitação
                                    const template = document.getElementById('template-inscricao-modal-indeferir');
                                    if (!template) return;
                                    document.body.appendChild(template.content.cloneNode(true));
                                    const modal = new Modal('#inscricao-modal-indeferir');
                                    const form = document.getElementById('inscricao-form-indeferir');
                                    // preferir id da inscricao do usuário quando disponível
                                    const insId = m.inscricao_usuario?.id || m.turma_id;
                                    form.action = `/inscricoes/${insId}/cancelar`;
                                    form.querySelector('input[name="inscricao_id"]')?.setAttribute('value', insId);
                                    modal.abrir();
                                    new Formulario('inscricao-form-indeferir', {
                                        onSuccess: function(resp) {
                                            modal.fechar();
                                            if (typeof notificador !== 'undefined') notificador.sucesso(resp.mensagem || 'Solicitação cancelada.');
                                            window.location.reload();
                                        }
                                    });
                                }
                            });

                            actions.appendChild(btn);
                            card.appendChild(title);
                            card.appendChild(time);
                            card.appendChild(vagas);
                            card.appendChild(actions);
                            cell.appendChild(card);
                        });
                    }

                    grid.appendChild(cell);
                });
            });

            table.appendChild(grid);
            return table;
        }

        if (calendario) {
            calendario.innerHTML = '<p class="text-sm text-gray-500">Carregando calendário...</p>';
            fetch('/inscricoes/calendario', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
                .then(r => {
                    if (r.status === 403) {
                        calendario.innerHTML = '<p class="text-sm text-red-500">Acesso ao calendário proibido (403).</p>';
                        return r.json().then(j => { throw new Error(j.erro || 'Acesso proibido'); });
                    }
                    return r.json();
                })
                .then(resp => {
                    if (!resp || !resp.data) {
                        calendario.innerHTML = '<p class="text-sm text-gray-500">Nenhum horário encontrado.</p>';
                        return;
                    }

                    let view = 'week';
                    function render() {
                        calendario.innerHTML = '';
                        const node = buildGrid(resp.data, view);
                        calendario.appendChild(node);
                    }

                    // attach view buttons
                    btnDay?.addEventListener('click', function() { view = 'day'; render(); });
                    btnWeek?.addEventListener('click', function() { view = 'week'; render(); });
                    btnMonth?.addEventListener('click', function() { view = 'month'; render(); });

                    render();
                })
                .catch(() => {
                    calendario.innerHTML = '<p class="text-sm text-gray-500">Erro ao carregar calendário.</p>';
                });
        }
    } catch (e) {
        console.error('[Inscricoes] Erro ao carregar calendário:', e);
    }

    // Inicializações em páginas administrativas (solicitacoes / parcial / resultado)
    try {
        if (document.getElementById('container-solicitacoes')) {
            const datagridSolic = new DataGrid({ endpoint: '/inscricoes/solicitacoes/filtrar', container: '#container-solicitacoes', template: '#template-lista-item-inscricao', metodo: 'GET' });
            datagridSolic.carregar();

            document.getElementById('container-solicitacoes').addEventListener('click', function(event) {
                const btnDeferir = event.target.closest('button[data-action="solicitar"]');
                const btnIndeferir = event.target.closest('button[data-action="cancelar"]');
                const item = event.target.closest('.inscricao-item');
                if (!item) return;
                const id = item.getAttribute('data-id');

                if (btnDeferir) {
                    const template = document.getElementById('template-inscricao-modal-deferir');
                    document.body.appendChild(template.content.cloneNode(true));
                    const modal = new Modal('#inscricao-modal-deferir');
                    document.getElementById('inscricao-form-deferir').action = `/inscricoes/${id}/deferir`;
                    modal.abrir();
                    new Formulario('#inscricao-form-deferir', { onSuccess: () => datagridSolic.recarregar() });
                }

                if (btnIndeferir) {
                    const template = document.getElementById('template-inscricao-modal-indeferir');
                    document.body.appendChild(template.content.cloneNode(true));
                    const modal = new Modal('#inscricao-modal-indeferir');
                    document.getElementById('inscricao-form-indeferir').action = `/inscricoes/${id}/indeferir`;
                    modal.abrir();
                    new Formulario('#inscricao-form-indeferir', { onSuccess: () => datagridSolic.recarregar() });
                }
            });
        }
    } catch (e) {
        console.error('[Inscricoes] Erro inicializando solicitacoes:', e);
    }

});
