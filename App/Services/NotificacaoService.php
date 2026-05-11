<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Usuario;
use App\Models\Notificacao;
use App\Models\NotificacaoModelo;
use App\Models\NotificacaoLeitura;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Michelf\Markdown;
use InvalidArgumentException;
use Exception;



class NotificacaoService
{

    private static function normalizarTipoDestinatario(?string $tipo): string
    {
        $tipo = trim((string) $tipo);
        $tipo = trim($tipo, " \t\n\r\0\x0B'\"");
        $tipo = str_replace('/', '\\', $tipo);
        $tipo = preg_replace('/\\\\+/', '\\', $tipo) ?? $tipo;
        return ltrim($tipo, '\\');
    }

    private static function aplicarFiltroDestinatario($query, Usuario $usuario): void
    {
        $usuarioId = $usuario->obterId();
        $usuarioTipo = self::normalizarTipoDestinatario(get_class($usuario));

        $query->where('destinatario_id', $usuarioId)
            ->where(function ($q) use ($usuarioTipo): void {
                // Match exato para dados já normalizados.
                $q->where('destinatario_tipo', $usuarioTipo)
                    // Compatibilidade: alguns registros podem ter barra inicial.
                    ->orWhere('destinatario_tipo', '\\' . $usuarioTipo)
                    // Compatibilidade: remove aspas e converte '/' para '\\'.
                    ->orWhereRaw(
                        "TRIM(LEADING '\\\\' FROM REPLACE(REPLACE(REPLACE(TRIM(destinatario_tipo), '\"', ''), '\'', ''), '/', '\\\\')) = ?",
                        [$usuarioTipo]
                    );
            });
    }

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
            // Cria a notificação principal
            $notificacao = Notificacao::create([
                'modelo_id' => $notificacaoModelo->obterId(),
                'titulo' => $notificacaoModelo->obterTitulo(),
                'mensagem' => $mensagemFinal,
            ]);

            // Cria os registros de destino polimórficos
            foreach ($destinatarios as $destinatario) {
                if (!$destinatario instanceof Model) {
                    // Se algo der errado, a exceção será capturada e a transação desfeita
                    throw new InvalidArgumentException('Todos os destinatários devem ser instâncias de um Model Eloquent.');
                }
                $notificacao->destinos()->create([
                    'destinatario_id' => $destinatario->obterId(),
                    'destinatario_tipo' => self::normalizarTipoDestinatario(get_class($destinatario)),
                ]);
            }

            // Se tudo correu bem até aqui, confirma as operações no banco de dados
            Capsule::connection()->commit();

            return $notificacao;

        } catch (Exception $e) {
            // Em caso de qualquer erro (Exception), desfaz todas as operações
            Capsule::connection()->rollBack();
            throw $e;
        }
    }

    /**
     * Lista as notificações de um usuário, enriquecendo-as com dados do modelo e status de leitura
     *
     * @param Usuario $usuario O Model do usuário.
     * @param array $opcoes Opções de filtro: ['status' => 'todas'|'lidas'|'nao_lidas', 'porPagina' => 15]
     * @return LengthAwarePaginator
     */
    public function listarPorUsuario(Usuario $usuario, array $opcoes = []): LengthAwarePaginator
    {

        try {
        
            $status = $opcoes['status'] ?? 'todas';
            $porPagina = $opcoes['porPagina'] ?? 15;
            $pagina = $opcoes['pagina'] ?? 1;
            $busca = $opcoes['busca'] ?? null;

            $usuarioId = $usuario->obterId();

            $query = Notificacao::query()
                ->with('modelo')
                ->whereHas('destinos', function ($q) use ($usuario): void {
                    self::aplicarFiltroDestinatario($q, $usuario);
                });

            // Aplica o filtro de status de leitura
            switch ($status) {
                case 'lidas':
                    $query->whereHas('leituras', fn($q) => $q->where('usuario_id', $usuarioId));
                    break;
                case 'nao_lidas':
                    $query->whereDoesntHave('leituras', fn($q) => $q->where('usuario_id', $usuarioId));
                    break;
            }

            // Aplica o filtro de busca textual, se houver
            if (!empty($busca)) {
                $query->where(function ($q) use ($busca) {
                    $q->where('mensagem', 'LIKE', '%' . $busca . '%')
                        ->orWhere('titulo', 'LIKE', '%' . $busca . '%');
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

                $dataRegistro = $notificacao->data_registro;
                $dataCarbon = ($dataRegistro instanceof \DateTimeInterface)
                    ? Carbon::instance($dataRegistro)
                    : Carbon::parse((string) $dataRegistro);

                $notificacao->hora_formatada = $dataCarbon->format('H:i');
                $notificacao->data_formatada = $dataCarbon->locale('pt_BR')->translatedFormat('d M Y');

                unset($notificacao->leituras);
                unset($notificacao->modelo);
                unset($notificacao->modelo_id);
                unset($notificacao->autor_id);
                unset($notificacao->autor);

                return $notificacao;
            });

            return $notificacoes;
        } catch (Exception $e) {
            throw new Exception('Erro ao listar notificações: ' . $e->getMessage());
        }
    }



    /**
     * Marca uma notificação específica como lida para um usuário.
     *
     * @param int $notificacaoId O ID da notificação a ser marcada como lida
     * @param int $usuarioId O ID do usuário que leu a notificação
     * @return bool Retorna true se uma nova leitura foi criada, false se já estava lida
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
        $usuarioId = $usuario->obterId();

        // 1. Busca os IDs de todas as notificações não lidas do usuário
        $idsNaoLidas = Notificacao::whereDoesntHave('leituras', fn($q) => $q->where('usuario_id', $usuarioId))
            ->whereHas('destinos', function ($q) use ($usuario): void {
                self::aplicarFiltroDestinatario($q, $usuario);
            })
            ->pluck('id');

        if ($idsNaoLidas->isEmpty()) {
            return 0;
        }

        // 2. Prepara os dados para uma inserção em massa (bulk insert)
        $agora = date('Y-m-d H:i:s');
        $leiturasParaInserir = $idsNaoLidas->map(fn($id) => [
            'notificacao_id' => $id,
            'usuario_id' => $usuarioId,
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
        $usuarioId = $usuario->obterId();

        return Notificacao::whereDoesntHave('leituras', fn($q) => $q->where('usuario_id', $usuarioId))
            ->whereHas('destinos', function ($q) use ($usuario): void {
                self::aplicarFiltroDestinatario($q, $usuario);
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
            Log::registrar($usuario->obterId(), 'NOTIFICACAO_DESTINO_FALHA', 'Tentativa de acessar notificação inexistente.', [
                'notificacao_id' => $notificacaoId,
                'usuario_id' => $usuario->obterId(),
                'usuario_tipo' => ltrim(get_class($usuario), '\\'),
                'usuario_tipo_normalizado' => self::normalizarTipoDestinatario(get_class($usuario)),
            ]);
            return false;
        }

        $usuarioId = $usuario->obterId();
        $usuarioTipo = ltrim(get_class($usuario), '\\');
        $usuarioTipoNormalizado = self::normalizarTipoDestinatario(get_class($usuario));

        $destinos = $notificacao->destinos()->get(['destinatario_id', 'destinatario_tipo']);
        $destinatarioEncontrado = $destinos->contains(function ($destino) use ($usuarioId, $usuarioTipo, $usuarioTipoNormalizado): bool {
            $destinatarioId = (int) ($destino->destinatario_id ?? 0);
            $destinatarioTipoRaw = (string) ($destino->destinatario_tipo ?? '');
            $destinatarioTipo = ltrim($destinatarioTipoRaw, '\\');
            $destinatarioTipoNormalizado = self::normalizarTipoDestinatario($destinatarioTipoRaw);

            if ($destinatarioId !== $usuarioId) {
                return false;
            }

            return $destinatarioTipo === $usuarioTipo || $destinatarioTipoNormalizado === $usuarioTipoNormalizado;
        });

        if (!$destinatarioEncontrado) {
            Log::registrar($usuarioId, 'NOTIFICACAO_DESTINO_FALHA', 'Usuário não é destinatário desta notificação.', [
                'notificacao_id' => $notificacaoId,
                'usuario_id' => $usuarioId,
                'usuario_tipo' => $usuarioTipo,
                'usuario_tipo_normalizado' => $usuarioTipoNormalizado,
                'destinos' => $destinos->take(20)->map(fn($d) => [
                    'destinatario_id' => $d->destinatario_id,
                    'destinatario_tipo' => $d->destinatario_tipo,
                    'destinatario_tipo_normalizado' => self::normalizarTipoDestinatario($d->destinatario_tipo),
                ])->values()->all(),
            ]);
        }

        return $destinatarioEncontrado;
    }
}
