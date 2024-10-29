<?php
session_start();
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Justificativa de faltas e plano de reposição</title>
  <link rel="icon" type="image/x-icon" href="images/favicon.ico">
  <link rel="stylesheet" type="text/css" href="Style/main.css" />
  <script src="Components/footer.js" type="text/javascript" defer></script>

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
    <nav>
      <ul>
        <li><a href="index.php">Início</a></li>
        <li style="float: right;"><a href="auth/logout.php">Sair</a></li>

        <li style="float: right;"><a href="status.html">Área do Professor</a></li>
        <li style="float: right;"><a href="PagCoord.html">Área do Coordenador</a></li>
        

      </ul>
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

  <footer-component></footer-component>


</body>

</html>