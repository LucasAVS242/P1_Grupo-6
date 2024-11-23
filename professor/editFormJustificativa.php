<?php
require '../conexao.php';
date_default_timezone_set('America/Sao_Paulo');
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.html');
}

function getCursos($conn)
{
    $cursos = $conn->query("SELECT id_curso, nome_curso FROM tb_cursos")->fetchAll(PDO::FETCH_ASSOC);
    return $cursos;
}
function getTipoFalta($conn)
{
    $tipo_falta = $conn->query("SELECT tipo, id_tipo_falta FROM tb_tipos_falta")->fetchAll(PDO::FETCH_ASSOC);
    return $tipo_falta;
}

function getMotivoMed($conn)
{
    $faltas_medica = $conn->query("SELECT motivo, id_motivo FROM tb_motivos WHERE id_tipo_falta = 1")->fetchAll(PDO::FETCH_ASSOC);
    return $faltas_medica;
}

function getMotivoLegis($conn)
{
    $faltas_legislacao = $conn->query("SELECT motivo, id_motivo FROM tb_motivos WHERE id_tipo_falta = 2")->fetchAll(PDO::FETCH_ASSOC);
    return $faltas_legislacao;
}

function getDisciplina($conn, $id_usuario)
{
    $disciplinas = $conn->query("SELECT tb_disciplinas.nome_disciplina, tb_disciplinas.qtde_aulas, tb_disciplinas.id_disciplina, tb_usuarios.id_usuario
    FROM tb_disciplinas
    INNER JOIN tb_usuarioDisciplina 
        ON tb_disciplinas.id_disciplina = tb_usuarioDisciplina.id_disciplina
    INNER JOIN tb_usuarios 
        ON tb_usuarios.id_usuario = tb_usuarioDisciplina.id_usuario
    WHERE tb_usuarios.id_usuario = $id_usuario
    ")->fetchAll(PDO::FETCH_ASSOC);
    return $disciplinas;
}

function exibirDisciplinas($disciplinas)
{
    foreach ($disciplinas as $disciplina) {
        echo "<option value='" . $disciplina['id_disciplina'] . "'>" . $disciplina['nome_disciplina'] . '</option>';
    }
}


function fazerUpload($arquivo)
{
    if ($arquivo['error'] === UPLOAD_ERR_OK) {
        $nomeArquivo = uniqid() . "-" . $arquivo['name'];
        if (move_uploaded_file($arquivo['tmp_name'], "../uploads/$nomeArquivo")) {
            return $nomeArquivo;
        }
    }
}

function setFormJustificativaArquivo($conn, $curso, $data_envio, $tipo_falta, $motivo, $nomeArquivo, $id_formulario)
{
    $stmt = $conn->prepare("UPDATE tb_formsJustificativa SET id_curso = ?, data_envio = ?, id_tipo_falta = ?, id_motivo = ?, nome_arquivo = ?, status = 'PENDENTE' WHERE id_formJustificativa = ? ");
    $stmt->execute([$curso, $data_envio, $tipo_falta, $motivo, $nomeArquivo, $id_formulario]);
}

function setFormJustificativa($conn, $curso, $data_envio, $tipo_falta, $motivo, $id_formulario)
{
    $stmt = $conn->prepare("UPDATE tb_formsJustificativa SET id_curso = ?, data_envio = ?, id_tipo_falta = ?, id_motivo = ?, status = 'PENDENTE' WHERE id_formJustificativa = ? ");
    $stmt->execute([$curso, $data_envio, $tipo_falta, $motivo, $id_formulario]);
}

function removerAulasNaoMinistradas($conn, $id_formulario)
{
    $stmt = $conn->prepare("DELETE  FROM tb_aulasNaoMinistradas WHERE id_formJustificativa = ?");
    $stmt->execute([$id_formulario]);
}
function setAulasNaoMinistradas($data, $conn, $idFormulario, $qtde, $disciplina)
{
    for ($i = 0; $i < count($data); $i++) {
        if (!empty($data[$i]) && $data[$i] != '0000-00-00' && !empty($qtde[$i])) {
            $stmt = $conn->prepare("INSERT INTO tb_aulasNaoMinistradas(data, quantidade_aulas, id_disciplina, id_formJustificativa) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data[$i], $qtde[$i], $disciplina[$i], $idFormulario]);
        }
    }
}

// Recebendo os dados para exibir no formulario
function getDados($conn, $idFormulario)
{
    $dados = $conn->query("SELECT 
        tb_formsJustificativa.nome_arquivo,
        tb_cursos.id_curso,
        tb_tipos_falta.id_tipo_falta,
        tb_motivos.id_motivo
    FROM tb_formsJustificativa
    INNER JOIN tb_cursos
        ON tb_formsJustificativa.id_curso = tb_cursos.id_curso
    INNER JOIN tb_tipos_falta
        ON tb_formsJustificativa.id_tipo_falta = tb_tipos_falta.id_tipo_falta
    INNER JOIN tb_motivos
        ON tb_formsJustificativa.id_motivo = tb_motivos.id_motivo
    WHERE tb_formsJustificativa.id_formJustificativa = $idFormulario ")->fetch(PDO::FETCH_ASSOC);
    return $dados;
}

function selectCurso($dados, $curso)
{
    if ($dados['id_curso'] == $curso['id_curso'])
        echo "selected";
}

function selectMotivo($dados, $tipo_falta)
{
    if ($dados['id_tipo_falta'] == $tipo_falta['id_tipo_falta']) {
        echo "selected";
    }
}

function selectMotivoMed($dados, $falta_medica)
{
    if ($dados['id_motivo'] == $falta_medica['id_motivo']) {
        echo "selected";
    }
}

function selectMotivoLeg($dados, $falta_legislacao)
{
    if ($dados['id_motivo'] == $falta_legislacao['id_motivo']) {
        echo "selected";
    }
}


function getAulasNaoMinistradas($conn, $idFormulario)
{
    $stmt = $conn->prepare("
    SELECT 
        tb_aulasNaoMinistradas.data,
        tb_aulasNaoMinistradas.quantidade_aulas,
        tb_disciplinas.id_disciplina
    FROM tb_aulasNaoMinistradas
    INNER JOIN tb_disciplinas
        ON tb_aulasNaoMinistradas.id_disciplina = tb_disciplinas.id_disciplina
    WHERE tb_aulasNaoMinistradas.id_formJustificativa = :idFormulario
    ");
    $stmt->bindParam(':idFormulario', $idFormulario, PDO::PARAM_INT);
    $stmt->execute();
    $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $aulas;
}

$id_formulario = $_GET['id_formulario'];
$dados = getDados($conn, $id_formulario);
$aulas = getAulasNaoMinistradas($conn, $id_formulario);

$tipos_falta = getTipoFalta($conn);
$faltas_legislacao = getMotivoLegis($conn);
$faltas_medica = getMotivoMed($conn);
$cursos = getCursos($conn);
$disciplinas = getDisciplina($conn, $_SESSION['id_usuario']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo = $_POST['tipo_falta'];

    if ($tipo == 1) {
        $motivo = $_POST['selectFaltaMedica'];
    } else {
        $motivo = $_POST['selectFaltaLT'];
    }

    $arquivo = $_FILES['comprovante'];
    $data_envio = date('Y-m-d');
    $curso = $_POST['curso'];
    if (!empty($arquivo['name'])) {
        $nomeArquivo = fazerUpload($arquivo);
        setFormJustificativaArquivo($conn, $curso, $data_envio, $tipo, $motivo,  $nomeArquivo, $id_formulario);
    } else {
        setFormJustificativa($conn, $curso, $data_envio, $tipo, $motivo, $id_formulario);
    }
    removerAulasNaoMinistradas($conn, $id_formulario);
    $data = $_POST['data'];
    $disciplina = $_POST['disciplina'];
    $qtde = $_POST['qtde'];
    setAulasNaoMinistradas($data, $conn, $id_formulario, $qtde, $disciplina);

    header('Location: editFormReposicao.php?id_formJustificativa=' . $id_formulario);
    exit;
}
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

    <script>
        function validarSelecao(event) {
            const select1 = document.getElementById("selectFaltaMedica");
            const select2 = document.getElementById("selectFaltaLT");

            // Verifica se pelo menos um motivo tenha sido selecionado
            if ((!select1.value) && (!select2.value)) {
                alert("Selecione o motivo da falta.");
                return false;
            }

            // Verifica se apenas um motivo tenha selecionado
            /*if ((select1.value && !select2.value) || (!select1.value && select2.value)) {
                return true;
            } else {
                alert("Por favor, selecione apenas um motivo.");
                select1.value = "";
                select2.value = "";
                event.preventDefault();
                return false;
            }*/
        }
    </script>
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

    <main id="justificativa">
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h1 style="text-align: center;">Justificativa de Faltas</h1>
                    <form id="form" method="POST" enctype="multipart/form-data" onsubmit="validarSelecao(event)">
                        <div class="mb-3">

                            <label><strong>Nome:</strong> </label>
                            <?= $_SESSION['nome'] ?>
                            <label><strong>Mátricula:</strong> </label>
                            <?= $_SESSION['matricula'] ?>
                            <br>
                            <p><strong>FUNÇÃO:</strong> Professor de Ensino Superior <strong>REGIME JURÍDICO:</strong> CLT</p>
                            <strong><label>CURSO ENVOLVIDO NA AUSÊNCIA: </label></strong>
                            <select name="curso" class="form-control" required>
                                <option value="" disabled selected>Selecione um curso</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?= $curso['id_curso'] ?>" <?php selectCurso($dados, $curso) ?>><?= $curso['nome_curso'] ?></option>
                                <?php endforeach ?>
                            </select>

                            <span id="mensagemErro" style="color: red; padding-left: 1%;"></span>

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
                                <tbody id="conteinerLinhas">
                                    <?php if (isset($aulas)): ?>
                                        <?php foreach ($aulas as $aula): ?>
                                            <tr>
                                                <td>#</td>
                                                <td><input type="date" class="form-control" name="data[]" value="<?= $aula['data'] ?>" min="2024-06-01" max="<?= date('Y-m-d') ?>" required></td>
                                                <td><input type="number" class="form-control" value="<?= $aula['quantidade_aulas'] ?>" min="1" name='qtde[]' required></td>
                                                <td>
                                                    <select name="disciplina[]" class="form-control" required>
                                                        <option value="" disabled>Selecione uma disciplina</option>
                                                        <?php foreach ($disciplinas as $disciplina): ?>
                                                            <option
                                                                value="<?= $disciplina['id_disciplina'] ?>"
                                                                <?= $aula['id_disciplina'] == $disciplina['id_disciplina'] ? 'selected' : '' ?>>
                                                                <?= $disciplina['nome_disciplina'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php endif ?>

                                </tbody>
                            </table>
                            <button type="button" class="botao" onclick="adicionarLinha()">Adicionar disciplina</button>
                            <button type="button" class="botao" onclick="removerUltimaLinha()">Remover Disciplina</button>
                            <br><br>

                            <p>*Selecionar um item e anexar o comprovante.</p>

                            <h3 class="text-decoration-underline">Motivo da falta</h3>

                            <select style="margin-bottom: 5%;" class="form-select" name="tipo_falta" id="motivo" onchange="exibDiv()" required>
                                <option value="" selected>Selecione um motivo</option>
                                <?php foreach ($tipos_falta as $tipo_falta): ?>
                                    <option value="<?= $tipo_falta['id_tipo_falta'] ?>" <?= selectMotivo($dados, $tipo_falta) ?>><?= $tipo_falta['tipo'] ?></option>
                                <?php endforeach ?>
                            </select>



                            <div id="faltaMedica" class="motivoDiv" hidden>
                                <h4>Licença e falta médica</h4>
                                <select class="form-select" name="selectFaltaMedica" id="selectFaltaMedica">
                                    <option value="" disabled selected>Selecione uma opção</option>
                                    <?php foreach ($faltas_medica as $falta_medica): ?>
                                        <option value="<?= $falta_medica['id_motivo'] ?>" <?= selectMotivoMed($dados, $falta_medica) ?>><?= $falta_medica['motivo'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div id="faltaLT" class="motivoDiv" hidden>
                                <h4>Falta prevista na legislação trabalhista</h4>
                                <select class="form-select" name="selectFaltaLT" id="selectFaltaLT">
                                    <option value="" disabled selected>Selecione uma opção</option>
                                    <?php foreach ($faltas_legislacao as $falta_legislacao): ?>
                                        <option value="<?= $falta_legislacao['id_motivo'] ?>" <?= selectMotivoLeg($dados, $falta_legislacao) ?>><?= $falta_legislacao['motivo'] ?></option>
                                    <?php endforeach ?>

                                </select>
                            </div>


                            <br>


                            <section>
                                <p>Campo para envio de comprovante. Deixe em branco para não alterar</p>
                                <input class="botao" type="file" name="comprovante" id="arquivo" accept=".pdf">
                            </section>

                            <br>


                            <input class="botao" type="submit" value="Avançar">

                        </div>
                    </form>
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
                <h3>Área do Professor</h3>

                <a href="status.php">
                    <p>Status</p>
                </a>
                <a href="justificativa.php">
                    <p>Justificativa de Faltas</p>
                </a>

            </div>

            <div class="item item-6"></div>

        </div>

    </footer>

    <script>
        // Este código parou de funcionar quando comecei a puxar os dados no banco então fiz o código de baixo
        // Mostra as opções baseado no motivo da falta
        /*
        document.getElementById('motivo').addEventListener('change', function() {
            document.querySelectorAll('.motivoDiv').forEach(div => div.hidden = true);
            let divToShow = document.getElementById(this.value);
            if (divToShow) divToShow.hidden = false;
        });
        */
        document.addEventListener('DOMContentLoaded', function() {
            exibDiv();
        });

        function exibDiv() {
            const selectMotivo = document.getElementById('motivo');
            const divFaltaMed = document.getElementById('faltaMedica');
            const divFaltaLeg = document.getElementById('faltaLT');

            if (selectMotivo.value == 1) {
                divFaltaMed.hidden = false;
                divFaltaLeg.hidden = true;
            } else if (selectMotivo.value == 2) {
                divFaltaMed.hidden = true;
                divFaltaLeg.hidden = false
            } else {
                divFaltaMed.hidden = true;
                divFaltaLeg.hidden = true;
            }
        }


        function onlyOne(checkbox) {
            var checkboxes = document.getElementsByName(checkbox.name)
            checkboxes.forEach((item) => {
                if (item !== checkbox) item.checked = false
            })
        }

        document.addEventListener("DOMContentLoaded", () => {
            // Define a numeração inicial ao carregar a página
            atualizarNumeracao();
        })

        function adicionarLinha() {
            // Cria uma nova linha <tr> com o conteúdo desejado
            const novaLinha = document.createElement("tr");
            novaLinha.innerHTML = `
                <td></td>
                <td><input type="date" class="form-control" name="data[]" min="2024-06-01" max="<?= date('Y-m-d') ?>" required></td>
                <td><input type="number" class="form-control" min="1" name="qtde[]" required></td>
                <td>
                    <select name="disciplina[]" class="form-control" required>
                        <option value="" disabled selected>Selecione uma opção</option>
                        <?= exibirDisciplinas($disciplinas) ?>
                    </select>
                </td>
            `;

            // Adiciona a nova linha ao container
            const conteinerLinhas = document.getElementById("conteinerLinhas");
            conteinerLinhas.appendChild(novaLinha);

            // Atualiza os números das linhas
            atualizarNumeracao();
        }

        // Função para remover a última linha
        function removerUltimaLinha() {
            const conteinerLinhas = document.getElementById("conteinerLinhas");
            const linhas = conteinerLinhas.querySelectorAll("tr");

            // Verifica se há mais de uma linha antes de tentar remover
            if (linhas.length > 1) { // Mantém a linha inicial
                conteinerLinhas.removeChild(linhas[linhas.length - 1]); // Remove a última linha
                atualizarNumeracao(); // Atualiza a numeração após a remoção
            } else {
                alert("Pelo menos uma disciplina tem que ser preenchida!"); // Mensagem caso não haja mais linhas
            }
        }

        // Função para atualizar a numeração da primeira coluna
        function atualizarNumeracao() {
            const linhas = document.querySelectorAll("#conteinerLinhas tr");
            linhas.forEach((linha, index) => {
                linha.cells[0].innerText = (index + 1).toString().padStart(2, '0');
            });
        }
    </script>
</body>

</html>