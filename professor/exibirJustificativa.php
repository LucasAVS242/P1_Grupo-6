<?php
require '../conexao.php';
session_start();
$id_formJustificativa = $_GET['id_formJustificativa'];
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
    <script src="../Components/footer.js" type="text/javascript" defer></script>
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
    <nav>
        <ul>
            <li><a href="index.php">Início</a></li>
            <li><a href="justificativa.php">Justificativa de Faltas</a></li>
            <li><a href="status.php">Status</a></li>
            <li style="float: right;"><a href="logout.php">Sair</a></li>
            <li style="float: right;"><a style="text-decoration-line: underline;" href="status.php">Área do
                    Professor</a></li>
            <li style="float: right;"><a href="PagCoord.php">Área do Coordenador</a></li>
        </ul>
    </nav>

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

    <footer-component></footer-component>

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