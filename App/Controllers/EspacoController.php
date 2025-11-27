<?php

/**
 * @file EspacoController.php
 * @description Controlador responsável pelo gerenciamento dos espaços no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Core\Request;
use App\Models\Espaco;
use App\Models\Log;
use App\Models\Enumerations\EspacoTipo;
use App\Services\AutenticacaoService;
use Exception;

/**
 * Classe EspacoController
 *
 * Gerencia os espaços no sistema
 *
 * @package App\Controllers
 * @extends Controller
 */
class EspacoController extends Controller
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
            ['label' => 'Espaços', 'url' => '/espacos']
        ];

        $tipos = EspacoTipo::cases();

        // Renderiza a página inicial do sistema
        $this->renderizar('espacos/index', [
            'titulo' => 'Espaços',
            'tipos' => $tipos,
            'breadcrumbs' => $breadcrumbs
        ]);
    }


    // --- MÉTODOS DE REQUISIÇÕES ---

    /**
     * Função para obter os espaços
     * 
     * @param Request $request
     * @return void
     */
    public function obterEspacos(Request $request): void
    {
        try {

            // TODO: Token CSRF

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não autenticado.', 401);
            }

            // Obtém os espaços
            $espacos = Espaco::obterTodos();

            // Ordenar pelo nome, ordenando primeiramente pelo status (arquivados primeiro)
            $espacos = $espacos->sortBy(function ($espaco) {
                return [$espaco->status === 'arquivado' ? 1 : 0, $espaco->nome];
            })->values();

            // Alterar o nome dos status para exibição
            // 0: Arquivado
            // 1: Ativo
            foreach ($espacos as $espaco) {
                $espaco['status'] = $espaco['status'] ? 'Ativo' : 'Arquivado';
            }

            $this->responderJSON([
                'status' => 'sucesso',
                'dados' => $espacos
            ]);


        } catch (Exception $e) {
            
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage()
            ], $e->getCode());
        }
    }


    /**
     * Função para filtrar os cursos, com base nos parâmetros enviados
     * 
     * @param Request $request
     * @return void
     */
    public function filtrarEspacos(Request $request): void
    {
        try {

            // TODO: Adicionar token CSRF

            $tipo = $request->get('status') ?? null;
            $pagina = $request->get('pagina') ?? 1;
            $busca = $request->get('busca') ?? null;

            $query = Espaco::query();

            // Se tiver tipo, filtrar por tipo
            if ($tipo) {
                $query->where('tipo', $tipo);
            }

            // Se tiver busca, filtrar por nome ou código
            if ($busca) {
                $query->where(function ($subquery) use ($busca) {
                    $subquery->where('nome', 'LIKE', "%$busca%")
                             ->orWhere('codigo', 'LIKE', "%$busca%");
                });
            }

            // Paginação
            $espacos = $query->paginate(15);

            // Converter para array e modificar status e tipo
            $espacos = $espacos->map(function ($espaco) {
                // Obter o enum pelo nome (ex: "AUDITORIO" -> EspacoTipo::AUDITORIO)
                // $tipoEnum = constant(EspacoTipo::class . '::' . $espaco->tipo);
                
                return array_merge($espaco->obterDados(), [
                    'status' => $espaco->status ? 'ativo' : 'arquivado'
                ]);
            });

            // Ordenar: arquivados primeiro, depois por nome
            $espacos = $espacos->sortBy(function ($espaco) {
                return [$espaco['status'] === 'arquivado' ? 1 : 0, $espaco['nome']];
            })->values();

            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $espacos
            ]);


        } catch (Exception $e) {
            
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage()
            ], $e->getCode());
        }
    }


    /**
     * Função para adicionar um novo espaço no sistema
     * 
     * @param Request $request
     * @return void
     */
    public function adicionarEspaco(Request $request): void
    {
        try {

            // TODO: Adicionar token CSRF

            $usuario = AutenticacaoService::usuarioAutenticado();
            if (!$usuario) {
                throw new Exception('Usuário não autenticado.', 401);
            }

            $nome = $request->post('nome');
            $capacidadeMaxima = $request->post('capacidade-maxima');
            $tipo = $request->post('tipo');

            // Validações básicas
            if (!$nome || !$capacidadeMaxima || !$tipo) {
                throw new Exception('Todos os campos são obrigatórios.', 400);
            }

            // Se a capacidade máxima for menor que 1
            if ($capacidadeMaxima < 1) {
                throw new Exception('A capacidade máxima deve ser maior que zero.', 400);
            }

            if (!EspacoTipo::fromName($tipo)) {
                throw new Exception('Tipo de espaço inválido.', 400);
            }
            
            // Cria o novo espaço
            $espaco = new Espaco();
            $espaco->atribuirNome($nome);
            $espaco->atribuirCapacidadeMaxima($capacidadeMaxima);
            $espaco->atribuirTipo(EspacoTipo::fromName($tipo));
            $espaco->atribuirStatus(true);
            $espaco->salvar(); // Salva primeiro para gerar o ID
            
            // Gera o código após ter o ID
            $espaco->atribuirCodigo($espaco->gerarCodigo());
            $espaco->salvar(); // Salva novamente com o código

            // Monta mensagem de criação do espaço
            $mensagemLog = sprintf(
                'Novo espaço "%s" adicionado ao sistema.' . PHP_EOL . PHP_EOL .
                'Nome: %s' . PHP_EOL .
                'Código: %s' . PHP_EOL .
                'Capacidade máxima: %d pessoas' . PHP_EOL .
                'Tipo: %s',
                $nome,
                $nome,
                $espaco->obterCodigo(),
                $capacidadeMaxima,
                $tipo
            );

            // Registra log da criação do espaço
            Log::registrar($usuario->obterId(), 'Criação do Espaço', $mensagemLog);

            // Resposta de sucesso
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Espaço adicionado com sucesso.',
                'data' => $espaco
            ]);

        } catch (Exception $e) {
            
            // Retorna resposta de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage()
            ], $e->getCode());
        }
    }


    /**
     * Função para editar um espaço existente
     * 
     * @param Request $request
     * @return void
     */
    public function editarEspaco(Request $request): void
    {

        try {

            $usuario = AutenticacaoService::usuarioAutenticado();
            if (!$usuario) {
                throw new Exception('Usuário não autenticado.', 401);
            }
            // TODO: Adicionar token CSRF

            $id = $request->parametroRota('id');
            $nome = $request->post('nome');
            $capacidadeMaxima = $request->post('capacidade-maxima');
            $tipo = $request->post('tipo');

            // Validações básicas
            if (!$nome || !$capacidadeMaxima || !$tipo) {
                throw new Exception('Todos os campos são obrigatórios.', 400);
            }

            // Verifica se o espaço existe
            $espaco = Espaco::find($id);

            if (!$espaco) {
                throw new Exception('Espaço não encontrado.', 404);
            }

            // Se a capacidade máxima for menor que 1
            if ($capacidadeMaxima < 1) {
                throw new Exception('A capacidade máxima deve ser maior que zero.', 400);
            }

            // Se o tipo do espaço for inválido (buscar pelo índice e não pelo valor)
            $espacoTipoTodos = array_column(EspacoTipo::cases(), 'name');
            if (!in_array($tipo, $espacoTipoTodos)) {
                throw new Exception('Tipo de espaço inválido.', 400);
            }


            if ($espaco->obterNome() === $nome &&
                $espaco->obterCapacidadeMaxima() === (int)$capacidadeMaxima &&
                $espaco->obterTipo()->name === $tipo) {
                throw new Exception('Nenhum dado foi alterado.');
            }

            // Prepara dados antigos e novos para o log
            $dadosAntigos = [];
            $dadosNovos = [];

            if ($espaco->obterNome() !== $nome) {
                $dadosAntigos[] = 'Nome: ' . $espaco->obterNome();
                $dadosNovos[] = 'Nome: ' . $nome;
            }

            if ($espaco->obterCapacidadeMaxima() !== (int)$capacidadeMaxima) {
                $dadosAntigos[] = 'Capacidade Máxima: ' . $espaco->obterCapacidadeMaxima();
                $dadosNovos[] = 'Capacidade Máxima: ' . $capacidadeMaxima;
            }

            if ($espaco->obterTipo()->name !== $tipo) {
                $dadosAntigos[] = 'Tipo: ' . $espaco->obterTipo()->value;
                $tipoEnumNovo = constant(EspacoTipo::class . '::' . $tipo);
                $dadosNovos[] = 'Tipo: ' . $tipoEnumNovo->value;
            }

            // Atualiza os dados do espaço
            $espaco->atribuirNome($nome);
            $espaco->atribuirCapacidadeMaxima($capacidadeMaxima);
            $espaco->atribuirTipo(constant(EspacoTipo::class . '::' . $tipo));
            $espaco->salvar();

            // Monta mensagem de alteração do espaço
            $mensagemLog = sprintf(
                'Os detalhes do espaço "%s" (Código: %d) foram atualizados.' . PHP_EOL . PHP_EOL .
                'Dados antigos: %s' . PHP_EOL .
                'Dados novos: %s',
                $espaco->obterNome(),
                $espaco->obterCodigo(),
                !empty($dadosAntigos) ? implode(', ', $dadosAntigos) : 'Nenhum',
                !empty($dadosNovos) ? implode(', ', $dadosNovos) : 'Nenhum'
            );

            // Registra log da atualização do espaço
            Log::registrar($usuario->obterId(), 'Atualização do Espaço', $mensagemLog);

            // Resposta de sucesso
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Espaço atualizado com sucesso.'
            ]);


        } catch (Exception $e) {
         
            // Retorna resposta de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage()
            ], $e->getCode());
        }
    }


    /**
     * Função para arquivar um espaço
     * 
     * @param Request $request
     * @return void
     */
    public function arquivarEspaco(Request $request): void
    {

        try {

            $usuario = AutenticacaoService::usuarioAutenticado();
            if (!$usuario) {
                throw new Exception('Usuário não autenticado.', 401);
            }

            $id = $request->parametroRota('id');

            if (!$id) {
                throw new Exception('ID do espaço não fornecido.', 400);
            }

            // Verifica se o espaço existe
            $espaco = Espaco::find($id);

            if (!$espaco) {
                throw new Exception('Espaço não encontrado.', 404);
            }

            if (!$espaco->obterStatus()) {
                throw new Exception('O espaço já está arquivado.', 400);
            }

            // Arquiva o espaço
            $espaco->arquivar();
            $espaco->salvar();

            // Monta mensagem de arquivamento do espaço
            $mensagemLog = sprintf(
                'O espaço "%s" (Código: %d) foi arquivado.',
                $espaco->obterNome(),
                $espaco->obterCodigo()
            );

            // Registra log do arquivamento do espaço
            Log::registrar($usuario->obterId(), 'Arquivamento do Espaço', $mensagemLog);

            // Resposta de sucesso
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Espaço arquivado com sucesso.',
                'data' => $espaco
            ]);

        } catch (Exception $e) {
            // Retorna resposta de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage()
            ], $e->getCode());
        }

    }


    /**
     * Função para excluir um espaço
     * 
     * @param Request $request
     * @return void
     */
    public function excluirEspaco(Request $request): void
    {

        try {

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não autenticado.', 401);
            }

            $id = $request->parametroRota('id');

            if (!$id) {
                throw new Exception('ID do espaço não fornecido.', 400);
            }

            // Verifica se o espaço existe
            $espaco = Espaco::find($id);

            if (!$espaco) {
                throw new Exception('Espaço não encontrado.', 404);
            }

            // Monta mensagem de exclusão do espaço
            $mensagemLog = sprintf(
                'O espaço "%s" (Código: %d) foi excluído do sistema.',
                $espaco->obterNome(),
                $espaco->obterCodigo()
            );

            $espacoCopia = clone $espaco;

            // Exclui o espaço
            $espaco->excluir();

            // Registra log da exclusão do espaço
            Log::registrar($usuario->obterId(), 'Exclusão do Espaço', $mensagemLog);

            // Resposta de sucesso
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Espaço excluído com sucesso.',
                'data' => $espacoCopia
            ]);

        } catch (Exception $e) {
            // Retorna resposta de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage()
            ], $e->getCode());
        }

    }


    /**
     * Função para reativar um espaço
     * 
     * @param Request $request
     * @return void
     */
    public function reativarEspaco(Request $request): void
    {

        try {

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não autenticado.', 401);
            }

            $id = $request->parametroRota('id');

            if (!$id) {
                throw new Exception('ID do espaço não fornecido.', 400);
            }

            // Verifica se o espaço existe
            $espaco = Espaco::find($id);

            if (!$espaco) {
                throw new Exception('Espaço não encontrado.', 404);
            }

            if ($espaco->obterStatus()) {
                throw new Exception('O espaço já está ativo.', 400);
            }

            // Reativa o espaço
            $espaco->atribuirStatus(true);
            $espaco->salvar();

            // Monta mensagem de reativação do espaço
            $mensagemLog = sprintf(
                'O espaço "%s" (Código: %d) foi reativado.',
                $espaco->obterNome(),
                $espaco->obterCodigo()
            );

            // Registra log da reativação do espaço
            Log::registrar($usuario->obterId(), 'Reativação do Espaço', $mensagemLog);

            // Resposta de sucesso
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Espaço reativado com sucesso.',
                'data' => $espaco
            ]);

        } catch (Exception $e) {
            // Retorna resposta de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage()
            ], $e->getCode());
        }

    }

}