<?php
	require '../conexao.php';
	session_start();
  if(!isset($_SESSION['id_usuario'])){
    header('Location: ../index.php');
  }

	function getFormsJustificativa($conn, $id_usuario){
		$formsJustificativa = $conn -> query("SELECT
			tb_formsJustificativa.id_formJustificativa,
			tb_formsJustificativa.data_envio,
			tb_formsJustificativa.status,
			tb_formsJustificativa.observacoes_coordenador,
			tb_usuarios.id_usuario,
			tb_cursos.sigla
		FROM tb_formsJustificativa
		INNER JOIN tb_usuarios 
			ON tb_usuarios.id_usuario = tb_formsJustificativa.id_usuario
		INNER JOIN tb_cursos
			ON tb_formsJustificativa.id_curso = tb_cursos.id_curso
		WHERE tb_formsJustificativa.id_usuario = $id_usuario
    ORDER BY tb_formsJustificativa.id_formJustificativa DESC
		") -> fetchAll(PDO::FETCH_ASSOC);

		return $formsJustificativa;
	}
	
	function exibirFormulario($formulario){
		return "exibirJustificativa.php?id_formJustificativa=$formulario[id_formJustificativa]";    
	}

	function exibirStatus($formulario){
		if ($formulario['status'] == 'APROVADO'){
			return "<td style='color: #fff; background-color: rgb(3, 139, 3);'><b><i class='fa-solid fa-circle-check'> </i>  $formulario[status]</b></td>";
		} else if ($formulario['status'] == 'REPROVADO'){
			return "<td style='color: #fff; background-color: rgb(177, 7, 7);'><b><i class='fa-solid fa-circle-xmark'> </i>  $formulario[status]</b></td>";
		} else {
			return "<td style='color: #fff; background-color: rgb(209, 198, 47);'><b><i class='fa-solid fa-circle-exclamation'> </i>  $formulario[status]</b></td>";
		}
	}

  function exibirObservacao($formulario) {
    $id_modal = "modalObservacao" . $formulario['id_formJustificativa'];
    return "<div class='modal fade' id='$id_modal' tabindex='-1' aria-labelledby='{$id_modal}Label' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h4 class='modal-title w-100 text-center' id='{$id_modal}Label'>Observações do Coordenador</h4>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
      </div>
      <div class='modal-body text-center'>
        <p>" . htmlspecialchars($formulario['observacoes_coordenador']) . "</p>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fechar</button>
      </div>
    </div>
  </div>
</div>";
}


	$formsJustificativa = getFormsJustificativa($conn, $_SESSION['id_usuario']);

	// Ordenando pela data de envio
	usort($formsJustificativa, function($a, $b) {
    return strtotime($b['data_envio']) - strtotime($a['data_envio']);
});
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Status - Área do Professor</title>
  <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" href="../Style/main.css" />
  <script src="../Components/footer.js" type="text/javascript" defer></script>
  <!--<script src="../Components/footer.js" type="text/javascript" defer></script>-->
  <script src="../Components/modal.js" type="text/javascript" defer></script>
  <script src="https://kit.fontawesome.com/26c14cdb57.js" crossorigin="anonymous"></script>

  <style>
    thead td {
      color: #fff !important;
      background-color: #a90e0b !important;
    }

    textarea {
      resize: none;
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
  </header>

<main>
  <h1><i class="fa-solid fa-user-gear mt-4"></i>&nbsp;Área do Professor</h1><br>
  <h2>Lista de requisições</h2>

  <table style="text-align:center; width:100%;" class="table table-bordered table-hover">
    <thead>
      <tr>
        <td style="width: 12%;">Nº da requisição</td>
        <td>Curso</td>
        <td>Data de envio</td>
        <td>Visualizar Documento</td>
        <td>Status</td>
        <td>Observação</td>
      </tr>
    </thead>
    <tbody>
	  	<?php 
          	$cont = count($formsJustificativa) + 1;
          	foreach($formsJustificativa as $formulario):
            	$cont--;
    	?>
        <tr>
          <td><?= $cont ?></td>
          
          <td><?= $formulario['sigla'] ?></td>
          <td><?= date('d/m/Y', strtotime($formulario['data_envio'])) ?></td>
          <td><a href="<?=exibirFormulario($formulario)?>" target="_blank"><button class="botao" title="Visualizar documentos" onclick=""><i class="fa-solid fa-file-contract"></i></button></a></td>
          <?= exibirStatus($formulario) ?>
          <td><button class="botao" title="Visualizar observação" data-bs-toggle="modal" data-bs-target="#modalObservacao<?= $formulario['id_formJustificativa'] ?>">
          <i class="fa-solid fa-comment"></i></button></td>
        </tr>
        <!-- Modal individual para cada formulário -->
        <?= exibirObservacao($formulario) ?>
		<?php endforeach ?>
    </tbody>
  </table>

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
        
          <a href="status.php"><p>Status</p></a>
          <a href="justificativa.php"><p>Justificativa de Faltas</p></a>
        
      </div>

      <div class="item item-6"></div>

    </div>

  </footer>



</body>

</html>
