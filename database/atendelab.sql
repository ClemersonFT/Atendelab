CREATE DATABASE IF NOT EXISTS atendelab;

USE atendelab;


CREATE TABLE usuarios (

    id INT NOT NULL AUTO_INCREMENT,

    nome VARCHAR(100) NOT NULL,

    email VARCHAR(100),

    senha VARCHAR(255) NOT NULL,

    perfil ENUM('admin','usuario') DEFAULT 'usuario',

    status ENUM('ativo','inativo') DEFAULT 'ativo',

    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,


    PRIMARY KEY(id),

    UNIQUE(email)

);



CREATE TABLE tipos_atendimentos (

    id INT NOT NULL AUTO_INCREMENT,

    nome VARCHAR(100) NOT NULL,

    descricao TEXT,

    status ENUM('ativo','inativo') DEFAULT 'ativo',


    PRIMARY KEY(id)

);



CREATE TABLE pessoas (

    id INT NOT NULL AUTO_INCREMENT,


    nome VARCHAR(100) NOT NULL,

    documento VARCHAR(20),

    telefone VARCHAR(20),

    email VARCHAR(100),


    curso VARCHAR(100),

    periodo VARCHAR(100),


    observacoes TEXT,


    status ENUM('ativo','inativo') DEFAULT 'ativo',


    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,


    PRIMARY KEY(id),

    UNIQUE(documento)

);



CREATE TABLE atendimentos (

    id INT NOT NULL AUTO_INCREMENT,


    pessoa_id INT NOT NULL,


    tipo_atendimento_id INT NOT NULL,


    usuario_id INT NOT NULL,


    data_atendimento DATE,


    horario_atendimento TIME,


    descricao TEXT,


    observacao_final TEXT,


    status ENUM(
        'ativo',
        'inativo',
        'cancelado'
    ) DEFAULT 'ativo',


    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,


    PRIMARY KEY(id),


    CONSTRAINT fk_atendimento_pessoa

    FOREIGN KEY(pessoa_id)

    REFERENCES pessoas(id),


    CONSTRAINT fk_atendimento_tipo

    FOREIGN KEY(tipo_atendimento_id)

    REFERENCES tipos_atendimentos(id),


    CONSTRAINT fk_atendimento_usuario

    FOREIGN KEY(usuario_id)

    REFERENCES usuarios(id)

);