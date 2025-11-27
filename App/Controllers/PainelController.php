<?php

/**
 * @file PainelController.php
 * @description Controlador responsável pelo gerenciamento das requisições que envolvem o painel (sistema)
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Models\AlunoMatricula;
use App\Models\Enumerations\TurmaStatus;
use App\Models\PeriodoLetivo;
use App\Models\Professor;
use App\Models\Turma;
use App\Services\AutenticacaoService;
use App\Core\Response;
use Illuminate\Support\Facades\Cache;
use App\Models\Aluno;
use App\Models\Inscricao;

/**
 * Classe PainelController
 *
 * Gerencia as requisições que envolvem o painel (sistema)
 *
 * @package App\Controllers
 * @extends Controller
*/
class PainelController extends Controller
{

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Renderiza a página inicial do sistema
     *
     * @return void
     */
    public function exibirIndex(): void
    {

        // Verifica a permissão do usuário autenticado
        $usuario = AutenticacaoService::usuarioAutenticado();

        $gruposPermissao = $usuario->grupos;

        foreach ($gruposPermissao as $grupo) {

            switch (strtoupper($grupo->obterNome())) {
                case 'ADMINISTRADOR':
                    $this->exibirPainelAdministrador();
                    return;

                case 'ALUNO':
                    // Renderiza a página inicial do aluno
                    $this->exibirPainelAluno();
                    return;

                case 'PROFESSOR':
                    // Renderiza a página inicial do professor
                    $this->exibirPainelProfessor();
                    return;
            }
        }

        // Se não tiver permissão, redireciona para a página de acesso negado
        Response::atribuirCodigoStatus(403);

    }

    /**
     * Renderiza o painel do aluno
     * 
     * @return void
     */
    public function exibirPainelAluno(): void
    {
        // Verificar permissão da conta

        // Breadcrumbs = links de navegação
        $breadcrumbs = [];

        // TODO: Arrumar estatísticas reais (e cacheá-las)
        $estatisticas = (object) [
            'disciplinas_andamento' => 0,
            'andamento_curso' => 0,
            'coeficiente_rendimento' => 0.0,
            'periodo_atual' => 2025.2
        ];

        $usuario = AutenticacaoService::usuarioAutenticado();
        $turmas = collect();
        if ($usuario) {
            $aluno = Aluno::where('usuario_id', $usuario->obterId())->first();
            $matricula = $aluno?->obterUltimaMatricula();
            if ($matricula) {
                $cacheKey = 'painel.aluno.turmas.' . $usuario->obterId() . '.' . $matricula->id;
                $turmas = Cache::remember($cacheKey, 300, function() use ($matricula) {
                    $inscricoes = Inscricao::with([
                        'turma.disciplina.componenteCurricular:id,nome',
                        'turma.disciplina:id,sigla,componente_curricular_id',
                        'turma.professor:id,usuario_id',
                        'turma.professor.usuario:id,nome_civil,nome_social'
                    ])
                    ->where('aluno_matricula_id', $matricula->id)
                    ->whereHas('turma', function($query) {
                        $query->where('status', TurmaStatus::ATIVA);
                    })
                    ->orderByDesc('id')
                    ->get();
                    $turmas = $inscricoes->map(function($inscricao) {
                        return $inscricao->turma;
                    })->filter()->values();
                    return $turmas instanceof \Illuminate\Support\Collection ? $turmas : collect($turmas);
                });
            }
        }

        // Renderiza a página inicial do aluno
        $this->renderizar('painel/aluno', [
            'titulo' => 'Início',
            'breadcrumbs' => $breadcrumbs,
            'estatisticas' => $estatisticas,
            'turmas' => $turmas
        ]);

    }

    /**
     * Renderiza o painel do professor
     * 
     * @return void
     */
    public function exibirPainelProfessor(): void
    {
        // Verificar permissão da conta

        // Breadcrumbs = links de navegação
        $breadcrumbs = [];

        // Renderiza a página inicial do professor
        $this->renderizar('painel/professor', ['titulo' => 'Início', 'breadcrumbs' => $breadcrumbs]);

    }

    /**
     * Renderiza o painel do administrador
     * 
     * @return void
     */
    public function exibirPainelAdministrador(): void
    {
        // Renderiza a página inicial do administrador
        $periodoUltimo = PeriodoLetivo::obterUltimo();

        // Breadcrumbs = links de navegação
        $breadcrumbs = [];
        
        // Cache de estatísticas por 5 minutos (300 segundos)
        $estatisticas = Cache::remember('painel.admin.estatisticas', 300, function() use ($periodoUltimo) {
            return (object) [
                'turmas_ativas' => Turma::contarAtivas(),
                'alunos_matriculados' => AlunoMatricula::contarAtivos(),
                'professores_alocados' => Professor::contarAlocados(),
                'periodo_ultimo' => $periodoUltimo?->obterSigla() ?? 'N/A',
                'alunos_integralizando' => 0,
            ];
        });

        // Cache das turmas ativas do painel do administrador por 5 minutos
        $turmas = Cache::remember('painel.admin.turmas', 300, function() {
            return Turma::with([
                'disciplina.componenteCurricular:id,nome',
                'disciplina:id,sigla,componente_curricular_id',
                'professor:id,usuario_id',
                'professor.usuario:id,nome_civil,nome_social'
            ])
            ->where('status', TurmaStatus::ATIVA)
            ->orderBy('codigo', 'desc')
            ->get();
        });

        $this->renderizar('painel/administrador', [
            'titulo' => 'Início',
            'breadcrumbs' => $breadcrumbs,
            'estatisticas' => $estatisticas,
            'turmas' => $turmas
        ]);

    }
}
