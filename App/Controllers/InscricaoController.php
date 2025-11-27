<?php

/**
 * @file InscricaoController.php
 * @description Controlador responsável pelo gerenciamento das inscrições em turmas.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Core\Request;
use App\Models\Enumerations\UF;
use App\Models\Usuario;
use App\Models\UsuarioContato;
use App\Services\AutenticacaoService;
use App\Services\ViaCepService;
use Exception;
use Random\RandomException;
use App\Models\Enumerations\Turno;
use App\Models\Turma;
use App\Models\Enumerations\TurmaStatus;
use App\Models\Inscricao;
use App\Models\AlunoMatricula;
use App\Models\Enumerations\InscricaoStatus;
use App\Models\Enumerations\DiaSemana;
use App\Models\Enumerations\EnsinoModalidade;

/**
 * Classe InscricaoController
 *
 * Gerencia as inscrições em turmas
 *
 * @package App\Controllers
 * @extends Controller
 */
class InscricaoController extends Controller
{

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Renderiza a página de inscrições em turmas
     *
     * @return void
     */
    public function exibirIndex(): void
    {

        $breadcrumbs = [
            ['label' => 'Inscrições', 'url' => '/inscricoes']
        ];

        $this->renderizar('inscricoes/index', [
            'titulo' => 'Inscrições',
            'breadcrumbs' => $breadcrumbs,
            'turnos' => Turno::cases()
        ]);
    }


    // --- MÉTODOS DE REQUISIÇÃO ---

    /**
     * Função para filtrar turmas para solicitação de inscrição
     * 
     * @param Request $request
     * @return void
     */
    public function filtrarTurmas(Request $request): void
    {

        try {

            // TODO: Token CSRF

            // Obtém os dados do filtro
            $turno = $request->get('turno', null);
            $busca = $request->get('busca', null); // [nome da disciplina, nome do professor]
            $pagina = $request->get('pagina', 1);
            $itensPorPagina = $request->get('itens_por_pagina', 30);

            // Converte o turno para a enumeração correspondente
            $turno = $turno ? Turno::fromName(strtoupper($turno)) : null;

            // Monta o array de filtros
            $filtros = [
                'turno' => $turno,
                'busca' => $busca,
                'status' => null, // Pode ser implementado futuramente
                'limite' => $itensPorPagina,
                'pagina' => $pagina
            ];


            // --- FILTRAGEM ---

            // Verifica se o aluno possui matrícula ativa
            $aluno = AutenticacaoService::usuarioAutenticado()->aluno()->first();

            if (!$aluno) {
                throw new Exception('Usuário autenticado não é um aluno.');
            }

            $matricula = AlunoMatricula::buscarMatriculaAtivaPorAluno($aluno->obterId());

            if (!$matricula) {
                throw new Exception('Aluno não possui matrícula ativa.');
            }
            
            // Montando os filtros
            $query = Turma::query()
                ->with(['disciplina.componenteCurricular', 'periodo', 'professor.usuario', 'horarios.tempoAula'])
                ->withCount('inscricoes')
                ->ofertasAtivas() // Scope
                ->curso($matricula->obterCursoId());

            // 1. Filtro por Turno
            if ($filtros['turno']) {
                $query->turno($filtros['turno']);
            }

            // 2. Filtro por Busca (Disciplina, Sigla, Código ou Professor)
            if ($filtros['busca'] && trim($filtros['busca']) !== '') {
                $termoBusca = '%' . trim($filtros['busca']) . '%';
                $query->where(function($q) use ($termoBusca) {
                    // Buscar no nome da disciplina (componente curricular)
                    $q->whereHas('disciplina.componenteCurricular', function ($subQ) use ($termoBusca) {
                        $subQ->where('nome', 'LIKE', $termoBusca)
                            // OU na sigla do componente curricular
                            ->orWhere('sigla', 'LIKE', $termoBusca);
                    })
                    // OU buscar no código da turma
                    ->orWhere('codigo', 'LIKE', $termoBusca)
                    // OU buscar no nome do professor
                    ->orWhereHas('professor.usuario', function ($subQ) use ($termoBusca) {
                        $subQ->where('nome_civil', 'LIKE', $termoBusca)
                            ->orWhere('nome_social', 'LIKE', $termoBusca);
                    });
                });
            }

            // Buscar disciplinas aprovadas e inscrições do aluno
            $disciplinasAprovadas = Inscricao::where('inscricoes.aluno_matricula_id', $matricula->obterId())
                ->where('inscricoes.status', InscricaoStatus::APROVADO->name)
                ->join('turmas', 'inscricoes.turma_id', '=', 'turmas.id')
                ->pluck('turmas.disciplina_id')
                ->unique()
                ->toArray();
                
            $inscricoesAtivas = Inscricao::where('aluno_matricula_id', $matricula->obterId())
                ->whereIn('status', [InscricaoStatus::SOLICITADA->name, InscricaoStatus::DEFERIDA->name])
                ->pluck('turma_id')
                ->toArray();

            // 3. Excluir disciplinas já aprovadas
            if (!empty($disciplinasAprovadas)) {
                $query->whereNotIn('disciplina_id', $disciplinasAprovadas);
            }

            // 4. Paginação e Formatação
            $paginator = $query->paginate((int) ($request->get('limite') ?? 30));

            // Formatar dados para enviar apenas o necessário ao template
            $turmasFormatadas = collect($paginator->items())->map(function($turma) use ($disciplinasAprovadas, $inscricoesAtivas, $matricula) {
                $horarios = $turma->horarios->filter(function($horario) {
                    return $horario->tempoAula !== null;
                })->map(function($horario) {
                    $diaSemana = $horario->tempoAula->dia_semana;
                    // Converter enum DiaSemana para valor legível
                    if ($diaSemana instanceof DiaSemana) {
                        $diaSemana = $diaSemana->value;
                    } elseif (is_string($diaSemana)) {
                        // Tentar converter string do banco (SEGUNDA_FEIRA) para enum
                        $enumDia = DiaSemana::fromName($diaSemana);
                        $diaSemana = $enumDia ? $enumDia->value : $diaSemana;
                    } else {
                        $diaSemana = 'N/A';
                    }
                    
                    return [
                        'dia_semana' => $diaSemana,
                        'inicio' => $horario->tempoAula->hora_inicio->format('H:i'),
                        'fim' => $horario->tempoAula->hora_termino->format('H:i')
                    ];
                })->toArray();

                // Agrupar e mesclar horários sequenciais
                $horarioTexto = 'N/A';
                if (!empty($horarios)) {
                    $porDia = [];
                    
                    // Agrupar por dia da semana
                    foreach ($horarios as $h) {
                        $dia = $h['dia_semana'];
                        if ($dia === 'N/A') continue;
                        
                        if (!isset($porDia[$dia])) {
                            $porDia[$dia] = [];
                        }
                        $porDia[$dia][] = [
                            'inicio' => $h['inicio'],
                            'fim' => $h['fim']
                        ];
                    }
                    
                    // Mesclar horários sequenciais para cada dia
                    $horariosFormatados = [];
                    foreach ($porDia as $dia => $slots) {
                        // Ordenar por horário de início
                        usort($slots, function($a, $b) {
                            return strcmp($a['inicio'], $b['inicio']);
                        });
                        
                        // Mesclar horários com intervalo <= 10 minutos
                        $merged = [];
                        $grupoAtual = null;
                        
                        foreach ($slots as $slot) {
                            if ($grupoAtual === null) {
                                $grupoAtual = ['inicio' => $slot['inicio'], 'fim' => $slot['fim']];
                            } else {
                                // Calcular diferença em minutos
                                list($fimHora, $fimMin) = explode(':', $grupoAtual['fim']);
                                list($inicioHora, $inicioMin) = explode(':', $slot['inicio']);
                                $fimMinutos = (int)$fimHora * 60 + (int)$fimMin;
                                $inicioMinutos = (int)$inicioHora * 60 + (int)$inicioMin;
                                $diferenca = $inicioMinutos - $fimMinutos;
                                
                                if ($diferenca >= 0 && $diferenca <= 10) {
                                    // Estender o grupo
                                    $grupoAtual['fim'] = $slot['fim'];
                                } else {
                                    // Salvar grupo atual e iniciar novo
                                    $merged[] = $grupoAtual['inicio'] . ' - ' . $grupoAtual['fim'];
                                    $grupoAtual = ['inicio' => $slot['inicio'], 'fim' => $slot['fim']];
                                }
                            }
                        }
                        
                        // Adicionar último grupo
                        if ($grupoAtual !== null) {
                            $merged[] = $grupoAtual['inicio'] . ' - ' . $grupoAtual['fim'];
                        }
                        
                        $horariosFormatados[] = $dia . ': ' . implode(', ', $merged);
                    }
                    
                    $horarioTexto = !empty($horariosFormatados) ? implode('<br>', $horariosFormatados) : 'N/A';
                }

                // Converter turno para value
                $turnoValue = 'N/A';
                if ($turma->turno instanceof Turno) {
                    $turnoValue = $turma->turno->value;
                } elseif (is_string($turma->turno)) {
                    // Tentar converter string do banco (MANHA, TARDE, etc.) para enum
                    $enumTurno = Turno::fromName($turma->turno);
                    $turnoValue = $enumTurno ? $enumTurno->value : $turma->turno;
                }
                
                // Converter modalidade para value
                $modalidadeValue = 'N/A';
                if ($turma->modalidade instanceof EnsinoModalidade) {
                    $modalidadeValue = $turma->modalidade->value;
                } elseif (is_string($turma->modalidade)) {
                    // Tentar converter string do banco para enum
                    $enumModalidade = EnsinoModalidade::fromName($turma->modalidade);
                    $modalidadeValue = $enumModalidade ? $enumModalidade->value : $turma->modalidade;
                }

                // Determinar status da disciplina em relação ao aluno
                $disciplinaId = $turma->disciplina_id;
                $jaAprovado = in_array($disciplinaId, $disciplinasAprovadas);
                $inscricaoId = null;
                $temInscricao = false;
                
                // Verificar se aluno tem inscrição nesta turma
                $inscricao = Inscricao::where('aluno_matricula_id', $matricula->obterId())
                    ->where('turma_id', $turma->id)
                    ->whereIn('status', [InscricaoStatus::SOLICITADA->name, InscricaoStatus::DEFERIDA->name])
                    ->first();
                    
                if ($inscricao) {
                    $temInscricao = true;
                    $inscricaoId = $inscricao->id;
                }
                
                // Determinar prioridade de ordenação
                if ($temInscricao) {
                    $ordem = 1; // Inscrito/Solicitado - primeira prioridade
                } elseif ($jaAprovado) {
                    $ordem = 3; // Já aprovado/Concluído - última prioridade
                } else {
                    $ordem = 2; // Disponível para inscrição - prioridade intermediária
                }

                // Formatar nome do professor com prefixo e sufixo
                $nomeProfessor = $turma->professor->usuario->nome_social ?? $turma->professor->usuario->nome_civil ?? 'N/A';
                if ($nomeProfessor !== 'N/A') {
                    $pronome = $turma->professor->usuario->pronome ?? null;
                    $sufixo = ($pronome === 'ELA_DELA') ? 'ª' : '';
                    $nomeProfessor = 'Prof.' . $sufixo . ' ' . $nomeProfessor;
                }

                return [
                    'id' => $turma->id,
                    'codigo' => $turma->codigo,
                    'sigla' => $turma->disciplina->componenteCurricular->sigla ?? 'N/A',
                    'disciplina' => $turma->disciplina->componenteCurricular->nome ?? 'N/A',
                    'professor' => $nomeProfessor,
                    'turno' => $turnoValue,
                    'modalidade' => $modalidadeValue,
                    'horario' => $horarioTexto,
                    'horarios' => $horarios,
                    'inscricoes_count' => $turma->inscricoes_count ?? 0,
                    'ja_aprovado' => $jaAprovado ? 1 : 0,
                    'tem_inscricao' => $temInscricao ? 1 : 0,
                    'inscricao_id' => $inscricaoId,
                    'ordem' => $ordem,
                    'disciplina_id' => $disciplinaId
                ];
            })->sortBy('ordem')->values()->toArray();

            $this->responderJSON([
                'status' => 'sucesso', 
                'data' => $turmasFormatadas, 
                'current_page' => $paginator->currentPage(), 
                'last_page' => $paginator->lastPage()
            ]);

        } catch (Exception $e) {
            $this->responderJSON(['status' => 'erro', 'mensagem' => 'Erro ao filtrar turmas: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Solicitar inscrição em uma turma
     * 
     * @param Request $request
     * @return void
     */
    public function solicitarInscricao(Request $request): void
    {
        try {
            $turmaId = $request->post('turma_id');
            
            if (!$turmaId) {
                throw new Exception('Turma não informada.');
            }

            $aluno = AutenticacaoService::usuarioAutenticado()->aluno()->first();
            if (!$aluno) {
                throw new Exception('Usuário autenticado não é um aluno.');
            }

            $matricula = AlunoMatricula::buscarMatriculaAtivaPorAluno($aluno->obterId());
            if (!$matricula) {
                throw new Exception('Aluno não possui matrícula ativa.');
            }

            // Verificar se já existe inscrição para esta turma
            $inscricaoExistente = Inscricao::where('aluno_matricula_id', $matricula->obterId())
                ->where('turma_id', $turmaId)
                ->first();

            if ($inscricaoExistente) {
                throw new Exception('Você já possui uma solicitação para esta turma.');
            }

            // Buscar turma solicitada com seus horários
            $turmaSolicitada = Turma::with(['horarios.tempoAula', 'disciplina.componenteCurricular'])
                ->find($turmaId);
            
            if (!$turmaSolicitada) {
                throw new Exception('Turma não encontrada.');
            }

            // Buscar inscrições ativas do aluno (SOLICITADA, DEFERIDA)
            $inscricoesAtivas = Inscricao::where('aluno_matricula_id', $matricula->obterId())
                ->whereIn('status', [InscricaoStatus::SOLICITADA->name, InscricaoStatus::DEFERIDA->name])
                ->with(['turma.horarios.tempoAula', 'turma.disciplina.componenteCurricular'])
                ->get();

            error_log("DEBUG: Verificando conflitos para turma ID: {$turmaId}");
            error_log("DEBUG: Inscrições ativas encontradas: " . $inscricoesAtivas->count());
            error_log("DEBUG: Horários da turma solicitada: " . $turmaSolicitada->horarios->count());

            // Verificar conflitos de horário
            $conflitos = [];
            foreach ($inscricoesAtivas as $inscricao) {
                $turmaAtiva = $inscricao->turma;
                
                if (!$turmaAtiva) continue;
                
                foreach ($turmaSolicitada->horarios as $horarioSolicitado) {
                    if (!$horarioSolicitado->tempoAula) continue;
                    
                    $diaSemanaEnum = $horarioSolicitado->tempoAula->dia_semana;
                    
                    foreach ($turmaAtiva->horarios as $horarioAtivo) {
                        if (!$horarioAtivo->tempoAula) continue;
                        
                        // Verificar se é o mesmo dia da semana
                        $diaAtivo = $horarioAtivo->tempoAula->dia_semana;
                        
                        error_log("DEBUG: Comparando dias - Solicitado: {$diaSemanaEnum} vs Ativo: {$diaAtivo}");
                        
                        if ($diaSemanaEnum === $diaAtivo) {
                            // Converter horários para string no formato HH:MM para comparação
                            $inicioSolicitado = $horarioSolicitado->tempoAula->hora_inicio;
                            $fimSolicitado = $horarioSolicitado->tempoAula->hora_termino;
                            $inicioAtivo = $horarioAtivo->tempoAula->hora_inicio;
                            $fimAtivo = $horarioAtivo->tempoAula->hora_termino;
                            
                            // Converter para string se for objeto DateTime
                            $inicioSolStr = is_object($inicioSolicitado) ? $inicioSolicitado->format('H:i') : $inicioSolicitado;
                            $fimSolStr = is_object($fimSolicitado) ? $fimSolicitado->format('H:i') : $fimSolicitado;
                            $inicioAtvStr = is_object($inicioAtivo) ? $inicioAtivo->format('H:i') : $inicioAtivo;
                            $fimAtvStr = is_object($fimAtivo) ? $fimAtivo->format('H:i') : $fimAtivo;
                            
                            error_log("DEBUG: Horários - Solicitado: {$inicioSolStr}-{$fimSolStr} vs Ativo: {$inicioAtvStr}-{$fimAtvStr}");
                            
                            // Há conflito se os horários se sobrepõem
                            // Conflito: início_A < fim_B AND fim_A > início_B
                            if ($inicioSolStr < $fimAtvStr && $fimSolStr > $inicioAtvStr) {
                                error_log("DEBUG: CONFLITO DETECTADO!");
                                $diaSemana = DiaSemana::fromName($diaSemanaEnum);
                                $conflitos[] = [
                                    'disciplina' => $turmaAtiva->disciplina->componenteCurricular->nome ?? 'Disciplina',
                                    'sigla' => $turmaAtiva->disciplina->componenteCurricular->sigla ?? 'N/A',
                                    'codigo' => $turmaAtiva->codigo,
                                    'dia' => $diaSemana ? $diaSemana->value : $diaSemanaEnum,
                                    'horario' => $inicioAtvStr . ' - ' . $fimAtvStr
                                ];
                                break 2; // Sair dos dois loops
                            }
                        }
                    }
                }
            }

            // Se houver conflitos, retornar erro com detalhes
            if (!empty($conflitos)) {
                $conflito = $conflitos[0];
                $mensagem = "Houve um choque de horários com a disciplina \"{$conflito['sigla']} - {$conflito['disciplina']}\". Por favor, cancele a inscrição conflitante antes de prosseguir.";
                
                $this->responderJSON([
                    'status' => 'erro',
                    'mensagem' => $mensagem,
                    'conflito' => $conflito
                ], 400);
                return;
            }

            // Criar nova inscrição
            $inscricao = new Inscricao();
            $inscricao->aluno_matricula_id = $matricula->obterId();
            $inscricao->turma_id = $turmaId;
            $inscricao->status = 'SOLICITADA';
            $inscricao->save();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Solicitação de inscrição enviada com sucesso!'
            ]);

        } catch (Exception $e) {
            $this->responderJSON(['status' => 'erro', 'mensagem' => $e->getMessage()], 400);
        }
    }

    /**
     * Cancelar inscrição em uma turma
     * 
     * @param Request $request
     * @return void
     */
    public function cancelarInscricao(Request $request): void
    {
        try {
            $inscricaoId = $request->post('inscricao_id');
            
            if (!$inscricaoId) {
                throw new Exception('Inscrição não informada.');
            }

            $aluno = AutenticacaoService::usuarioAutenticado()->aluno()->first();
            if (!$aluno) {
                throw new Exception('Usuário autenticado não é um aluno.');
            }

            $matricula = AlunoMatricula::buscarMatriculaAtivaPorAluno($aluno->obterId());
            if (!$matricula) {
                throw new Exception('Aluno não possui matrícula ativa.');
            }

            // Buscar inscrição
            $inscricao = Inscricao::where('id', $inscricaoId)
                ->where('aluno_matricula_id', $matricula->obterId())
                ->first();

            if (!$inscricao) {
                throw new Exception('Inscrição não encontrada ou não pertence a você.');
            }

            // Deletar inscrição
            $inscricao->delete();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Inscrição cancelada com sucesso!'
            ]);

        } catch (Exception $e) {
            $this->responderJSON(['status' => 'erro', 'mensagem' => $e->getMessage()], 400);
        }
    }
}
