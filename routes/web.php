<?php

declare(strict_types=1);

/**
 * @file web.php
 * @description Arquivo responsável pelo mapeamento de todas as rotas da aplicação. Utiliza o componente de roteamento do Illuminate (Laravel).
 * @author Thiago Moreira
 * @copyright Copyright (c) 2026
 */

// Importação de classes
use Illuminate\Routing\Router;

// Importação de Controllers
use App\Controllers\AuthController;
use App\Controllers\ValidadorController;
use App\Controllers\PainelController;
use App\Controllers\ContaController;
use App\Controllers\NotificacaoController;
use App\Controllers\TurmaController;
use App\Controllers\GrupoController;
use App\Controllers\CursoController;
use App\Controllers\MatrizCurricularController;
use App\Controllers\UnidadeController;
use App\Controllers\UsuarioController;
use App\Controllers\PeriodoController;
use App\Controllers\EspacoController;
use App\Controllers\ProfessorController;
use App\Controllers\AlunoController;
use App\Controllers\InscricaoController;
use App\Controllers\AvaliacaoController;
use App\Controllers\FrequenciaController;
use App\Controllers\RelatorioController;
use App\Controllers\LogController;

// Importação de Middlewares
use App\Middlewares\AuthMiddleware;
use App\Middlewares\AcessoMiddleware;

/** @var Router $router */


// ------------------------------
// Autenticação
// ------------------------------
$router->get('/login', [AuthController::class, 'exibirLogin']);
$router->post('/login', [AuthController::class, 'autenticar']);
$router->get('/sair', [AuthController::class, 'sair']);

// ------------------------------
// Recuperação de senha
// ------------------------------
$router->get('/esqueci-senha', [AuthController::class, 'exibirEsqueciSenha']);
$router->post('/esqueci-senha', [AuthController::class, 'enviarLinkRedefinicao']);

// ------------------------------
// Redefinição de senha
// ------------------------------
$router->get('/redefinir-senha/{token}', [AuthController::class, 'exibirRedefinicaoSenha'])
    ->where('token', '[A-Za-z0-9\-\_]+');

$router->post('/redefinir-senha', [AuthController::class, 'salvarNovaSenha']);

// ------------------------------
// Alteração obrigatória de senha
// ------------------------------
$router->get('/alterar-senha/{token}', [AuthController::class, 'exibirAlterarSenha'])
    ->where('token', '[A-Za-z0-9\-\_]+');

$router->post('/alterar-senha', [AuthController::class, 'salvarSenhaAlterada']);

// ------------------------------
// Validador de documentos (externo)
// ------------------------------
$router->get('/validar', [ValidadorController::class, 'exibirValidador']);
$router->post('/validar', [ValidadorController::class, 'validarDocumento']);

// ------------------------------
// Carteirinha de estudante (externo)
// ------------------------------
$router->get('/carteirinha/buscar', [ValidadorController::class, 'buscarDadosCarteirinha']);

$router->get('/carteirinha/{token}', [ValidadorController::class, 'exibirCarteirinha'])
    ->where('token', '[a-zA-Z0-9]+');


/* ========================================
 * ROTAS INTERNAS (AUTENTICADAS)
 ======================================== */

/**
 * Todas as rotas abaixo exigem autenticação do usuário.
 */
$router->group(['middleware' => [AuthMiddleware::class]], function (Router $router) {

    // ------------------------------
    // Painel principal
    // ------------------------------
    $router->get('/', [PainelController::class, 'exibirIndex']);
    $router->get('/inicio', [PainelController::class, 'exibirIndex']);

    // ------------------------------
    // Configurações e notificações
    // ------------------------------
    $router->get('/configuracoes', [ContaController::class, 'exibirConfiguracoes']);

    $router->post('/configuracoes/informacoes-pessoais', [ContaController::class, 'salvarInformacoesPessoais']);
    $router->post('/configuracoes/senha', [ContaController::class, 'salvarSenha']);
    $router->post('/configuracoes/contato', [ContaController::class, 'salvarInformacoesContato']);




    $router->get('/notificacoes', [NotificacaoController::class, 'exibirIndex']);

    $router->get('/carteirinha', [ContaController::class, 'exibirCarteirinha'])
            ->middleware(AcessoMiddleware::class . ':carteirinha.visualizar');

    
    $router->group(['middleware' => [AcessoMiddleware::class . ':turmas.*']], function (Router $router) {

        $router->get('/turmas', [TurmaController::class, 'exibirIndex']);
        $router->get('/turmas/{id}', [TurmaController::class, 'exibirTurma'])->where('id', '[0-9]+');

    });

    $router->get('/usuarios', [UsuarioController::class, 'exibirIndex'])
        ->middleware(AcessoMiddleware::class . ':usuarios.visualizar,usuarios.gerenciar');

    $router->get('/cursos', [CursoController::class, 'exibirIndex'])
        ->middleware(AcessoMiddleware::class . ':cursos.*');

    $router->get('/alunos', [AlunoController::class, 'exibirIndex'])
        ->middleware(AcessoMiddleware::class . ':discentes.*');

    $router->get('/professores', [ProfessorController::class, 'exibirIndex'])
        ->middleware(AcessoMiddleware::class . ':docentes.*');

    $router->get('/logs', [LogController::class, 'exibirIndex'])
        ->middleware(AcessoMiddleware::class . ':logs.visualizar');

    $router->get('/relatorios', [RelatorioController::class, 'exibirIndex'])
        ->middleware(AcessoMiddleware::class . ':relatorios.*');

    $router->get('/grupos', [GrupoController::class, 'exibirIndex'])
        ->middleware(AcessoMiddleware::class . ':grupos.*');

    $router->get('/matrizes-curriculares', [MatrizCurricularController::class, 'exibirIndex'])
        ->middleware(AcessoMiddleware::class . ':matrizes.*');

    $router->get('/periodos-letivos', [PeriodoController::class, 'exibirIndex'])
        ->middleware(AcessoMiddleware::class . ':periodos.*');

    $router->get('/espacos', [EspacoController::class, 'exibirIndex'])
        ->middleware(AcessoMiddleware::class . ':espacos.*');

    $router->get('/inscricoes', [InscricaoController::class, 'exibirIndex'])
        ->middleware(AcessoMiddleware::class . ':inscricoes.*');

});