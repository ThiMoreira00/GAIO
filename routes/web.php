<?php

use App\Core\Router;
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

// Validações Públicas
Router::grupo(['prefixo' => 'validar'], function() {
    // Documentos
    Router::get('/documento', 'ValidacaoController@exibirDocumento');
    Router::post('/documento', 'ValidacaoController@validarDocumento');

    // Carteirinha
    Router::get('/carteirinha', 'ValidacaoController@exibirCarteirinha');
    Router::post('/carteirinha', 'ValidacaoController@validarCarteirinha');
});


/*  ========================================
 * ROTAS INTERNAS
 ======================================== */

// Rotas para usuário (usuários logados podem executar)
Router::grupo(['middleware' => new ControleAcessoMiddleware('usuario')], function () {

    // Configurações
    Router::grupo([], function() {
        Router::get('/configuracoes', 'ContaController@exibirConfiguracoes');
        Router::post('/configuracoes/informacoes-pessoais', 'ContaController@salvarInformacoesPessoais');
        Router::post('/configuracoes/senha', 'ContaController@salvarSenha');
        Router::post('/configuracoes/notificacoes', 'ContaController@salvarNotificacoes');
        Router::post('/configuracoes/contato', 'ContaController@salvarContato');
    });

    // Notificações
    Router::grupo([], function() {
        Router::get('/notificacoes', 'NotificacaoController@exibirIndex');
        Router::post('/notificacoes/ler', 'NotificacaoController@marcarComoLida');
        Router::post('/notificacoes/ler-todas', 'NotificacaoController@marcarTodasComoLidas');
    });

});

Router::grupo(['middleware' => new ControleAcessoMiddleware('aluno')], function () {

    // Dashboard
    Router::get('/', 'AlunoController@index');
    Router::get('/inicio', 'AlunoController@index');

});


Router::grupo(['middleware' => new ControleAcessoMiddleware('professor')], function () {

    Router::get('/', 'ProfessorController@index');
    Router::get('/inicio', 'ProfessorController@index');

});


Router::grupo(['middleware' => new ControleAcessoMiddleware('administrador')], function () {

    Router::get('/', 'AdministradorController@index');
    Router::get('/inicio', 'AdministradorController@index');

});

Router::despachar();