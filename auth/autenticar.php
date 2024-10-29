<?php
session_start();
require '../conexao.php';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $stmt = $conn -> prepare("SELECT id_usuario, login, senha, tipo_usuario, nome, matricula FROM tb_usuarios WHERE login = :login");
    $stmt -> bindParam(':login', $login);
    $stmt -> execute();
    $usuario = $stmt -> fetch(PDO::FETCH_ASSOC);

    if($senha == $usuario['senha']){
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
        $_SESSION['id_usuario'] = $usuario['id_usuario'];

        $stmt = $conn -> prepare("SELECT *
            FROM tb_usuarios
            WHERE id_usuario = ?");
            $stmt -> execute([$_SESSION['id_usuario']]);
            $dados = $stmt -> fetch(PDO::FETCH_ASSOC);
        
        $_SESSION['nome'] = $dados['nome'];
        $_SESSION['matricula'] = $dados['matricula'];
        header('Location: ../index.php');
        exit;
    } else {
        $_SESSION['erro_login'] = 'Matricula ou senha inválidas';
        header('Location: login.html');
        exit;
    }
}
?>