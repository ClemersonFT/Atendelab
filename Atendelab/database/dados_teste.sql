ALTER TABLE usuarios
    MODIFY perfil ENUM('admin', 'atendente', 'aluno') DEFAULT 'atendente';

INSERT INTO usuarios (nome, email, senha, perfil, status)
VALUES (
    'Administrador',
    'admin@atendelab.com',
    '$2y$10$nHrIvFpJUgiKvaizd3pccesmp/c8DgJuoNebGQlmYvbEvG9GQ6Zb.',
    'admin',
    'ativo'
);

INSERT INTO tipos_atendimentos (nome, descricao, status) VALUES
    ('Orientação Acadêmica',  'Dúvidas sobre disciplinas e grade curricular', 'ativo'),
    ('Suporte Psicológico',   'Apoio emocional e encaminhamento',             'ativo'),
    ('Assistência Estudantil','Bolsas, auxílios e moradia estudantil',        'ativo');


INSERT INTO pessoas (nome, documento, telefone, curso, periodo, status) VALUES
    ('João da Silva',  '111.111.111-11', '(47) 99999-1111', 'Engenharia de Software', '3º', 'ativo'),
    ('Maria Oliveira', '222.222.222-22', '(47) 99999-2222', 'Ciência da Computação',  '5º', 'ativo');


INSERT INTO atendimentos
    (pessoa_id, tipo_atendimento, usuario_id, data_atendimento, hora_atendimento, descricao, status)
VALUES
    (1, 1, 1, CURDATE(), CURTIME(), 'Aluno com dúvidas sobre rematrícula', 'ativo');
