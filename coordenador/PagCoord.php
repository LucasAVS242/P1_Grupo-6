<?php
	require '../conexao.php';
	session_start();

	if($_SESSION['tipo_usuario'] != 'COORDENADOR'){
		header('Location: ../index.php');
		exit;
	}
	function getFormsJustificativa($conn, $id_usuario){
		$formsJustificativa = $conn -> query("SELECT
			'Justificativa' AS tipo_formulario,
			tb_formsJustificativa.id_formJustificativa,
			tb_formsJustificativa.data_envio,
			tb_formsJustificativa.status,
			tb_formsJustificativa.observacoes_coordenador,
			tb_usuarios.id_usuario,
			tb_usuarios.nome,
			tb_usuarios.matricula,
			tb_cursos.sigla
		FROM tb_formsJustificativa
		INNER JOIN tb_usuarios 
			ON tb_usuarios.id_usuario = tb_formsJustificativa.id_usuario
		INNER JOIN tb_cursos
			ON tb_formsJustificativa.id_curso = tb_cursos.id_curso
		WHERE tb_formsJustificativa.status = 'PENDENTE'
		") -> fetchAll(PDO::FETCH_ASSOC);

		return $formsJustificativa;
	}
	function getFormsReposicao($conn, $id_usuario){
		$formsReposicao = $conn -> query("SELECT 
		'Reposição' AS tipo_formulario,
			tb_formsReposicao.id_formReposicao,
			tb_formsReposicao.data_envio, 
			tb_formsReposicao.status,
			tb_formsReposicao.observacoes_coordenador, 
			tb_cursos.sigla,
			tb_usuarios.id_usuario,
			tb_usuarios.nome,
			tb_usuarios.matricula
		FROM tb_formsReposicao
		INNER JOIN tb_usuarios 
			ON tb_usuarios.id_usuario = tb_formsReposicao.id_usuario
		INNER JOIN tb_cursos 
			ON tb_formsReposicao.id_curso = tb_cursos.id_curso
		WHERE tb_formsReposicao.status = 'PENDENTE'
		") -> fetchAll(PDO::FETCH_ASSOC);

		return $formsReposicao;
	}

	function mapearFormularios($form, $tipoID){
		return [
			'tipo' => $form['tipo_formulario'],
			$tipoID => $form[$tipoID],
			'data_envio' => $form['data_envio'],
			'observacoes' => $form['observacoes_coordenador'],
			'sigla' => $form['sigla'],
			'id_usuario' => $form['id_usuario'],
			'nome' => $form['nome'],
			'matricula' => $form['matricula']
		];
	}
	function criarLista($formsReposicao, $formsJustificativa,){
		$formularios = [];
		foreach($formsReposicao as $form){
			$formularios[] = mapearFormularios($form, 'id_formReposicao'); 
		}
		foreach($formsJustificativa as $form){
			$formularios[] = mapearFormularios($form, 'id_formJustificativa');
		}

		return $formularios;
	}

	function exibirFormulario($formulario){
		if ($formulario['tipo'] == 'Justificativa'){
			return "../exibirForm/exibirJustificativa.php?id_formJustificativa=$formulario[id_formJustificativa]";    
		} else {
			return "../exibirForm/exibirReposicao.php?id_formReposicao=$formulario[id_formReposicao]";
		}
	}

	function aprovarFormulario($formulario){
		if($formulario['tipo'] == 'Justificativa'){
			return "aprovarProcesso.php?tipoForm=Justificativa&id_form=$formulario[id_formJustificativa]";
		} else {
			return "aprovarProcesso.php?tipoForm=Reposição&id_form=$formulario[id_formReposicao]";
		}
	}
	function reprovarFormulario($formulario){
		if($formulario['tipo'] == 'Justificativa'){
			return "reprovarProcesso.php?tipoForm=Justificativa&id_form=$formulario[id_formJustificativa]";
		} else {
			return "reprovarProcesso.php?tipoForm=Reposição&id_form=$formulario[id_formReposicao]";
		}
	}

	$formsReposicao = getFormsReposicao($conn, $_SESSION['id_usuario']);
	$formsJustificativa = getFormsJustificativa($conn, $_SESSION['id_usuario']);

	$formularios = criarLista($formsReposicao, $formsJustificativa);

	// Ordenando pela data de envio
	usort($formularios, function($a, $b) {
	return strtotime($a['data_envio']) - strtotime($b['data_envio']);
	});
?>

<!DOCTYPE html>
<html lang="pt-br">

<header>
    <nav>
        <ul>
            <li><a href="../index.php">Início</a></li>
            <li><a href="../professor/justificativa.php">Justificativa de Faltas</a></li>
            <li><a href="../professor/reposicao.php">Plano de Reposição</a></li>
            <li><a href="../professor/status.php">Status</a></li>
            <li style="float: right;"><a href="../auth/logout.php">Sair</a></li>
            <li style="float: right;"><a style="text-decoration-line: underline;" href="../professor/status.php">Área do Professor</a></li>
            <li style="float: right;" ><a href="../coordenador/PagCoord.php">Área do Coordenador</a></li>
        </ul>
    </nav>
</header>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Área do Coordenador</title>
  <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
  <link rel="stylesheet" type="text/css" href="../Style/main.css" />
  <script src="../Components/footer.js" type="text/javascript" defer></script>
  <script src="../Components/modal.js" type="text/javascript" defer></script>
  <script src="https://kit.fontawesome.com/26c14cdb57.js" crossorigin="anonymous"></script>

  <style>
	thead {
	  color: #fff;
	  background-color: #a90e0b;
	}
  </style>
</head>

<body>

  <main>
	<h1><i class="fa-solid fa-user-gear"></i>&nbsp;Área do Coordenador</h1>
	<h2>Lista de professores aguardando aprovação</h2>

	<table style="text-align:center; width:100%;">
	  <thead>
		<tr>
		  <td>Cód. Requisição</td>
		  <td>Nome</td>
		  <td>Matrícula</td>
		  <td>Assunto</td>
		  <td>Curso</td>
		  <td>Data de envio</td>
		  <td>Documentos</td>
		  <td>Aprovação</td>
		  <td>Observação</td>
		</tr>
	  </thead>
	
	  <tbody>
		<?php
		  $cont = 0;
		  foreach($formularios as $formulario):
			$cont++
		?>
		<tr>
		  <td><?= $cont ?></td>
		  <td><?= $formulario['nome'] ?></td>
		  <td><?= $formulario['matricula'] ?></td>
		  <td><?= $formulario['tipo'] ?></td>
		  <td><?= $formulario['sigla'] ?></td>
		  <td><?= date('d/m/Y',strtotime($formulario['data_envio'])) ?></td>
   
		  <td><a href="<?= exibirFormulario($formulario) ?>" target="_blank"><button class="botao" title="Visualizar formulário"><i class="fa-solid fa-file-contract"></i></button></a></td>
		  
		  <td>
			<a href="<?= aprovarFormulario($formulario) ?>" onclick="return confirm('Deseja aprovar o formulario?')"><button class="botao" style="background-color: green;" title="Aprovar" ><i class="fa-solid fa-check"></i></button></a> 
			<a href="<?= reprovarFormulario($formulario) ?>" onclick="return confirm('Deseja rejeitar o formulario?')"><button class="botao" style="background-color: red;" title="Rejeitar" ><i class="fa-solid fa-x"></i></button></a>
		  </td>
			  

		  <td><button class="botao" title="Adicionar observação" onclick="openModal('observacao')"><i class="fa-solid fa-comment"></i></button></td>
		</tr>
		<?php endforeach ?>


	  </tbody>
	</table>

	<!-- Modal para confirmar aprovação da requisição -->
	<div id="modalAprovar" class="modal">
	  <div class="modal-content">
		<span class="close" onclick="closeModal()">&times;</span>
		<p>Você tem certeza que deseja aprovar essa requisição?</p>
		<div class="button-container">
		  <button onclick="aprovarSim()">Sim</button>
		  <button onclick="closeModal()">Não</button>
		</div>
	  </div>
	</div>

	<!-- Modal para confirmar rejeição da requisição -->
	<div id="modalRejeitar" class="modal">
	  <div class="modal-content">
		<span class="close" onclick="closeModal()">&times;</span>
		<p>Você tem certeza que deseja rejeitar essa requisição?</p>
		<div class="button-container">
		  <button onclick="rejeitarSim()">Sim</button>
		  <button onclick="closeModal()">Não</button>
		</div>
	  </div>
	</div>

	<!-- Modal para adicionar uma observação -->
	<div id="modalObservacao" class="modal">
	  <div class="modal-content">
		<span class="close" onclick="closeModal()">&times;</span>
		<p>Adicione sua observação abaixo:</p>
		<textarea name="observacao" id="observacao" cols="40" rows="10"></textarea>
		<div class="button-container">
		  <a href=""><button onclick="adicionarObservacao()">Adicionar</button></a>
		  <button onclick="closeModal()">Cancelar</button>
		</div>
	  </div>
	</div>

  </main>

  <footer-component></footer-component>
  
</body>

</html>
