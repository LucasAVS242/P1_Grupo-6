<?php
require '../conexao.php';
$acao = $_GET['acao'];
$id_formulario = $_GET['id_formulario'];
if ($acao == 'aprovar'){
    $stmt = $conn -> prepare("UPDATE tb_formsJustificativa SET status = ? WHERE id_formJustificativa = ?");
    $stmt -> execute(['APROVADO' ,$id_formulario]);
} else {
    $stmt = $conn -> prepare("UPDATE tb_formsJustificativa SET status = ? WHERE id_formJustificativa = ?" );
    $stmt -> execute(['REPROVADO', $id_formulario]);
}

header("Location: PagCoord.php");
exit;

?>
