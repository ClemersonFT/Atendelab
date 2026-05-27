<?php


$host = "localhost";
$banco = "atendelab";
$port = "3307";
$usuario = "root";
$senha = "";
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$banco;charset=utf8",
            $usuario,
            $senha
        );
        echo "Conexão realizada com sucesso!";
    }   catch (PDOException $e){
        echo "Erro: " . $e->getMessage();
    }