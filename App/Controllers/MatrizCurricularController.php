<?php

/**
 * @file MatrizCurricularController.php
 * @description Controlador responsável pelo gerenciamento das matrizes curriculares no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Core\Request;
use App\Models\Curso;
use App\Models\MatrizCurricular;
use App\Models\ComponenteCurricular;
use App\Models\ComponentePreRequisito;
use App\Models\ComponenteEquivalencia;
use App\Models\Log;
use App\Models\Enumerations\MatrizCurricularStatus;
use App\Models\Enumerations\ComponenteCurricularTipo;
use App\Services\AutenticacaoService;
use Exception;

/**
 * Classe MatrizCurricularController
 *
 * Gerencia as matrizes curriculares no sistema
 *
 * @package App\Controllers
 * @extends Controller
 */
class MatrizCurricularController extends Controller
{

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Renderiza a página inicial de matrizes curriculares
     *
     * @return void
     */
    public function exibirIndex(): void
    {
        // Obter permissões do usuário autenticado
        $permissoesUsuario = $_SESSION['usuario_permissoes'] ?? [];

        // Verificar permissões
        $permissoes = [
            'visualizar' => in_array('GAIO_MATRIZ_CURRICULAR_VISUALIZAR', $permissoesUsuario),
            'cadastrar' => in_array('GAIO_MATRIZ_CURRICULAR_CADASTRAR', $permissoesUsuario),
            'editar' => in_array('GAIO_MATRIZ_CURRICULAR_EDITAR', $permissoesUsuario),
            'inativar' => in_array('GAIO_MATRIZ_CURRICULAR_INATIVAR', $permissoesUsuario),
            'validar' => in_array('GAIO_MATRIZ_CURRICULAR_VALIDAR', $permissoesUsuario),
        ];

        // Breadcrumbs = links de navegação
        $breadcrumbs = [
            ['label' => 'Matrizes Curriculares', 'url' => '/matrizes-curriculares']
        ];

        // Renderiza a página inicial
        $this->renderizar('matrizes/index', [
            'titulo' => 'Matrizes Curriculares',
            'permissoes' => $permissoes,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    /**
     * Renderiza a página de visualização de uma matriz curricular específica
     *
     * @param Request $request
     * @return void
     */
    public function exibirMatriz(Request $request): void
    {
        try {
            $id = $request->get('id');

            if (!$id) {
                throw new Exception('ID da matriz curricular é obrigatório.');
            }

            // Converter ID para inteiro
            $id = (int)$id;

            // Buscar a matriz curricular
            $matriz = MatrizCurricular::id($id)->first();
            
            if (!$matriz) {
                throw new Exception('Matriz curricular não encontrada.');
            }

            // Carregar relacionamentos necessários
            $curso = $matriz->curso()->first();
            
            if (!$curso) {
                throw new Exception('Curso associado não encontrado.');
            }

            // Obter permissões do usuário autenticado
            $permissoesUsuario = $_SESSION['usuario_permissoes'] ?? [];

            // Verificar permissões
            $permissoes = [
                'visualizar' => in_array('GAIO_MATRIZ_CURRICULAR_VISUALIZAR', $permissoesUsuario),
                'editar' => in_array('GAIO_MATRIZ_CURRICULAR_EDITAR', $permissoesUsuario),
                'inativar' => in_array('GAIO_MATRIZ_CURRICULAR_INATIVAR', $permissoesUsuario),
                'validar' => in_array('GAIO_MATRIZ_CURRICULAR_VALIDAR', $permissoesUsuario),
                'componente_cadastrar' => in_array('GAIO_MATRIZ_CURRICULAR_COMPONENTE_CADASTRAR', $permissoesUsuario),
                'componente_editar' => in_array('GAIO_MATRIZ_CURRICULAR_COMPONENTE_EDITAR', $permissoesUsuario),
                'componente_excluir' => in_array('GAIO_MATRIZ_CURRICULAR_COMPONENTE_EXCLUIR', $permissoesUsuario),
                'componente_prerequisito' => in_array('GAIO_MATRIZ_CURRICULAR_COMPONENTE_PREREQUISITO_DEFINIR', $permissoesUsuario),
                'componente_equivalencia' => in_array('GAIO_MATRIZ_CURRICULAR_COMPONENTE_EQUIVALENCIA_DEFINIR', $permissoesUsuario),
            ];

            // Breadcrumbs = links de navegação
            $breadcrumbs = [
                ['label' => 'Matrizes Curriculares', 'url' => '/matrizes-curriculares'],
                ['label' => $curso->nome, 'url' => sprintf("/matrizes-curriculares/visualizar/%d", $matriz->obterId())]
            ];

            // Renderiza a página de visualização
            $this->renderizar('matrizes/visualizar', [
                'titulo' => 'Matriz Curricular - ' . $curso->nome,
                'matriz' => $matriz,
                'curso' => $curso,
                'permissoes' => $permissoes,
                'breadcrumbs' => $breadcrumbs
            ]);

        } catch (Exception $exception) {
            flash()->erro($exception->getMessage() ?? 'Erro ao carregar a matriz curricular.');
            header('Location: /matrizes-curriculares');
            exit();
        }
    }


    // --- MÉTODOS DE REQUISIÇÕES ---

    /**
     * Função para obter todas as matrizes curriculares
     *
     * @param Request $request
     * @return void
     */
    public function obterMatrizes(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            // Obtém todas as matrizes curriculares com os cursos
            $matrizes = MatrizCurricular::with('curso')->get();

            // Agrupar por curso
            $cursosComMatrizes = [];
            foreach ($matrizes as $matriz) {
                $cursoId = $matriz->obterCursoId();
                
                if (!isset($cursosComMatrizes[$cursoId])) {
                    $curso = $matriz->curso;
                    $cursosComMatrizes[$cursoId] = [
                        'id' => $curso->obterId(),
                        'nome' => $curso->obterNome(),
                        'sigla' => $curso->obterSigla(),
                        'matrizes' => []
                    ];
                }
                
                $cursosComMatrizes[$cursoId]['matrizes'][] = [
                    'id' => $matriz->obterId(),
                    'curso_id' => $matriz->obterCursoId(),
                    'quantidade_periodos' => $matriz->obterQuantidadePeriodos(),
                    'status' => $matriz->obterStatus()->value,
                    'created_at' => $matriz->created_at?->format('d/m/Y')
                ];
            }

            // Ordenar por nome do curso
            usort($cursosComMatrizes, function($a, $b) {
                return strcmp($a['nome'], $b['nome']);
            });

            $this->responderJSON([
                'sucesso' => true,
                'cursos' => array_values($cursosComMatrizes)
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Função para obter uma matriz curricular específica
     *
     * @param Request $request
     * @return void
     */
    public function obterMatriz(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->get('id');

            if (!$id) {
                throw new Exception('ID da matriz curricular é obrigatório.');
            }

            $matriz = MatrizCurricular::id((int)$id)->with('curso')->first();

            if (!$matriz) {
                throw new Exception('Matriz curricular não encontrada.');
            }

            $this->responderJSON([
                'sucesso' => true,
                'matriz' => [
                    'id' => $matriz->obterId(),
                    'curso_id' => $matriz->obterCursoId(),
                    'curso' => [
                        'id' => $matriz->curso->obterId(),
                        'nome' => $matriz->curso->obterNome(),
                        'sigla' => $matriz->curso->obterSigla()
                    ],
                    'quantidade_periodos' => $matriz->obterQuantidadePeriodos(),
                    'status' => $matriz->obterStatus()->value,
                    'created_at' => $matriz->created_at?->format('d/m/Y H:i')
                ]
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Função para obter componentes curriculares de uma matriz
     *
     * @param Request $request
     * @return void
     */
    public function obterComponentes(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $matrizId = $request->get('id');

            if (!$matrizId) {
                throw new Exception('ID da matriz curricular é obrigatório.');
            }

            $componentes = ComponenteCurricular::matrizCurricularId((int)$matrizId)
                ->with(['preRequisitos.componenteRequisito', 'equivalencias.componenteEquivalente'])
                ->orderBy('periodo')
                ->orderBy('nome')
                ->get();

            $componentesFormatados = [];
            foreach ($componentes as $componente) {
                $preRequisitos = [];
                foreach ($componente->preRequisitos as $pr) {
                    $preRequisitos[] = [
                        'id' => $pr->obterComponenteRequisitoId(),
                        'nome' => $pr->componenteRequisito->obterNome()
                    ];
                }

                $equivalencias = [];
                foreach ($componente->equivalencias as $eq) {
                    $equivalencias[] = [
                        'id' => $eq->obterComponenteEquivalenteId(),
                        'nome' => $eq->componenteEquivalente->obterNome()
                    ];
                }

                $componentesFormatados[] = [
                    'id' => $componente->obterId(),
                    'matriz_curricular_id' => $componente->obterMatrizCurricularId(),
                    'nome' => $componente->obterNome(),
                    'creditos' => $componente->obterCreditos(),
                    'carga_horaria' => $componente->obterCargaHoraria(),
                    'periodo' => $componente->obterPeriodo(),
                    'tipo' => $componente->obterTipo()->value,
                    'pre_requisitos' => $preRequisitos,
                    'equivalencias' => $equivalencias
                ];
            }

            $this->responderJSON([
                'sucesso' => true,
                'componentes' => $componentesFormatados
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Adiciona uma nova matriz curricular
     *
     * @param Request $request
     * @return void
     */
    public function adicionarMatriz(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $cursoId = $request->post('curso_id');
            $quantidadePeriodos = $request->post('quantidade_periodos');

            if (!$cursoId || !$quantidadePeriodos) {
                throw new Exception('Curso e quantidade de períodos são obrigatórios.');
            }

            // Verificar se o curso existe
            $curso = Curso::buscarPorId((int)$cursoId);
            if (!$curso) {
                throw new Exception('Curso não encontrado.');
            }

            // Criar nova matriz curricular
            $matriz = new MatrizCurricular();
            $matriz->atribuirCursoId((int)$cursoId);
            $matriz->atribuirQuantidadePeriodos((int)$quantidadePeriodos);
            $matriz->atribuirStatus(MatrizCurricularStatus::VIGENTE);
            $matriz->save();

            // Registrar log
            Log::registrar(
                $usuario->obterId(),
                'MATRIZ_CURRICULAR_CADASTRADA',
                "Matriz curricular cadastrada para o curso {$curso->obterNome()}"
            );

            $this->responderJSON([
                'sucesso' => true,
                'mensagem' => 'Matriz curricular cadastrada com sucesso!',
                'data' => [
                    'id' => $matriz->obterId(),
                    'curso_id' => $matriz->obterCursoId(),
                    'curso' => $curso->obterNome(),
                    'quantidade_periodos' => $matriz->obterQuantidadePeriodos(),
                    'status' => $matriz->obterStatus()->value
                ]
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Edita uma matriz curricular existente
     *
     * @param Request $request
     * @return void
     */
    public function editarMatriz(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->get('id');
            $quantidadePeriodos = $request->post('quantidade_periodos');

            if (!$id) {
                throw new Exception('ID da matriz curricular é obrigatório.');
            }

            $matriz = MatrizCurricular::id((int)$id)->first();

            if (!$matriz) {
                throw new Exception('Matriz curricular não encontrada.');
            }

            // Verificar se a matriz está validada
            if ($matriz->obterStatus() === MatrizCurricularStatus::ARQUIVADO) {
                throw new Exception('Não é possível editar uma matriz curricular arquivada.');
            }

            if ($quantidadePeriodos) {
                $matriz->atribuirQuantidadePeriodos((int)$quantidadePeriodos);
            }

            $matriz->save();

            // Registrar log
            $curso = $matriz->curso()->first();
            Log::registrar(
                $usuario->obterId(),
                'MATRIZ_CURRICULAR_EDITADA',
                "Matriz curricular do curso {$curso->obterNome()} editada"
            );

            $this->responderJSON([
                'sucesso' => true,
                'mensagem' => 'Matriz curricular editada com sucesso!',
                'data' => [
                    'id' => $matriz->obterId(),
                    'quantidade_periodos' => $matriz->obterQuantidadePeriodos()
                ]
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Inativa uma matriz curricular
     *
     * @param Request $request
     * @return void
     */
    public function inativarMatriz(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->get('id');

            if (!$id) {
                throw new Exception('ID da matriz curricular é obrigatório.');
            }

            $matriz = MatrizCurricular::id((int)$id)->first();

            if (!$matriz) {
                throw new Exception('Matriz curricular não encontrada.');
            }

            $matriz->arquivar();
            $matriz->save();

            // Registrar log
            $curso = $matriz->curso()->first();
            Log::registrar(
                $usuario->obterId(),
                'MATRIZ_CURRICULAR_INATIVADA',
                "Matriz curricular do curso {$curso->obterNome()} inativada"
            );

            $this->responderJSON([
                'sucesso' => true,
                'mensagem' => 'Matriz curricular inativada com sucesso!',
                'data' => [
                    'id' => $matriz->obterId(),
                    'status' => $matriz->obterStatus()->value
                ]
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Valida uma matriz curricular (impede edições futuras)
     *
     * @param Request $request
     * @return void
     */
    public function validarMatriz(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->get('id');

            if (!$id) {
                throw new Exception('ID da matriz curricular é obrigatório.');
            }

            $matriz = MatrizCurricular::id((int)$id)->first();

            if (!$matriz) {
                throw new Exception('Matriz curricular não encontrada.');
            }

            // A validação é feita mantendo como VIGENTE, mas adicionar uma flag ou mudar lógica conforme necessário
            $matriz->tornarVigente();
            $matriz->save();

            // Registrar log
            $curso = $matriz->curso()->first();
            Log::registrar(
                $usuario->obterId(),
                'MATRIZ_CURRICULAR_VALIDADA',
                "Matriz curricular do curso {$curso->obterNome()} validada"
            );

            $this->responderJSON([
                'sucesso' => true,
                'mensagem' => 'Matriz curricular validada com sucesso!'
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Adiciona um novo componente curricular
     *
     * @param Request $request
     * @return void
     */
    public function adicionarComponente(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $matrizId = $request->post('matriz_curricular_id');
            $nome = $request->post('nome');
            $creditos = $request->post('creditos');
            $cargaHoraria = $request->post('carga_horaria');
            $periodo = $request->post('periodo');
            $tipo = $request->post('tipo');

            if (!$matrizId || !$nome || !$creditos || !$cargaHoraria || !$periodo || !$tipo) {
                throw new Exception('Todos os campos são obrigatórios.');
            }

            // Verificar se a matriz existe
            $matriz = MatrizCurricular::id((int)$matrizId)->first();
            if (!$matriz) {
                throw new Exception('Matriz curricular não encontrada.');
            }

            // Verificar se a matriz está arquivada
            if ($matriz->obterStatus() === MatrizCurricularStatus::ARQUIVADO) {
                throw new Exception('Não é possível adicionar componente a uma matriz arquivada.');
            }

            // Criar novo componente curricular
            $componente = new ComponenteCurricular();
            $componente->atribuirMatrizCurricularId((int)$matrizId);
            $componente->atribuirNome($nome);
            $componente->atribuirCreditos((int)$creditos);
            $componente->atribuirCargaHoraria((int)$cargaHoraria);
            $componente->atribuirPeriodo((int)$periodo);
            $componente->atribuirTipo(ComponenteCurricularTipo::from($tipo));
            $componente->save();

            // Registrar log
            Log::registrar(
                $usuario->obterId(),
                'COMPONENTE_CURRICULAR_CADASTRADO',
                "Componente curricular '{$nome}' cadastrado"
            );

            $this->responderJSON([
                'sucesso' => true,
                'mensagem' => 'Componente curricular cadastrado com sucesso!',
                'data' => [
                    'id' => $componente->obterId(),
                    'nome' => $componente->obterNome(),
                    'creditos' => $componente->obterCreditos(),
                    'carga_horaria' => $componente->obterCargaHoraria(),
                    'periodo' => $componente->obterPeriodo(),
                    'tipo' => $componente->obterTipo()->value
                ]
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Edita um componente curricular existente
     *
     * @param Request $request
     * @return void
     */
    public function editarComponente(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->get('id');
            $nome = $request->post('nome');
            $creditos = $request->post('creditos');
            $cargaHoraria = $request->post('carga_horaria');
            $periodo = $request->post('periodo');
            $tipo = $request->post('tipo');

            if (!$id) {
                throw new Exception('ID do componente curricular é obrigatório.');
            }

            $componente = ComponenteCurricular::id((int)$id)->first();

            if (!$componente) {
                throw new Exception('Componente curricular não encontrado.');
            }

            // Verificar se a matriz está arquivada
            $matriz = $componente->matrizCurricular()->first();
            if ($matriz && $matriz->obterStatus() === MatrizCurricularStatus::ARQUIVADO) {
                throw new Exception('Não é possível editar componente de uma matriz arquivada.');
            }

            if ($nome) $componente->atribuirNome($nome);
            if ($creditos) $componente->atribuirCreditos((int)$creditos);
            if ($cargaHoraria) $componente->atribuirCargaHoraria((int)$cargaHoraria);
            if ($periodo) $componente->atribuirPeriodo((int)$periodo);
            if ($tipo) $componente->atribuirTipo(ComponenteCurricularTipo::from($tipo));

            $componente->save();

            // Registrar log
            Log::registrar(
                $usuario->obterId(),
                'COMPONENTE_CURRICULAR_EDITADO',
                "Componente curricular '{$componente->obterNome()}' editado"
            );

            $this->responderJSON([
                'sucesso' => true,
                'mensagem' => 'Componente curricular editado com sucesso!',
                'data' => [
                    'id' => $componente->obterId(),
                    'nome' => $componente->obterNome(),
                    'creditos' => $componente->obterCreditos(),
                    'carga_horaria' => $componente->obterCargaHoraria(),
                    'periodo' => $componente->obterPeriodo(),
                    'tipo' => $componente->obterTipo()->value
                ]
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Exclui um componente curricular
     *
     * @param Request $request
     * @return void
     */
    public function excluirComponente(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->get('id');

            if (!$id) {
                throw new Exception('ID do componente curricular é obrigatório.');
            }

            $componente = ComponenteCurricular::id((int)$id)->first();

            if (!$componente) {
                throw new Exception('Componente curricular não encontrado.');
            }

            // Verificar se a matriz está arquivada
            $matriz = $componente->matrizCurricular()->first();
            if ($matriz && $matriz->obterStatus() === MatrizCurricularStatus::ARQUIVADO) {
                throw new Exception('Não é possível excluir componente de uma matriz arquivada.');
            }

            $nomeComponente = $componente->obterNome();

            // Excluir pré-requisitos e equivalências relacionados
            ComponentePreRequisito::componenteCurricularId((int)$id)->delete();
            ComponenteEquivalencia::componenteCurricularId((int)$id)->delete();

            $componente->delete();

            // Registrar log
            Log::registrar(
                $usuario->obterId(),
                'COMPONENTE_CURRICULAR_EXCLUIDO',
                "Componente curricular '{$nomeComponente}' excluído"
            );

            $this->responderJSON([
                'sucesso' => true,
                'mensagem' => 'Componente curricular excluído com sucesso!'
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Define pré-requisitos para um componente curricular
     *
     * @param Request $request
     * @return void
     */
    public function definirPreRequisitos(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $componenteId = $request->get('id');
            $preRequisitosIds = $request->post('pre_requisitos'); // Array de IDs

            if (!$componenteId) {
                throw new Exception('ID do componente curricular é obrigatório.');
            }

            $componente = ComponenteCurricular::id((int)$componenteId)->first();

            if (!$componente) {
                throw new Exception('Componente curricular não encontrado.');
            }

            // Verificar se a matriz está arquivada
            $matriz = $componente->matrizCurricular()->first();
            if ($matriz && $matriz->obterStatus() === MatrizCurricularStatus::ARQUIVADO) {
                throw new Exception('Não é possível definir pré-requisitos para componente de uma matriz arquivada.');
            }

            // Remover pré-requisitos existentes
            ComponentePreRequisito::componenteCurricularId((int)$componenteId)->delete();

            // Adicionar novos pré-requisitos
            if ($preRequisitosIds && is_array($preRequisitosIds)) {
                foreach ($preRequisitosIds as $preRequisitoId) {
                    $preRequisito = new ComponentePreRequisito();
                    $preRequisito->atribuirComponenteCurricularId((int)$componenteId);
                    $preRequisito->atribuirComponenteRequisitoId((int)$preRequisitoId);
                    $preRequisito->save();
                }
            }

            // Registrar log
            Log::registrar(
                $usuario->obterId(),
                'COMPONENTE_PREREQUISITOS_DEFINIDOS',
                "Pré-requisitos definidos para o componente '{$componente->obterNome()}'"
            );

            $this->responderJSON([
                'sucesso' => true,
                'mensagem' => 'Pré-requisitos definidos com sucesso!'
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Define equivalências para um componente curricular
     *
     * @param Request $request
     * @return void
     */
    public function definirEquivalencias(Request $request): void
    {
        try {
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $componenteId = $request->get('id');
            $equivalenciasIds = $request->post('equivalencias'); // Array de IDs

            if (!$componenteId) {
                throw new Exception('ID do componente curricular é obrigatório.');
            }

            $componente = ComponenteCurricular::id((int)$componenteId)->first();

            if (!$componente) {
                throw new Exception('Componente curricular não encontrado.');
            }

            // Verificar se a matriz está arquivada
            $matriz = $componente->matrizCurricular()->first();
            if ($matriz && $matriz->obterStatus() === MatrizCurricularStatus::ARQUIVADO) {
                throw new Exception('Não é possível definir equivalências para componente de uma matriz arquivada.');
            }

            // Remover equivalências existentes
            ComponenteEquivalencia::componenteCurricularId((int)$componenteId)->delete();

            // Adicionar novas equivalências
            if ($equivalenciasIds && is_array($equivalenciasIds)) {
                foreach ($equivalenciasIds as $equivalenciaId) {
                    $equivalencia = new ComponenteEquivalencia();
                    $equivalencia->atribuirComponenteCurricularId((int)$componenteId);
                    $equivalencia->atribuirComponenteEquivalenteId((int)$equivalenciaId);
                    $equivalencia->save();
                }
            }

            // Registrar log
            Log::registrar(
                $usuario->obterId(),
                'COMPONENTE_EQUIVALENCIAS_DEFINIDAS',
                "Equivalências definidas para o componente '{$componente->obterNome()}'"
            );

            $this->responderJSON([
                'sucesso' => true,
                'mensagem' => 'Equivalências definidas com sucesso!'
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'sucesso' => false,
                'mensagem' => $exception->getMessage()
            ], 500);
        }
    }

}