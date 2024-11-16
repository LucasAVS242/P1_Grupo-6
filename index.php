<?php
session_start();
function exibirBtnLogin($sessao)
{
  if (isset($sessao['id_usuario'])) {
    echo "<div class='d-lg-flex col-lg-3 justify-content-lg-end'>
            <a href='auth/logout.php'><button class='btn btn-primary' style='background-color: #005C6D; border: none;'>Sair</button></a>
          </div>";
  } else {
    echo "<div class='d-lg-flex col-lg-3 justify-content-lg-end'>
            <a href='auth/login.html'><button class='btn btn-primary'style='background-color: #00C1CF; border: none;'>Login</button></a>
          </div>";
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Justificativa de faltas e plano de reposição</title>
  <link rel="icon" type="image/x-icon" href="images/favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" href="Style/main.css" />
  <!--<script src="Components/footer.js" type="text/javascript" defer></script>-->

  <style>
    img {
      width: 450px;
      display: block;
      margin-top: 4%;
      margin-left: auto;
      margin-right: auto;
      margin-bottom: 4%;
      border-radius: 15px;
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
            <?php if (isset($_SESSION["nome"])): ?>
             <?= $_SESSION['tipo_usuario'] ?>: <?= $_SESSION['nome'] ?>
            <?php endif; ?>
          </span>
          <ul class="navbar-nav col-lg-6 justify-content-lg-center">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="index.php">Início</a>
            </li>
            <?php if (isset($_SESSION["nome"]) && $_SESSION['tipo_usuario'] === 'PROFESSOR'): ?>
              <li class="nav-item">
                <a class="nav-link" href="professor/justificativa.php">Justificativa de Faltas</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="professor/status.php">Status</a>
              </li>
            <?php endif; ?>

            <?php if (isset($_SESSION["nome"]) && $_SESSION['tipo_usuario'] === 'COORDENADOR'): ?>
              <li class="nav-item">
							  <a class="nav-link" href="coordenador/PagCoord.php">Lista de requisições</a>
						  </li>
            <?php endif; ?>
          </ul>
          <?= exibirBtnLogin($_SESSION) ?>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <img src="images/fatec.png" alt="Fatec">


    <div>
      <h2>Sistema de Justificativa de Faltas e Plano de Reposição de Aulas</h2>
      <p>Justifique e planeje reposições de aulas de forma prática e eficiente.</p>

      <p>O nosso sistema foi desenvolvido para facilitar o processo de justificativa de faltas dos professores e
        permitir que eles criem planos de reposição de aulas de maneira organizada.</p>
      <p>Recursos do sistema:</p>
      <ul>
        <li>Registre suas faltas de forma rápida e fácil.</li>
        <li>Anexe documentos comprobatórios.</li>
        <li>Escolha entre diferentes tipos de justificativas.</li>
        <li>Crie planos de reposição de aulas de acordo com a sua disponibilidade.</li>
      </ul>
      <p>Estamos comprometidos em tornar o processo mais transparente e eficiente para todos os envolvidos.</p>
    </div>
  </main>

  <footer>
    
    <div class="container-footer">

      <div class="item item-1"><a href="index.html"><img src="images/logo_fatec_br.png"></a></div>

      <div class="item item-3">
      </div>

      <div class="item item-4">
      </div>

      <div class="item item-5">
        <?php if(isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == "PROFESSOR") :?>
        <h3>Área do Professor</h3>
        
            <a href="status.php"><p>Status</p></a>
            <a href="justificativa.php"><p>Justificativa de Faltas</p></a>

        <?php endif ?>

        <?php if(isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == "COORDENADOR") :?>

            <h3>Área do Coordenador</h3>
        	<a href="PagCoord.php"><p>Lista de Requisições</p></a>

        <?php endif ?>
      </div>

      <div class="item item-6"></div>

    </div>

    </footer>


</body>

</html>