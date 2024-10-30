<?php
require '../conexao.php';
session_start();
//mudando o codigo para ficar mais limpo
function getDisciplina($conn, $id_usuario){
    $disciplinas = $conn -> query("SELECT tb_disciplinas.nome_disciplina, tb_disciplinas.qtde_aulas, tb_disciplinas.id_disciplina, tb_usuarios.id_usuario
    FROM tb_disciplinas
    INNER JOIN tb_usuarioDisciplina 
        ON tb_disciplinas.id_disciplina = tb_usuarioDisciplina.id_disciplina
    INNER JOIN tb_usuarios 
        ON tb_usuarios.id_usuario = tb_usuarioDisciplina.id_usuario
    WHERE tb_usuarios.id_usuario = $id_usuario
    ") -> fetchAll(PDO::FETCH_ASSOC);
    return $disciplinas;
}

function setFormReposicao($conn,$turno,$motivo,$curso,$id_usuario, $data_envio){
    $stmt = $conn -> prepare("INSERT INTO tb_formsReposicao(turno, motivo_reposicao, id_curso, id_usuario, status, data_envio) VALUES (?,?,?,?, 'PENDENTE', ?)");
    $stmt -> execute([$turno, $motivo, $curso, $id_usuario, $data_envio]);
}

function setAulasNaoMinistradas($data,$conn,$idFormulario,$qtde,$disciplina){
    for($i = 0; $i < count($data); $i++){
        if(!empty($data[$i]) && $data[$i] != '0000-00-00' && !empty($qtde[$i])){
            $stmt = $conn -> prepare("INSERT INTO tb_aulasNaoMinistradas(data, quantidade_aulas, id_disciplina, id_formReposicao) VALUES (?, ?, ?, ?)");
            $stmt -> execute([$data[$i], $qtde[$i], $disciplina[$i], $idFormulario]);
        }
    }
}

function setAulasReposicao($conn, $dataRepo, $horaInicio, $horaFinal, $disciplinaRepo ,$idFormulario){
    for($i = 0; $i < count($dataRepo); $i++){
        if(!empty($dataRepo[$i]) && $dataRepo[$i] != '0000-00-00' 
        && !empty($horaInicio[$i]) && $horaInicio[$i] != '00:00:00' 
        && !empty($horaFinal[$i]) && $horaFinal[$i] != '00:00:00'){
            $stmt = $conn -> prepare("INSERT INTO tb_aulasReposicao(data, horario_inicio, horario_final, id_disciplina, id_formReposicao) VALUES (?,?,?,?,?)");
            $stmt -> execute([$dataRepo[$i], $horaInicio[$i], $horaFinal[$i], $disciplinaRepo[$i], $idFormulario]);
        }
    }   
}

function exibirDisciplinas($disciplinas){
    foreach ($disciplinas as $disciplina){
        echo "<option value='". $disciplina['id_disciplina']. "'>" . $disciplina['nome_disciplina'] . '</option>';                                    
    }
}

$disciplinas = getDisciplina($conn, $_SESSION['id_usuario']);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //Primeira parte do formulario
    $curso = $_POST['curso'];
    $turno = $_POST['turno'];
    $motivo = $_POST['reposicao'];
    $data_envio = date('Y-m-d');
    setFormReposicao($conn,$turno, $motivo, $curso,$_SESSION['id_usuario'], $data_envio);

    //Aulas não ministradas
    $idFormulario = $conn -> lastInsertId();
    $data = $_POST['data'];
    $disciplina = $_POST['disciplina'];
    $qtde = $_POST['qtde'];
    setAulasNaoMinistradas($data, $conn, $idFormulario,$qtde,$disciplina);
    
    //Aulas de reposição
    $dataRepo = $_POST['dataRepo'];
    $horaInicio = $_POST['horaInicio'];
    $horaFinal = $_POST['horaFinal'];
    $disciplinaRepo = $_POST['disciplinaRepo'];
    setAulasReposicao($conn, $dataRepo, $horaInicio, $horaFinal, $disciplinaRepo, $idFormulario);
}



?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de reposição de aulas - Área do Professor</title>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="../Style/main.css">
    <script src="../Components/footer.js" type="text/javascript" defer></script>
    <script src="../Components/tabela.js" type="text/javascript" defer></script>
    <script src="../Components/validacao.js" type="text/javascript" defer></script>
</head>

<body>
<header>
    <nav>
        <ul>
            <li><a href="../index.php">Início</a></li>
            <li><a href="justificativa.php">Justificativa de Faltas</a></li>
            <li><a href="reposicao.php">Plano de Reposição</a></li>
            <li><a href="status.php">Status</a></li>
            <li style="float: right;"><a href="../auth/logout.php">Sair</a></li>
            <li style="float: right;"><a style="text-decoration-line: underline;" href="status.php">Área do Professor</a></li>
            <li style="float: right;" ><a href="../coordenador/PagCoord.php">Área do Coordenador</a></li>
        </ul>
    </nav>
</header>
    <main id="reposicao" style="padding-left: 12%; padding-right: 12%; padding-top: 3%;">


        <form name="repo" id="repo" method="post" action="reposicao.php">

            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;" colspan="3">
                            <h3 style="text-decoration: underline; text-align: center;">PLANO - REPOSIÇÃO/SUBSTITUIÇÃO
                                DE AULAS</h3>
                        </th>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding-left: 1%;" colspan="3">NOME DO PROFESSOR: <?= $_SESSION['nome']?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <p>Curso:</p><label for="curso" name="curso"></label>
                            <input type="checkbox" name="curso" id="curso" value="1" onclick="onlyOne(this)">DSM
                            <input type="checkbox" name="curso" id="curso" value="3" onclick="onlyOne(this)">GPI
                            <input type="checkbox" name="curso" id="curso" value="2" onclick="onlyOne(this)">GTI
                            <input type="checkbox" name="curso" id="curso" value="4" onclick="onlyOne(this)">GE
                        </th>
                        <th>
                            <p>Turno:</p><label for="turno" name="turno"></label>
                            <input type="checkbox" name="turno" id="turno" value="Manhã" onclick="onlyOne(this)">Manhã
                            <input type="checkbox" name="turno" id="turno" value="Tarde" onclick="onlyOne(this)">Tarde
                            <input type="checkbox" name="turno" id="turno" value="Noite" onclick="onlyOne(this)">Noite
                        </th>
                        <th>
                            <p>Reposição em virtude de:</p> <label for="reposicao" name="reposicao"></label>
                            <input type="checkbox" name="reposicao" id="reposicao" value="Claro Docente" onclick="onlyOne(this)">Claro Docente
                            <input type="checkbox" name="reposicao" id="reposicao" value="Falta" onclick="onlyOne(this)">Falta
                            <input type="checkbox" name="reposicao" id="reposicao" value="Substituição" onclick="onlyOne(this)">Substituição
                        </th>
                    </tr>

                </tbody>
            </table>

            <table style="margin-top: 10px; text-align:center; width:100%;">

                <thead>
                    <tr>
                        <th style="background-color: rgba(224, 224, 224, 0.514);" colspan="4"><label for="aulaministrada" name="aulaministrada">Dados da(s) aulas não
                                ministradas</label></th>
                    </tr>
                    <tr>
                        <th>Ordem</th>
                        <th>Data das Aulas Não Ministradas</th>
                        <th>Nº de Aulas</th>
                        <th>Nome das Disciplinas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01</td>
                        <td><input type="date" name="data[]"></td>
                        <td><input type="number" name="qtde[]"></td>
                        <td>
                            <select name='disciplina[]'>
                                <?= exibirDisciplinas($disciplinas) ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>02</td>
                        <td><input type="date" name="data[]"></td>
                        <td><input type="number" name="qtde[]"></td>
                        <td>
                            <select name='disciplina[]'>
                                <?= exibirDisciplinas($disciplinas) ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>03</td>
                        <td><input type="date" name="data[]"></td>
                        <td><input type="number" name="qtde[]"></td>
                        <td>
                            <select name='disciplina[]'>
                                <?= exibirDisciplinas($disciplinas) ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>04</td>
                        <td><input type="date" name="data[]"></td>
                        <td><input type="number" name="qtde[]"></td>
                        <td>
                            <select name='disciplina[]'>
                                <?= exibirDisciplinas($disciplinas) ?>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table style="margin-top: 10px; text-align:center; width:100%;">

                <thead>
                    <tr>
                        <th style="background-color: rgba(224, 224, 224, 0.514);" colspan="4">Dados da(s) aulas de
                                reposição</label></th>
                    </tr>
                    <tr>
                        <th>Ordem</th>
                        <th>Data da Reposição</th>
                        <th style=" width: 40%;">Horário de Início e Término</th>
                        <th>Disciplina(s)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01</td>
                        <td><input name="dataRepo[]" type="date"></td>
                        <td><input name="horaInicio[]" type="time"> as <input name="horaFinal[]" type="time"></td>
                        <td>
                            <select name="disciplinaRepo[]">
                                <?= exibirDisciplinas($disciplinas) ?>
                            </select>
                        </td>

                    </tr>
                    <tr>
                        <td>02</td>
                        <td><input type="date" name="dataRepo[]"></td>
                        <td><input type="time" name="horaInicio[]"> as <input type="time" name="horaFinal[]"></td>
                        <td>
                            <select name="disciplinaRepo[]">
                                <?= exibirDisciplinas($disciplinas) ?>
                            </select>
                        </td>

                    </tr>
                    <tr>
                        <td>03</td>
                        <td><input type="date" name="dataRepo[]"></td>
                        <td><input type="time" name="horaInicio[]"> as <input type="time" name="horaFinal[]"></td>
                        <td>
                            <select name="disciplinaRepo[]">
                                <?= exibirDisciplinas($disciplinas) ?>
                            </select>
                        </td>

                    </tr>
                    <tr>
                        <td>04</td>
                        <td><input type="date" name="dataRepo[]"></td>
                        <td><input type="time" name="horaInicio[]"> as <input type="time" name="horaFinal[]"></td>
                        <td>
                            <select name="disciplinaRepo[]">
                                <?= exibirDisciplinas($disciplinas) ?>
                            </select>
                        </td>

                    </tr>
                </tbody>
            </table>

            <input style="margin: 1%;" class="botao" type="submit" value="Enviar">

            <button id="gerarPDF" class="botao" onclick="printForm()">Gerar PDF do documento</button>

        </form>
        
    </main>

    <footer-component></footer-component>

    <script>
        // Código para preencher com a data corrente
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        const today = new Date();

        const formattedDate = formatDate(today);

        document.getElementById('data').value = formattedDate;
        document.getElementById('dataRecebida').value = formattedDate;

        // Código para gerar PDF
        function printForm() {
            const form = document.getElementById('repo');
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