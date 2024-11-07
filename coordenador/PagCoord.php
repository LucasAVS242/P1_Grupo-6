<?php
require '../conexao.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
	header('Location: ../index.php');
}
if (isset($_SESSION['id_usuario']) and $_SESSION['tipo_usuario'] != 'COORDENADOR') {
	header('Location: ../index.php');
	exit;
}


function getFormsJustificativa($conn)
{
	$formsJustificativa = $conn->query("SELECT
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
	ORDER BY tb_formsJustificativa.id_formJustiFicativa DESC
	")->fetchAll(PDO::FETCH_ASSOC);
	return $formsJustificativa;
}

function exibirFormulario($formulario)
{
	return "exibirJustificativa.php?id_formJustificativa=$formulario[id_formJustificativa]";
}

function aprovarFormulario($formulario)
{
	return "aprovarProcesso.php?tipoForm=Justificativa&id_form=$formulario[id_formJustificativa]";
}

function reprovarFormulario($formulario)
{
	return "reprovarProcesso.php?tipoForm=Justificativa&id_form=$formulario[id_formJustificativa]";
}

$formularios = getFormsJustificativa($conn);



usort($formularios, function ($a, $b) {
	return strtotime($b['data_envio']) - strtotime($a['data_envio']);
});

?>

<!DOCTYPE html>
<html lang="pt-br">

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
	<header>
		<nav>
			<ul>
				<li><a href="../index.php">Início</a></li>
				<li style="float: right;"><a href="../auth/logout.php">Sair</a></li>
				<li style="float: right;"><a href="../professor/status.php">Área do Professor</a></li>
				<li style="float: right;"><a style="text-decoration-line: underline;" href="PagCoord.php">Área do
						Coordenador</a></li>

			</ul>
		</nav>
	</header>

	<main>
		<h1><i class="fa-solid fa-user-gear"></i>&nbsp;Área do Coordenador</h1>
		<h2>Lista de professores aguardando aprovação</h2>

		<table style="text-align:center; width:100%;">
			<thead>
				<tr>
					<td>Cód. Requisição</td>
					<td>Nome</td>
					<td>Matrícula</td>
					<td>Curso</td>
					<td>Data de envio</td>
					<td>Documentos</td>
					<td>Aprovação</td>
					<td>Observação</td>
				</tr>
			</thead>

			<tbody>
				<?php
				$cont = count($formularios) + 1;
				foreach ($formularios as $formulario):
					$cont--;
				?>
					<tr>
						<td><?= $cont ?></td>
						<td><?= $formulario['nome'] ?></td>
						<td><?= $formulario['matricula'] ?></td>
						<td><?= $formulario['sigla'] ?></td>
						<td><?= date('d/m/Y', strtotime($formulario['data_envio'])) ?></td>
						<td><a href="<?= exibirFormulario($formulario) ?>" target="_blank"><button class="botao"
									title="Visualizar documentos"><i class="fa-solid fa-file-contract"></i></button></a>
						</td>
						<td><button class="botao" style="background-color: green" title="Aprovar" onclick="openModal('aprovar')"><i class="fa-solid fa-check"></i></button>
							<button class="botao" style="background-color: red;" title="Rejeitar" onclick="openModal('rejeitar')"><i class="fa-solid fa-x"></i></button>
						</td>
						<td><button class="botao" title="Adicionar observação" onclick="openModal('observacao')"><i
									class="fa-solid fa-comment"></i></button></td>
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

		<!-- Modal para visualizar uma observação -->
		<div id="modalObservacao" class="modal">
			<div class="modal-content">
				<span class="close" onclick="closeModal()">&times;</span>
				<p>Observações do Coordenador</p>
				<textarea name="observacao" id="observacao" cols="40" rows="10" readonly></textarea>
				<div class="button-container">
					<button onclick="closeModal()">Fechar</button>
				</div>
			</div>
		</div>

	</main>

	<footer-component></footer-component>

	<script>
		function aprovarSim() {
			window.location.href = "manipularFormulario.php?acao=aprovar&id_formulario=<?= $formulario['id_formJustificativa'] ?>";
			closeModal();
		}

		function rejeitarSim() {
			window.location.href = "manipularFormulario.php?acao=reprovar&id_formulario=<?= $formulario['id_formJustificativa'] ?>"
			closeModal();
		}
	</script>

</body>

</html>
