<?php

/**
 * @file GrupoController.php
 * @description Controlador responsável pelo gerenciamento de grupos no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Models\Grupo;
use App\Models\Log;
use App\Models\Permissao;
use App\Models\Usuario;
use App\Core\Request;
use App\Services\AutenticacaoService;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * Classe GrupoController
 *
 * Gerencia os grupos no sistema
 *
 * @package App\Controllers
 * @extends Controller
 */
class GrupoController extends Controller
{

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Renderiza a página de permissões do grupo
     *
     * @return void
     * @throws Exception
     */
    public function exibirPermissoes(): void
    {

        // Verifica se o usuário está autenticado
        $usuario = AutenticacaoService::usuarioAutenticado();

        if (!$usuario) {
            throw new Exception('Usuário não encontrado.');
        }

        // Obtém todos os grupos
        $grupos = Grupo::obterTodos();

        // Obtém todas as permissões
        $permissoes = Permissao::obterTodos();

        // Breadcrumbs = links de navegação
        $breadcrumbs = [
            ['label' => 'Grupos', 'url' => '/grupos'],
            ['label' => 'Permissões', 'url' => '/grupos/permissoes']
        ];

        // Renderiza a página de permissões
        $this->renderizar('grupos/permissoes', [
            'titulo' => 'Permissões dos Grupos',
            'breadcrumbs' => $breadcrumbs,
            'usuario' => $usuario,
            'grupos' => $grupos,
            'permissoes' => $permissoes
        ]);
    }


    /**
     * Exibe a página de membros do grupo
     *
     * @return void
     * @throws Exception
     */
    public function exibirMembros(): void
    {
        // Cache de grupos (válido por 10 minutos)
        $grupos = Cache::remember('grupos_lista', 600, function () {
            return Grupo::select('id', 'nome', 'padrao')
                ->orderByRaw('padrao DESC')
                ->orderBy('nome')
                ->get();
        });

        // Breadcrumbs = links de navegação
        $breadcrumbs = [
            ['label' => 'Grupos', 'url' => '/grupos'],
            ['label' => 'Membros', 'url' => '/grupos/membros']
        ];

        // Renderiza a página (membros carregados via AJAX)
        $this->renderizar('grupos/membros', [
            'titulo' => 'Membros dos Grupos',
            'breadcrumbs' => $breadcrumbs,
            'grupos' => $grupos
        ]);

    }


    // --- MÉTODOS DE REQUISIÇÕES ---

    /**
     * Função para obter as permissões de um grupo
     *
     * @param Request $request
     * @return void
     */
    public function obterPermissoes(Request $request): void {

        try {

            // Verifica se o usuário está autenticado
            $usuarioAutenticado = AutenticacaoService::usuarioAutenticado();

            if (!$usuarioAutenticado) {
                throw new Exception('Não foi possível identificar a sua conta. Tente novamente mais tarde.');
            }

            // Obtém o ID do grupo
            $grupoId = $request->get('id');

            // Obtém o grupo com base no ID
            $grupo = Grupo::buscarPorId($grupoId);

            if (!$grupo) {
                throw new Exception('Grupo não encontrado.');
            }

            // Obtém todas as permissões registradas no sistema
            $permissoes = Permissao::obterTodos();

            // Recebe apenas os IDs das permissões do grupo
            $idsPermissoesDoGrupo = $grupo->permissoes()->pluck('permissoes.id')->toArray();

            // Adiciona uma coluna de status para que verifique se o grupo tem a permissão ou não
            $permissoes->transform(function ($permissao) use ($idsPermissoesDoGrupo) {
                $permissao->status = in_array($permissao->id, $idsPermissoesDoGrupo);
                return $permissao;
            });

            // Retorna resposta JSON
            $this->responderJSON([
                'status' => 'sucesso',
                'grupo' => [
                    'id' => $grupo->obterId(),
                    'nome' => $grupo->obterNome(),
                    'padrao' => $grupo->obterPadrao(),
                    'permissoes' => $permissoes
                ]
            ]);

        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
               'status' => 'erro',
               'mensagem' => $exception->getMessage() ?? 'Erro ao obter as permissões do grupo.'
            ]);
        }
    }

    /**
     * Função para obter os membros de um grupo
     *
     * @param Request $request
     * @return void
     */
    public function obterMembros(Request $request): void {

        try {

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $grupoId = $request->get('id');

            $grupo = Grupo::buscarPorId($grupoId);

            if (!$grupo) {
                throw new Exception('Grupo não encontrado.');
            }

            // Parâmetros de paginação
            $pagina = (int) $request->get('pagina', 1);
            $porPagina = (int) $request->get('por_pagina', 20);
            $busca = trim($request->get('busca', ''));

            // Validação
            if ($pagina < 1) $pagina = 1;
            if ($porPagina < 1 || $porPagina > 100) $porPagina = 20;

            // Chave de cache única por grupo, página e busca
            $cacheKey = sprintf('grupo_%d_membros_p%d_pp%d_%s', $grupoId, $pagina, $porPagina, md5($busca));
            
            // Cache por 5 minutos
            $resposta = Cache::remember($cacheKey, 300, function () use ($grupo, $busca, $pagina, $porPagina) {
                // Query base
                $query = $grupo->usuarios();

                // Aplicar busca se fornecida
                if (!empty($busca)) {
                    $query->where(function($q) use ($busca) {
                        $q->where('usuarios.nome_social', 'LIKE', "%{$busca}%")
                          ->orWhere('usuarios.nome_civil', 'LIKE', "%{$busca}%")
                          ->orWhere('usuarios.email_institucional', 'LIKE', "%{$busca}%")
                          ->orWhere('usuarios.email_pessoal', 'LIKE', "%{$busca}%");
                    });
                }

                // Ordenar por nome
                $query->orderBy('usuarios.nome_social')
                      ->orderBy('usuarios.nome_civil');

                // Aplicar paginação
                $offset = ($pagina - 1) * $porPagina;
                $membros = $query->skip($offset)->take($porPagina)->get();

                // Mapear apenas os campos necessários
                $membrosFormatados = $membros->map(function($membro) {
                    return [
                        'id' => $membro->obterId(),
                        'nome' => $membro->obterNomeSocial() ?: $membro->obterNomeCivil(),
                        'email' => $membro->obterEmailInstitucional() ?: $membro->obterEmailPessoal(),
                        'foto_perfil' => $membro->obterFotoPerfil()
                    ];
                })->values()->toArray();

                return [
                    'status' => 'sucesso',
                    'grupo' => [
                        'id' => $grupo->obterId(),
                        'nome' => $grupo->obterNome(),
                        'padrao' => $grupo->obterPadrao(),
                        'membros' => $membrosFormatados
                    ]
                ];
            });

            $this->responderJSON($resposta);

        } catch (Exception $exception) {

            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao obter os membros do grupo.'
            ]);

        }

    }

    /**
     * Função para obter os membros disponíveis a serem adicionados em um grupo
     *
     * @param Request $request
     * @return void
     */
    public function obterMembrosDisponiveis(Request $request): void {


        try {

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }


            $grupoId = $request->get('id');

            $grupo = Grupo::buscarPorId($grupoId);

            if (!$grupo) {
                throw new Exception('Grupo não encontrado.');
            }

            if ($grupo->obterPadrao()) {
                throw new Exception('Não é possível adicionar membros a um grupo padrão do sistema.');
            }

            $usuarios = Usuario::obterTodos();


            // Filtrar e transformar em array de chaves
            $usuarios = $usuarios->filter(function ($usuario) use ($grupo) {
                return !$grupo->usuarios()->where('usuarios.id', $usuario->obterId())->exists();
            })->map(function ($usuario) {
                return [
                    'id' => $usuario->obterId(),
                    'nome' => $usuario->obterNomeReduzido(),
                    'email' => $usuario->obterEmailInstitucional() ?: $usuario->obterEmailPessoal(),
                    'foto_perfil' => $usuario->obterFotoPerfil()
                ];
            })->values()->toArray();

            $this->responderJSON([
                'status' => 'sucesso',
                'usuarios' => $usuarios
            ]);


        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao obter os membros disponíveis para o grupo.'
            ]);
        }


    }

    /**
     * Função para obter grupos
     *
     * @param Request $request
     * @return void
     */
    public function obterGrupos(Request $request): void {

        try {

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            // Cache de grupos (válido por 10 minutos)
            $grupos = Cache::remember('grupos_lista_api', 600, function () {
                return Grupo::select('id', 'nome', 'padrao')
                    ->orderByRaw('padrao DESC')
                    ->orderBy('nome')
                    ->get()
                    ->toArray();
            });

            $this->responderJSON([
                'status' => 'sucesso',
                'grupos' => $grupos
            ]);

        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao obter os grupos.'
            ]);
        }
    }


    /**
     * Função para obter um grupo específico
     *
     * @param Request $request
     * @return void
     */
    public function obterGrupo(Request $request): void {

        try {
            
            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $grupo = Grupo::buscarPorId($request->get('id'));

            if (!$grupo) {
                throw new Exception('Grupo não encontrado.');
            }

            if (empty($grupo->obterDescricao())) {
                $grupo->atribuirDescricao('[sem descrição]');
            }

            $this->responderJSON([
                'status' => 'sucesso',
                'grupo' => $grupo
            ]);
        } catch (Exception $exception) {
            $this->responderJSON([
                'status'=> 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao obter o grupo.'
            ]);
        }
    }

    /**
     * Função para salvar as permissões de um grupo
     *
     * @param Request $request
     * @return void
     */
    public function salvarPermissoes(Request $request): void
    {

        try {

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            // Obtenção dos dados do formulário
            $grupoId = $request->post('grupo_id');

            $grupo = Grupo::buscarPorId($grupoId);

            if (!$grupo) {
                throw new Exception('Grupo não encontrado.');
            }

            // Obtém array de permissões do formulário (se não houver, será array vazio)
            $permissoesFormulario = $request->post('permissoes', []);

            // Obtém todas as permissões do sistema
            $permissoes = Permissao::obterTodos();

            $pdo = $grupo->getConnection()->getPdo();
            $pdo->beginTransaction();

            try {
                // Obtém as permissões atuais do grupo
                $permissoesAtuais = $grupo->permissoes()->pluck('permissoes.id')->toArray();

                // Identifica quais permissões devem ser mantidas/adicionadas
                $permissoesParaAdicionar = [];
                foreach ($permissoes as $permissao) {
                    if (in_array($permissao->codigo, $permissoesFormulario)) {
                        $permissoesParaAdicionar[] = $permissao->id;
                    }
                }

                // Remove todas as permissões que não estão no array de permissões do formulário
                $permissoesParaRemover = array_diff($permissoesAtuais, $permissoesParaAdicionar);

                // Obtém os nomes das permissões para o log
                $nomesPermissoesAdicionadas = [];
                $nomesPermissoesRemovidas = [];

                // Coleta nomes das permissões que serão removidas
                if (!empty($permissoesParaRemover)) {
                    foreach ($permissoesParaRemover as $permissaoId) {
                        $permissao = Permissao::buscarPorId($permissaoId);
                        if ($permissao) {
                            $nomesPermissoesRemovidas[] = $permissao->nome;
                        }
                    }
                    $grupo->permissoes()->detach($permissoesParaRemover);
                }

                // Adiciona as novas permissões
                if (!empty($permissoesParaAdicionar)) {
                    foreach ($permissoesParaAdicionar as $permissaoId) {
                        // Verifica se já não existe na tabela pivô antes de adicionar
                        if (!in_array($permissaoId, $permissoesAtuais)) {
                            $permissao = Permissao::buscarPorId($permissaoId);
                            if ($permissao) {
                                $nomesPermissoesAdicionadas[] = $permissao->nome;
                            }
                            $grupo->permissoes()->attach($permissaoId);
                        }
                    }
                }

                // Salva as alterações
                $grupo->salvar();

                // Se chegou até aqui, confirma a transação
                $pdo->commit();

                // Monta a mensagem de log
                $mensagemLog = sprintf(
                    'As permissões do grupo "%s" (ID: %d) foram atualizadas.' . PHP_EOL . PHP_EOL .
                    'Permissões adicionadas: %s' . PHP_EOL .
                    'Permissões removidas: %s',
                    $grupo->obterNome(),
                    $grupo->obterId(),
                    !empty($nomesPermissoesAdicionadas) ? implode(', ', $nomesPermissoesAdicionadas) : 'Nenhuma',
                    !empty($nomesPermissoesRemovidas) ? implode(', ', $nomesPermissoesRemovidas) : 'Nenhuma'
                );

                // Registra log da atualização das permissões do grupo
                Log::registrar($usuario->obterId(), 'Atualização de Permissões do Grupo', $mensagemLog);

                // Retorna resposta JSON
                $this->responderJSON([
                    'status' => 'sucesso',
                    'mensagem' => 'Permissões do grupo atualizadas com sucesso.'
                ]);

            } catch (Exception $e) {

                // Verifica se tem transação ativa
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                throw $e;
            }


        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao salvar as permissões do grupo.'
            ]);
        }
    }


    /**
     * Função para criar um novo grupo
     *
     * @param Request $request
     * @return void
     */
    public function criarGrupo(Request $request): void {

        try {

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            // Obtenção dos dados do formulário
            $nome = $request->post('grupo-nome');
            $descricao = $request->post('grupo-descricao') ?: null;

            // Verificar se o nome foi preenchido
            if (empty($nome)) {
                throw new Exception('O nome do grupo é um campo obrigatório. Preencha-o e tente novamente.');
            }

            // Verifica se já existe um grupo com o mesmo nome
            $verificarGrupoNome = Grupo::buscarPorNome($nome);

            if ($verificarGrupoNome) {
                throw new Exception('Já existe um grupo cadastrado no sistema com o nome informado. Preencha-o e tente novamente.');
            }

            // Criação do grupo com os atributos
            $grupo = new Grupo();
            $grupo->atribuirNome($nome);
            $grupo->atribuirDescricao($descricao);
            $grupo->salvar();

            // Registra log da criação do grupo
            Log::registrar($usuario->obterId(), 'Criação de Grupo', sprintf('O grupo "%s" foi criado no sistema.', $nome));

            // Limpar cache de grupos
            $this->limparCacheGrupos();

            // Retorna resposta JSON
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => sprintf('Grupo "%s" criado com sucesso.', $nome),
            ]);



        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao criar o grupo.'
            ]);
        }
    }

    /**
     * Função para excluir um grupo
     *
     * @param Request $request
     * @return void
     */
    public function excluirGrupo(Request $request): void {

        try {

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $grupoId = $request->parametroRota('id');

            // Obtém o grupo com base no ID
            $grupo = Grupo::buscarPorId($grupoId);

            if (!$grupo) {
                throw new Exception('Grupo não encontrado.');
            }

            if ($grupo->obterPadrao()) {
                throw new Exception(sprintf('Não é possível excluir o grupo "%s", pois ele é um grupo padrão do sistema.', $grupo->obterNome()));
            }

            // Verifica se há usuários associados a esse grupo
            $membrosGrupo = $grupo->usuarios()->count();

            if ($membrosGrupo) {
                throw new Exception(sprintf('Não é possível excluir o grupo "%s", pois há usuários associados a ele. Remova os usuários do grupo antes de excluí-lo.', $grupo->obterNome()));
            }

            $grupoNome = $grupo->obterNome();

            // Exclui o grupo
            $grupo->excluir();

            // Registra log da exclusão do grupo
            Log::registrar($usuario->obterId(), 'Exclusão de Grupo', sprintf('O grupo "%s" (ID: %d) foi excluído do sistema.', $grupoNome, $grupoId));

            // Limpar cache de grupos
            $this->limparCacheGrupos();

            // Retorna resposta JSON
            $this->responderJSON([
                'status'=> 'sucesso',
                'id'=> $grupoId,
                'mensagem' => sprintf('Grupo "%s" excluído com sucesso.', $grupoNome)
            ]);

        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status'=> 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao excluir o grupo.'
            ]);
        }

    }


    /**
     * Função para adicionar membros a um grupo
     *
     * @param Request $request
     * @return void
     */
    public function adicionarMembros(Request $request): void {

        try {

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $grupoId = $request->get('id');

            $grupo = Grupo::buscarPorId($grupoId);

            if (!$grupo) {
                throw new Exception('Grupo não encontrado.');
            }

            if ($grupo->obterPadrao()) {
                throw new Exception('Não é possível adicionar membros a um grupo padrão do sistema.');
            }

            $membros = $request->post('membros');

            try {

                $pdo = $grupo->getConnection()->getPdo();
                $pdo->beginTransaction();

                foreach ($membros as $membroId) {

                    // Verificar se o usuário já está no grupo
                    if ($grupo->usuarios()->where('usuarios.id', $membroId)->exists()) {
                        throw new Exception(sprintf('O usuário %s já é membro do grupo. Remova-o da lista e tente novamente.', Usuario::buscarPorId($membroId)?->obterNomeReduzido()));
                    }

                    $grupo->usuarios()->attach($membroId);

                }

                $pdo->commit();

                // Monta a mensagem de log
                $mensagemLog = sprintf('%d membros foram adicionados ao grupo "%s": '. PHP_EOL . PHP_EOL . '%s',
                    count($membros),
                    $grupo->obterNome(),
                    implode(', ', array_map(function($membroId) {
                        $membro = Usuario::buscarPorId($membroId);
                        return $membro ? sprintf('%s (%s)', $membro->obterNomeReduzido(), ($membro->obterEmailInstitucional() ?: $membro->obterEmailPessoal())) : '';
                    }, $membros))
                );

                // Registra log da atualização dos membros do grupo
                Log::registrar($usuario->obterId(), 'Atualização de Membros no Grupo', $mensagemLog);

                // Limpar cache de membros do grupo
                $this->limparCacheMembros($grupoId);

            } catch (Exception $e) {

                $pdo->rollBack();
                throw $e;
            }

            $this->responderJSON([
                'status'=> 'sucesso',
                'mensagem' => sprintf('%d membros adicionados no grupo "%s" com sucesso.', count($membros), $grupo->obterNome())
            ]);


        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status'=> 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao excluir o grupo.'
            ]);

        }


    }

    /**
     * Função para remover um membro específico de um grupo
     *
     * @param Request $request
     * @return void
     */
    public function removerMembro(Request $request): void {

        try {

            $usuario = AutenticacaoService::usuarioAutenticado();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $grupoId = $request->get('grupoId');

            $grupo = Grupo::buscarPorId($grupoId);

            if (!$grupo) {
                throw new Exception('Grupo não encontrado.');
            }

            if ($grupo->obterPadrao()) {
                throw new Exception('Não é possível remover membros de um grupo padrão do sistema.');
            }

            $membroId = $request->get('membroId');

            $membro = Usuario::buscarPorId($membroId);

            if (!$membro) {
                throw new Exception('Usuário não encontrado.');
            }

            // Verificar se o usuário já está no grupo
            if (!$grupo->usuarios()->where('usuarios.id', $membroId)->exists()) {
                throw new Exception(sprintf('O usuário %s não é membro do grupo %s. Adicione-o à lista e tente novamente.', Usuario::buscarPorId($membroId)?->obterNomeReduzido(), $grupo->obterNome()));
            }

            $grupo->usuarios()->detach($membroId);

            // Monta a mensagem de log
            $mensagemLog = sprintf('O membro "%s" foi removido do grupo "%s".',
                $membro->obterNomeReduzido(),
                $grupo->obterNome()
            );

            // Registra log da remoção do membro do grupo
            Log::registrar($usuario->obterId(), 'Remoção de Membro do Grupo', $mensagemLog);

            // Limpar cache de membros do grupo
            $this->limparCacheMembros($grupoId);

            $this->responderJSON([
                'status'=> 'sucesso',
                'mensagem' => sprintf('Membro "%s" removido(a) do grupo "%s" com sucesso.', $membro->obterNomeReduzido(), $grupo->obterNome()),
                'grupo' => [
                    'id' => $grupo->obterId()
                ]
            ]);


        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status'=> 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao excluir o grupo.'
            ]);

        }
    }

    /**
     * Limpa o cache de membros de um grupo específico
     *
     * @param int $grupoId
     * @return void
     */
    private function limparCacheMembros(int $grupoId): void {
        // Remove cache de membros do grupo (todas as páginas e buscas)
        $cacheDir = __DIR__ . '/../../storage/cache/';
        
        if (is_dir($cacheDir)) {
            $pattern = sprintf('grupo_%d_membros_*', $grupoId);
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
     * Limpa o cache de listagem de grupos
     *
     * @return void
     */
    private function limparCacheGrupos(): void {
        // Remove cache de grupos usando Cache facade
        Cache::forget('grupos_lista');
        Cache::forget('grupos_lista_api');
    }


}

