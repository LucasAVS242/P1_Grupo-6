<?php
require '../conexao.php';
session_start();
$id_formJustificativa = $_GET['id_formJustificativa'];
$id_formReposicao = $conn -> query("SELECT id_formReposicao FROM tb_formsReposicao WHERE id_formJustificativa = $id_formJustificativa") -> fetch(PDO::FETCH_ASSOC);

function getDados($conn, $idFormulario){
    $dados = $conn -> query("SELECT 
        tb_formsJustificativa.motivo,
        tb_formsJustificativa.nome_arquivo,
        tb_usuarios.nome,
        tb_usuarios.matricula,
        tb_cursos.sigla
    FROM tb_formsJustificativa
    INNER JOIN tb_usuarios
        ON tb_formsJustificativa.id_usuario = tb_usuarios.id_usuario
    INNER JOIN tb_cursos
        ON tb_formsJustificativa.id_curso = tb_cursos.id_curso
    WHERE tb_formsJustificativa.id_formJustificativa = $idFormulario ") -> fetch(PDO::FETCH_ASSOC);
    return $dados;
}

function getAulasNaoMinistradas($conn, $idFormulario){
    $aulas = $conn -> query("SELECT
        tb_aulasNaoMinistradas.data,
        tb_aulasNaoMinistradas.quantidade_aulas,
        tb_disciplinas.nome_disciplina
    FROM tb_aulasNaoMinistradas
    INNER JOIN tb_disciplinas
        ON tb_aulasNaoMinistradas.id_disciplina = tb_disciplinas.id_disciplina
    WHERE tb_aulasNaoMinistradas.id_formJustificativa = $idFormulario    
    ") -> fetchAll(PDO::FETCH_ASSOC);
    return $aulas;
}


$dados = getDados($conn, $id_formJustificativa);
$aulas = getAulasNaoMinistradas($conn, $id_formJustificativa);
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de justificativa de faltas - Área do Professor</title>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <!--<script src="../Components/footer.js" type="text/javascript" defer></script>-->
    <script src="../Components/validacao.js" type="text/javascript" defer></script>
    <link rel="stylesheet" type="text/css" href="../Style/main.css">

    <style>
        input {
            border-bottom: solid thin;
        }

        form {
            border: black 2px solid;
            border-radius: 10px;
            padding: 2%;
        }

        section {
            border: black 2px solid;
            padding: 1%;
            margin: 2%;
        }

        .center-block {
            display: block;
            margin-right: auto;
            margin-left: auto;
        }
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
    </header>

    <main id="justificativa">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h1 style="text-align: center;">Justificativa de Faltas</h1>
                    <form id="form" method="POST" enctype="multipart/form-data" onsubmit="validarSelecao(event)">
                    <div id="form">
                        <div class="mb-3" id="form">

                            <label><strong>Nome:</strong> </label>
                            <?= $dados['nome'] ?>
                            <label><strong>Mátricula:</strong> </label>
                            <?= $dados['matricula'] ?>
                            <br>
                            <p><strong>FUNÇÃO:</strong> Professor de Ensino Superior <strong>REGIME JURÍDICO:</strong> CLT</p>
                            <strong><label>CURSO(S) ENVOLVIDO(S) NA AUSÊNCIA: </label></strong>
                                <?= $dados['sigla'] ?>
                            <br><br>

                            <h5>Dados da(s) aulas não ministradas</h5>
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
                                        foreach($aulas as $aula):
                                            $cont++;
                                    ?>
                                    <tr>
                                        <td><?= $cont ?></td>
                                        <td><?= date('d/m/Y', strtotime($aula['data'])) ?></td>
                                        <td><?= $aula['quantidade_aulas'] ?></td>
                                        <td><?= $aula['nome_disciplina'] ?> </td>
                                    </tr>
                                        <?php endforeach ?>
                                </tbody>
                                        
                            </table>
                            </p>


                            <h3 class="text-decoration-underline">Motivo da falta</h3>
                            <?= $dados['motivo']?>
                            
                            </div>
                            </strong>
                            <br>  
                                <a href="../uploads/<?= htmlspecialchars($dados['nome_arquivo'])?>" target="_blank" class="botao">Visualizar comprovante</a>
                                <a href="exibirReposicao.php?id_formJustificativa=<?= $id_formJustificativa ?>" target="_blank" class="botao">Visualizar Formulario de Reposição</a>
                            <br>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

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

    <script>
        // Mostra as opções baseado no motivo da falta
        document.getElementById('motivo').addEventListener('change', function() {
            document.querySelectorAll('.motivoDiv').forEach(div => div.hidden = true);
            let divToShow = document.getElementById(this.value);
            if (divToShow) divToShow.hidden = false;
        });

        // Código para gerar PDF
        function printForm() {
            const form = document.getElementById('form');
            const body = document.body;
            const originalContent = body.innerHTML;

            body.innerHTML = '';
            body.appendChild(form);

            window.print();

            body.innerHTML = originalContent;
        }
        
        function onlyOne(checkbox) {
            var checkboxes = document.getElementsByName(checkbox.name)
            checkboxes.forEach((item) => {
                if (item !== checkbox) item.checked = false
            })
        }
    </script>
</body>

</html>