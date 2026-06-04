-- ─────────────────────────────────────────────────────────────────────────────
-- Script para rodar NO phpMyAdmin após a Aula 02
-- Execute apenas se a tabela 'usuarios' ainda não aceitar o perfil 'aluno'
-- ─────────────────────────────────────────────────────────────────────────────

-- Adiciona 'aluno' ao ENUM do campo perfil da tabela usuarios
ALTER TABLE usuarios
    MODIFY perfil ENUM('admin', 'atendente', 'aluno') DEFAULT 'atendente';

-- ─────────────────────────────────────────────────────────────────────────────
-- Dados de teste — rode na ordem abaixo para respeitar as chaves estrangeiras
-- ─────────────────────────────────────────────────────────────────────────────

-- 1. Usuário administrador (senha = 123456)
INSERT INTO usuarios (nome, email, senha, perfil, status)
VALUES (
    'Administrador',
    'admin@atendelab.com',
    '$2y$10$J9P2kU2BAMZ3TZcuxTsW4e1D/lka8EocYHzvyoOZmCNcWDQz3RuVC',
    'admin',
    'ativo'
);

-- 2. Tipos de atendimento
INSERT INTO tipos_atendimentos (nome, descricao, status) VALUES
    ('Orientação Acadêmica',  'Dúvidas sobre disciplinas e grade curricular', 'ativo'),
    ('Suporte Psicológico',   'Apoio emocional e encaminhamento',             'ativo'),
    ('Assistência Estudantil','Bolsas, auxílios e moradia estudantil',        'ativo');

-- 3. Pessoas (alunos atendidos)
INSERT INTO pessoas (nome, documento, telefone, curso, periodo, status) VALUES
    ('João da Silva',  '111.111.111-11', '(47) 99999-1111', 'Engenharia de Software', '3º', 'ativo'),
    ('Maria Oliveira', '222.222.222-22', '(47) 99999-2222', 'Ciência da Computação',  '5º', 'ativo');

-- 4. Atendimento de exemplo (use IDs gerados acima; ajuste se necessário)
-- Supõe: usuario_id=1, pessoa_id=1, tipo_atendimento=1
INSERT INTO atendimentos
    (pessoa_id, tipo_atendimento, usuario_id, data_atendimento, hora_atendimento, descricao, status)
VALUES
    (1, 1, 1, CURDATE(), CURTIME(), 'Aluno com dúvidas sobre rematrícula', 'ativo');
