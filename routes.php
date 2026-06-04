<?php
// ─── ROUTES.PHP ──────────────────────────────────────────────────────────────
// Este arquivo é o roteador do sistema: lê os parâmetros da URL e decide qual
// controller e qual método executar.
// Fluxo: navegador → public/index.php → routes.php → Controller → banco

// Importa todos os controllers disponíveis
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';

// Lê os parâmetros controller e action da URL
// Exemplo: ?controller=usuarios&action=listar
$controller = $_GET['controller'] ?? 'index';
$action     = $_GET['action']     ?? 'index';

// ─── USUARIOS ────────────────────────────────────────────────────────────────
if ($controller === 'usuarios') {

    // ATENÇÃO: a classe se chama UsuariosController (com "s")
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
            echo json_encode(['erro' => 'Ação de usuários não encontrada.']);
    }

// ─── PESSOAS ─────────────────────────────────────────────────────────────────
} elseif ($controller === 'pessoas') {

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

        case 'inativar':
            $pessoasController->inativar();
            break;

        default:
            echo json_encode(['erro' => 'Ação de pessoas não encontrada.']);
    }

// ─── TIPOS DE ATENDIMENTO ─────────────────────────────────────────────────────
} elseif ($controller === 'tipos') {

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

        case 'inativar':
            $tiposController->inativar();
            break;

        default:
            echo json_encode(['erro' => 'Ação de tipos de atendimento não encontrada.']);
    }

// ─── ATENDIMENTOS ─────────────────────────────────────────────────────────────
} elseif ($controller === 'atendimentos') {

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

        case 'atualizarStatus':
            $atendimentosController->atualizarStatus();
            break;

        default:
            echo json_encode(['erro' => 'Ação de atendimentos não encontrada.']);
    }

// ─── PÁGINA INICIAL ───────────────────────────────────────────────────────────
} else {
    echo '<h1>AtendeLab</h1>';
    echo '<p>Sistema no ar. Exemplos de uso:</p>';
    echo '<ul>';
    echo '<li><a href="?controller=usuarios&action=listar">Listar usuários</a></li>';
    echo '<li><a href="?controller=pessoas&action=listar">Listar pessoas</a></li>';
    echo '<li><a href="?controller=tipos&action=listar">Listar tipos de atendimento</a></li>';
    echo '<li><a href="?controller=atendimentos&action=listar">Listar atendimentos</a></li>';
    echo '</ul>';
}
