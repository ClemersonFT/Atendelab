<?php
// Controller responsável por todas as operações da tabela 'usuarios'.
// Em MVC, o controller recebe a requisição, valida os dados e acessa o banco.

class UsuariosController
{
    private PDO $pdo;

    public function __construct()
    {
        // Importa o arquivo de conexão. O $pdo criado lá dentro fica disponível aqui.
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    // ─── LISTAR ──────────────────────────────────────────────────────────────
    // GET: ?controller=usuarios&action=listar
    public function listar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $sql = 'SELECT id, nome, email, perfil, status, criado_em
                FROM usuarios
                ORDER BY id DESC';

        $stmt     = $this->pdo->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // ─── BUSCAR POR ID ────────────────────────────────────────────────────────
    // GET: ?controller=usuarios&action=buscar&id=1
    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // filter_input valida e sanitiza o ID vindo pela URL (GET)
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT id, nome, email, perfil, status, criado_em
                FROM usuarios
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            http_response_code(404);
            echo json_encode(['erro' => 'Usuário não encontrado.']);
            return;
        }

        echo json_encode($usuario, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // ─── CRIAR ────────────────────────────────────────────────────────────────
    // POST: ?controller=usuarios&action=criar
    // Body (form-encode): nome, email, senha, perfil, status
    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // Lê os campos do formulário enviados via POST
        $nome   = trim($_POST['nome'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $senha  = $_POST['senha'] ?? '';
        $perfil = trim($_POST['perfil'] ?? 'atendente');
        $status = trim($_POST['status'] ?? 'ativo');

        // Validação de campos obrigatórios
        if ($nome === '' || $email === '' || $senha === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'Nome, e-mail e senha são obrigatórios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['erro' => 'E-mail inválido.']);
            return;
        }

        // Whitelist de perfis aceitos
        if (!in_array($perfil, ['admin', 'atendente', 'aluno'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Perfil inválido.']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        // Nunca salvar senha em texto puro — password_hash gera um hash seguro
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $sql = 'INSERT INTO usuarios (nome, email, senha, perfil, status)
                    VALUES (:nome, :email, :senha, :perfil, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome',   $nome);
            $stmt->bindValue(':email',  $email);
            $stmt->bindValue(':senha',  $senhaHash);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Usuário cadastrado com sucesso.',
                'id'       => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar usuário.']);
        }
    }

    // ─── ATUALIZAR ────────────────────────────────────────────────────────────
    // POST: ?controller=usuarios&action=atualizar
    // Body (form-encode): id, nome, email, perfil, status
    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // O ID vem no corpo do POST pois é uma operação de escrita
        $id     = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome   = trim($_POST['nome']   ?? '');
        $email  = trim($_POST['email']  ?? '');
        $perfil = trim($_POST['perfil'] ?? 'atendente');
        $status = trim($_POST['status'] ?? 'ativo');

        if (!$id || $nome === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID, nome e e-mail são obrigatórios.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['erro' => 'E-mail inválido.']);
            return;
        }

        if (!in_array($perfil, ['admin', 'atendente', 'aluno'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Perfil inválido.']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Status inválido.']);
            return;
        }

        try {
            $sql = 'UPDATE usuarios
                    SET nome   = :nome,
                        email  = :email,
                        perfil = :perfil,
                        status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome',   $nome);
            $stmt->bindValue(':email',  $email);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id',     $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(
                ['mensagem' => 'Usuário atualizado com sucesso.'],
                JSON_UNESCAPED_UNICODE
            );

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'erro' => $e->getMessage()
        ]);
}
    }

    // ─── EXCLUIR ─────────────────────────────────────────────────────────────
    // POST: ?controller=usuarios&action=excluir
    // Body (form-encode): id
    public function excluir(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        try {
            // DELETE físico — em sistemas reais prefira inativar via UPDATE status
            $sql  = 'DELETE FROM usuarios WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(
                ['mensagem' => 'Usuário excluído com sucesso.'],
                JSON_UNESCAPED_UNICODE
            );

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'erro' => $e->getMessage()
            ]);
        }
    }
}
