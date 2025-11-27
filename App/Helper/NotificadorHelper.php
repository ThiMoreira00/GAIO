<?php

/**
 * @file NotificadorFlash.php
 * @description Classe-auxiliar para gerenciamento de mensagens relacionadas ao sistema para o usuário.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Helper;

/**
 * Classe NotificadorFlash
 *
 * Responsável por gerenciar e exibir mensagens flash para o usuário
 *
 * @package App\Helper
 */
class NotificadorHelper
{

    // --- ATRIBUTOS ---

    /**
     * Chave usada para armazenar as mensagens na sessão
     * @var string
     */
    protected string $chaveSessao = 'mensagem_flash';

    /**
     * Mapa de configurações para cada tipo de notificação (o tipo a classes CSS, ícones SVG e títulos padrão)
     * @var array
     */
    private array $configuracaoTipos = [
        'erro' => [
            'div_classes' => 'bg-red-100 p-4 rounded-lg mb-6',
            'icone_classes' => 'h-5 w-5 text-red-400',
            'icone_svg_path' => 'M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z',
            'titulo_classes' => 'text-sm font-medium text-red-800',
            'mensagem_classes' => 'mt-2 text-sm text-red-700',
            'titulo_padrao' => 'Oops!'
        ],
        'sucesso' => [
            'div_classes' => 'bg-green-100 p-4 rounded-lg mb-6',
            'icone_classes' => 'h-5 w-5 text-green-400',
            'icone_svg_path' => 'M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.707-9.293a1 1 0 0 0-1.414-1.414L9 10.586 7.707 9.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4Z',
            'titulo_classes' => 'text-sm font-medium text-green-800',
            'mensagem_classes' => 'mt-2 text-sm text-green-700',
            'titulo_padrao' => 'Sucesso!'
        ],
        'aviso' => [
            'div_classes' => 'bg-yellow-100 p-4 rounded-lg mb-6',
            'icone_classes' => 'h-5 w-5 text-yellow-400',
            'icone_svg_path' => 'M8.485 2.495c.646-1.113 2.384-1.113 3.03 0l6.28 10.875c.646 1.113-.273 2.505-1.515 2.505H3.72c-1.242 0-2.161-1.392-1.515-2.505l6.28-10.875ZM10 6a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z',
            'titulo_classes' => 'text-sm font-medium text-yellow-800',
            'mensagem_classes' => 'mt-2 text-sm text-yellow-700',
            'titulo_padrao' => 'Atenção!'
        ],
        'info' => [
            'div_classes' => 'bg-blue-100 p-4 rounded-lg mb-6',
            'icone_classes' => 'h-5 w-5 text-blue-400',
            'icone_svg_path' => 'M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15h.506a1.75 1.75 0 0 0 1.74-1.934l-.459-2.066a.25.25 0 0 1 .244-.304H13a.75.75 0 0 0 0-1.5H9Z',
            'titulo_classes' => 'text-sm font-medium text-blue-800',
            'mensagem_classes' => 'mt-2 text-sm text-blue-700',
            'titulo_padrao' => 'Informação'
        ]
    ];


    // --- MÉTODOS ---

    /**
     * Construtor da classe
     *
     * @return void
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Adiciona uma mensagem flash
     *
     * @param string $tipo
     * @param string $mensagem
     * @param string|null $titulo
     * @return void
     */
    public function adicionar(string $tipo, string $mensagem, ?string $titulo = null): void
    {
        // Verifica se a chave de sessão existe, se não, cria uma
        if (!isset($_SESSION[$this->chaveSessao])) {
            $_SESSION[$this->chaveSessao] = [];
        }

        // Adiciona a mensagem à lista de mensagens
        $_SESSION[$this->chaveSessao][] = [
            'tipo' => $tipo,
            'mensagem' => $mensagem,
            'titulo' => $titulo
        ];
    }

    /**
     * Adicionar mensagem de sucesso
     *
     * @param string $mensagem
     * @param string|null $titulo
     * @return void
     */
    public function sucesso(string $mensagem, ?string $titulo = null): void
    {
        $this->adicionar('sucesso', $mensagem, $titulo);
    }

    /**
     * Adicionar mensagem de erro
     *
     * @param string $mensagem
     * @param string|null $titulo
     * @return void
     */
    public function erro(string $mensagem, ?string $titulo = null): void
    {
        $this->adicionar('erro', $mensagem, $titulo);
    }

    /**
     * Adicionar mensagem de informação
     *
     * @param string $mensagem
     * @param string|null $titulo
     * @return void
     */
    public function info(string $mensagem, ?string $titulo = null): void
    {
        $this->adicionar('info', $mensagem, $titulo);
    }

    /**
     * Adicionar mensagem de aviso
     *
     * @param string $mensagem
     * @param string|null $titulo
     * @return void
     */
    public function aviso(string $mensagem, ?string $titulo = null): void
    {
        $this->adicionar('aviso', $mensagem, $titulo);
    }

    /**
     * Limpa todas as mensagens flash da sessão
     *
     * @return void
     */
    protected function limparMensagens(): void
    {
        unset($_SESSION[$this->chaveSessao]);
    }

    /**
     * Obtém todas as mensagens flash da sessão
     *
     * @return array
     */
    protected function obterMensagens(): array
    {
        $mensagens = $_SESSION[$this->chaveSessao] ?? [];
        return $mensagens;
    }

    /**
     * Renderiza todas as mensagens pendentes em HTML estilizado com Tailwind
     *
     * @param bool $limpar
     * @return string
     */
    public function exibir(bool $limpar = true): string
    {

        // Obtém todas as mensagens flash da sessão
        $mensagens = $this->obterMensagens();

        // Verifica se deve limpar as mensagens flash após renderização
        if ($limpar) {
            $this->limparMensagens();
        }

        // Renderiza cada mensagem em HTML
        $html = '';

        foreach ($mensagens as $msg) {
            $html .= $this->renderizarAlerta($msg);
        }

        // Retorna o HTML final
        return $html;
    }

    /**
     * Renderiza as mensagens flash em HTML (estilizado com TailwindCSS)
     *
     * @param array $notificacao
     * @return string
     */

    private function renderizarAlerta(array $notificacao): string
    {
        // Obtém a configuração visual baseada no tipo da notificação
        $config = $this->configuracaoTipos[$notificacao['tipo']] ?? $this->configuracaoTipos['info'];

        // Define o título do alerta, utilizando o título personalizado ou o padrão da configuração
        $titulo = !empty($notificacao['titulo']) ? $notificacao['titulo'] : $config['titulo_padrao'];

        // Sanitiza o título
        $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
        $mensagem = htmlspecialchars($notificacao['mensagem'], ENT_QUOTES, 'UTF-8');

        // Retorna o HTML estruturado com as classes do TailwindCSS aplicadas
        return sprintf(
            '<div class="flex %s" role="alert" id="mensagem-flash">
            <div class="flex-shrink-0">
                <svg class="%s" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="%s" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="%s">%s</h3>
                <div class="%s">%s</div>
            </div>
        </div>',
            $config['div_classes'],
            $config['icone_classes'],
            $config['icone_svg_path'],
            $config['titulo_classes'],
            $titulo,
            $config['mensagem_classes'],
            $mensagem
        );
    }
}
