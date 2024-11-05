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
            $stmt = $conn -> prepare("INSERT INTO tb_aulasnaoministradas(data, quantidade_aulas, id_disciplina, id_formReposicao) VALUES (?, ?, ?, ?)");
            $stmt -> execute([$data[$i], $qtde[$i], $disciplina[$i], $idFormulario]);
        }
    }
}

function setAulasReposicao($conn, $dataRepo, $horaInicio, $horaFinal, $disciplinaRepo ,$idFormulario){
    for($i = 0; $i < count($dataRepo); $i++){
        if(!empty($dataRepo[$i]) && $dataRepo[$i] != '0000-00-00' 
        && !empty($horaInicio[$i]) && $horaInicio[$i] != '00:00:00' 
        && !empty($horaFinal[$i]) && $horaFinal[$i] != '00:00:00'){
            $stmt = $conn -> prepare("INSERT INTO tb_aulasreposicao(data, horario_inicio, horario_final, id_disciplina, id_formReposicao) VALUES (?,?,?,?,?)");
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
    
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../Style/main.css">
    <script src="../Components/footer.js" type="text/javascript" defer></script>
    <script src="../Components/validacao.js" type="text/javascript" defer></script>



</head>
<body>
<body>
    <nav>
        <ul>
            <li><a href="../index.html">Início</a></li>
            <li><a href="../justificativa.html">Justificativa de Faltas</a></li>
            <li><a href="../reposicao.html">Plano de Reposição</a></li>
            <li><a href="../status.html">Status</a></li>
            <li style="float: right;"><a style="text-decoration-line: underline;" href="status.html">Área do
                    Professor</a></li>
            <li style="float: right;"><a href="PagCoord.html">Área do Coordenador</a></li>
        </ul>
    </nav>
     <br>
    <h2 class="text-center text-decoration-underline">PLANO - REPOSIÇÃO/SUBSTITUIÇÃO DE AULAS</h2>
    <br>

    <form method=post>

        <div class="container-repo1">
        <div class="repo item-11">
            <legend>Curso:</legend>
            <select name="curso" id="curso">
                <option disabled selected value>Selecione o Curso</option>
                <option value="GPI">GPI</option>
                <option value="GTI">GTI</option>
                <option value="GE">GE</option>  
              </select>
        </div>
        <br>
        <div class="repo item-12">
            <legend>Turno:</legend>  
            <select name="turno" id="turno">
                   <option disabled selected value>Selecione o Turno</option>
                   <option value="manha">Manhã</option>
                   <option value="tarde">Tarde</option>
                   <option value="noite">Noite</option>
                 </select>
        </div>
        <br>
        <div class="repo item-13">
            <legend>Motivo de Reposição:</legend>
            <select name="Motivo" id="Motivo">
                <option disabled selected value>Selecione o Motivo</option>
                <option value="GPI">Claro Docente</option>
                <option value="GTI">Falta</option>
                <option value="GE">Substituição</option>
              </select>
        </div>
        <br>
        </div>

        <div class="container-aulaN">
        <h4>Dados da(s) aulas não ministradas</h4>
        <table>
            <thead>
                <tr>
                    <th>Ordem</th>
                    <th>Data</th>
                    <th>Nº de Aulas</th>
                    <th>Disciplina</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>01</td>
                    <td><input type="date" name="data[]" class="form-control" required></td>
                    <td ><input type="number" name="qtde[]" class="form-control" min="1" required></td>
                    
                    <td class="form-control"><select name='disciplina[]'>
                    <option disabled selected value>Selecione a disciplina</option>
                                <?= exibirDisciplinas($disciplinas) ?>
                            </select>
                    </td>        
                </tr>
              
            </tbody>
        </table>
        </div>
        <br>
        <div class="container-aulaN">
        <h4>Dados da(s) aulas de reposição</h4>
        <table>
            <thead>
                <tr>
                    <th>Ordem</th>
                    <th>Data</th>
                    <th>Horário de Inicio</th>
                    <th>Horário de Término</th>

                    <th>Disciplina</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>01</td>
                    <td><input type="date" name="dataRepo[]" class="form-control" required></td>
                    <td ><input type="time" name="horaInicio[]" class="form-control" required></td>
                    <td><input type="time" name="horaFinal[]" class="form-control" required></td>
                    <td class="form-control"> <select name="disciplinaRepo[]">
                    <option disabled selected value>Selecione a disciplina</option>
                                <?= exibirDisciplinas($disciplinas) ?>
                            </select></td>
                </tr>
               
            </tbody>
        </table>
        </div>
        <div class="repo-btn">
        <button type="submit">Enviar</button>
        </div>


    </form>

        





    <footer-component></footer-component>
</body>

    
