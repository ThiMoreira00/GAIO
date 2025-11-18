<?php

use App\Core\Router;
use App\Middlewares\AjaxMiddleware;
use App\Middlewares\AutenticacaoMiddleware;
use App\Middlewares\ControleAcessoMiddleware;

/* ========================================
 * ROTAS EXTERNAS
 ======================================== */

// Autenticação
Router::get('/login', 'AutenticacaoController@exibirLogin');
Router::post('/login', 'AutenticacaoController@autenticar');
Router::get('/sair', 'AutenticacaoController@sair');

// Recuperação de senha
Router::get('/esqueci-senha', 'AutenticacaoController@exibirEsqueciSenha');
Router::post('/esqueci-senha', 'AutenticacaoController@enviarLinkRedefinicao');

// Redefinição de senha
Router::get('/redefinir-senha/{token}', 'AutenticacaoController@exibirRedefinicaoSenha', ['token' => '[a-zA-Z0-9]+']);
Router::post('/redefinir-senha', 'AutenticacaoController@salvarNovaSenha');

// Documentos
Router::get('/documento', 'ValidacaoController@exibirDocumento');
Router::post('/documento', 'ValidacaoController@validarDocumento');

// Carteirinha
Router::get('/carteirinha', 'ValidacaoController@exibirCarteirinha');
Router::post('/carteirinha', 'ValidacaoController@validarCarteirinha');

/*  ========================================
 * ROTAS INTERNAS
 ======================================== */

// Rotas para usuário (usuários logados podem executar)
Router::grupo(['middleware' => new AutenticacaoMiddleware()], function (): void {

    // Painel
    Router::get('/', 'PainelController@exibirIndex');
    Router::get('/inicio', 'PainelController@exibirIndex');
    Router::get('/configuracoes', 'ContaController@exibirConfiguracoes'); // Configurações
    Router::get('/notificacoes', 'NotificacaoController@exibirIndex'); // Notificações

    // Rotas para requisições AJAX
    Router::grupo(['middleware' => new AjaxMiddleware()], function (): void {

        // Configurações
        Router::post('/configuracoes/informacoes-pessoais', 'ContaController@salvarInformacoesPessoais');
        Router::post('/configuracoes/senha', 'ContaController@salvarSenha');
        Router::post('/configuracoes/notificacoes', 'ContaController@salvarNotificacoes');
        Router::post('/configuracoes/contato', 'ContaController@salvarInformacoesContato');

        // Notificações
        Router::get('/notificacoes/filtrar', 'NotificacaoController@filtrarNotificacoes');
        Router::get('/notificacoes/obter', 'NotificacaoController@buscarNotificacoes');
        Router::post('/notificacoes/{id}/ler', 'NotificacaoController@marcarComoLida', ['id' => '[0-9]+']);
        Router::post('/notificacoes/ler-todas', 'NotificacaoController@marcarTodasComoLidas');
    });

    // Rotas para administrador
    Router::grupo(['middleware' => new ControleAcessoMiddleware('ADMINISTRADOR')], function (): void {
        
        // Rotas para requisições AJAX
        Router::grupo(['middleware' => new AjaxMiddleware()], function (): void {

            # Grupos de Permissão
            Router::get('/grupos/{id}/permissoes/obter', 'GrupoController@obterPermissoes', ['id' => '[0-9]+']);
            Router::get('/grupos/{id}/membros/obter', 'GrupoController@obterMembros', ['id' => '[0-9]+']);
            Router::get('/grupos/{id}/membros/disponiveis', 'GrupoController@obterMembrosDisponiveis', ['id' => '[0-9]+']);
            Router::get('/grupos/{id}/obter', 'GrupoController@obterGrupo', ['id' => '[0-9]+']);
            Router::get('/grupos/obter', 'GrupoController@obterGrupos');
            Router::post('/grupos/permissoes/salvar', 'GrupoController@salvarPermissoes');
            Router::post('/grupos/criar', 'GrupoController@criarGrupo');
            Router::post('/grupos/{id}/excluir', 'GrupoController@excluirGrupo', ['id' => '[0-9]+']);
            Router::post('/grupos/{id}/membros/adicionar', 'GrupoController@adicionarMembros', ['id' => '[0-9]+']);
            Router::post('/grupos/{grupoId}/membros/{membroId}/remover', 'GrupoController@removerMembro', ['grupoId' => '[0-9]+', 'membroId' => '[0-9]+']);

            # Cursos
            Router::get('/cursos/obter', 'CursoController@obterCursos');
            Router::get('/cursos/{id}/obter', 'CursoController@obterCurso', ['id' => '[0-9]+']);
            Router::get('/cursos/filtrar', 'CursoController@filtrarCursos');
            Router::post('/cursos/adicionar', 'CursoController@adicionarCurso');
            Router::post('/cursos/{id}/editar', 'CursoController@editarCurso', ['id' => '[0-9]+']);
            Router::post('/cursos/{id}/arquivar', 'CursoController@arquivarCurso', ['id' => '[0-9]+']);
            // Router::post('/cursos/{id}/reativar', 'CursoController@reativarCurso', ['id' => '[0-9]+']);

            # Usuários
            Router::get('/usuarios/buscar', 'UsuarioController@buscarUsuarios');

            # Períodos Letivos
            Router::get('/periodos-letivos/filtrar', 'PeriodoController@filtrarPeriodosLetivos');
            Router::post('/periodos-letivos/adicionar', 'PeriodoController@adicionarPeriodoLetivo');

            # Espaços
            Router::get('/espacos/filtrar', 'EspacoController@filtrarEspacos');
            Router::post('/espacos/adicionar', 'EspacoController@adicionarEspaco');
            Router::post('/espacos/{id}/editar', 'EspacoController@editarEspaco', ['id' => '[0-9]+']);
            Router::post('/espacos/{id}/arquivar', 'EspacoController@arquivarEspaco', ['id' => '[0-9]+']);
            Router::post('/espacos/{id}/reativar', 'EspacoController@reativarEspaco', ['id' => '[0-9]+']);
            Router::post('/espacos/{id}/excluir', 'EspacoController@excluirEspaco', ['id' => '[0-9]+']);

            # Alunos
            Router::get('/alunos/filtrar', 'AlunoController@filtrarAlunos');
            Router::post('/alunos/adicionar', 'AlunoController@adicionarAluno');
            Router::post('/alunos/importar-sisu', 'AlunoController@importarSisu');
            Router::get('/alunos/template-sisu', 'AlunoController@baixarTemplateSisu');

        });

        # Grupos de Permissão
        Router::get('/grupos/permissoes', 'GrupoController@exibirPermissoes');
        Router::get('/grupos/membros', 'GrupoController@exibirMembros');
        Router::get('/usuarios', 'UsuarioController@exibirIndex');

        # Cursos
        Router::get('/cursos', 'CursoController@exibirIndex');
        Router::get('/cursos/visualizar/{id}-{nome}', 'CursoController@exibirCurso', ['id' => '[0-9]+', 'nome' => '[a-zA-Z0-9\%][a-zA-Z0-9\-\%]*']);

        # Espaços
        Router::get('/espacos', 'EspacoController@exibirIndex');

        # Períodos Letivos
        Router::get('/periodos', 'PeriodoController@exibirIndex');
        Router::get('/periodos/{id}', 'PeriodoController@exibirPeriodo', ['id' => '[0-9]+']);

        # Alunos
        Router::get('/alunos', 'AlunoController@exibirIndex');
        
        # Matrizes Curriculares
        Router::get('/matrizes', 'MatrizCurricularController@exibirIndex');

        # Logs
        Router::get('/logs', 'LogController@exibirIndex');
        
        # Rotas AJAX para espaços
        Router::grupo(['middleware' => new AjaxMiddleware()], function (): void {
            Router::get('/espacos/{id}/dados', 'EspacoController@obterDados', ['id' => '[0-9]+']);
        });
        

    });



});



Router::despachar();
