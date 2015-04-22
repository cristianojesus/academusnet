<?php
	session_start();
	include("buscasessao.php" );
	$linha = BuscaSessao(null);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];

	include("cabecalho.php" );
	
	include "menup.inc";
	
	echo "<div class='jumbotron'>\n";
	
	include "connectdb.php";
	$qUsuario = mysql_query( "SELECT nome FROM usuario WHERE id = '$id'" ) or die(mysql_error());
	$aUsuario = mysql_fetch_array($qUsuario);
	mysql_close($dblink);
	
	echo "<p><h2>" . $aUsuario["nome"] . "</a></h2></p>\n";
	echo "<p><a href='#' onClick='abrirPag(" . '"perfil.php", "usuid=' . $id . '")' . "'>" . _("Perfil") . "</a>\n";
	echo "&nbsp;<a href='#' onClick='abrirPag(" . '"endereco.php", "pAction=SELECT"' . ")'>" .
	_("Institui&ccedil;&otilde;es") . "</a>\n";
	echo "&nbsp;<a href='matricula.php'>" . _("Matricular-se") . "</a></p>\n";
	echo "<p>" . _("Somente ser&atilde;o listados aqui as disciplinas em que voc&ecirc; foi incluido como estudante pelos professores") . "&nbsp;" .
	_("ou cursos livres os quais seu pedido de matr&iacute;cula foi aceito.") . "</p>\n";
	
	echo "</div>";
	
	echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Registro") . "</h3></div>";
	echo "<div class='panel-body'>";

	if ($pAction == "UPDATE") {		
		$sql = "UPDATE usuend SET ra = '$ra' WHERE endid = '$endid' AND usuid = '$id'";
		include( "./connectdb.php" );
		$res = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		mysql_close($dblink);
				
	} else {

		$sql = "SELECT DISTINCT e.id, e.nome, e.endereco, e.cidade, e.cep, e.estado, e.pais, e.telefone, e.email, e.url, e.id, e.sigla, ue.usuid, ue.ra 
		FROM enderecos e INNER JOIN usuend ue ON (e.id = ue.endid) WHERE e.id = '$endid' AND ue.usuid = '$usuid' ORDER BY 1";

		include( "./connectdb.php" );
		
		$res = mysql_query( $sql, $dblink ) or die(mysql_error());
		
		if ( mysql_num_rows($res) > 0) {
			$linha = mysql_fetch_array($res);
			echo "<br><br>" . $linha["nome"] . "<br>";
			if (!empty($linha["sigla"])) {
				echo $linha["sigla"] . "<br>";
			}
			if (!empty($linha["endereco"])) {
				echo $linha["endereco"] . "<br>";
			}
			if (!empty($linha["cidade"])) {
				echo $linha["cidade"] . " " . $linha["cep"] . " " . $linha["estado"] . "<br>";
			}
			if (!empty($linha["telefone"])) {
				echo $linha["telefone"] . "<br>";
			}
			if (!empty($linha["email"])) {
				echo $linha["email"] . "<br>";
			}
			if (!empty($linha["url"])) {
				if (substr(trim($linha["url"]),0,4) <> "http") {
					$url = "http://" . $linha["url"];
				} else {
					$url = $linha["url"];
				}
				echo "<a href='" . $url . "' target='window'>" . $url . "</a><br><br>";
			}
			
			echo "<form action='alterara.php' method='POST'>\n";
			echo "<input type='text' name='pAction' value='UPDATE' hidden>";
			echo "<p><label for='ra'>" . _("RA") . "</label>\n";
			echo "<br><input type='text' name='ra' value='" . $linha["ra"] . "' size=30 maxlength=30 class='form-control' autofocus required>\n";
			echo "<input type='submit' name='enviara' class='btn btn-default' value='Enviar'></form>";
			
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Dados apresentando inconsist&ecirc;ncia ...") . "</strong></div><br>";
		}
		mysql_close($dblink);
	}
	
	echo "</div></div>";
	
	include 'rodape.inc';
?>