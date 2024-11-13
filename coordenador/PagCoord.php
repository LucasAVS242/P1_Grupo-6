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
	return "../professor/exibirJustificativa.php?id_formJustificativa=$formulario[id_formJustificativa]";
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
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous"></script>
	<link rel="stylesheet" type="text/css" href="../Style/main.css" />
	<script src="../Components/footer.js" type="text/javascript" defer></script>
	<script src="../Components/modal.js" type="text/javascript" defer></script>
	<script src="https://kit.fontawesome.com/26c14cdb57.js" crossorigin="anonymous"></script>

	<style>
		thead td {
			color: #fff !important;
			background-color: #a90e0b !important;
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
						<li class="nav-item">
							<a class="nav-link" href="PagCoord.php">Lista de requisições</a>
						</li>
					</ul>
					<div class='d-lg-flex col-lg-3 justify-content-lg-end'>
						<a href='../auth/logout.php'><button class='btn btn-primary' style='background-color: #005C6D; border: none;'>Sair</button></a>
					</div>
				</div>
			</div>
		</nav>
	</header>

	<main>
		<h1 class="mt-4"><i class="fa-solid fa-user-gear"></i>&nbsp;Área do Coordenador</h1><br>
		<h2>Lista de professores aguardando aprovação</h2>

		<table style="text-align:center; width:100%;" class="table table-bordered table-hover">
			<thead>
				<tr>
					<td>Cód. Requisição</td>
					<td>Nome</td>
					<td>Matrícula</td>
					<td>Curso</td>
					<td>Data de envio</td>
					<td>Documentos</td>
					<td>Aprovação</td>
					<!-- <td>Observação</td> -->
				</tr>
			</thead>

			<tbody>
				<?php
				$cont = count($formularios) + 1;
				foreach ($formularios as $formulario):
					$cont--;
					$modalAprovarId = "modalAprovar" . $formulario['id_formJustificativa'];
					$modalRejeitarId = "modalRejeitar" . $formulario['id_formJustificativa'];
					$modalObservacaoAId = "staticBackdropObservacaoA" . $formulario['id_formJustificativa'];
					$modalObservacaoRId = "staticBackdropObservacaoR" . $formulario['id_formJustificativa'];
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
						<td>
							<button class="botao" style="background-color: green" title="Aprovar" data-bs-toggle="modal" data-bs-target="#<?= $modalAprovarId ?>">
								<i class="fa-solid fa-check"></i>
							</button>
							<button class="botao" style="background-color: red;" title="Rejeitar" data-bs-toggle="modal" data-bs-target="#<?= $modalRejeitarId ?>">
								<i class="fa-solid fa-x"></i>
							</button>
						</td>
					</tr>

					<!-- Modal de confirmação de aprovação -->
					<div class="modal fade" id="<?= $modalAprovarId ?>" tabindex="-1" aria-labelledby="modalAprovarLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title w-100 text-center" id="modalAprovarLabel">Você tem certeza que deseja <strong>aprovar</strong> essa requisição?</h1>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body text-center">
									<button class="btn btn-primary" data-bs-target="#<?= $modalObservacaoAId ?>" data-bs-toggle="modal">Sim</button>
									<button class="btn btn-danger" data-bs-dismiss="modal">Não</button>
								</div>
							</div>
						</div>
					</div>

					<!-- Modal de confirmação de rejeição -->
					<div class="modal fade" id="<?= $modalRejeitarId ?>" tabindex="-1" aria-labelledby="modalRejeitarLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title w-100 text-center" id="modalRejeitarLabel">Você tem certeza que deseja <strong>rejeitar</strong> essa requisição?</h1>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body text-center">
									<button class="btn btn-primary" data-bs-target="#<?= $modalObservacaoRId ?>" data-bs-toggle="modal">Sim</button>
									<button class="btn btn-danger" data-bs-dismiss="modal">Não</button>
								</div>
							</div>
						</div>
					</div>

					<!-- Modal de observação após aprovação -->
					<div class="modal fade" id="<?= $modalObservacaoAId ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropObservacaoALabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title w-100 text-center" id="staticBackdropObservacaoALabel">Adicionar observação</h4>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body text-center">
									<textarea name="observacaoA" id="observacaoA<?= $formulario['id_formJustificativa'] ?>" cols="40" rows="10"></textarea>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
									<button class="btn btn-primary" onclick="aprovarSim(<?= $formulario['id_formJustificativa'] ?>)">Sim</button>
								</div>
							</div>
						</div>
					</div>

					<!-- Modal de observação após rejeição -->
					<div class="modal fade" id="<?= $modalObservacaoRId ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropObservacaoRLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title w-100 text-center" id="staticBackdropObservacaoRLabel">Adicionar observação</h4>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body text-center">
									<textarea name="observacaoR" id="observacaoR<?= $formulario['id_formJustificativa'] ?>" cols="40" rows="10"></textarea>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
									<button class="btn btn-primary" onclick="rejeitarSim(<?= $formulario['id_formJustificativa'] ?>)">Sim</button>
								</div>
							</div>
						</div>
					</div>

				<?php endforeach ?>
			</tbody>

		</table>

	</main>

	<footer-component></footer-component>

	<script>
		function aprovarSim(idFormulario) {
			const observacao = document.getElementById("observacaoA" + idFormulario).value;
			window.location.href = "manipularFormulario.php?acao=aprovar&observacao=" + encodeURIComponent(observacao) + "&id_formulario=" + idFormulario;
		}

		function rejeitarSim(idFormulario) {
			const observacao = document.getElementById("observacaoR" + idFormulario).value;
			window.location.href = "manipularFormulario.php?acao=reprovar&observacao=" + encodeURIComponent(observacao) + "&id_formulario=" + idFormulario;
		}
	</script>

</body>

</html>