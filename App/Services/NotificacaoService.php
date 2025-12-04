<?php

declare(strict_types=1);

namespace App\Services;

use App\Helper\DataFormatador;
use App\Models\Usuario;
use App\Models\Notificacao;
use App\Models\NotificacaoModelo;
use App\Models\NotificacaoLeitura;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Michelf\Markdown;
use InvalidArgumentException;
use Exception;



class NotificacaoService
{

    public static function criar(string $codigoModelo, array $destinatarios, array $dados): Notificacao
    {
        if (empty($destinatarios)) {
            throw new InvalidArgumentException('É necessário fornecer ao menos um destinatário.');
        }

        $notificacaoModelo = NotificacaoModelo::buscarPorCodigo($codigoModelo);

        if (!$notificacaoModelo) {
            throw new InvalidArgumentException('Modelo de notificação não encontrado.');
        }

        $mensagemFinal = NotificacaoService::construirMensagem($notificacaoModelo->obterMensagem(), $dados);

        // Inicia a transação manualmente a partir do Capsule
        Capsule::connection()->beginTransaction();

        try {
            // 1. Cria a notificação principal
            $notificacao = Notificacao::create([
                'modelo_id' => $notificacaoModelo->obterId(),
                'titulo' => $notificacaoModelo->obterTitulo(),
                'mensagem' => $mensagemFinal,
            ]);

            // 2. Cria os registros de destino polimórficos
            foreach ($destinatarios as $destinatario) {
                if (!$destinatario instanceof Model) {
                    // Se algo der errado, a exceção será capturada e a transação desfeita
                    throw new InvalidArgumentException('Todos os destinatários devem ser instâncias de um Model Eloquent.');
                }
                $notificacao->destinos()->create([
                    'destinatario_id' => $destinatario->obterId(),
                    'destinatario_tipo' => get_class($destinatario),
                ]);
            }

            // Se tudo correu bem até aqui, confirma as operações no banco de dados.
            Capsule::connection()->commit();

            return $notificacao;

        } catch (Exception $e) {
            // Em caso de qualquer erro (Exception), desfaz todas as operações.
            Capsule::connection()->rollBack();

            // É uma boa prática relançar a exceção para que o código que chamou este método
            // saiba que algo deu errado e possa tratar o erro.
            throw $e;
        }
    }

    /**
     * Lista as notificações de um usuário, enriquecendo-as com dados do modelo e status de leitura.
     *
     * @param Usuario $usuario O Model do usuário.
     * @param array $opcoes Opções de filtro: ['status' => 'todas'|'lidas'|'nao_lidas', 'porPagina' => 15]
     * @return LengthAwarePaginator
     */
    public function listarPorUsuario(Usuario $usuario, array $opcoes = []): LengthAwarePaginator
    {

        $status = $opcoes['status'] ?? 'todas';
        $porPagina = $opcoes['porPagina'] ?? 15;
        $pagina = $opcoes['pagina'] ?? 1;
        $busca = $opcoes['busca'] ?? null;

        $query = Notificacao::query()
            ->with('modelo')
            ->whereHas('destinos', function ($q) use ($usuario): void {
                $q->where('destinatario_id', $usuario->id)
                    ->where('destinatario_tipo', get_class($usuario));
            });

        // Aplica o filtro de status de leitura
        switch ($status) {
            case 'lidas':
                $query->whereHas('leituras', fn($q) => $q->where('usuario_id', $usuario->id));
                break;
            case 'nao_lidas':
                $query->whereDoesntHave('leituras', fn($q) => $q->where('usuario_id', $usuario->id));
                break;
        }

        // Aplica o filtro de busca textual, se houver
        if (!empty($busca)) {
            $query->where(function ($q) use ($busca) {
                $q->where('mensagem', 'LIKE', '%' . $busca . '%');
            });
        }

        // Adiciona o status de leitura específico do usuário atual a cada notificação
        $notificacoes = $query->with(['leituras' => fn($q) => $q->where('usuario_id', $usuario->id)])
            ->latest('data_registro')
            ->paginate($porPagina, ['*'], 'page', $pagina);

        $notificacoes->getCollection()->transform(function ($notificacao) {
            $notificacao->lida = $notificacao->leituras->isNotEmpty();
            $notificacao->data_leitura = $notificacao->lida ? $notificacao->leituras->first()->data_leitura : null;
            $notificacao->icone = $notificacao->modelo->icone ?? 'fas fa-bell';
            $notificacao->cor = $notificacao->modelo->cor ?? 'gray';
            $notificacao->mensagem_html = self::formatarMensagem($notificacao->mensagem);
            $notificacao->autor = ($notificacao->autor_id) ? Usuario::buscarPorId($notificacao->autor_id) : null;

            $notificacao->hora_formatada = DataFormatador::formatar($notificacao->data_registro, 'HH:mm');
            $notificacao->data_formatada = DataFormatador::formatar($notificacao->data_registro, 'dd MMM Y');

            unset($notificacao->leituras);
            unset($notificacao->modelo);
            unset($notificacao->modelo_id);
            unset($notificacao->autor_id);
            unset($notificacao->autor);

            return $notificacao;
        });

        return $notificacoes;
    }



    /**
     * Marca uma notificação específica como lida para um usuário.
     *
     * @param Notificacao $notificacao O Model da notificação.
     * @param Usuario $usuario O Model do usuário.
     * @return bool Retorna true se uma nova leitura foi criada, false se já estava lida.
     */
    public function marcarComoLida(int $notificacaoId, int $usuarioId): bool
    {
        // Usa firstOrCreate para ser idempotente: cria apenas se não existir.
        $leitura = NotificacaoLeitura::firstOrCreate(
            [
                'notificacao_id' => $notificacaoId,
                'usuario_id' => $usuarioId,
                'data_leitura' => date('Y-m-d H:i:s')
            ]
        );

        // A propriedade 'wasRecentlyCreated' nos diz se o registro foi criado agora ou se já existia.
        return $leitura->wasRecentlyCreated;
    }

    /**
     * Marca todas as notificações não lidas de um usuário como lidas.
     *
     * @param Usuario $usuario
     * @return int O número de notificações que foram marcadas como lidas.
     */
    public function marcarTodasComoLidas(Usuario $usuario): int
    {
        // 1. Busca os IDs de todas as notificações não lidas do usuário
        $idsNaoLidas = Notificacao::whereDoesntHave('leituras', fn($q) => $q->where('usuario_id', $usuario->id))
            ->whereHas('destinos', function ($q) use ($usuario): void {
                $q->where('destinatario_id', $usuario->id)
                    ->where('destinatario_tipo', get_class($usuario));
            })
            ->pluck('id');

        if ($idsNaoLidas->isEmpty()) {
            return 0;
        }

        // 2. Prepara os dados para uma inserção em massa (bulk insert)
        $agora = date('Y-m-d H:i:s');
        $leiturasParaInserir = $idsNaoLidas->map(fn($id) => [
            'notificacao_id' => $id,
            'usuario_id' => $usuario->id,
            'data_leitura' => $agora,
        ])->all();

        // 3. Executa a inserção em massa
        NotificacaoLeitura::insert($leiturasParaInserir);

        return count($leiturasParaInserir);
    }

    /**
     * Conta o número de notificações não lidas para um usuário.
     *
     * @param Usuario $usuario
     * @return int
     */
    public static function contarNaoLidas(Usuario $usuario): int
    {
        return Notificacao::whereDoesntHave('leituras', fn($q) => $q->where('usuario_id', $usuario->id))
            ->whereHas('destinos', function ($q) use ($usuario): void {
                $q->where('destinatario_id', $usuario->id)
                    ->where('destinatario_tipo', get_class($usuario));
            })
            ->count();
    }

    // --- MÉTODOS PRIVADOS DE APOIO ---

    /**
     * Constrói a string da mensagem final a partir de um modelo e um array de valores.
     */
    private static function construirMensagem(string $mensagemModelo, array $dados): string
    {

        // Substitui placeholders no formato {chave} pelos valores do array de dados.
        // Isso é mais flexível que vsprintf.
        foreach ($dados as $chave => $valor) {
            $mensagemModelo = str_replace('{' . $chave . '}', (string) $valor, $mensagemModelo);
        }

        return $mensagemModelo;
    }

    /**
     * Formata a mensagem com Markdown e aplica classes de estilo.
     */
    private static function formatarMensagem(string $mensagem): string
    {
        $html = Markdown::defaultTransform($mensagem);
        return str_replace(
            ['<p>', '</p>', '<strong>', '</strong>'],
            ['', '', '<span class="font-semibold">', '</span>'],
            $html
        );
    }

    /**
     * Verifica se o usuário é destinatário da notificação
     * 
     * @param int $notificacaoId
     * @param Usuario $usuario
     * @return bool
     */
    public function verificarDestinatarioNotificacao(int $notificacaoId, Usuario $usuario): bool
    {
        $notificacao = Notificacao::find($notificacaoId);

        if (!$notificacao) {
            return false;
        }

        $destinatarioEncontrado = $notificacao->destinos()
            ->where('destinatario_id', $usuario->obterId())
            ->where('destinatario_tipo', get_class($usuario))
            ->exists();

        return $destinatarioEncontrado;
    }
}
