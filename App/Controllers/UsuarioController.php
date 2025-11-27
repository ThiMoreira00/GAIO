<?php

/**
 * @file UsuarioController.php
 * @description Controlador responsável pelo gerenciamento dos usuários no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Core\Request;
use App\Models\Usuario;

/**
 * Classe UsuarioController
 *
 * Controlador responsável pelo gerenciamento dos usuários no sistema
 *
 * @package App\Controllers
 * @extends Controller
 */
class UsuarioController extends Controller
{

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Renderiza a página de gerenciamento de usuários
     *
     * @return void
     */
    public function exibirIndex(): void
    {

        // Obtém todos os usuários no sistema
        $usuarios = Usuario::obterTodos();

        // Breacrumbs = links de navegação
        $breadcrumbs = [
            ['label' => 'Administração', 'url' => '/admin'],
            ['label' => 'Usuários', 'url' => '/admin/usuarios']
        ];

        // Renderiza a página
        $this->renderizar('usuarios/index', [
            'titulo' => 'Usuários',
            'breadcrumbs' => $breadcrumbs,
            'usuarios' => $usuarios
        ]);
    }


    // --- MÉTODOS DE REQUISIÇÕES ---

    /**
     * Busca o usuário com base no nome
     * 
     * @param Request $request
     * @return void
     */
    public function buscarUsuarios(Request $request): void
    {
        $termo = $request->get('busca', '');
        $limite = (int) $request->get('limite', 10);

        $usuarios = Usuario::buscarPorNome($termo);

        $resultado = [];

        for ($i = 0; $i < $limite; $i++) {
            $usuario = $usuarios[$i];
            if (!$usuario) break;

            $resultado[] = [
                'id' => $usuario->obterId(),
                'nome_reduzido' => $usuario->obterNomeReduzido(),
                'email' => $usuario->obterEmailInstitucional() ?: $usuario->obterEmailPessoal()
            ];
        }

        $this->responderJSON([
            'usuarios' => $resultado
        ]);

    }
}