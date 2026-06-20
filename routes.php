<?php

// ============================================================
// AtendeLab - routes.php  (Aula 05)
// Ponto central de roteamento da aplicação.
// Todas as requisições passam por public/index.php -> aqui.
// ============================================================

require_once __DIR__ . '/app/Middleware/auth.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/FrontendController.php';

$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action']     ?? 'login';

if ($controller === 'auth') {
    $auth = new AuthController();

    switch ($action) {
        case 'login':
            $auth->exibirLogin();
            break;

        case 'entrar':
            $auth->entrar();
            break;

        case 'dashboard':
            exigirAutenticacao();
            $auth->dashboard();
            break;

        case 'logout':
            $auth->logout();
            break;

        default:
            http_response_code(404);
            echo 'Ação de autenticação não encontrada.';
    }

    exit;
}

// ─── ROTAS PROTEGIDAS ─────────────────────────────────────────────────────────
// Todas as rotas abaixo exigem sessão ativa
exigirAutenticacao();

switch ($controller) {

    // ── Usuários ──────────────────────────────────────────────────────────────
    case 'usuarios':
        $obj = new UsuariosController();
        break;

    // ── Pessoas ───────────────────────────────────────────────────────────────
    case 'pessoas':
        $obj = new PessoasController();
        break;

    // ── Tipos de atendimento ──────────────────────────────────────────────────
    case 'tipos':
        $obj = new TiposAtendimentosController();
        break;

    // ── Atendimentos ──────────────────────────────────────────────────────────
    case 'atendimentos':
        $obj = new AtendimentosController();
        break;

    // ── Frontend (páginas visuais integradas ao backend) ─────────────────────
    case 'frontend':
        $obj = new FrontendController();
        break;

    default:
        http_response_code(404);
        exit('Controller não encontrado.');
}

// Verifica se a action existe no controller antes de chamar
if (!method_exists($obj, $action)) {
    http_response_code(404);
    exit('Ação não encontrada.');
}

$obj->$action();
