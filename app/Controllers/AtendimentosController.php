<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    private function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function listar(): void
    {
        $sql = 'SELECT
                    a.id,
                    p.nome  AS pessoa_nome,
                    t.nome  AS tipo_nome,
                    u.nome  AS responsavel_nome,
                    a.descricao,
                    a.status,
                    a.data_atendimento,
                    a.horario_atendimento,
                    a.observacao_final,
                    a.criado_em
                FROM atendimentos a
                INNER JOIN pessoas           p ON p.id = a.pessoa_id
                INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
                INNER JOIN usuarios          u ON u.id = a.usuario_id
                ORDER BY a.id DESC';

        $atendimentos = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($atendimentos as &$a) {
            $a['protocolo'] = 'ATD-' . str_pad((string) $a['id'], 4, '0', STR_PAD_LEFT);
        }
        unset($a);

        $this->json($atendimentos);
    }

    public function buscar(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 400);
            return;
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                 a.*,
                 p.nome AS pessoa_nome,
                 t.nome AS tipo_nome,
                 u.nome AS responsavel_nome
             FROM atendimentos a
             INNER JOIN pessoas           p ON p.id = a.pessoa_id
             INNER JOIN tipos_atendimentos t ON t.id = a.tipo_atendimento_id
             INNER JOIN usuarios          u ON u.id = a.usuario_id
             WHERE a.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$atendimento) {
            $this->json(['erro' => 'Atendimento não encontrado.'], 404);
            return;
        }

        $atendimento['protocolo'] = 'ATD-' . str_pad((string) $atendimento['id'], 4, '0', STR_PAD_LEFT);

        $this->json($atendimento);
    }

    public function criar(): void
    {
        $pessoaId   = filter_var($_POST['pessoa_id']           ?? null, FILTER_VALIDATE_INT);
        $tipoId     = filter_var($_POST['tipo_atendimento_id'] ?? null, FILTER_VALIDATE_INT);
        $usuarioId  = usuarioAtual()["id"] ?? null;
        $descricao  = trim($_POST['descricao']                 ?? '');
        $data       = $_POST['data_atendimento']               ?? '';
        $horario    = $_POST['horario_atendimento']            ?? '';
        $status     = $_POST['status']                         ?? 'aberto';

        if (!$pessoaId || !$tipoId || !$usuarioId || $descricao === '' || $data === '' || $horario === '') {
            $this->json(['erro' => 'Preencha os campos obrigatórios: pessoa_id, tipo_atendimento_id, usuario_id, descricao, data_atendimento, horario_atendimento.'], 422);
            return;
        }

        if (!in_array($status, ['aberto', 'em_andamento'], true)) {
            $this->json(['erro' => 'Status inicial inválido. Use aberto ou em_andamento.'], 422);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO atendimentos
                     (pessoa_id, tipo_atendimento_id, usuario_id, descricao,
                      status, data_atendimento, horario_atendimento)
                 VALUES
                     (:pessoa_id, :tipo_id, :usuario_id, :descricao,
                      :status, :data, :horario)'
            );
            $stmt->execute([
                'pessoa_id'  => $pessoaId,
                'tipo_id'    => $tipoId,
                'usuario_id' => $usuarioId,
                'descricao'  => $descricao,
                'status'     => $status,
                'data'       => $data,
                'horario'    => $horario,
            ]);

            $novoId = $this->pdo->lastInsertId();

            $this->json([
                'mensagem'  => 'Atendimento registrado com sucesso.',
                'id'        => $novoId,
                'protocolo' => 'ATD-' . str_pad((string) $novoId, 4, '0', STR_PAD_LEFT),
            ], 201);

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->json(['erro' => 'pessoa_id, tipo_atendimento_id ou usuario_id inválido.'], 422);
            } else {
                $this->json(['erro' => 'Não foi possível registrar o atendimento.'], 500);
            }
        }
    }

    public function alterarStatus(): void
    {
        $id          = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $status      = $_POST['status']               ?? '';
        $observacao  = trim($_POST['observacao_final'] ?? '');

        if (!$id || !in_array($status, ['aberto', 'em_andamento', 'concluido'], true)) {
            $this->json(['erro' => 'ID ou status inválido.'], 422);
            return;
        }

        if ($status === 'concluido' && $observacao === '') {
            $this->json(['erro' => 'Informe a observação final para concluir o atendimento.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE atendimentos
             SET status           = :status,
                 observacao_final = :observacao
             WHERE id = :id'
        );
        $stmt->execute([
            'status'     => $status,
            'observacao' => $observacao !== '' ? $observacao : null,
            'id'         => $id,
        ]);

        $this->json(['mensagem' => 'Status atualizado com sucesso.']);
    }

    public function excluir(): void
    {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id) {
            $this->json(['erro' => 'ID inválido.'], 422);
            return;
        }

        $stmt = $this->pdo->prepare('DELETE FROM atendimentos WHERE id = :id');
        $stmt->execute([':id' => $id]);

        $this->json(['mensagem' => 'Atendimento excluído com sucesso.']);
    }
}
