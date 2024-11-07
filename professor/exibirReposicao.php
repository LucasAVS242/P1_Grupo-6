<?php
require '../conexao.php';
session_start();
function getDados($conn, $idFormulario){
    $dados = $conn -> query("SELECT 
		tb_formsReposicao.id_formReposicao,
        tb_usuarios.nome,
        tb_formsReposicao.turno, 
        tb_cursos.sigla, 
        tb_formsReposicao.motivo_reposicao
    FROM tb_formsReposicao
    INNER JOIN tb_formsJustificativa
    ON tb_formsReposicao.id_formJustificativa = tb_formsJustificativa.id_formJustificativa
    INNER JOIN tb_usuarios 
        ON tb_formsJustificativa.id_usuario = tb_usuarios.id_usuario
    INNER JOIN tb_cursos 
        ON tb_formsJustificativa.id_curso = tb_cursos.id_curso
    WHERE tb_formsReposicao.id_formJustificativa = $idFormulario
    ") -> fetch(PDO::FETCH_ASSOC);
    return $dados;
}

function getAulasNaoMinistradas($conn, $idFormulario){
    $aulasNaoMinistradas = $conn -> query("SELECT 
        tb_aulasNaoMinistradas.id_aulaNaoMinistrada,
        tb_aulasNaoMinistradas.quantidade_aulas,
        tb_aulasNaoMinistradas.data,
        tb_disciplinas.nome_disciplina
    FROM tb_aulasNaoMinistradas
    INNER JOIN tb_disciplinas 
        ON tb_aulasNaoMinistradas.id_disciplina = tb_disciplinas.id_disciplina
    INNER JOIN tb_formsJustificativa 
        ON tb_aulasNaoMinistradas.id_formJustificativa = tb_formsJustificativa.id_formJustificativa
    WHERE tb_formsJustificativa.id_formJustificativa = $idFormulario
    ") -> fetchAll(PDO::FETCH_ASSOC);
    return $aulasNaoMinistradas;
}

function getAulasReposicao($conn, $idFormulario){
    $aulasReposicao = $conn -> query("SELECT tb_aulasReposicao.id_aulasReposicao,
        tb_aulasReposicao.data,
        tb_aulasReposicao.horario_inicio,
        tb_aulasReposicao.horario_final,
        tb_disciplinas.nome_disciplina
    FROM tb_aulasReposicao
    INNER JOIN tb_disciplinas 
        ON tb_aulasReposicao.id_disciplina = tb_disciplinas.id_disciplina
    INNER JOIN tb_formsReposicao 
        ON tb_aulasReposicao.id_formReposicao = tb_formsReposicao.id_formReposicao
    WHERE tb_formsReposicao.id_formReposicao = $idFormulario
    ") -> fetchAll(PDO::FETCH_ASSOC);
    return $aulasReposicao;
}

$id_formJustificativa = $_GET['id_formJustificativa'];
$dados = getDados($conn, $id_formJustificativa);
$aulasNaoMinistradas = getAulasNaoMinistradas($conn, $id_formJustificativa);

$id_formReposicao = $dados['id_formReposicao'];
$aulasReposicao = getAulasReposicao($conn, $id_formReposicao);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de reposição de aulas - Área do Professor</title>
    
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../Style/main.css">
    <script src="../Components/footer.js" type="text/javascript" defer></script>
    <script src="../Components/validacao.js" type="text/javascript" defer></script>

    <style>
        
    </style>
    


</head>

<body>
    
    <header>
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse d-lg-flex">
                    <span class="navbar-text col-lg-3 me-0">
                        <?= $_SESSION['tipo_usuario'] ?>: <?= $_SESSION['nome'] ?>
                    </span>
                    <ul class="navbar-nav col-lg-6 justify-content-lg-center">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="../index.php">Início</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="justificativa.php">Justificativa de Faltas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="status.php">Status</a>
                        </li>
                    </ul>
                    <div class='d-lg-flex col-lg-3 justify-content-lg-end'>
                        <a href='../auth/logout.php'><button class='btn btn-primary' style='background-color: #005C6D; border: none;'>Sair</button></a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <br>

    <div class="container mt-4">
    
        <h2 class="text-center text-decoration-underline">PLANO - REPOSIÇÃO/SUBSTITUIÇÃO DE AULAS</h2>
        <fieldset class="form-group">  
            <div class="form-check"> 
                <legend>Curso:</legend>
                <p><?= $dados['sigla'] ?></p>
            </div>
        </fieldset>
        <fieldset class="form-group">
            <div class="form-check"> 
            <legend>Turno:</legend>  
                <p><?= $dados['turno'] ?></p>
            </div>
        </fieldset>
        <fieldset class="form-group">
            
            <div class="form-check">
                <legend>Motivo de Reposição:</legend>
                <p><?= $dados['motivo_reposicao'] ?></p>
            </div>
        </fieldset>
        <h4>Dados da(s) aulas não ministradas</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Ordem</th>
                    <th>Data</th>
                    <th>Nº de Aulas</th>
                    <th>Disciplinas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $cont = 0;
                    foreach($aulasNaoMinistradas as $aulas):
                        $cont++;
                ?>
                <tr>
                    <td><?= $cont ?></td>
                    <td><?= date('d/m/Y', strtotime($aulas['data'])) ?></td>
                    <td><?= $aulas['quantidade_aulas'] ?></td>
                    <td><?= $aulas['nome_disciplina'] ?> </td>
                </tr>
                    <?php endforeach ?>
            </tbody>
        </table>
    
        <h4>Dados da(s) aulas de reposição</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Ordem</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Disciplina(s)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $cont = 0;
                    foreach($aulasReposicao as $aula):
                        $cont++;
                ?>
                <tr>
                    <td><?= $cont ?></td>
                    <td><?= date('d/m/Y', strtotime($aula['data'])) ?></td>
                    <td><?= date('H:i', strtotime($aula['horario_inicio'])) ?> às <?= date('H:i', strtotime($aula['horario_final'])) ?></td>
                    <td><?= $aula['nome_disciplina'] ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    
        <p><strong>Observe as exigências legais:</strong> máximo 8 horas diárias de trabalho, intervalo de 1 hora entre um expediente e outro e 6 horas em cada expediente.</p>
    
    </div>



    <footer-component></footer-component>
</body>

</html>