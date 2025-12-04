<?php

declare(strict_types=1);

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
// Router::get('/carteirinha', 'ValidacaoController@exibirCarteirinha'); // TODO: Implementar
// Router::post('/carteirinha', 'ValidacaoController@validarCarteirinha'); // TODO: Implementar

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

    // Rotas de Turmas - Aluno
    Router::grupo(['middleware' => new ControleAcessoMiddleware('ALUNO')], function (): void {
        Router::get('/turmas', 'TurmaController@exibirIndexAluno');
        Router::get('/turmas/{id}', 'TurmaController@exibirTurmaAluno', ['id' => '[0-9]+']);
        
        Router::grupo(['middleware' => new AjaxMiddleware()], function (): void {
            Router::get('/turmas/filtrar', 'TurmaController@filtrarTurmasAluno');
            Router::get('/turmas/{id}/dados', 'TurmaController@obterTurma', ['id' => '[0-9]+']);
            Router::get('/turmas/dados-filtros', 'TurmaController@obterDadosFiltros');
            Router::get('/turmas/estatisticas', 'TurmaController@obterEstatisticas');
        });
    });

    // Rotas de Turmas - Professor
    Router::grupo(['middleware' => new ControleAcessoMiddleware('PROFESSOR')], function (): void {
        Router::get('/turmas', 'TurmaController@exibirIndexProfessor');
        Router::get('/turmas/{id}', 'TurmaController@exibirTurmaProfessor', ['id' => '[0-9]+']);
        
        Router::grupo(['middleware' => new AjaxMiddleware()], function (): void {
            Router::get('/turmas/filtrar', 'TurmaController@filtrarTurmasProfessor');
            Router::get('/turmas/{id}/dados', 'TurmaController@obterTurma', ['id' => '[0-9]+']);
            Router::get('/turmas/dados-filtros', 'TurmaController@obterDadosFiltros');
            Router::get('/turmas/estatisticas', 'TurmaController@obterEstatisticas');
        });
    });

    // Rotas de Turmas - Administrador
    Router::grupo(['middleware' => new ControleAcessoMiddleware('ADMINISTRADOR')], function (): void {
        Router::get('/turmas', 'TurmaController@exibirIndexAdministrador');
        Router::get('/turmas/{id}', 'TurmaController@exibirTurmaAdministrador', ['id' => '[0-9]+']);
        
        Router::grupo(['middleware' => new AjaxMiddleware()], function (): void {
            Router::get('/turmas/filtrar', 'TurmaController@filtrarTurmasAdministrador');
            Router::get('/turmas/{id}/dados', 'TurmaController@obterTurma', ['id' => '[0-9]+']);
            Router::get('/turmas/dados-filtros', 'TurmaController@obterDadosFiltros');
            Router::get('/turmas/estatisticas', 'TurmaController@obterEstatisticas');
        });
    });

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

            # Matrizes Curriculares
            Router::get('/matrizes-curriculares/obter', 'MatrizCurricularController@obterMatrizes');
            Router::get('/matrizes-curriculares/{id}/obter', 'MatrizCurricularController@obterMatriz', ['id' => '[0-9]+']);
            Router::get('/matrizes-curriculares/{id}/componentes/obter', 'MatrizCurricularController@obterComponentes', ['id' => '[0-9]+']);
            Router::post('/matrizes-curriculares/adicionar', 'MatrizCurricularController@adicionarMatriz');
            Router::post('/matrizes-curriculares/{id}/editar', 'MatrizCurricularController@editarMatriz', ['id' => '[0-9]+']);
            Router::post('/matrizes-curriculares/{id}/inativar', 'MatrizCurricularController@inativarMatriz', ['id' => '[0-9]+']);
            Router::post('/matrizes-curriculares/{id}/validar', 'MatrizCurricularController@validarMatriz', ['id' => '[0-9]+']);
            
            # Componentes Curriculares
            Router::post('/matrizes-curriculares/componentes/adicionar', 'MatrizCurricularController@adicionarComponente');
            Router::post('/matrizes-curriculares/componentes/{id}/editar', 'MatrizCurricularController@editarComponente', ['id' => '[0-9]+']);
            Router::post('/matrizes-curriculares/componentes/{id}/excluir', 'MatrizCurricularController@excluirComponente', ['id' => '[0-9]+']);
            Router::post('/matrizes-curriculares/componentes/{id}/prerequisitos', 'MatrizCurricularController@definirPreRequisitos', ['id' => '[0-9]+']);
            Router::post('/matrizes-curriculares/componentes/{id}/equivalencias', 'MatrizCurricularController@definirEquivalencias', ['id' => '[0-9]+']);

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

            # Turmas (Admin)
            Router::get('/turmas/disciplinas', 'TurmaController@buscarDisciplinasPorCurso');
            Router::get('/turmas/horarios', 'TurmaController@buscarHorariosPorTurno');
            Router::post('/turmas/adicionar', 'TurmaController@adicionarTurma');
            Router::post('/turmas/{id}/editar', 'TurmaController@editarTurma', ['id' => '[0-9]+']);
            Router::post('/turmas/{id}/arquivar', 'TurmaController@arquivarTurma', ['id' => '[0-9]+']);
            Router::post('/turmas/{id}/confirmar', 'TurmaController@confirmarTurma', ['id' => '[0-9]+']);
            Router::post('/turmas/{id}/finalizar', 'TurmaController@finalizarTurma', ['id' => '[0-9]+']);
            Router::post('/turmas/{id}/liberar', 'TurmaController@liberarTurma', ['id' => '[0-9]+']);
            Router::get('/turmas/{id}/alunos', 'TurmaController@obterAlunos', ['id' => '[0-9]+']);
            Router::post('/turmas/{id}/alunos/adicionar', 'TurmaController@adicionarAlunos', ['id' => '[0-9]+']);
            Router::post('/turmas/{id}/alunos/{aluno_id}/remover', 'TurmaController@removerAluno', ['id' => '[0-9]+', 'aluno_id' => '[0-9]+']);

        });

        # Grupos de Permissão
        Router::get('/grupos/permissoes', 'GrupoController@exibirPermissoes');
        Router::get('/grupos/membros', 'GrupoController@exibirMembros');
        Router::get('/usuarios', 'UsuarioController@exibirIndex');

        # Cursos
        Router::get('/cursos', 'CursoController@exibirIndex');
        Router::get('/cursos/visualizar/{id}-{nome}', 'CursoController@exibirCurso', ['id' => '[0-9]+', 'nome' => '[a-zA-Z0-9\%][a-zA-Z0-9\-\%]*']);

        # Matrizes Curriculares
        Router::get('/matrizes-curriculares', 'MatrizCurricularController@exibirIndex');
        Router::get('/matrizes-curriculares/visualizar/{id}', 'MatrizCurricularController@exibirMatriz', ['id' => '[0-9]+']);

        # Espaços
        Router::get('/espacos', 'EspacoController@exibirIndex');

        # Períodos Letivos
        Router::get('/periodos', 'PeriodoController@exibirIndex');
        Router::get('/periodos/{id}', 'PeriodoController@exibirPeriodo', ['id' => '[0-9]+']);

        # Alunos
        Router::get('/alunos', 'AlunoController@exibirIndex');

        # Logs
        Router::get('/logs', 'LogController@exibirIndex');
        
    });

});

// Rotas administrativas para inscrições (apenas ADMIN)
Router::grupo(['middleware' => new AutenticacaoMiddleware()], function (): void {

    # Inscrições (aluno)
    Router::get('/inscricoes', 'InscricaoController@exibirIndex');

    Router::grupo(['middleware' => new AjaxMiddleware()], function (): void {

        # Solicitações de Inscrição
        Router::get('/inscricoes/turmas/filtrar', 'InscricaoController@filtrarTurmas');
        Router::post('/inscricoes/solicitar', 'InscricaoController@solicitarInscricao');
        Router::post('/inscricoes/cancelar', 'InscricaoController@cancelarInscricao');

    });


    Router::get('/carteirinha', 'PainelController@exibirWIP'); // TODO: Implementar
    Router::get('/curso', 'PainelController@exibirWIP'); // TODO: Implementar
    Router::get('/disciplinas', 'PainelController@exibirWIP'); // TODO: Implementar
    Router::get('/frequencias', 'PainelController@exibirWIP'); // TODO: Implementar
    Router::get('/avaliacoes', 'PainelController@exibirWIP'); // TODO: Implementar

    Router::get('/documentos/{tipo}', 'PainelController@exibirWIP', ['tipo' => '[a-zA-Z0-9\-]+']); // TODO: Implementar

    Router::get('/turmas/arquivadas', 'PainelController@exibirWIP'); // TODO: Implementar


    
});

Router::despachar();
