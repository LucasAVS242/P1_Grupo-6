<?php
require '../conexao.php';
session_start();
function getDados($conn, $idFormulario){
    $dados = $conn -> query("SELECT 
		tb_formsReposicao.id_formReposicao,
        tb_usuarios.nome,
        tb_formsReposicao.turno, 
        tb_cursos.nome_curso, 
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
if($dados == null && $_SESSION['tipo_usuario'] == "COORDENADOR"){
    echo "<script>
    alert('Este formulário não possui um plano de reposição preenchido!');
    window.location.href = '../coordenador/PagCoord.php';
    </script>";
} elseif($dados == null && $_SESSION['tipo_usuario'] == "PROFESSOR"){
    echo "<script>
    alert('Este formulário não possui um plano de reposição preenchido!');
    window.location.href = 'reposicao.php?id_formJustificativa=$id_formJustificativa';
    </script>";
}
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
    
    <!--<script src="../Components/footer.js" type="text/javascript" defer></script>-->
    <script src="../Components/validacao.js" type="text/javascript" defer></script>



</head>

<body>

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
                <?php if($_SESSION['tipo_usuario'] == "PROFESSOR"): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="justificativa.php">Justificativa de Faltas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="status.php">Status</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
						<a class="nav-link" href="coordenador/PagCoord.php">Lista de requisições</a>
					</li>
                    <?php endif; ?>
                </ul>
                <div class='d-lg-flex col-lg-3 justify-content-lg-end'>
                    <a href='../auth/logout.php'><button class='btn btn-primary' style='background-color: #005C6D; border: none;'>Sair</button></a>
                </div>
            </div>
        </div>
    </nav>
    
    <br>
    <h2 class="text-center text-decoration-underline mt-4">PLANO - REPOSIÇÃO/SUBSTITUIÇÃO DE AULAS</h2>
    <br>

        <div class="container-repo1">
        <div class="repo item-11">
            <legend>Curso:</legend>
            <p>&nbsp;<?= $dados['nome_curso'] ?></p>
        </div>
        <br>
        <div class="repo item-12">
            <legend>Turno:</legend>  
            <p>&nbsp;<?= $dados['turno'] ?></p>    
        </div>
        <br>
        <div class="repo item-13">
            <legend>Motivo de Reposição:</legend>
            <p>&nbsp;<?= $dados['motivo_reposicao'] ?></p>
        </div>
        <br>
        </div>

      <div class="container-aulaN">
    <h4>Dados da(s) aulas não ministradas</h4>
    <table id="tabelaAulasNaoMinistradas">
        <thead>
            <tr>
                <th>Ordem</th>
                <th>Data</th>
                <th>Nº de Aulas</th>
                <th>Disciplina</th>
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
                    <td><input type="date" class="form-control" value="<?= $aulas['data'] ?>" readonly></td>
                    <td><input type="number" class="form-control" value="<?= $aulas['quantidade_aulas'] ?>" readonly></td>
                    <td><input type="text" class="form-control" value="<?= $aulas['nome_disciplina'] ?>" readonly> </td>
                </tr>
                    <?php endforeach ?>
            </tbody>
    </table>
</div>

<br>

<div class="container-aulaN">
    <h4>Dados da(s) aulas de reposição</h4>
    <table id="tabelaReposicao">
        <thead>
            <tr>
                <th>Ordem</th>
                <th>Data</th>
                <th>Horário de Início e Término</th>
                <th>Disciplina</th>
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
                    <td><input type="date" class="form-control" value="<?= $aula['data'] ?>" readonly></td>
                    <td><input type="text" class="form-control" value="<?= date('H:i', strtotime($aula['horario_inicio'])) ?> às <?= date('H:i', strtotime($aula['horario_final'])) ?>" readonly></td>
                    <td><input type="text" class="form-control" value="<?= $aula['nome_disciplina'] ?>" readonly></td>
                </tr>
                <?php endforeach ?>
            </tbody>
    </table>
</div>

<footer>
    
    <div class="container-footer">

      <div class="item item-1"><a href="../index.html"><img src="../images/logo_fatec_br.png"></a></div>

      <div class="item item-3">
      </div>

      <div class="item item-4">
      </div>

      <div class="item item-5">
        <?php if($_SESSION['tipo_usuario'] == "PROFESSOR") :?>
        <h3>Área do Professor</h3>
        
            <a href="status.php"><p>Status</p></a>
            <a href="justificativa.php"><p>Justificativa de Faltas</p></a>

        <?php else: ?>
            <h3>Área do Coordenador</h3>
        	<a href="../coordenador/PagCoord.php"><p>Lista de Requisições</p></a>

        <?php endif ?>
      </div>

      <div class="item item-6"></div>

    </div>
    
    </footer>
</body>

</html>
