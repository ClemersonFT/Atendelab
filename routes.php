<?php

require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Middleware/auth.php';

$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action']     ?? 'login';

switch ($controller) {

    // ─── AUTH ─────────────────────────────────────────────────────────────────
    case 'auth':
        $authController = new AuthController();

        switch ($action) {
            case 'login':
                $authController->exibirLogin();
                break;

            case 'entrar':
                $authController->entrar();
                break;

            case 'dashboard':
                $authController->dashboard();
                break;

            case 'logout':
                $authController->logout();
                break;

            default:
                http_response_code(404);
                echo 'Acao de autenticacao nao encontrada.';
        }
        break;

    // ─── USUARIOS ─────────────────────────────────────────────────────────────
    case 'usuarios':
        exigirAutenticacao();
        $usuariosController = new UsuariosController();

        switch ($action) {
            case 'listar':
                $usuariosController->listar();
                break;

            case 'buscar':
                $usuariosController->buscarPorId();
                break;

            case 'criar':
                $usuariosController->criar();
                break;

            case 'atualizar':
                $usuariosController->atualizar();
                break;

            case 'excluir':
                $usuariosController->excluir();
                break;

            default:
                http_response_code(404);
                echo 'Acao de usuarios nao encontrada.';
        }
        break;

    // ─── PESSOAS ──────────────────────────────────────────────────────────────
    case 'pessoas':
        exigirAutenticacao();
        $pessoasController = new PessoasController();

        switch ($action) {
            case 'listar':
                $pessoasController->listar();
                break;

            case 'buscar':
                $pessoasController->buscarPorId();
                break;

            case 'criar':
                $pessoasController->criar();
                break;

            case 'atualizar':
                $pessoasController->atualizar();
                break;

            case 'excluir':
                $pessoasController->excluir();
                break;

            default:
                http_response_code(404);
                echo 'Acao de pessoas nao encontrada.';
        }
        break;

    // ─── TIPOS DE ATENDIMENTO ─────────────────────────────────────────────────
    case 'tipos':
        exigirAutenticacao();
        $tiposController = new TiposAtendimentosController();

        switch ($action) {
            case 'listar':
                $tiposController->listar();
                break;

            case 'buscar':
                $tiposController->buscarPorId();
                break;

            case 'criar':
                $tiposController->criar();
                break;

            case 'atualizar':
                $tiposController->atualizar();
                break;

            case 'excluir':
                $tiposController->excluir();
                break;

            default:
                http_response_code(404);
                echo 'Acao de tipos nao encontrada.';
        }
        break;

    // ─── ATENDIMENTOS ─────────────────────────────────────────────────────────
    case 'atendimentos':
        exigirAutenticacao();
        $atendimentosController = new AtendimentosController();

        switch ($action) {
            case 'listar':
                $atendimentosController->listar();
                break;

            case 'buscar':
                $atendimentosController->buscarPorId();
                break;

            case 'criar':
                $atendimentosController->criar();
                break;

            case 'atualizar':
                $atendimentosController->atualizar();
                break;

            case 'excluir':
                $atendimentosController->excluir();
                break;

            default:
                http_response_code(404);
                echo 'Acao de atendimentos nao encontrada.';
        }
        break;

    default:
        http_response_code(404);
        echo 'Controller nao encontrado.';
}
