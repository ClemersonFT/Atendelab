<?php

class TiposAtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    // ─── helper interno ───────────────────────────────────────────────────────
    private function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // ─── LISTAR ───────────────────────────────────────────────────────────────
    // GET ?controller=tipos&action=listar
    public function listar(): void
    {
        $sql = 'SELECT id, nome, descricao, status, criado_em
                FROM tipos_atendimentos
                ORDER BY nome ASC';

        $tipos = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $this->json($tipos);
    }

    // ─── BUSCAR POR ID ────────────────────────────────────────────────────────
    // GET ?controller=tipos&action=buscar&id=1
    public function buscar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        $stmt = $this->pdo->prepare(
            'SELECT id, nome, descricao, status, criado_em
             FROM tipos_atendimentos
             WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tipo) {
            $this->json(['erro' => 'Tipo de atendimento não encontrado.'], 404);
            return;
        }

        $this->json($tipo);
    }

    // ─── CRIAR ────────────────────────────────────────────────────────────────
    // POST ?controller=tipos&action=criar
    public function criar(): void
    {
        $nome      = trim($_POST['nome']      ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status    = $_POST['status']         ?? 'ativo';

        if ($nome === '') {
            $this->json(['erro' => 'Nome é obrigatório.'], 422);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO tipos_atendimentos (nome, descricao, status)
             VALUES (:nome, :descricao, :status)'
        );
        $stmt->execute(compact('nome', 'descricao', 'status'));

        $this->json([
            'mensagem' => 'Tipo de atendimento cadastrado com sucesso.',
            'id'       => $this->pdo->lastInsertId()
        ], 201);
    }

    // ─── ATUALIZAR ────────────────────────────────────────────────────────────
    // POST ?controller=tipos&action=atualizar
    public function atualizar(): void
    {
        $id        = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $nome      = trim($_POST['nome']      ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status    = $_POST['status']         ?? 'ativo';

        if (!$id || $nome === '') {
            $this->json(['erro' => 'ID e nome são obrigatórios.'], 422);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->json(['erro' => 'Status inválido.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE tipos_atendimentos
             SET nome      = :nome,
                 descricao = :descricao,
                 status    = :status
             WHERE id = :id'
        );
        $stmt->execute(compact('id', 'nome', 'descricao', 'status'));

        $this->json(['mensagem' => 'Tipo de atendimento atualizado com sucesso.']);
    }

    // ─── INATIVAR ─────────────────────────────────────────────────────────────
    // POST ?controller=tipos&action=inativar
    // Soft-delete: tipos inativos não aparecem em novos atendimentos
    public function inativar(): void
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE tipos_atendimentos SET status = 'inativo' WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);

        $this->json(['mensagem' => 'Tipo inativado com sucesso.']);
    }

    // ─── EXCLUIR ──────────────────────────────────────────────────────────────
    // POST ?controller=tipos&action=excluir
    public function excluir(): void
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 422);
            return;
        }

        try {
            $stmt = $this->pdo->prepare('DELETE FROM tipos_atendimentos WHERE id = :id');
            $stmt->execute([':id' => $id]);

            $this->json(['mensagem' => 'Tipo de atendimento excluído com sucesso.']);

        } catch (PDOException $e) {
            $this->json(['erro' => 'Não é possível excluir: tipo possui atendimentos vinculados.'], 409);
        }
    }
}
