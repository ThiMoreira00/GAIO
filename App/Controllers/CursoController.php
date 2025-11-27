<?php

/**
 * @file CursoController.php
 * @description Controlador responsável pelo gerenciamento dos cursos no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Core\Request;
use App\Models\Curso;
use App\Models\CursoGrau;
use App\Models\Log;
use App\Models\Enumerations\CursoStatus;
use App\Services\AutenticacaoService;
use Exception;

/**
 * Classe CursoController
 *
 * Gerencia os cursos no sistema
 *
 * @package App\Controllers
 * @extends Controller
 */
class CursoController extends Controller
{

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Renderiza a página inicial do sistema
     *
     * @return void
     */
    public function exibirIndex(): void
    {

        // Breadcrumbs = links de navegação
        $breadcrumbs = [
            ['label' => 'Cursos', 'url' => '/cursos']
        ];

        $graus = CursoGrau::obterTodos();

        // Renderiza a página inicial do sistema
        $this->renderizar('cursos/index', [
            'titulo' => 'Cursos',
            'graus' => $graus,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    /**
     * Renderiza a página de visualização de um curso específico
     *
     * @param Request $request
     * @return void
     */
    public function exibirCurso(Request $request): void
    {
        try {
            $id = $request->get('id');
            $nome = $request->get('nome');

            if (!$id || !$nome) {
                throw new Exception('ID e nome do curso são obrigatórios.');
            }

            // Converter ID para inteiro
            $id = (int)$id;

            // Buscar o curso
            $curso = Curso::buscarPorId($id);
            
            if (!$curso) {
                throw new Exception('Curso não encontrado.');
            }

            if ($curso->obterNomeSimplificado() !== $nome) {
                throw new Exception('Nome do curso não corresponde ao ID fornecido.');
            }

            // Carregar relacionamentos necessários
            $grau = $curso->grau()->first();
            if ($grau) {
                $curso->grau = $grau->nome;
            }

            // Breadcrumbs = links de navegação
            $breadcrumbs = [
                ['label' => 'Cursos', 'url' => '/cursos'],
                ['label' => $curso->nome, 'url' => sprintf("/cursos/visualizar/%d-%s", $curso->obterId(), $curso->obterNomeSimplificado())]
            ];

            // Renderiza a página de visualização
            $this->renderizar('cursos/visualizar', [
                'titulo' => $curso->nome,
                'curso' => $curso,
                'graus' => CursoGrau::obterTodos(),
                'breadcrumbs' => $breadcrumbs
            ]);

        } catch (Exception $exception) {
            // Redirecionar para página de erro ou listagem de cursos

            flash()->erro($exception->getMessage() ?? 'Erro ao carregar o curso.');

            header('Location: /cursos');
            exit();
        }
    }


    // --- MÉTODOS DE REQUISIÇÕES ---

    /**
     * Função para obter cursos
     *
     * @param Request $request
     * @return void
     */
    public function obterCursos(Request $request): void
    {
        try {

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            // Obtém os cursos (com paginação)
            $cursos = Curso::obterTodos();

            // Adicionar alguns campos
            foreach ($cursos as $curso) {
                $curso->grau = $curso->grau()->first()->obterNome();
            }

            // Ordenar pelo nome, ordenando primeiramente pelo status 'arquivado' e depois pelo nome
            $cursos = $cursos->sortBy(function($curso) {
                return [$curso->status === 'arquivado' ? 1 : 0, $curso->nome];
            })->values();

            $this->responderJSON([
                'status' => 'sucesso',
                'cursos' => $cursos
            ]);

        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao obter os cursos.'
            ]);
        }
    }

    /**
     * Função para obter um curso específico
     * 
     * @param Request $request
     * @return void
     */
    public function obterCurso(Request $request) {

        try {

            $id = $request->get('id');

            if (!$id) {
                throw new Exception('ID do curso é obrigatório.');
            }

            // Buscar o curso
            $curso = Curso::buscarPorId($id);
            if (!$curso) {
                throw new Exception('Curso não encontrado.');
            }

            $this->responderJSON([
                'status' => 'sucesso',
                'curso' => $curso
            ]);



        } catch (Exception $exception) {

            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao obter o curso.'
            ]);

        }

    }

    /**
     * Função para filtrar os cursos, com base nos parâmetros passados
     *
     * @param Request $request
     * @return void
     */
    public function filtrarCursos(Request $request): void {
        try {

            // TODO: Adicionar token CSRF

            $grau = $request->get('status') ?? null;
            $pagina = $request->get('pagina') ?? null;
            $busca = $request->get('busca') ?? null;

            // Puxa o ID do grau antes
            $grau_id = ($grau) ? CursoGrau::buscarPorNome($grau)?->obterId() : null;

            $query = Curso::query();

            if ($grau_id) {
                $query->where('grau_id', $grau_id);
            }

            if ($busca) {
                $query->where('nome', 'LIKE', "%$busca%");
            }

            $cursos = $query->paginate(15);

            foreach ($cursos as $curso) {
                $curso->grau = $curso->grau()->first()->obterNome();
            }

            // Ordenar pelo nome, ordenando primeiramente pelo status 'arquivado' e depois pelo nome
            $cursos = $cursos->sortBy(function($curso) {
                return [$curso->status === 'arquivado' ? 1 : 0, $curso->nome];
            })->values();

            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $cursos
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao filtrar os cursos.'
            ]);
        }
    }

    /**
     * Função para adicionar um novo curso
     * 
     * @param Request $request
     * @return void
     */
    public function adicionarCurso(Request $request): void {
        try {

            // TODO: Validar CSRF
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $nome = $request->post('nome');
            $sigla = empty($request->post('sigla')) ? null : $request->post('sigla');
            $emec_codigo = empty($request->post('emec-codigo')) ? null : $request->post('emec-codigo');
            $grau_id = $request->post('grau');
            $duracao_minima = $request->post('duracao-minima');
            $duracao_maxima = $request->post('duracao-maxima');

            if (!$nome || !$grau_id || !$duracao_minima || !$duracao_maxima) {
                throw new Exception('Nome, grau, duração mínima e máxima do curso são obrigatórios.');
            }

            // Verifica se tem até 8 números no código e-MEC, se informado
            if ($emec_codigo && !preg_match('/^\d{1,8}$/', $emec_codigo)) {
                throw new Exception('O código (e-MEC) deve conter até 8 números.');
            }

            // Verifica se o ID do grau existe
            $grau = CursoGrau::buscarPorId($grau_id);

            if (!$grau) {
                throw new Exception('Grau do curso inválido.');
            }

            // Validar períodos
            if ($duracao_minima > $duracao_maxima) {
                throw new Exception('A duração mínima não pode ser maior que a máxima.');
            }

            // Consulta se o curso com o mesmo código já existe, se não for nulo
            if ($emec_codigo) {
                $curso_por_emec_codigo = Curso::buscarPorEmecCodigo($emec_codigo);
                if ($curso_por_emec_codigo) {
                    throw new Exception('Já existe um curso cadastrado com o mesmo código (e-MEC).');
                }
            }

            // Monta mensagem de criação do curso
            $mensagemLog = sprintf(
                'Novo curso "%s" adicionado ao sistema.' . PHP_EOL . PHP_EOL .
                'Nome: %s' . PHP_EOL .
                'Sigla: %s' . PHP_EOL .
                'Código (e-MEC): %s' . PHP_EOL .
                'Grau: %s' . PHP_EOL .
                'Duração mínima: %d períodos' . PHP_EOL .
                'Duração máxima: %d períodos',
                $nome,
                $nome,
                $sigla ?? 'N/A',
                $emec_codigo ?? 'N/A',
                $grau->obterNome(),
                $duracao_minima,
                $duracao_maxima
            );

            // Registra log da criação do curso
            Log::registrar($usuario->obterId(), 'Criação do Curso', $mensagemLog);

            $curso = new Curso();
            $curso->atribuirNome($nome);
            $curso->atribuirSigla($sigla);
            $curso->atribuirEmecCodigo($emec_codigo);
            $curso->atribuirGrau($grau);
            $curso->atribuirDuracaoMinima($duracao_minima);
            $curso->atribuirDuracaoMaxima($duracao_maxima);
            $curso->salvar();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Curso adicionado com sucesso.'
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao adicionar o curso.'
            ]);
        }
    }

    /**
     * Função para editar um curso existente
     * 
     * @param Request $request
     * @return void
     */
    public function editarCurso(Request $request): void {
        try {

            // TODO: Validar CSRF
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $id = $request->parametroRota('id');
            $nome = $request->post('nome');
            $sigla = empty($request->post('sigla')) ? null : $request->post('sigla');
            $emec_codigo = empty($request->post('emec-codigo')) ? null : $request->post('emec-codigo');
            $grau_id = $request->post('grau');
            $duracao_minima = $request->post('duracao-minima');
            $duracao_maxima = $request->post('duracao-maxima');

            if (!$id) {
                throw new Exception('ID do curso é obrigatório.');
            }

            // Buscar o curso existente
            $curso = Curso::buscarPorId($id);
            if (!$curso) {
                throw new Exception('Curso não encontrado.');
            }

            // Verifica se o ID do grau existe
            $grau = CursoGrau::buscarPorId($grau_id);
            if (!$grau) {
                throw new Exception('Grau do curso inválido.');
            }

            // Validar períodos
            if ($duracao_minima && $duracao_maxima && $duracao_minima > $duracao_maxima) {
                throw new Exception('A duração do curso não pode ser maior que a máxima.');
            }

            // Consulta se o curso com o mesmo código já existe (excluindo o atual)
            if ($emec_codigo) {
                $curso_por_emec_codigo = Curso::buscarPorEmecCodigo($emec_codigo);
                if ($curso_por_emec_codigo && $curso_por_emec_codigo->obterId() != $id) {
                    throw new Exception('Já existe um curso cadastrado com o mesmo código (e-MEC).');
                }
            }


            if ($curso->obterNome() === $nome &&
                $curso->obterSigla() === $sigla &&
                $curso->obterEmecCodigo() === $emec_codigo &&
                $curso->obterGrauId() === (int)$grau_id &&
                $curso->obterDuracaoMinima() === (int)$duracao_minima &&
                $curso->obterDuracaoMaxima() === (int)$duracao_maxima) {
                throw new Exception('Nenhum dado foi alterado.');
            }

            // Prepara dados antigos e novos para o log
            $dadosAntigos = [];
            $dadosNovos = [];

            if ($curso->obterNome() !== $nome) {
                $dadosAntigos[] = 'Nome: ' . $curso->obterNome();
                $dadosNovos[] = 'Nome: ' . $nome;
            }

            if ($curso->obterSigla() !== $sigla) {
                $dadosAntigos[] = 'Sigla: ' . $curso->obterSigla();
                $dadosNovos[] = 'Sigla: ' . $sigla;
            }

            if ($curso->obterEmecCodigo() !== $emec_codigo) {
                $dadosAntigos[] = 'Código (e-MEC): ' . $curso->obterEmecCodigo();
                $dadosNovos[] = 'Código (e-MEC): ' . $emec_codigo;
            }

            if ($curso->obterGrauId() !== (int)$grau_id) {
                $dadosAntigos[] = 'Grau: ' . $curso->obterGrauId();
                $dadosNovos[] = 'Grau: ' . (int)$grau_id;
            }

            if ($curso->obterDuracaoMinima() !== (int)$duracao_minima) {
                $dadosAntigos[] = 'Duração mínima: ' . $curso->obterDuracaoMinima();
                $dadosNovos[] = 'Duração mínima: ' . (int)$duracao_minima;
            }

            if ($curso->obterDuracaoMaxima() !== (int)$duracao_maxima) {
                $dadosAntigos[] = 'Duração máxima: ' . $curso->obterDuracaoMaxima();
                $dadosNovos[] = 'Duração máxima: ' . (int)$duracao_maxima;
            }

            // Monta mensagem de alteração do curso
            $mensagemLog = sprintf(
                'Os detalhes do curso "%s" (ID: %d) foram atualizados.' . PHP_EOL . PHP_EOL .
                'Dados antigos: %s' . PHP_EOL .
                'Dados novos: %s',
                $curso->obterNome(),
                $curso->obterId(),
                !empty($dadosAntigos) ? implode(', ', $dadosAntigos) : 'Nenhum',
                !empty($dadosNovos) ? implode(', ', $dadosNovos) : 'Nenhum'
            );

            // Registra log da atualização do curso
            Log::registrar($usuario->obterId(), 'Atualização do Curso', $mensagemLog);

            // Atualizar dados do curso
            $curso->atribuirNome($nome);
            $curso->atribuirSigla($sigla);
            $curso->atribuirEmecCodigo($emec_codigo);
            $curso->atribuirGrau($grau);
            $curso->atribuirDuracaoMinima($duracao_minima);
            $curso->atribuirDuracaoMaxima($duracao_maxima);
            $curso->salvar();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Curso atualizado com sucesso.',
                'data' => $curso
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao editar o curso.'
            ]);
        }
    }

    /**
     * Função para arquivar um curso
     * 
     * @param Request $request
     * @return void
     */
    public function arquivarCurso(Request $request): void {

        try {

            // TODO: Validar CSRF

            $id = $request->get('id');

            if (!$id) {
                throw new Exception('ID do curso é obrigatório.');
            }

            // Buscar o curso existente
            $curso = Curso::buscarPorId($id);
            if (!$curso) {
                throw new Exception('Curso não encontrado.');
            }

            if ($curso->obterStatus() === CursoStatus::ARQUIVADO->value) {
                throw new Exception('O curso já está arquivado.');
            }

            // TODO: Adicionar outras validações
            // - Verificar se o curso já está arquivado
            // - Verificar se o curso está com período letivo ativo
            // - Verificar se o curso está associado a turmas ativas


            // Arquivar o curso
            $curso->arquivar();
            $curso->salvar();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Curso arquivado com sucesso.',
                'data' => $curso
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao arquivar o curso.'
            ]);
        }
    }

}