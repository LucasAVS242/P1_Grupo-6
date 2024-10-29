<?php
include '../conexao.php';
session_start();

function fazerUpload($arquivo)
{
    if ($arquivo['error'] === UPLOAD_ERR_OK) {
        $nomeArquivo = uniqid() . "-" . $arquivo['name'];
        if (move_uploaded_file($arquivo['tmp_name'], "../uploads/$nomeArquivo")) {
            return $nomeArquivo;
        }
    }
}

function setFormJustificativa($conn, $curso, $data_envio, $motivo, $faltaInicio, $faltaFim, $nomeArquivo)
{
    $stmt = $conn->prepare("INSERT INTO tb_formsJustificativa (id_usuario, id_curso, data_envio, motivo, data_inicio, data_final, nome_arquivo, status, observacoes_coordenador) VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDENTE', ' ')");
    $stmt->execute([$_SESSION['id_usuario'], $curso, $data_envio, $motivo, $faltaInicio, $faltaFim, $nomeArquivo]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectFaltaMedica = $_POST['selectFaltaMedica'];
    $selectFaltaLT = $_POST['selectFaltaLT'];

    if (!$selectFaltaMedica) {
        $motivo = $selectFaltaLT;
    } else {
        $motivo = $selectFaltaMedica;
    }

    $arquivo = $_FILES['arquivo'];
    $data_envio = date('Y-m-d');
    $curso = $_POST['cursos'];
    $faltaInicio = $_POST['faltaInicio'];
    $faltaFim = $_POST['faltaFim'];
    $nomeArquivo = fazerUpload($arquivo);

    setFormJustificativa($conn, $curso, $data_envio, $motivo, $faltaInicio, $faltaFim, $nomeArquivo);
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
            if ((select1.value && !select2.value) || (!select1.value && select2.value)) {
                return true;
            } else {
                alert("Por favor, selecione apenas um motivo.");
                select1.value = "";
                select2.value = "";
                event.preventDefault();
                return false;
            }
        }
    </script>
</head>

<body>
    <nav>
        <ul>
            <li><a href="../index.php">Início</a></li>
            <li><a href="justificativa.php">Justificativa de Faltas</a></li>
            <li><a href="reposicao.php">Plano de Reposição</a></li>
            <li><a href="status.php">Status</a></li>
            <li style="float: right;"><a style="text-decoration-line: underline;" href="status.html">Área do
                    Professor</a></li>
            <li style="float: right;"><a href="PagCoord.html">Área do Coordenador</a></li>
        </ul>
    </nav>

    <main id="justificativa">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h1 style="text-align: center;">Justificativa de Faltas</h1>
                    <form id="form" method="post" enctype="multipart/form-data" onsubmit="validarSelecao(event)">
                        <div class="mb-3">

                            <p></p><label><strong>Nome:</strong> </label>
                            <?= $_SESSION['nome'] ?>
                            <label><strong>Mátricula:</strong> </label>
                            <?= $_SESSION['matricula'] ?></p>
                            
                            <p><strong>FUNÇÃO:</strong> Professor de Ensino Superior <strong>REGIME JURÍDICO:</strong> CLT</p>
                            <strong><label>CURSO(S) ENVOLVIDO(S) NA AUSÊNCIA: </label>
                                <input class="form-check-input" type="checkbox" name="cursos" id="cst-dsm" value="1" onclick="onlyOne(this)"> CST-DSM
                                <input class=" form-check-input" type="checkbox" name="cursos" id="cst-ge" value="2" onclick="onlyOne(this)"> CST-GE
                                <input class=" form-check-input" type="checkbox" name="cursos" id="cst-gpi" value="3" onclick="onlyOne(this)"> CST-GPI
                                <input class=" form-check-input" type="checkbox" name="cursos" id="cst-gti" value="4" onclick="onlyOne(this)"> CST-GTI
                                <!-- <input class=" form-check-input" type="checkbox" name="cursos" id="hae" value="HAE"> HAE -->
                            </strong>
                                <span id="mensagemErro" style="color: red; padding-left: 1%;"></span>

                            <br>

                            <!-- <p><label for="diaFalta">Falta referente ao dia:</label>
                                <input type="date" name="diaFalta" id="diaFalta"><br> ou<br>
                                Período de
                                <input type="number" name="qtdDias" id="qtdDias">
                                dias:
                                <input type="date" name="faltaInicio" id="faltaInicio">
                                até
                                <input type="date" name="faltaFim" id="faltaFim">
                            </p> -->

                            <br>

                            <p>
                                <label for="faltaInicio">Período:</label>
                                <input type="date" name="faltaInicio" id="faltaInicio">
                                <label for="faltaFim">até</label>
                                <input type="date" name="faltaFim" id="faltaFim">
                            </p>

                            <p>*Selecionar um item e anexar o comprovante.</p>

                            <h3 class="text-decoration-underline">Motivo da falta</h3>

                            <select style="margin-bottom: 5%;" class="form-select" name="motivo" id="motivo" required>
                                <option value="" selected>Selecione um motivo</option>
                                <option value="faltaMedica">Licença e falta médica</option>
                                <!-- <option value="faltaInjustificada">Falta injustificada</option>
                                <option value="faltaJustificada">Falta justificada</option> -->
                                <option value="faltaLT">Falta prevista na legisação trabalhista</option>
                            </select>

                            <div id="faltaMedica" class="motivoDiv" hidden>
                                <h4>Licença e falta médica</h4>
                                <select class="form-select" name="selectFaltaMedica" id="selectFaltaMedica">
                                    <option value="" disabled selected>Selecione uma opção</option>
                                    <option value="Falta Médica">Falta Médica (Atestado médico de 1 dia)</option>
                                    <option value="Comparecimento ao Médico">Comparecimento ao Médico</option>
                                    <option value="Licença-Saúde">Licença-Saúde (Atestado médico igual ou superior a 2 dias)</option>
                                    <option value="Licença-Maternidade">Licença-Maternidade (Atestado médico até 15 dias)</option>
                                </select>
                            </div>

                            <!-- <div id="faltaInjustificada" class="motivoDiv" hidden>
                                <h4>Falta injustificada</h4>
                                <select class="form-select" name="faltaInjustificada" id="faltaInjustificada">
                                    <option value="" disabled selected>Selecione uma opção</option>
                                    <option value="Falta">Falta</option>
                                    <option value="Atraso ou Saída Antecipada">Atraso ou Saída Antecipada</option>
                                </select>
                            </div>

                            <div id="faltaJustificada" class="motivoDiv" hidden>
                                <h4>Falta justificada (Se deferido, não implicam em desconto do Descanso Semanal Remunerado – DSR)</h4>
                                <select class="form-select" name="faltaJustificada" id="faltaJustificada">
                                    <option value="" disabled selected>Selecione uma opção</option>
                                    <option value="Falta">Falta por motivo de</option>
                                    <option value="Atraso ou Saída Antecipada">Atraso ou Saída Antecipada</option>
                                </select>

                                <br>

                                <label>Motivo</label>
                                <input class="form-control" type="text" name="motivoPersonalizado" id="motivoPersonalizado">
                            </div> -->

                            <div id="faltaLT" class="motivoDiv" hidden>
                                <h4>Falta prevista na legislação trabalhista</h4>
                                <select class="form-select" name="selectFaltaLT" id="selectFaltaLT">
                                    <option value="" disabled selected>Selecione uma opção</option>
                                    <option value="Falecimento de cônjuge, pai, mãe, filho">Falecimento de cônjuge, pai, mãe, filho. (9 dias
                                        consecutivos)</option>
                                    <option value="Falecimento ascendente">Falecimento ascendente (exceto pai e mãe), descendente
                                        (exceto filho), irmão ou pessoa declarada na CTPS, que viva sob sua dependência econômica. (2
                                        dias consecutivos)</option>
                                    <option value="Casamento">Casamento. (9 dias consecutivos)</option>
                                    <option value="Nascimento de filho">Nascimento de filho, no decorrer da primeira semana. (5
                                        dias)</option>
                                    <option value="Acompanhar esposa ou companheira no período de gravidez">Acompanhar esposa ou companheira no período de gravidez,
                                        em consultas médicas e exames complementares. (Até 2 dias)</option>
                                    <option value="Acompanhar filho de até 6 anos em consulta médica">Acompanhar filho de até 6 anos em consulta médica. (1 dia
                                        por ano)</option>
                                    <option value="Doação voluntária de sangue">Doação voluntária de sangue. (1 dia em cada 12 meses de
                                        trabalho)</option>
                                    <option value="Alistamento como eleitor">Alistamento como eleitor. (Até 2 dias consecutivos ou
                                        não)</option>
                                    <option value="Convocação para depoimento judicial">Convocação para depoimento judicial</option>
                                    <option value="Comparecimento como jurado no Tribunal do Júri">Comparecimento como jurado no Tribunal do Júri</option>
                                    <option value="Convocação para serviço eleitoral">Convocação para serviço eleitoral</option>
                                    <option value="Dispensa dos dias devido à nomeação para compor as mesas receptoras ou juntas eleitorais nas eleições ou requisitado para auxiliar seus trabalhos">Dispensa dos dias devido à nomeação para compor as mesas
                                        receptoras ou juntas eleitorais nas eleições ou requisitado para auxiliar seus trabalhos (Lei nº
                                        9.504/97)</option>
                                    <option value="Realização de Prova de Vestibular para ingresso em estabelecimento de ensino superior">Realização de Prova de Vestibular para ingresso em
                                        estabelecimento de ensino superior</option>
                                    <option value="Comparecimento necessário como parte na Justiça do Trabalho">Comparecimento necessário como parte na Justiça do
                                        Trabalho (Enunciado TST nº 155). (Horas necessárias)</option>
                                    <option value="Atrasos decorrentes de acidente">Atrasos decorrentes de acidente</option>
                                </select>
                            </div>


                            <br>


                            <section>
                                <p>Campo para envio de comprovante</p>
                                <input class="botao" type="file" name="arquivo" id="comprovante" accept=".pdf" required>
                            </section>

                            <br>


                            <button id="gerarPDF" class="botao" type="button" onclick="printForm()">Gerar PDF do documento</button>
                            <input class="botao" type="submit" value="Enviar">

                        </div>
                    </form>
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

        // Código para selecionar apenas um checkbox
        function onlyOne(checkbox) {
            var checkboxes = document.getElementsByName(checkbox.name)
            checkboxes.forEach((item) => {
                if (item !== checkbox) item.checked = false
            })
        }
    </script>
</body>

</html>