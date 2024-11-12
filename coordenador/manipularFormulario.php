<?php
require '../conexao.php';
$acao = $_GET['acao'];
$observacoes_coordenador = $_GET['observacao'];
$id_formulario = $_GET['id_formulario'];
if ($acao == 'aprovar'){
    $stmt = $conn -> prepare("UPDATE tb_formsJustificativa SET status = ?, observacoes_coordenador = ? WHERE id_formJustificativa = ?");
    $stmt -> execute(['APROVADO', $observacoes_coordenador, $id_formulario]);
} else {
    $stmt = $conn -> prepare("UPDATE tb_formsJustificativa SET status = ?, observacoes_coordenador = ? WHERE id_formJustificativa = ?" );
    $stmt -> execute(['REPROVADO', $observacoes_coordenador, $id_formulario]);
}

header("Location: PagCoord.php");
exit;

?>
