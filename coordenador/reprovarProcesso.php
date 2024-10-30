<?php
require '../conexao.php';
$id_form = $_GET['id_form'];
$tipo_form = $_GET['tipoForm'];
if($tipo_form == 'Reposição'){
    $stmt = $conn -> prepare("UPDATE tb_formsReposicao SET status = ? WHERE id_formReposicao = ?");
    $stmt -> execute(["REPROVADO",$id_form]);
} else {
    $stmt =$conn -> prepare("UPDATE tb_formsJustificativa SET status = ? WHERE id_formJustificativa = ?");
    $stmt -> execute(["REPROVADO", $id_form]);
}
header("Location: PagCoord.php");
exit;

?>
