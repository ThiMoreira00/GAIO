<?php

/**
 * @file TurmaController.php
 * @description Controlador responsável pelo gerenciamento das turmas no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Core\Request;
use App\Models\Turma;
use App\Models\Disciplina;
use App\Models\Curso;
use App\Models\ComponenteCurricular;
use App\Models\PeriodoLetivo;
use App\Models\Professor;
use App\Models\GradeHoraria;
use App\Models\Inscricao;
use App\Models\Log;
use App\Models\Enumerations\TurmaStatus;
use App\Models\Enumerations\Turno;
use App\Models\Enumerations\EnsinoModalidade;
use App\Models\Enumerations\InscricaoStatus;
use App\Services\AutenticacaoService;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * Classe TurmaController
 *
 * Gerencia as turmas no sistema
 *
 * @package App\Controllers
 * @extends Controller
 */
class TurmaController extends Controller
{

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Renderiza a página inicial de turmas
     *
     * @return void
     */
    public function exibirIndex(): void
    {
        // Breadcrumbs = links de navegação
        $breadcrumbs = [
            ['label' => 'Turmas', 'url' => '/turmas']
        ];

        // Buscar dados para filtros com cache (válido por 10 minutos)
        $periodos = Cache::remember('turmas_periodos_filtro', 600, function () {
            return PeriodoLetivo::obterTodos();
        });
        
        $cursos = Cache::remember('turmas_cursos_filtro', 600, function () {
            return Curso::obterTodos();
        });
        
        $professores = Cache::remember('turmas_professores_filtro', 600, function () {
            return Professor::obterTodos();
        });
        
        $turnos = Turno::cases();
        $modalidades = EnsinoModalidade::cases();
        $statusList = TurmaStatus::cases();

        $usuario = AutenticacaoService::usuarioAutenticado();

        $gruposPermissao = $usuario->grupos;

        foreach ($gruposPermissao as $grupo) {

            switch (strtoupper($grupo->obterNome())) {
                case 'ADMINISTRADOR':
                    $this->exibirIndexAdministrador();
                    return;

                case 'ALUNO':
                    // Renderiza a página inicial do aluno
                    $this->exibirIndexAluno();
                    return;

                case 'PROFESSOR':
                    // Renderiza a página inicial do professor
                    $this->exibirIndexProfessor();
                    return;
            }
        }

        // Renderiza a pagina inicial de turmas
        /* $this->renderizar('turmas/index', [
            'titulo' => 'Turmas',
            'periodos' => $periodos,
            'cursos' => $cursos,
            'professores' => $professores,
            'turnos' => $turnos,
            'modalidades' => $modalidades,
            'statusList' => $statusList,
            'breadcrumbs' => $breadcrumbs
        ]);
        */
    }

    /**
     * Renderiza a página de turmas do aluno
     *
     * @return void
     */
    public function exibirIndexAluno(): void
    {
        $breadcrumbs = [
            ['label' => 'Turmas', 'url' => '/turmas']
        ];

        $this->renderizar('turmas/aluno/index', [
            'titulo' => 'Minhas Turmas',
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    /**
     * Renderiza a página de turmas do professor
     *
     * @return void
     */
    public function exibirIndexProfessor(): void
    {
        $breadcrumbs = [
            ['label' => 'Minhas Turmas', 'url' => '/turmas/professor']
        ];

        $this->renderizar('turmas/professor/index', [
            'titulo' => 'Minhas Turmas - Professor',
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    /**
     * Renderiza a página de turmas do administrador
     *
     * @return void
     */
    public function exibirIndexAdministrador(): void
    {
        $breadcrumbs = [
            ['label' => 'Turmas', 'url' => '/turmas']
        ];

        // Renderiza a pagina inicial de turmas
        $this->renderizar('turmas/administrador/index', [
            'titulo' => 'Turmas',
            'breadcrumbs' => $breadcrumbs
        ]);
    }


    /**
     * Renderiza a página de visualização da turma pelo aluno
     *
     * @param Request $request
     * @return void
     */
    public function exibirTurmaAluno(Request $request): void
    {
        try {
            $id = $request->get('id');
            if (!$id) {
                throw new Exception('ID da turma é obrigatório.');
            }

            $turma = Turma::buscarPorId((int)$id);
            if (!$turma) {
                throw new Exception('Turma não encontrada.');
            }

            // Verificar se o aluno tem acesso a esta turma
            $usuario = AutenticacaoService::usuarioAutenticado();
            $aluno = $usuario->aluno()->first();
            
            if (!$aluno) {
                throw new Exception('Aluno não encontrado.');
            }

            // Obter IDs de todas as matrículas do aluno
            $matriculaIds = $aluno->matriculas()->pluck('id')->toArray();

            if (empty($matriculaIds)) {
                throw new Exception('Nenhuma matrícula encontrada para este aluno.');
            }

            // Verificar se o aluno está inscrito na turma com status apropriado
            $inscricao = Inscricao::where('turma_id', $turma->obterId())
                ->whereIn('aluno_matricula_id', $matriculaIds)
                ->whereIn('status', [
                    InscricaoStatus::CURSANDO->name,
                    InscricaoStatus::APROVADO->name,
                    InscricaoStatus::REPROVADO_FALTA->name,
                    InscricaoStatus::REPROVADO_MEDIA->name
                ])
                ->first();

            if (!$inscricao) {
                throw new Exception('Você não tem permissão para acessar esta turma.');
            }

            // Carregar relacionamentos
            $disciplina = $turma->disciplina()->first();
            $periodo = $turma->periodo()->first();
            $professor = $turma->professor()->first();
            
            $disciplinaNome = $disciplina && $disciplina->componenteCurricular
                ? $disciplina->componenteCurricular()->first()->obterNome()
                : 'N/A';
            
            if ($professor && $professor->usuario) {
                $usuarioProf = $professor->usuario()->first();
                $professorNome = $usuarioProf->obterNomeSocial() ?: $usuarioProf->obterNomeCivil();
            } else {
                $professorNome = 'N/A';
            }
            
            $periodoNome = $periodo ? $periodo->obterSigla() : 'N/A';

            $breadcrumbs = [
                ['label' => 'Turmas', 'url' => '/turmas'],
                ['label' => $turma->obterCodigo(), 'url' => sprintf("/turmas/%d", $turma->obterId())]
            ];

            $this->renderizar('turmas/aluno/visualizar', [
                'titulo' => $disciplinaNome . ' - ' . $turma->obterCodigo(),
                'turma' => $turma,
                'disciplina_nome' => $disciplinaNome,
                'periodo_nome' => $periodoNome,
                'professor_nome' => $professorNome,
                'breadcrumbs' => $breadcrumbs
            ]);

        } catch (Exception $exception) {
            flash()->erro($exception->getMessage());
            header('Location: /turmas');
            exit();
        }
    }

    /**
     * Renderiza a página de visualização da turma pelo professor
     *
     * @param Request $request
     * @return void
     */
    public function exibirTurmaProfessor(Request $request): void
    {
        try {
            $id = $request->get('id');
            if (!$id) {
                throw new Exception('ID da turma é obrigatório.');
            }

            $turma = Turma::buscarPorId((int)$id);
            if (!$turma) {
                throw new Exception('Turma não encontrada.');
            }

            // Verificar se o professor tem acesso a esta turma
            $usuario = AutenticacaoService::usuarioAutenticado();
            $professor = $usuario->professor()->first();
            
            if (!$professor || $turma->obterProfessorId() !== $professor->obterId()) {
                throw new Exception('Você não tem permissão para acessar esta turma.');
            }

            // Carregar relacionamentos
            $disciplina = $turma->disciplina()->first();
            $periodo = $turma->periodo()->first();
            
            $disciplinaNome = $disciplina && $disciplina->componenteCurricular
                ? $disciplina->componenteCurricular()->first()->obterNome()
                : 'N/A';
            
            $periodoNome = $periodo ? $periodo->obterSigla() : 'N/A';

            // Verificar permissões
            $permissoesUsuario = $_SESSION['usuario_permissoes'] ?? [];
            $permissoes = [
                'visualizar_alunos' => true,
                'adicionar_alunos' => in_array('GAIO_TURMAS_ALUNOS_ADICIONAR', $permissoesUsuario),
                'remover_alunos' => in_array('GAIO_TURMAS_ALUNOS_REMOVER', $permissoesUsuario),
                'visualizar_avaliacoes' => true,
                'gerenciar_criterios' => in_array('GAIO_TURMAS_AVALIACAO_CRITERIOS_GERENCIAR', $permissoesUsuario),
                'lancar_notas' => in_array('GAIO_TURMAS_AVALIACAO_NOTAS_LANCAR', $permissoesUsuario),
                'alterar_notas' => in_array('GAIO_TURMAS_AVALIACAO_NOTAS_ALTERAR', $permissoesUsuario),
                'visualizar_frequencias' => true,
                'configurar_frequencias' => in_array('GAIO_TURMAS_FREQUENCIAS_CONFIGURAR', $permissoesUsuario),
                'visualizar_conteudos' => true,
                'editar' => false,
                'arquivar' => false
            ];

            $breadcrumbs = [
                ['label' => 'Minhas Turmas', 'url' => '/turmas/professor'],
                ['label' => $turma->obterCodigo(), 'url' => sprintf("/turmas/professor/%d", $turma->obterId())]
            ];

            $this->renderizar('turmas/professor/visualizar', [
                'titulo' => $disciplinaNome . ' - ' . $turma->obterCodigo(),
                'turma' => $turma,
                'disciplina_nome' => $disciplinaNome,
                'periodo_nome' => $periodoNome,
                'professor_nome' => $usuario->obterNomeSocial() ?: $usuario->obterNomeCivil(),
                'permissoes' => $permissoes,
                'breadcrumbs' => $breadcrumbs
            ]);

        } catch (Exception $exception) {
            flash()->erro($exception->getMessage());
            header('Location: /turmas/professor');
            exit();
        }
    }

    /**
     * Renderiza a página de visualização da turma pelo administrador
     *
     * @param Request $request
     * @return void
     */
    public function exibirTurmaAdministrador(Request $request): void
    {
        // Redireciona para a visualização padrão
        $id = $request->get('id');
        header("Location: /turmas/{$id}");
        exit();
    }

    /**
     * Renderiza a página de visualização de uma turma específica
     *
     * @param Request $request
     * @return void
     */
    public function exibirTurma(Request $request): void
    {
        try {
            $id = $request->get('id');

            if (!$id) {
                throw new Exception('ID da turma é obrigatório.');
            }

            // Converter ID para inteiro
            $id = (int)$id;

            // Buscar a turma
            $turma = Turma::buscarPorId($id);
            
            if (!$turma) {
                throw new Exception('Turma não encontrada.');
            }

            // Carregar relacionamentos necessários
            $disciplina = $turma->disciplina()->first();
            $periodo = $turma->periodo()->first();
            $professor = $turma->professor()->first();
            
            // Obter nome da disciplina através do componente curricular
            $disciplinaNome = 'N/A';
            if ($disciplina) {
                $componenteCurricular = $disciplina->componenteCurricular()->first();
                if ($componenteCurricular) {
                    $disciplinaNome = $componenteCurricular->obterNome();
                }
            }
            
            // Obter nome do professor através do usuário
            $professorNome = 'N/A';
            if ($professor) {
                $usuario_professor = $professor->usuario()->first();
                if ($usuario_professor) {
                    $professorNome = $usuario_professor->obterNomeSocial() ?: $usuario_professor->obterNomeCivil();
                }
            }
            
            // Obter nome do período através da sigla
            $periodoNome = $periodo ? $periodo->obterSigla() : 'N/A';

            // Obter usuário autenticado
            $usuario = AutenticacaoService::usuarioAutenticado();

            // Verificar permissões para abas
            $permissoesUsuario = $_SESSION['usuario_permissoes'] ?? [];
            $permissoes = [
                'visualizar_alunos' => in_array('GAIO_TURMAS_GERENCIAR', $permissoesUsuario),
                'adicionar_alunos' => in_array('GAIO_TURMAS_ALUNOS_ADICIONAR', $permissoesUsuario),
                'remover_alunos' => in_array('GAIO_TURMAS_ALUNOS_REMOVER', $permissoesUsuario),
                'visualizar_avaliacoes' => in_array('GAIO_TURMAS_AVALIACAO_NOTAS_LANCAR', $permissoesUsuario) || in_array('GAIO_TURMAS_AVALIACAO_NOTAS_PROPRIAS_VISUALIZAR', $permissoesUsuario),
                'gerenciar_criterios' => in_array('GAIO_TURMAS_AVALIACAO_CRITERIOS_GERENCIAR', $permissoesUsuario),
                'lancar_notas' => in_array('GAIO_TURMAS_AVALIACAO_NOTAS_LANCAR', $permissoesUsuario),
                'alterar_notas' => in_array('GAIO_TURMAS_AVALIACAO_NOTAS_ALTERAR', $permissoesUsuario),
                'visualizar_frequencias' => in_array('GAIO_TURMAS_FREQUENCIAS_VISUALIZAR', $permissoesUsuario) || in_array('GAIO_TURMAS_FREQUENCIAS_PROPRIAS_VISUALIZAR', $permissoesUsuario),
                'configurar_frequencias' => in_array('GAIO_TURMAS_FREQUENCIAS_CONFIGURAR', $permissoesUsuario),
                'visualizar_conteudos' => in_array('GAIO_TURMAS_CONTEUDO_VISUALIZAR', $permissoesUsuario),
                'editar' => in_array('GAIO_TURMAS_EDITAR', $permissoesUsuario),
                'arquivar' => in_array('GAIO_TURMAS_ARQUIVAR', $permissoesUsuario),
                'confirmar' => in_array('GAIO_TURMAS_CONFIRMAR', $permissoesUsuario),
                'finalizar' => in_array('GAIO_TURMAS_FINALIZAR', $permissoesUsuario),
                'liberar' => in_array('GAIO_TURMAS_LIBERAR', $permissoesUsuario),
            ];

            // Breadcrumbs = links de navegação
            $breadcrumbs = [
                ['label' => 'Turmas', 'url' => '/turmas'],
                ['label' => $turma->obterCodigo(), 'url' => sprintf("/turmas/%d", $turma->obterId())]
            ];

            // Renderiza a página de visualização (com base na permissão)

            $usuario = AutenticacaoService::usuarioAutenticado();

            $usuarioGrupos = $usuario->grupos;

            foreach ($usuarioGrupos as $grupo) {

                switch (strtoupper($grupo->obterNome())) {
                    case 'ADMINISTRADOR':
                        $this->renderizar('turmas/administrador/visualizar', [
                            'titulo' => $turma->obterCodigo(),
                            'turma' => $turma,
                            'disciplina_nome' => $disciplinaNome,
                            'periodo_nome' => $periodoNome,
                            'professor_nome' => $professorNome,
                            'permissoes' => $permissoes,
                            'breadcrumbs' => $breadcrumbs
                        ]);
                        return;

                    case 'PROFESSOR': 
                        $this->renderizar('turmas/professor/visualizar', [
                            'titulo' => $turma->obterCodigo(),
                            'turma' => $turma,
                            'disciplina_nome' => $disciplinaNome,
                            'periodo_nome' => $periodoNome,
                            'professor_nome' => $professorNome,
                            'permissoes' => $permissoes,
                            'breadcrumbs' => $breadcrumbs
                        ]);
                        return;

                    case 'ALUNO':
                        $this->renderizar('turmas/aluno/visualizar', [
                            'titulo' => $turma->obterCodigo(),
                            'turma' => $turma,
                            'disciplina_nome' => $disciplinaNome,
                            'periodo_nome' => $periodoNome,
                            'professor_nome' => $professorNome,
                            'permissoes' => $permissoes,
                            'breadcrumbs' => $breadcrumbs
                        ]);
                        return;
                }
            }


            $this->renderizar('turmas/visualizar', [
                'titulo' => $turma->obterCodigo(),
                'turma' => $turma,
                'disciplina_nome' => $disciplinaNome,
                'periodo_nome' => $periodoNome,
                'professor_nome' => $professorNome,
                'permissoes' => $permissoes,
                'breadcrumbs' => $breadcrumbs
            ]);

        } catch (Exception $exception) {
            flash()->erro($exception->getMessage() ?? 'Erro ao carregar a turma.');
            header('Location: /turmas');
            exit();
        }
    }


    // --- MÉTODOS DE REQUISIÇÕES ---

    /**
     * Função para filtrar turmas do aluno
     *
     * @param Request $request
     * @return void
     */
    public function filtrarTurmasAluno(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();
            $aluno = $usuario->aluno()->first();

            if (!$aluno) {
                throw new Exception('Aluno não encontrado.');
            }

            // Obter IDs de todas as matrículas do aluno
            $matriculaIds = $aluno->matriculas()->pluck('id')->toArray();

            if (empty($matriculaIds)) {
                $this->responderJSON([
                    'status' => 'sucesso',
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                    'per_page' => 30
                ]);
                return;
            }

            $status = $request->get('status') ?? null;
            $periodo_id = $request->get('periodo_id') ?? null;
            $turno = $request->get('turno') ?? null;
            $modalidade = $request->get('modalidade') ?? null;
            $busca = $request->get('busca') ?? null;
            $pagina = $request->get('pagina') ?? 1;
            $limite = $request->get('limite') ?? 30;

            // Buscar turmas em que o aluno está inscrito com status relevantes
            $query = Turma::whereHas('inscricoes', function($q) use ($matriculaIds) {
                $q->whereIn('aluno_matricula_id', $matriculaIds)
                  ->whereIn('status', [
                      InscricaoStatus::CURSANDO->name,
                      InscricaoStatus::APROVADO->name,
                      InscricaoStatus::REPROVADO_FALTA->name,
                      InscricaoStatus::REPROVADO_MEDIA->name
                  ]);
            });

            // Aplicar filtros
            if ($status) {
                $statusEnum = TurmaStatus::fromName(strtoupper($status));
                if ($statusEnum) {
                    $query->where('status', $statusEnum->name);
                }
            }

            if ($periodo_id) {
                $query->where('periodo_id', $periodo_id);
            }

            if ($turno) {
                $turnoEnum = Turno::fromName(strtoupper($turno));
                if ($turnoEnum) {
                    $query->where('turno', $turnoEnum->name);
                }
            }

            if ($modalidade) {
                $modalidadeEnum = EnsinoModalidade::fromName(strtoupper($modalidade));
                if ($modalidadeEnum) {
                    $query->where('modalidade', $modalidadeEnum->name);
                }
            }

            if ($busca) {
                $query->where(function($q) use ($busca) {
                    $q->where('codigo', 'LIKE', "%$busca%")
                      ->orWhereHas('disciplina.componenteCurricular', function($q2) use ($busca) {
                          $q2->where('nome', 'LIKE', "%$busca%");
                      });
                });
            }

            $query->with([
                'disciplina.componenteCurricular:id,nome',
                'periodo:id,sigla',
                'professor.usuario:id,nome_civil,nome_social'
            ]);

            $paginator = $query->paginate($limite, ['*'], 'page', $pagina);
            $turmas = $paginator->getCollection();

            foreach ($turmas as $turma) {
                $turma->disciplina_nome = $turma->disciplina && $turma->disciplina->componenteCurricular
                    ? $turma->disciplina->componenteCurricular->nome
                    : 'Sem disciplina';
                $turma->periodo_nome = $turma->periodo ? $turma->periodo->sigla : 'Sem período';
                
                if ($turma->professor && $turma->professor->usuario) {
                    $usuario_prof = $turma->professor->usuario;
                    $turma->professor_nome = $usuario_prof->nome_social ?: $usuario_prof->nome_civil;
                } else {
                    $turma->professor_nome = 'Sem professor';
                }

                // Valores dos enums
                $turma->status_valor = $turma->obterStatus()->value;
                $turma->turno_valor = $turma->obterTurno()->value;
                $turma->modalidade_valor = $turma->obterModalidade()->value;
                
                unset($turma->disciplina, $turma->periodo, $turma->professor);
            }

            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $turmas,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage()
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Função para filtrar turmas do professor
     *
     * @param Request $request
     * @return void
     */
    public function filtrarTurmasProfessor(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();
            $professor = $usuario->professor()->first();

            if (!$professor) {
                throw new Exception('Professor não encontrado.');
            }

            $status = $request->get('status') ?? null;
            $periodo_id = $request->get('periodo_id') ?? null;
            $turno = $request->get('turno') ?? null;
            $modalidade = $request->get('modalidade') ?? null;
            $busca = $request->get('busca') ?? null;
            $pagina = $request->get('pagina') ?? 1;
            $limite = $request->get('limite') ?? 30;

            // Buscar turmas do professor
            $query = Turma::where('professor_id', $professor->obterId());

            // Aplicar filtros
            if ($status) {
                $statusEnum = TurmaStatus::fromName(strtoupper($status));
                if ($statusEnum) {
                    $query->where('status', $statusEnum->name);
                }
            }

            if ($periodo_id) {
                $query->where('periodo_id', $periodo_id);
            }

            if ($turno) {
                $turnoEnum = Turno::fromName(strtoupper($turno));
                if ($turnoEnum) {
                    $query->where('turno', $turnoEnum->name);
                }
            }

            if ($modalidade) {
                $modalidadeEnum = EnsinoModalidade::fromName(strtoupper($modalidade));
                if ($modalidadeEnum) {
                    $query->where('modalidade', $modalidadeEnum->name);
                }
            }

            if ($busca) {
                $query->where(function($q) use ($busca) {
                    $q->where('codigo', 'LIKE', "%$busca%")
                      ->orWhereHas('disciplina.componenteCurricular', function($q2) use ($busca) {
                          $q2->where('nome', 'LIKE', "%$busca%");
                      });
                });
            }

            $query->withCount('inscricoes')
                  ->with([
                      'disciplina.componenteCurricular:id,nome',
                      'periodo:id,sigla'
                  ]);

            $paginator = $query->paginate($limite, ['*'], 'page', $pagina);
            $turmas = $paginator->getCollection();

            foreach ($turmas as $turma) {
                $turma->disciplina_nome = $turma->disciplina && $turma->disciplina->componenteCurricular
                    ? $turma->disciplina->componenteCurricular->nome
                    : 'Sem disciplina';
                $turma->periodo_nome = $turma->periodo ? $turma->periodo->sigla : 'Sem período';
                
                // Cálculos de vagas
                $ocupadas = $turma->inscricoes_count ?? 0;
                $turma->vagas_ocupadas = $ocupadas;
                $turma->vagas_disponiveis = max(0, $turma->capacidade_maxima - $ocupadas);
                $turma->percentual_ocupacao = $turma->capacidade_maxima > 0 
                    ? round(($ocupadas / $turma->capacidade_maxima) * 100, 2) 
                    : 0;
                
                // Valores dos enums
                $turma->status_valor = $turma->obterStatus()->value;
                $turma->turno_valor = $turma->obterTurno()->value;
                $turma->modalidade_valor = $turma->obterModalidade()->value;
                
                unset($turma->disciplina, $turma->periodo);
            }

            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $turmas,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage()
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Função para filtrar as turmas, com base nos parâmetros passados
     * Redireciona para o método apropriado baseado no perfil do usuário
     *
     * @param Request $request
     * @return void
     */
    public function filtrarTurmas(Request $request): void {
        // Detecta o perfil do usuário e chama o método apropriado
        $usuario = AutenticacaoService::usuarioAutenticado();
        $gruposPermissao = $usuario->grupos;

        foreach ($gruposPermissao as $grupo) {
            switch (strtoupper($grupo->obterNome())) {
                case 'ADMINISTRADOR':
                    $this->filtrarTurmasAdministrador($request);
                    return;

                case 'ALUNO':
                    $this->filtrarTurmasAluno($request);
                    return;

                case 'PROFESSOR':
                    $this->filtrarTurmasProfessor($request);
                    return;
            }
        }

        // Fallback: se não tiver perfil específico, retorna erro
        $this->responderJSON([
            'status' => 'erro',
            'mensagem' => 'Perfil de usuário não reconhecido.'
        ]);
    }

    /**
     * Filtra as turmas para administradores
     *
     * @param Request $request
     * @return void
     */
    public function filtrarTurmasAdministrador(Request $request): void {
        try {
            $status = $request->get('status') ?? null;
            $periodo_id = $request->get('periodo_id') ?? null;
            $turno = $request->get('turno') ?? null;
            $modalidade = $request->get('modalidade') ?? null;
            $busca = $request->get('busca') ?? null;
            $pagina = $request->get('pagina') ?? 1;
            $limite = $request->get('limite') ?? 15;

            $query = Turma::query();

            if ($status) {
                $statusEnum = TurmaStatus::fromName(strtoupper($status));
                if ($statusEnum) {
                    $query->where('status', $statusEnum->name);
                }
            }

            if ($periodo_id) {
                $query->where('periodo_id', $periodo_id);
            }

            if ($turno) {
                $turnoEnum = Turno::fromName(strtoupper($turno));
                if ($turnoEnum) {
                    $query->where('turno', $turnoEnum->name);
                }
            }

            if ($modalidade) {
                $modalidadeEnum = EnsinoModalidade::fromName(strtoupper($modalidade));
                if ($modalidadeEnum) {
                    $query->where('modalidade', $modalidadeEnum->name);
                }
            }

            if ($busca) {
                $query->where(function($q) use ($busca) {
                    $q->where('codigo', 'LIKE', "%$busca%")
                      ->orWhereHas('disciplina.componenteCurricular', function($q2) use ($busca) {
                          $q2->where('nome', 'LIKE', "%$busca%");
                      })
                      ->orWhereHas('professor.usuario', function($q3) use ($busca) {
                          $q3->where('nome_civil', 'LIKE', "%$busca%")
                             ->orWhere('nome_social', 'LIKE', "%$busca%");
                      });
                });
            }

            // Paginação com eager loading otimizado
            $query->withCount('inscricoes')
                  ->with([
                      'disciplina.componenteCurricular:id,nome',
                      'periodo:id,sigla', 
                      'professor.usuario:id,nome_civil,nome_social'
                  ]);
            
            $paginator = $query->paginate($limite, ['*'], 'page', $pagina);
            
            $turmas = $paginator->getCollection();

            // Adicionar apenas informações básicas para listagem
            foreach ($turmas as $turma) {
                // Obter nomes dos relacionamentos
                $turma->disciplina_nome = $turma->disciplina && $turma->disciplina->componenteCurricular 
                    ? $turma->disciplina->componenteCurricular->nome 
                    : 'Sem disciplina';
                $turma->periodo_nome = $turma->periodo ? $turma->periodo->sigla : 'Sem período';
                
                // Para professor, usar nome social se existir, senão nome civil
                if ($turma->professor && $turma->professor->usuario) {
                    $usuario = $turma->professor->usuario;
                    $turma->professor_nome = $usuario->nome_social ?: $usuario->nome_civil;
                } else {
                    $turma->professor_nome = 'Sem professor';
                }
                
                // Cálculos diretos sem queries
                $ocupadas = $turma->inscricoes_count ?? 0;
                $turma->vagas_ocupadas = $ocupadas;
                $turma->vagas_disponiveis = max(0, $turma->capacidade_maxima - $ocupadas);
                $turma->percentual_ocupacao = $turma->capacidade_maxima > 0 
                    ? round(($ocupadas / $turma->capacidade_maxima) * 100, 2) 
                    : 0;
                
                // Obter valores dos enums usando getters do modelo
                $turma->status_valor = $turma->obterStatus()->value;
                $turma->turno_valor = $turma->obterTurno()->value;
                $turma->modalidade_valor = $turma->obterModalidade()->value;
                
                // Remover relacionamentos carregados para reduzir tamanho do JSON
                unset($turma->disciplina);
                unset($turma->periodo);
                unset($turma->professor);
            }

            // Ordenar por status e código
            $turmas = $turmas->sortBy(function($turma) {
                $ordem_status = [
                    'ATIVA' => 1,
                    'CONFIRMADA' => 2,
                    'OFERTADA' => 3,
                    'PLANEJADA' => 4,
                    'CONCLUIDA' => 5,
                    'CANCELADA' => 6,
                    'ARQUIVADA' => 7
                ];
                return [$ordem_status[$turma->status_valor] ?? 99, $turma->codigo];
            })->values();

            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $turmas,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'paginacao' => [
                    'total' => $paginator->total(),
                    'por_pagina' => $paginator->perPage(),
                    'pagina_atual' => $paginator->currentPage(),
                    'ultima_pagina' => $paginator->lastPage(),
                    'de' => $paginator->firstItem(),
                    'ate' => $paginator->lastItem()
                ]
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao filtrar as turmas.'
            ]);
        }
    }

    /**
     * Obtém dados completos de uma turma específica
     * 
     * @param Request $request
     * @return void
     */
    public function obterTurma(Request $request): void {
        try {
            $id = $request->get('id');

            if (!$id) {
                throw new Exception('ID da turma é obrigatório.');
            }

            // Buscar turma com relacionamentos
            $turma = Turma::with([
                'disciplina.componenteCurricular',
                'periodo',
                'professor.usuario'
            ])->withCount('inscricoes')->find($id);

            if (!$turma) {
                throw new Exception('Turma não encontrada.');
            }

            // Adicionar informações completas
            $turma->disciplina_nome = $turma->disciplina?->componenteCurricular?->nome ?? 'N/A';
            $turma->periodo_nome = $turma->periodo?->sigla ?? 'N/A';
            
            // Para professor, usar nome social se existir, senão nome civil
            if ($turma->professor && $turma->professor->usuario) {
                $usuario = $turma->professor->usuario;
                $turma->professor_nome = $usuario->nome_social ?: $usuario->nome_civil;
            } else {
                $turma->professor_nome = 'N/A';
            }
            
            $ocupadas = $turma->inscricoes_count ?? 0;
            $turma->vagas_ocupadas = $ocupadas;
            $turma->vagas_disponiveis = max(0, $turma->capacidade_maxima - $ocupadas);
            $turma->percentual_ocupacao = $turma->capacidade_maxima > 0 
                ? round(($ocupadas / $turma->capacidade_maxima) * 100, 2) 
                : 0;

            $turma->status_valor = $turma->obterStatus()->value;
            $turma->turno_valor = $turma->obterTurno()->value;
            $turma->modalidade_valor = $turma->obterModalidade()->value;

            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $turma
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao obter dados da turma.'
            ]);
        }
    }

    /**
     * Função para adicionar uma nova turma
     * 
     * @param Request $request
     * @return void
     */
    public function adicionarTurma(Request $request): void {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $disciplina_id = $request->post('disciplina_id');
            $periodo_id = $request->post('periodo_id');
            $professor_id = $request->post('professor_id');
            $codigo = $request->post('codigo');
            $grade_id = $request->post('grade_id');
            $turno = $request->post('turno');
            $capacidade_maxima = $request->post('capacidade_maxima');
            $modalidade = $request->post('modalidade');

            // Validações
            if (!$disciplina_id || !$periodo_id || !$professor_id || !$codigo || !$turno || !$capacidade_maxima || !$modalidade) {
                throw new Exception('Todos os campos obrigatórios devem ser preenchidos.');
            }

            // Verificar se o código já existe
            $turmaExistente = Turma::buscarPorCodigo($codigo);
            if ($turmaExistente) {
                throw new Exception('Já existe uma turma cadastrada com este código.');
            }

            // Verificar se a disciplina existe
            $disciplina = Disciplina::buscarPorId($disciplina_id);
            if (!$disciplina) {
                throw new Exception('Disciplina não encontrada.');
            }

            // Verificar se o período existe
            $periodo = PeriodoLetivo::buscarPorId($periodo_id);
            if (!$periodo) {
                throw new Exception('Período letivo não encontrado.');
            }

            // Verificar se o professor existe
            $professor = Professor::buscarPorId($professor_id);
            if (!$professor) {
                throw new Exception('Professor não encontrado.');
            }

            // Converter enums
            $turnoEnum = Turno::fromName(strtoupper($turno));
            $modalidadeEnum = EnsinoModalidade::fromName(strtoupper($modalidade));

            if (!$turnoEnum || !$modalidadeEnum) {
                throw new Exception('Turno ou modalidade inválidos.');
            }

            // Registrar log
            $mensagemLog = sprintf(
                'Nova turma "%s" adicionada ao sistema.' . PHP_EOL . PHP_EOL .
                'Código: %s' . PHP_EOL .
                'Disciplina: %s' . PHP_EOL .
                'Período: %s' . PHP_EOL .
                'Professor: %s' . PHP_EOL .
                'Turno: %s' . PHP_EOL .
                'Capacidade: %d vagas' . PHP_EOL .
                'Modalidade: %s',
                $codigo,
                $codigo,
                $disciplina->componenteCurricular ? $disciplina->componenteCurricular()->first()->obterNome() : 'N/A',
                $periodo->obterSigla(),
                $professor->usuario ? ($professor->usuario()->first()->obterNomeSocial() ?: $professor->usuario()->first()->obterNomeCivil()) : 'N/A',
                $turnoEnum->value,
                $capacidade_maxima,
                $modalidadeEnum->value
            );

            Log::registrar($usuario->obterId(), 'Criação de Turma', $mensagemLog);

            // Criar turma
            $turma = new Turma();
            $turma->atribuirDisciplinaId($disciplina_id);
            $turma->atribuirPeriodoId($periodo_id);
            $turma->atribuirProfessorId($professor_id);
            $turma->atribuirCodigo($codigo);
            if ($grade_id) {
                $turma->atribuirGradeId($grade_id);
            }
            $turma->atribuirTurno($turnoEnum);
            $turma->atribuirCapacidadeMaxima($capacidade_maxima);
            $turma->atribuirModalidade($modalidadeEnum);
            $turma->atribuirStatus(TurmaStatus::PLANEJADA);
            $turma->salvar();

            // Limpar cache de turmas
            $this->limparCacheTurmas();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Turma adicionada com sucesso.',
                'data' => $turma
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao adicionar a turma.'
            ]);
        }
    }

    /**
     * Função para editar uma turma existente
     * 
     * @param Request $request
     * @return void
     */
    public function editarTurma(Request $request): void {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->parametroRota('id');

            if (!$id) {
                throw new Exception('ID da turma é obrigatório.');
            }

            // Buscar a turma existente
            $turma = Turma::buscarPorId($id);
            if (!$turma) {
                throw new Exception('Turma não encontrada.');
            }

            $codigo = $request->post('codigo');
            $professor_id = $request->post('professor_id');
            $capacidade_maxima = $request->post('capacidade_maxima');
            $turno = $request->post('turno');
            $modalidade = $request->post('modalidade');

            // Verificar se houve alterações
            $alterado = false;
            $dadosAntigos = [];
            $dadosNovos = [];

            if ($codigo && $codigo !== $turma->obterCodigo()) {
                // Verificar se o novo código já existe
                $turmaExistente = Turma::buscarPorCodigo($codigo);
                if ($turmaExistente && $turmaExistente->obterId() !== $id) {
                    throw new Exception('Já existe uma turma cadastrada com este código.');
                }
                $dadosAntigos[] = 'Código: ' . $turma->obterCodigo();
                $dadosNovos[] = 'Código: ' . $codigo;
                $turma->atribuirCodigo($codigo);
                $alterado = true;
            }

            if ($professor_id && $professor_id != $turma->obterProfessorId()) {
                $professor = Professor::buscarPorId($professor_id);
                if (!$professor) {
                    throw new Exception('Professor não encontrado.');
                }
                $dadosAntigos[] = 'Professor ID: ' . $turma->obterProfessorId();
                $dadosNovos[] = 'Professor ID: ' . $professor_id;
                $turma->atribuirProfessorId($professor_id);
                $alterado = true;
            }

            if ($capacidade_maxima && $capacidade_maxima != $turma->obterCapacidadeMaxima()) {
                $dadosAntigos[] = 'Capacidade: ' . $turma->obterCapacidadeMaxima();
                $dadosNovos[] = 'Capacidade: ' . $capacidade_maxima;
                $turma->atribuirCapacidadeMaxima($capacidade_maxima);
                $alterado = true;
            }

            if ($turno) {
                $turnoEnum = Turno::fromName(strtoupper($turno));
                if ($turnoEnum && $turnoEnum !== $turma->obterTurno()) {
                    $dadosAntigos[] = 'Turno: ' . $turma->obterTurno()->value;
                    $dadosNovos[] = 'Turno: ' . $turnoEnum->value;
                    $turma->atribuirTurno($turnoEnum);
                    $alterado = true;
                }
            }

            if ($modalidade) {
                $modalidadeEnum = EnsinoModalidade::fromName(strtoupper($modalidade));
                if ($modalidadeEnum && $modalidadeEnum !== $turma->obterModalidade()) {
                    $dadosAntigos[] = 'Modalidade: ' . $turma->obterModalidade()->value;
                    $dadosNovos[] = 'Modalidade: ' . $modalidadeEnum->value;
                    $turma->atribuirModalidade($modalidadeEnum);
                    $alterado = true;
                }
            }

            if (!$alterado) {
                throw new Exception('Nenhum dado foi alterado.');
            }

            // Registrar log
            $mensagemLog = sprintf(
                'Os detalhes da turma "%s" (ID: %d) foram atualizados.' . PHP_EOL . PHP_EOL .
                'Dados antigos: %s' . PHP_EOL .
                'Dados novos: %s',
                $turma->obterCodigo(),
                $turma->obterId(),
                implode(', ', $dadosAntigos),
                implode(', ', $dadosNovos)
            );

            Log::registrar($usuario->obterId(), 'Atualização de Turma', $mensagemLog);

            $turma->salvar();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Turma atualizada com sucesso.',
                'data' => $turma
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao editar a turma.'
            ]);
        }
    }

    /**
     * Função para arquivar uma turma
     * 
     * @param Request $request
     * @return void
     */
    public function arquivarTurma(Request $request): void {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->post('id');

            if (!$id) {
                throw new Exception('ID da turma é obrigatório.');
            }

            // Buscar a turma existente
            $turma = Turma::buscarPorId($id);
            if (!$turma) {
                throw new Exception('Turma não encontrada.');
            }

            if ($turma->obterStatus() === TurmaStatus::ARQUIVADA) {
                throw new Exception('A turma já está arquivada.');
            }

            // Arquivar a turma
            $turma->atribuirStatus(TurmaStatus::ARQUIVADA);
            $turma->salvar();

            // Registrar log
            $mensagemLog = sprintf(
                'A turma "%s" (ID: %d) foi arquivada.',
                $turma->obterCodigo(),
                $turma->obterId()
            );

            Log::registrar($usuario->obterId(), 'Arquivamento de Turma', $mensagemLog);

            // Limpar cache de turmas
            $this->limparCacheTurmas();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Turma arquivada com sucesso.',
                'data' => $turma
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao arquivar a turma.'
            ]);
        }
    }

    /**
     * Função para confirmar uma turma planejada
     * 
     * @param Request $request
     * @return void
     */
    public function confirmarTurma(Request $request): void {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->post('id');

            if (!$id) {
                throw new Exception('ID da turma é obrigatório.');
            }

            // Buscar a turma
            $turma = Turma::buscarPorId($id);
            if (!$turma) {
                throw new Exception('Turma não encontrada.');
            }

            if ($turma->obterStatus() !== TurmaStatus::OFERTADA) {
                throw new Exception('Apenas turmas ofertadas podem ser confirmadas.');
            }

            // Confirmar a turma
            $turma->atribuirStatus(TurmaStatus::CONFIRMADA);
            $turma->salvar();

            // Registrar log
            Log::registrar($usuario->obterId(), 'Confirmação de Turma', 
                sprintf('A turma "%s" (ID: %d) foi confirmada.', $turma->obterCodigo(), $turma->obterId()));

            // Limpar cache de turmas
            $this->limparCacheTurmas();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Turma confirmada com sucesso.',
                'data' => $turma
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao confirmar a turma.'
            ]);
        }
    }

    /**
     * Função para finalizar uma turma
     * 
     * @param Request $request
     * @return void
     */
    public function finalizarTurma(Request $request): void {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->post('id');

            if (!$id) {
                throw new Exception('ID da turma é obrigatório.');
            }

            // Buscar a turma
            $turma = Turma::buscarPorId($id);
            if (!$turma) {
                throw new Exception('Turma não encontrada.');
            }

            if ($turma->obterStatus() !== TurmaStatus::ATIVA) {
                throw new Exception('Apenas turmas ativas podem ser finalizadas.');
            }

            // Finalizar a turma
            $turma->atribuirStatus(TurmaStatus::CONCLUIDA);
            $turma->salvar();

            // Registrar log
            Log::registrar($usuario->obterId(), 'Finalização de Turma', 
                sprintf('A turma "%s" (ID: %d) foi finalizada.', $turma->obterCodigo(), $turma->obterId()));

            // Limpar cache de turmas
            $this->limparCacheTurmas();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Turma finalizada com sucesso.',
                'data' => $turma
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao finalizar a turma.'
            ]);
        }
    }

    /**
     * Função para liberar uma turma para inscrições
     * 
     * @param Request $request
     * @return void
     */
    public function liberarTurma(Request $request): void {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->post('id');

            if (!$id) {
                throw new Exception('ID da turma é obrigatório.');
            }

            // Buscar a turma
            $turma = Turma::buscarPorId($id);
            if (!$turma) {
                throw new Exception('Turma não encontrada.');
            }

            if ($turma->obterStatus() !== TurmaStatus::PLANEJADA) {
                throw new Exception('Apenas turmas planejadas podem ser liberadas para inscrições.');
            }

            // Liberar a turma
            $turma->atribuirStatus(TurmaStatus::OFERTADA);
            $turma->salvar();

            // Registrar log
            Log::registrar($usuario->obterId(), 'Liberação de Turma', 
                sprintf('A turma "%s" (ID: %d) foi liberada para inscrições.', $turma->obterCodigo(), $turma->obterId()));

            // Limpar cache de turmas
            $this->limparCacheTurmas();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Turma liberada para inscrições com sucesso.',
                'data' => $turma
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao liberar a turma.'
            ]);
        }
    }

    /**
     * Função para obter os alunos de uma turma
     * 
     * @param Request $request
     * @return void
     */
    public function obterAlunos(Request $request): void {
        try {
            $id = $request->parametroRota('id');

            if (!$id) {
                throw new Exception('ID da turma é obrigatório.');
            }

            // Buscar a turma
            $turma = Turma::buscarPorId($id);
            if (!$turma) {
                throw new Exception('Turma não encontrada.');
            }

            // Buscar inscrições da turma
            $inscricoes = $turma->inscricoes()->with(['alunoMatricula.aluno.usuario'])->get();

            $alunos = [];
            foreach ($inscricoes as $inscricao) {
                $alunoMatricula = $inscricao->alunoMatricula;
                if ($alunoMatricula) {
                    $aluno = $alunoMatricula->aluno;
                    if ($aluno) {
                        $usuario_aluno = $aluno->usuario()->first();
                        $alunos[] = [
                            'id' => $aluno->obterId(),
                            'nome' => $usuario_aluno ? ($usuario_aluno->obterNomeSocial() ?: $usuario_aluno->obterNomeCivil()) : 'N/A',
                            'matricula' => $alunoMatricula->obterMatricula(),
                            'inscricao_id' => $inscricao->obterId(),
                            'status' => $inscricao->obterStatus()->value
                        ];
                    }
                }
            }

            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $alunos
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao obter alunos da turma.'
            ]);
        }
    }

    /**
     * Função para adicionar alunos a uma turma
     * 
     * @param Request $request
     * @return void
     */
    public function adicionarAlunos(Request $request): void {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $turma_id = $request->parametroRota('id');
            $alunos_ids = $request->post('alunos_ids'); // Array de IDs

            if (!$turma_id || !$alunos_ids || !is_array($alunos_ids)) {
                throw new Exception('Dados inválidos.');
            }

            // Buscar a turma
            $turma = Turma::buscarPorId($turma_id);
            if (!$turma) {
                throw new Exception('Turma não encontrada.');
            }

            // Adicionar alunos
            $adicionados = 0;
            foreach ($alunos_ids as $aluno_id) {
                // Verificar se já existe inscrição
                $inscricaoExistente = Inscricao::where('turma_id', $turma_id)
                    ->where('aluno_id', $aluno_id)
                    ->first();

                if (!$inscricaoExistente) {
                    $inscricao = new Inscricao();
                    $inscricao->atribuirTurmaId($turma_id);
                    $inscricao->atribuirAlunoId($aluno_id);
                    $inscricao->salvar();
                    $adicionados++;
                }
            }

            // Registrar log
            Log::registrar($usuario->obterId(), 'Adição de Alunos à Turma', 
                sprintf('%d aluno(s) adicionado(s) à turma "%s" (ID: %d).', $adicionados, $turma->obterCodigo(), $turma->obterId()));

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => sprintf('%d aluno(s) adicionado(s) com sucesso.', $adicionados)
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao adicionar alunos.'
            ]);
        }
    }

    /**
     * Função para remover um aluno de uma turma
     * 
     * @param Request $request
     * @return void
     */
    public function removerAluno(Request $request): void {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $turma_id = $request->parametroRota('id');
            $aluno_id = $request->parametroRota('aluno_id');

            if (!$turma_id || !$aluno_id) {
                throw new Exception('Dados inválidos.');
            }

            // Buscar inscrição
            $inscricao = Inscricao::where('turma_id', $turma_id)
                ->where('aluno_id', $aluno_id)
                ->first();

            if (!$inscricao) {
                throw new Exception('Inscrição não encontrada.');
            }

            $inscricao->delete();

            // Registrar log
            $turma = Turma::buscarPorId($turma_id);
            Log::registrar($usuario->obterId(), 'Remoção de Aluno da Turma', 
                sprintf('Aluno (ID: %d) removido da turma "%s" (ID: %d).', $aluno_id, $turma?->obterCodigo() ?? 'N/A', $turma_id));

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Aluno removido com sucesso.'
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao remover aluno.'
            ]);
        }
    }

    /**
     * Limpa o cache de turmas
     *
     * @return void
     */
    private function limparCacheTurmas(): void {
        // Remove cache de listagem de turmas
        Cache::forget('turmas_periodos_filtro');
        
        // Remove todos os caches de filtros de turmas
        $cacheDir = __DIR__ . '/../../storage/cache/';
        
        if (is_dir($cacheDir)) {
            $pattern = 'turmas_filtro_*';
            $files = glob($cacheDir . $pattern);
            
            if ($files) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }

    /**
     * Busca disciplinas por curso
     *
     * @param Request $request
     * @return void
     */
    public function buscarDisciplinasPorCurso(Request $request): void {
        try {
            $curso_id = $request->get('curso_id');

            if (!$curso_id) {
                throw new Exception('ID do curso é obrigatório.');
            }

            // Buscar curso
            $curso = Curso::buscarPorId($curso_id);
            if (!$curso) {
                throw new Exception('Curso não encontrado.');
            }

            // Buscar matrizes curriculares ativas do curso
            $matrizes = $curso->matrizes()->where('status', 'VIGENTE')->get();

            if ($matrizes->isEmpty()) {
                $this->responderJSON([
                    'status' => 'sucesso',
                    'data' => [],
                    'mensagem' => 'Nenhuma matriz curricular vigente encontrada para este curso.'
                ]);
                return;
            }

            // Buscar componentes curriculares de todas as matrizes
            $disciplinas = [];
            foreach ($matrizes as $matriz) {
                $componentes = ComponenteCurricular::where('matriz_curricular_id', $matriz->obterId())
                    ->with('disciplina')
                    ->get();

                foreach ($componentes as $componente) {
                    if ($componente->disciplina) {
                        $disciplinas[] = [
                            'id' => $componente->disciplina->obterId(),
                            'nome' => $componente->obterNome(),
                            'sigla' => $componente->disciplina->obterSigla(),
                            'periodo' => $componente->obterPeriodo(),
                            'carga_horaria' => $componente->obterCargaHoraria()
                        ];
                    }
                }
            }

            // Remover duplicatas
            $disciplinasUnicas = array_values(array_unique($disciplinas, SORT_REGULAR));

            // Ordenar por período e nome
            usort($disciplinasUnicas, function($a, $b) {
                if ($a['periodo'] === $b['periodo']) {
                    return strcmp($a['nome'], $b['nome']);
                }
                return $a['periodo'] <=> $b['periodo'];
            });

            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $disciplinasUnicas
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Busca horários disponíveis por turno
     *
     * @param Request $request
     * @return void
     */
    public function buscarHorariosPorTurno(Request $request): void {
        try {
            $turno = strtoupper($request->get('turno') ?? '');

            if (!$turno) {
                throw new Exception('Turno é obrigatório.');
            }

            // Definir horários por turno
            $horarios = [];
            
            switch ($turno) {
                case 'MANHA':
                    $horarios = [
                        ['inicio' => '07:00', 'fim' => '07:50'],
                        ['inicio' => '07:50', 'fim' => '08:40'],
                        ['inicio' => '08:40', 'fim' => '09:30'],
                        ['inicio' => '09:50', 'fim' => '10:40'],
                        ['inicio' => '10:40', 'fim' => '11:30'],
                        ['inicio' => '11:30', 'fim' => '12:20']
                    ];
                    break;
                
                case 'TARDE':
                    $horarios = [
                        ['inicio' => '13:00', 'fim' => '13:50'],
                        ['inicio' => '13:50', 'fim' => '14:40'],
                        ['inicio' => '14:40', 'fim' => '15:30'],
                        ['inicio' => '15:50', 'fim' => '16:40'],
                        ['inicio' => '16:40', 'fim' => '17:30'],
                        ['inicio' => '17:30', 'fim' => '18:20']
                    ];
                    break;
                
                case 'NOITE':
                    $horarios = [
                        ['inicio' => '18:30', 'fim' => '19:20'],
                        ['inicio' => '19:20', 'fim' => '20:10'],
                        ['inicio' => '20:10', 'fim' => '21:00'],
                        ['inicio' => '21:10', 'fim' => '22:00'],
                        ['inicio' => '22:00', 'fim' => '22:50']
                    ];
                    break;
                
                case 'INTEGRAL':
                    $horarios = [
                        ['inicio' => '07:00', 'fim' => '07:50'],
                        ['inicio' => '07:50', 'fim' => '08:40'],
                        ['inicio' => '08:40', 'fim' => '09:30'],
                        ['inicio' => '09:50', 'fim' => '10:40'],
                        ['inicio' => '10:40', 'fim' => '11:30'],
                        ['inicio' => '11:30', 'fim' => '12:20'],
                        ['inicio' => '13:00', 'fim' => '13:50'],
                        ['inicio' => '13:50', 'fim' => '14:40'],
                        ['inicio' => '14:40', 'fim' => '15:30'],
                        ['inicio' => '15:50', 'fim' => '16:40'],
                        ['inicio' => '16:40', 'fim' => '17:30'],
                        ['inicio' => '17:30', 'fim' => '18:20']
                    ];
                    break;
                
                default:
                    throw new Exception('Turno inválido.');
            }

            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $horarios
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Obter dados para os filtros das turmas
     *
     * @param Request $request
     * @return void
     */
    public function obterDadosFiltros(Request $request): void {
        try {
            // Buscar períodos letivos ativos
            $periodos = PeriodoLetivo::where('ativo', true)
                ->orderBy('ano', 'desc')
                ->orderBy('semestre', 'desc')
                ->get(['id', 'sigla', 'ano', 'semestre'])
                ->map(function($periodo) {
                    return [
                        'id' => $periodo->id,
                        'sigla' => $periodo->sigla,
                        'nome' => $periodo->sigla
                    ];
                });

            // Status das turmas
            $statusList = collect(TurmaStatus::cases())->map(function($status) {
                return [
                    'nome' => $status->name,
                    'valor' => $status->value,
                    'label' => $status->value
                ];
            });

            // Turnos
            $turnos = collect(Turno::cases())->map(function($turno) {
                return [
                    'nome' => $turno->name,
                    'valor' => $turno->value,
                    'label' => $turno->value
                ];
            });

            // Modalidades de ensino
            $modalidades = collect(EnsinoModalidade::cases())->map(function($modalidade) {
                return [
                    'nome' => $modalidade->name,
                    'valor' => $modalidade->value,
                    'label' => $modalidade->value
                ];
            });

            $this->responderJSON([
                'status' => 'sucesso',
                'data' => [
                    'periodos' => $periodos,
                    'status' => $statusList,
                    'turnos' => $turnos,
                    'modalidades' => $modalidades
                ]
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Obter estatísticas das turmas por perfil
     *
     * @param Request $request
     * @return void
     */
    public function obterEstatisticas(Request $request): void {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();
            $gruposPermissao = $usuario->grupos;

            foreach ($gruposPermissao as $grupo) {
                switch (strtoupper($grupo->obterNome())) {
                    case 'ADMINISTRADOR':
                        $this->obterEstatisticasAdministrador();
                        return;

                    case 'ALUNO':
                        $this->obterEstatisticasAluno();
                        return;

                    case 'PROFESSOR':
                        $this->obterEstatisticasProfessor();
                        return;
                }
            }

            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => 'Perfil de usuário não reconhecido.'
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Estatísticas para administrador
     *
     * @return void
     */
    private function obterEstatisticasAdministrador(): void {
        $totalTurmas = Turma::count();
        $turmasAtivas = Turma::where('status', TurmaStatus::ATIVA->name)->count();
        $turmasConfirmadas = Turma::where('status', TurmaStatus::CONFIRMADA->name)->count();
        $turmasOfertadas = Turma::where('status', TurmaStatus::OFERTADA->name)->count();

        $this->responderJSON([
            'status' => 'sucesso',
            'data' => [
                'total_turmas' => $totalTurmas,
                'turmas_ativas' => $turmasAtivas,
                'turmas_confirmadas' => $turmasConfirmadas,
                'turmas_ofertadas' => $turmasOfertadas,
                'ocupacao_media' => $this->calcularOcupacaoMedia()
            ]
        ]);
    }

    /**
     * Estatísticas para aluno
     *
     * @return void
     */
    private function obterEstatisticasAluno(): void {
        $usuario = AutenticacaoService::usuarioAutenticado();
        $aluno = $usuario->aluno()->first();

        if (!$aluno) {
            throw new Exception('Aluno não encontrado.');
        }

        $matriculaIds = $aluno->matriculas()->pluck('id')->toArray();
        
        $turmasInscrito = Turma::whereHas('inscricoes', function($q) use ($matriculaIds) {
            $q->whereIn('aluno_matricula_id', $matriculaIds)
              ->where('status', InscricaoStatus::CURSANDO->name);
        })->count();

        $turmasAprovado = Turma::whereHas('inscricoes', function($q) use ($matriculaIds) {
            $q->whereIn('aluno_matricula_id', $matriculaIds)
              ->where('status', InscricaoStatus::APROVADO->name);
        })->count();

        $this->responderJSON([
            'status' => 'sucesso',
            'data' => [
                'turmas_cursando' => $turmasInscrito,
                'turmas_aprovado' => $turmasAprovado,
                'total_turmas' => $turmasInscrito + $turmasAprovado
            ]
        ]);
    }

    /**
     * Estatísticas para professor
     *
     * @return void
     */
    private function obterEstatisticasProfessor(): void {
        $usuario = AutenticacaoService::usuarioAutenticado();
        $professor = $usuario->professor()->first();

        if (!$professor) {
            throw new Exception('Professor não encontrado.');
        }

        $totalTurmas = Turma::where('professor_id', $professor->obterId())->count();
        $turmasAtivas = Turma::where('professor_id', $professor->obterId())
            ->where('status', TurmaStatus::ATIVA->name)->count();

        $this->responderJSON([
            'status' => 'sucesso',
            'data' => [
                'total_turmas' => $totalTurmas,
                'turmas_ativas' => $turmasAtivas,
                'ocupacao_media' => $this->calcularOcupacaoMediaProfessor($professor->obterId())
            ]
        ]);
    }

    /**
     * Calcular ocupação média das turmas
     *
     * @return float
     */
    private function calcularOcupacaoMedia(): float {
        $turmas = Turma::withCount('inscricoes')->get();
        $ocupacaoTotal = 0;
        $turmasComCapacidade = 0;

        foreach ($turmas as $turma) {
            if ($turma->capacidade_maxima > 0) {
                $ocupacao = ($turma->inscricoes_count / $turma->capacidade_maxima) * 100;
                $ocupacaoTotal += $ocupacao;
                $turmasComCapacidade++;
            }
        }

        return $turmasComCapacidade > 0 ? round($ocupacaoTotal / $turmasComCapacidade, 2) : 0;
    }

    /**
     * Calcular ocupação média das turmas de um professor
     *
     * @param int $professorId
     * @return float
     */
    private function calcularOcupacaoMediaProfessor(int $professorId): float {
        $turmas = Turma::where('professor_id', $professorId)
            ->withCount('inscricoes')->get();
        $ocupacaoTotal = 0;
        $turmasComCapacidade = 0;

        foreach ($turmas as $turma) {
            if ($turma->capacidade_maxima > 0) {
                $ocupacao = ($turma->inscricoes_count / $turma->capacidade_maxima) * 100;
                $ocupacaoTotal += $ocupacao;
                $turmasComCapacidade++;
            }
        }

        return $turmasComCapacidade > 0 ? round($ocupacaoTotal / $turmasComCapacidade, 2) : 0;
    }

}
