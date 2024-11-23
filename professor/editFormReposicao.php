<?php
require '../conexao.php';
date_default_timezone_set('America/Sao_Paulo');
session_start();
//mudando o codigo para ficar mais limpo
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
        tb_disciplinas.nome_disciplina,
        tb_disciplinas.id_disciplina
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
        tb_disciplinas.nome_disciplina,
        tb_disciplinas.id_disciplina
    FROM tb_aulasReposicao
    INNER JOIN tb_disciplinas 
        ON tb_aulasReposicao.id_disciplina = tb_disciplinas.id_disciplina
    INNER JOIN tb_formsReposicao 
        ON tb_aulasReposicao.id_formReposicao = tb_formsReposicao.id_formReposicao
    WHERE tb_formsReposicao.id_formReposicao = $idFormulario
    ") -> fetchAll(PDO::FETCH_ASSOC);
    return $aulasReposicao;
}

function exibirDisciplinas($disciplinas){
    foreach ($disciplinas as $disciplina){
        echo "<option value='". $disciplina['id_disciplina']. "'>" . $disciplina['nome_disciplina'] . '</option>';                                    
    }
}

function setFormReposicao($conn,$turno,$motivo, $id_formReposicao){
    $stmt = $conn -> prepare("UPDATE tb_formsReposicao SET turno = ?, motivo_reposicao = ? WHERE id_formReposicao = ? ");
    $stmt -> execute([$turno, $motivo, $id_formReposicao]);
}

function removerAulasRepo($conn, $id_formulario){
    $stmt = $conn -> prepare("DELETE  FROM tb_aulasReposicao WHERE id_formReposicao = ?");
    $stmt -> execute([$id_formulario]);
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



$id_formJustificativa = $_GET['id_formJustificativa'];
$dados = getDados($conn, $id_formJustificativa);
$id_formReposicao = $dados['id_formReposicao'];
$aulasNaoMinistradas = getAulasNaoMinistradas($conn, $id_formJustificativa);
$aulasRepo = getAulasReposicao($conn, $id_formReposicao);
$disciplinas = $aulasNaoMinistradas;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //Primeira parte do formulario
    $turno = $_POST['turno'];
    $motivo = $_POST['Motivo'];
    setFormReposicao($conn,$turno, $motivo, $id_formReposicao);

    removerAulasRepo($conn, $id_formReposicao);
    $dataRepo = $_POST['dataRepo'];
    $horaInicio = $_POST['horaInicio'];
    $horaFinal = $_POST['horaFinal'];
    $disciplinaRepo = $_POST['disciplinaRepo'];
    setAulasReposicao($conn, $dataRepo, $horaInicio, $horaFinal, $disciplinaRepo, $id_formReposicao);

    header('Location: status.php');
    exit;
}



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
    
    <br>
    <h2 class="text-center text-decoration-underline mt-4">PLANO - REPOSIÇÃO/SUBSTITUIÇÃO DE AULAS</h2>
    <br>

    <form method=post>

        <div class="container-repo1">
        <div class="repo item-11">
            <legend>Curso:</legend>
            &nbsp;<?= $dados['nome_curso'] ?>

        </div>
        <br>
        <div class="repo item-12">
            <legend>Turno:</legend>  
            <select name="turno" id="turno" required>
                   <option disabled selected value>Selecione o Turno</option>
                   <option value="Manhã" <?= $dados['turno'] == "Manhã" ? "selected" : "" ?>>Manhã</option>
                   <option value="Tarde" <?= $dados['turno'] == "Tarde" ? "selected" : "" ?>>Tarde</option>
                   <option value="Noite" <?= $dados['turno'] == "Noite" ? "selected" : "" ?> >Noite</option>
                 </select>
        </div>
        <br>
        <div class="repo item-13">
            <legend>Motivo de Reposição:</legend>
            <select name="Motivo" id="Motivo" required>
                <option disabled selected value>Selecione o Motivo</option>
                <option value="Claro Docente" <?= $dados['motivo_reposicao'] == "Claro Docente" ? "selected" : "" ?>>Claro Docente</option>
                <option value="Falta" <?= $dados['motivo_reposicao'] == "Falta" ? "selected" : "" ?>>Falta</option>
                <option value="Substituição" <?= $dados['motivo_reposicao'] == "Substituição" ? "selected" : "" ?>>Substituição</option>
              </select>
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
        <?php
                $cont = 0;
                foreach($aulasNaoMinistradas as $aula):
                    $cont++;
            ?>
            <tr>
                <td><?= $cont?></td>
                <td><input type="date" name="data" class="form-control" value="<?= $aula['data'] ?>" readonly></td>
                <td><input type="number" name="qtde" class="form-control" min="1" value="<?= $aula['quantidade_aulas'] ?>" readonly></td>
                <td >
                   <input type="text" class="form-control" value="<?= $aula['nome_disciplina'] ?>" readonly>
                </td>
               
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
                <th>Horário de Início</th>
                <th>Horário de Término</th>
                <th>Disciplina</th>
                <th>Ação</th> <!-- Coluna para o botão de remover -->
            </tr>
        </thead>
        <tbody id="conteinerLinhas">
        <?php 
    $primeiraAula = true; // Variável para controlar a primeira aula
    foreach($aulasRepo as $aula): ?>
    <tr>
        <td><?= $primeiraAula ? 1 : '' ?></td> <!-- Só exibe '1' na primeira linha -->
        <td><input type="date" name="dataRepo[]" class="form-control" value="<?= $aula['data'] ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" max="2024-12-24" required></td>
        <td><input type="time" name="horaInicio[]" class="form-control" value="<?= $aula['horario_inicio'] ?>" required></td>
        <td><input type="time" name="horaFinal[]" class="form-control" value="<?= $aula['horario_final'] ?>" required></td>
        <td>
            <select name="disciplinaRepo[]" class="form-control">
                <option disabled selected value>Selecione a disciplina</option>
                <?php foreach ($disciplinas as $disciplina): ?>
                <option value="<?= $disciplina['id_disciplina'] ?>"
                     <?= $aula['id_disciplina'] == $disciplina['id_disciplina'] ? 'selected' : '' ?>>
                    <?= $disciplina['nome_disciplina'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <?php if ($primeiraAula): ?>
                <button type="button" class="btn-add" onclick="adicionarLinha()">+</button>
            <?php else: ?>
                <button type="button" class="btn-add" onclick="removerUltimaLinha()">-</button>
            <?php endif; ?>
        </td>
    </tr>
    <?php 
    $primeiraAula = false; // Depois da primeira aula, o botão não será exibido
    endforeach ?>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
            // Define a numeração inicial ao carregar a página
            atualizarNumeracao();
        })

        function adicionarLinha() {
            // Cria uma nova linha <tr> com o conteúdo desejado
            const novaLinha = document.createElement("tr");
            novaLinha.innerHTML = `
                <td></td>
                <td><input type="date" name="dataRepo[]" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" max="2024-12-24" required></td>
                <td><input type="time" name="horaInicio[]" class="form-control" required></td>
                <td><input type="time" name="horaFinal[]" class="form-control" required></td>
                <td>
                    <select name="disciplinaRepo[]" class="form-control">
                        <option disabled selected value>Selecione a disciplina</option>
                        <?= exibirDisciplinas($disciplinas) ?>
                    </select>
                </td>
                <td><button type="button" class="btn-add" onclick="removerUltimaLinha('')">-</button></td>
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



        </div>
        <div class="repo-btn">
        <button type="submit">Enviar</button>
        </div>


    </form>

        





    <footer>
    
    <div class="container-footer">

      <div class="item item-1"><a href="../index.html"><img src="../images/logo_fatec_br.png"></a></div>

      <div class="item item-3">
      </div>

      <div class="item item-4">
      </div>

      <div class="item item-5">
        <h3>Área do Professor</h3>
        
          <a href="status.php"><p>Status</p></a>
          <a href="justificativa.php"><p>Justificativa de Faltas</p></a>
        
      </div>

      <div class="item item-6"></div>

    </div>
</body>

</html>
