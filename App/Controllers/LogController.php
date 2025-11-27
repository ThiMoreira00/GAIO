<?php

/**
 * @file LogController.php
 * @description Controlador responsável pelo gerenciamento das logs no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Core\Request;
use App\Models\Log;

/**
 * Classe LogController
 *
 * Controlador responsável pelo gerenciamento das logs no sistema
 *
 * @package App\Controllers
 * @extends Controller
 */
class LogController extends Controller
{

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Renderiza a página de gerenciamento de usuários
     *
     * @return void
     */
    public function exibirIndex(): void
    {

        // Obtém todos as logs no sistema
        $logs = Log::obterTodos();

        // Breacrumbs = links de navegação
        $breadcrumbs = [
            ['label' => 'Administração', 'url' => '/admin'],
            ['label' => 'Logs', 'url' => '/logs']
        ];

        // Renderiza a página
        $this->renderizar('logs/index', [
            'titulo' => 'Logs',
            'breadcrumbs' => $breadcrumbs,
            'logs' => $logs
        ]);
    }
}