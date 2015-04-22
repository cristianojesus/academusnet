<?php
	session_start();
	include "buscasessao.php";
	$linha = BuscaSessao($disid);
	$usuid = $linha["usuid"];
	$tipo = $linha["professor"];
	if ($tipo != 1) {
		echo "Sess&atilde;o Expirada. Fa&ccedil;a um novo <a href='login.php'>login</a> ...\n";
		exit;
	}

function AlteraDados( $endid, $disid, $usuid, $curid) {
	include( "./connectdb.php" );
	if ($endid == 0 or $curid == 0) {
		echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Os dados n&atilde;o foram inclu&iacute;dos ...") . "</strong></div>" ;
	} else {
		$sql = "UPDATE disciplina SET endid = '$endid', curid = '$curid' WHERE id = '$disid'";		
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());		
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>";
	}
	mysql_close($dblink);
	return;
}

include( "cabecalho.php" );

include( "menu.inc" );

include 'dadosdis.inc';
	
echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-transfer' aria-hidden='true'></span>&nbsp;" . _("Transfer&ecirc;ncia de ambiente") . "</h3></div>";

echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
echo "<h3 class='panel-title'>" . _("Transfer&ecirc;ncia") . "</h3></div>";
echo "<div class='panel-body'>";

if ($pAction == "UPDATE") {
	if (!empty($curid)) {
		AlteraDados($endid, $disid, $id, $curid);
	}
}

include( "connectdb.php" );

echo "<p><label for='endereco'>" . _("Institui&ccedil;&atilde;o") . "</label><form action='altdisvin.php' method=post><select onchange='submit()' class='form-control' name='endid' >";
$sql = "SELECT e.id, e.nome FROM usuend ue INNER JOIN enderecos e ON ue.endid = e.id WHERE ue.usuid = '$usuid' ORDER BY 2";
$result = mysql_query($sql, $dblink) or die(mysql_error());
if ( mysql_num_rows($result) > 0) {
	echo "<option value=0>" . _("Selecione uma institui&ccedil;&atilde;o") . "</option>";
	while($linha = mysql_fetch_array($result)) {
		echo "<option value=" . $linha["id"];
		if ($endid == $linha["id"]) {
			echo " SELECTED";
		}
		echo ">" . $linha["nome"] . "</option>";
	}
}
echo "</select>";

echo "<br><p><label for='curso'>" . _("Curso") . "</label><select class='form-control' name='curid' >";
$sql = "SELECT DISTINCT c.id, c.nome FROM usucur uc INNER JOIN curso c ON uc.curid = c.id WHERE uc.usuid = '$usuid' AND c.endid = '$endid' ORDER BY 2";
$result = mysql_query($sql, $dblink) or die(mysql_error());
if ( mysql_num_rows($result) > 0) {
	echo "<option value=0>" . _("Selecione um curso") . "</option>";
	while($linha = mysql_fetch_array($result)) {
		echo "<option value=" . $linha["id"];
		if ($curid == $linha["id"]) {
			echo " SELECTED";
		}
		echo ">" . $linha["nome"] . "</option>";
	}
}
echo "</select>";
echo "<input type=hidden name=disid value=$disid>\n";
echo "<input type=hidden name=pAction value=UPDATE>\n";

echo "<p><input type='submit' class='btn btn-default' name='enviarage' value='" . _("Enviar") . "'></p>\n";

echo "</form>";

echo "</div></div>";

mysql_close($dblink);

include 'rodape.inc';

?>
