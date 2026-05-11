<?php

/**
 * @file NotificacaoController.php
 * @description Controlador responsável pelo gerenciamento das notificações do usuário
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação das classes
use App\Core\Controller;
use App\Services\AuthService;
use App\Services\NotificacaoService;
use App\Models\Usuario;
use Exception;
use Illuminate\Http\Request;

/**
 * Classe NotificacaoController
 *
 * Gerencia as notificações do usuário
 *
 * @package App\Controllers
 * @extends Controller
 */
class NotificacaoController extends Controller
{

    // --- ATRIBUTOS ---
    /**
     * Instância do serviço de notificações
     *
     * @var NotificacaoService
     */
    private NotificacaoService $notificacaoService;

    // --- MÉTODOS ---

    /**
     * Construtor da classe
     *
     */
    public function __construct()
    {
        $this->notificacaoService = new NotificacaoService();
    }

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Exibe a página de notificações do usuário
     *
     * @return void
     */
    public function exibirIndex(): void
    {
        try {

            // Verifica se o usuário está autenticado
            $usuario = AuthService::obterUsuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            // TODO: Melhorar performance de listar notificações por usuário
            $notificacoes = $this->notificacaoService->listarPorUsuario($usuario, [
                'porPagina' => 15
            ]);

            // Breadcrumbs = links de navegação
            $breadcrumbs = [
                ['label' => 'Notificações', 'url' => '/notificacoes']
            ];

            // Renderiza a página de notificações
            $this->renderizar('conta/notificacoes', [
                'titulo' => 'Notificações',
                'breadcrumbs' => $breadcrumbs,
                'notificacoes' => $notificacoes
            ]);

        } catch (Exception $e) {

            // Exibe mensagem de erro
            flash()->erro('Erro ao carregar notificações: ' . $e->getMessage() );
            $this->redirecionar('/inicio');

        }
    }

    /**
     * Marca a notificação como lida
     *
     * @param Request $request
     * @return void
     */
    public function marcarComoLida(Request $request, string|int $id): void
    {
        try {

            $this->validarTokenCSRF($request);

            // Obtenção dos dados do formulário
            $notificacaoId = (int) $id;

            if ($notificacaoId <= 0) {
                throw new Exception('ID de notificação inválido.');
            }

            // Verifica se o usuário está autenticado
            $usuario = AuthService::obterUsuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            // Verifica se o usuário é destinatário da notificação
            // 1. Ou precisa saber se o destinatário é do tipo Usuário e possui o ID de destinatário com o mesmo ID
            $verificarDestinatario = $this->notificacaoService->verificarDestinatarioNotificacao($notificacaoId, $usuario);

            if (!$verificarDestinatario) {
                throw new Exception('Usuário não é destinatário desta notificação.');
            }

            // Marca a notificação como lida
            $this->notificacaoService->marcarComoLida($notificacaoId, $usuario->obterId());

            // Retorna resposta JSON
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Notificação marcada como lida.'
            ]);

        } catch (Exception $e) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage() ?? 'Erro ao marcar a notificação como lida.'
            ]);
        }
    }

    /**
     * Marcar todas as notificações como lidas
     *
     * @param Request $request
     * @return void
     */
    public function marcarTodasComoLidas(Request $request): void
    {
        try {

            $this->validarTokenCSRF($request);

            // Verifica se o usuário está autenticado
            $usuario = AuthService::obterUsuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            // Marca todas as notificações como lidas
            $this->notificacaoService->marcarTodasComoLidas($usuario);

            // Retorna resposta JSON
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Todas as notificações foram marcadas como lidas.'
            ]);

        } catch (Exception $e) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage() ?? 'Erro ao marcar todas as notificações como lidas.'
            ]);
        }
    }

    /**
     * Busca as notificações do usuário (com base nos filtros)
     *
     * @return void
     */
    public function filtrarNotificacoes(Request $request): void
    {
        try {

            // Verifica se o usuário está autenticado
            $usuario = AuthService::obterUsuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $this->validarTokenCSRF($request);

            // Verifica se tem filtros
            $status = (string) ($request->input('status') ?? 'todas');
            if (!in_array($status, ['todas', 'lidas', 'nao_lidas'], true)) {
                $status = 'todas';
            }

            $pagina = (int) ($request->input('pagina') ?? 1);
            if ($pagina <= 0) {
                $pagina = 1;
            }

            $busca = trim((string) ($request->input('busca') ?? ''));
            if ($busca === '') {
                $busca = null;
            }

            $arr = array_filter([
                'porPagina' => 15,
                'status' => $status ?? null,
                'pagina' => $pagina ?? null,
                'busca' => $busca ?? null
            ]);

            // TODO: Melhorar performance de listar notificações por usuário
            $notificacoes = $this->notificacaoService->listarPorUsuario($usuario, $arr);

            // Retorna resposta JSON no formato esperado pela Tab2
            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $notificacoes->items(), // Array de notificações
                'paginacao' => [
                    'paginaAtual' => $notificacoes->currentPage(),
                    'ultimaPagina' => $notificacoes->lastPage(),
                    'total' => $notificacoes->total(),
                    'porPagina' => $notificacoes->perPage(),
                    'temMais' => $notificacoes->currentPage() < $notificacoes->lastPage()
                ]
            ]);

        } catch (Exception $e) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage() ?? 'Erro ao buscar notificações.'
            ]);
        }
    }
}