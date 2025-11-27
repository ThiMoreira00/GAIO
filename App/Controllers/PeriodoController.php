<?php

/**
 * @file PeriodoController.php
 * @description Controlador responsável pelo gerenciamento das requisições que envolvem os períodos letivos
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Core\Request;
use App\Models\PeriodoLetivo;
use App\Models\Enumerations\PeriodoLetivoStatus;
use Exception;
use DateTime;

/**
 * Classe PeriodoController
 *
 * Gerencia as requisições que envolvem os períodos letivos
 *
 * @package App\Controllers
 * @extends Controller
*/
class PeriodoController extends Controller
{
    
    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Renderiza a página de listagem dos períodos letivos
     *
     * @return void
     */
    public function exibirIndex()
    {
        
        // Breadcrumbs = links de navegação
        $breadcrumbs = [
            ['label' => 'Períodos Letivos', 'url' => '/periodos']
        ];

        // Obtém a lista de status dos períodos letivos
        $status_periodos = PeriodoLetivoStatus::cases();

        // Carrega períodos iniciais para exibir na página
        $periodos_letivos = PeriodoLetivo::query()->paginate(15)->sortByDesc('data_inicio')->values();

        // Renderiza a página de listagem dos períodos letivos
        $this->renderizar('periodos/index', [
            'titulo' => 'Períodos Letivos',
            'breadcrumbs' => $breadcrumbs,
            'status_periodos' => $status_periodos,
            'periodos_letivos' => $periodos_letivos
        ]);

    }


    // --- MÉTODOS DE REQUISIÇÕES ---

    /**
     * Função para filtrar os períodos letivos, com base nos parâmetros passados
     *
     * @param Request $request
     * @return void
     */
    public function filtrarPeriodosLetivos(Request $request): void {
        try {

            // TODO: Adicionar token CSRF

            $status = $request->get('status') ?: null;
            $busca = $request->get('busca') ?: null;

            $query = PeriodoLetivo::query();

            if ($status) {
                $query->where('status', $status);
            }

            if ($busca) {
                $query->where('sigla', 'LIKE', "%$busca%");
            }

            $periodos_letivos = $query->paginate(15)->sortByDesc('data_inicio')->values();

            // Se não for AJAX, retorna JSON (compatibilidade com código antigo)
            $this->responderJSON([
                'status' => 'sucesso',
                'data' => $periodos_letivos
            ]);

        } catch (Exception $exception) {
            error_log($exception);
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao filtrar os períodos letivos.'
            ]);
        }
    }

    /**
     * Adiciona um novo período letivo ao sistema
     * 
     * @param Request $request
     * @return void
     */
    public function adicionarPeriodoLetivo(Request $request): void {
        try {

            // TODO: Validar token CSRF

            $sigla = $request->post('sigla');
            $data_inicio = $request->post('data_inicio');
            $data_termino = $request->post('data_termino');


            if (!$sigla || !$data_inicio || !$data_termino) {
                throw new Exception('Todos os campos são obrigatórios.');
            }

            // Verifica se a sigla está no formato correto (XXXX.X)
            if (!preg_match('/^\d{4}\.\d$/', $sigla)) {
                throw new Exception("A sigla deve estar no formato 'AAAA.X', onde 'AAAA' é o ano com 4 dígitos e 'X' é o semestre (1 ou 2).");
            }

            // Converte as datas para o formato DateTime
            $data_inicio_dt = new DateTime($data_inicio);
            $data_termino_dt = new DateTime($data_termino);

            // Verifica se a data de término é posterior à data de início
            if ($data_termino_dt <= $data_inicio_dt) {
                throw new Exception('A data de término deve ser posterior à data de início.');
            }

            // Verifica se existe algum outro período letivo com a mesma sigla
            $periodo_existente = PeriodoLetivo::query()->where('sigla', $sigla)->first();
            if ($periodo_existente) {
                throw new Exception('Já existe um período letivo com essa sigla.');
            }

            // Verifica se existe algum outro período letivo que conflite com as datas informadas
            $periodo_conflitante = PeriodoLetivo::intervalo($data_inicio_dt, $data_termino_dt)->first();

            if ($periodo_conflitante) {
                throw new Exception('Já existe um período letivo que conflita com as datas informadas.');
            }

            // Cria o novo período letivo
            $novo_periodo = PeriodoLetivo::create([
                'sigla' => $sigla,
                'data_inicio' => $data_inicio_dt->format('Y-m-d'),
                'data_termino' => $data_termino_dt->format('Y-m-d'),
                'status' => (new DateTime())->format('Y-m-d') < $data_inicio_dt->format('Y-m-d') ? PeriodoLetivoStatus::PROGRAMADO : PeriodoLetivoStatus::ATIVO
            ]);

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => sprintf('Período letivo %s adicionado com sucesso.', $novo_periodo->obterSigla()),
            ]);

        } catch (Exception $exception) {
            error_log($exception);
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao adicionar o período letivo.'
            ]);
        }
    }
}
