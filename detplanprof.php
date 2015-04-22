<?php
session_start();
include( "buscasessao.php" );
$linha = BuscaSessao($disid);
$tipo = $linha["professor"];
$id = $linha["usuid"];

if ($tipo == 0) {
	if (empty($ra)) {
		include( "./connectdb.php" );
	  	$sql = "SELECT ra FROM usuend ue INNER JOIN disciplina d ON d.endid = ue.endid WHERE ue.usuid = '$id' AND d.id = '$disid'";
		$query =  mysql_query ($sql) or die(mysql_error());
		if (mysql_num_rows($query) > 0) {
			$linha = mysql_fetch_array($query);
			$ra = $linha["ra"];
		}
		mysql_close($aDBLink);
 		}
}

function ListaDisciplina($disid, $id, $tipo, $menu, $ra, $visitante) {

	include 'dadosdis.inc';
	
	echo "</div>";

	include( "./connectdb.php" );
	
	echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Objetivos") . "</h3></div>"; 
	echo "<div class='panel-body'><p class='lead'>" . nl2br($linhad["objetivo"]) . "</p></div></div>";

	echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Painel de monitoramento") . "</div><div class='panel-body'>";
	
	if ( !empty($linhad["cargah"])) {
	 	echo "<p class='lead'><strong>" . _("Carga hor&aacute;ria") . ":</strong>&nbsp;&nbsp;" . $linhad["cargah"] . "</p>";
	}
	
	if (!empty($linhad["faltas"])) {
		
	 	echo "<p class='lead'><strong>" . _("N&uacute;mero de faltas previstas permitidas") . ":</strong>&nbsp;&nbsp;" . $linhad["faltas"] . "</p>";
		
	 	if (!$tipo and !$visitante) {
	 		
	 		$sql = "SELECT SUM(f.faltas) faltas FROM aluno a INNER JOIN disalu da ON (da.aluid = a.id) INNER JOIN plano p ON (p.disid = da.disid) 
			LEFT JOIN frequencia f ON (f.aluid = a.id AND f.planid = p.id) WHERE da.disid = '$disid' AND a.id = '$ra' GROUP BY a.id";
	 		
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);

			if ($linha["faltas"] > 0) {
				if ($linha["faltas"] == 1) {
					echo "<p class='lead'>" . _("At&eacute; o momento h&aacute;");
					echo "<a href='#' id='textLink' onClick='abrirPag(" . '"frequencia.php", "disid=' . $disid . '"' . ")'>" . 
					_("uma") . " </a>" . _("falta registrada.") . "</p>";
				} else {
					echo "<p class='lead'>" . _("At&eacute; o momento h&aacute;");
					echo "<a href='#' id='textLink' onClick='abrirPag(" . '"frequencia.php", "disid=' . $disid . '"' . ")'>" . 
					$linha["faltas"] . " </a>" . _("faltas registradas.") . "</p>";
	 			}
			} else {
	 			echo "<p class='lead'>" . _("At&eacute; o momento n&atilde;o h&aacute; faltas registradas.") . "</p>";
	 		}
	 		
	 	}
	 	
	}
	
	if (!$visitante) {
		
		$sql = "SELECT UPPER(u.nome) as nome, u.email, u.id, u.id as perfil FROM usuario u INNER JOIN disciplina ON u.id = disciplina.usuid WHERE u.id = '$id' 
		AND disciplina.id = '$disid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		if (mysql_num_rows($result) > 0) {
			$email = trim($linha["nome"]) . " <" . trim($linha["email"]) . ">";

		}

		$sql = "SELECT sum(lido) as lido FROM mensagens WHERE destinatario = '$email' AND usuid = '$id' AND disid = $disid";

		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);

		if ($linha["lido"] == 0) {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; mensagens n&atilde;o lidas na sua caixa postal.") . "</p>";
		} else {
			if ($linha["lido"] == 1) {
		 		echo "<p class='lead'>" . _("H&aacute; uma") . "<a href='#' id='textLink' onClick='abrirPag(" . '"mensagem.php", "disid=' . $disid . '"' . ")'>" . _("mensagem") . "</a>" . 
		 		_("n&atilde;o lida na sua caixa postal.") . "</p>";
			} else {
				echo "<p class='lead'>" . _("H&aacute; ") . "<a href='#' id='textLink' onClick='abrirPag(" . '"mensagem.php", "disid=' . $disid . '"' . ")'>" . 
				$linha["lido"] . "</a> " . _("mensagens n&atilde;o lidas na sua caixa postal.") . "</p>";
		 	}
		 }
		 
	}
	
	echo "</div></div>";
	 
	 $sql = "SELECT texto, data, detalhe FROM agenda WHERE disid = '$disid' ORDER BY 2";
	 $result = mysql_query( $sql, $dblink ) or die(mysql_error());
	 if ( mysql_num_rows($result) > 0) {
	 	echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
	 	echo "<h3 class='panel-title'>" . _("Agenda") . "</div><div class='panel-body'>";
	 	echo "<table class='table'><thread><tr>\n";
		echo "<th>" . _("Data") . "</th><th>" . _("Compromisso") . "</th></thread><tbody>\n";
		while ($linha = mysql_fetch_array($result)) {
			ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $linha["data"], $regs);
			$data = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
			echo "<tr><td>" . $data . "</td>\n";
			echo "<td>" . wordwrap($linha["texto"],60,"<br />\n", true) . "<br>" . nl2br($linha["detalhe"]) . "</td></tr>\n";
		}
		echo "</tbody></table>";
	 }
	 
	 echo "</div></div>";
	 
	 $sql = "SELECT titulo, texto, id FROM aviso WHERE disid = '$disid' ORDER BY datav DESC";
	 $result = mysql_query( $sql, $dblink ) or die(mysql_error());
	 if ( mysql_num_rows($result) > 0) {	 	
	 	echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
	 	echo "<h3 class='panel-title'>" . _("Feed de Not&iacute;cias") . "</div><div class='panel-body'>";
	 	while ($linha = mysql_fetch_array($result)) {
	 		echo "<h3>" . $linha["titulo"] . "</h3>";
 			echo "<p class='lead'>" . $linha["texto"] . "</p>";
	 	}
	 	echo "</div></div>";
	 }
	 
	 echo "</div></div>";
	 
	mysql_close($dblink);
	return;
}

function ListaPerfil($perfil, $disid, $tipo) {
		echo "<button type='button' class='btn btn btn-default'><a href='#' 
		onClick='abrirPag(" . '"detplanprof.php", "disid=' . $disid . '"' . ")'>" . _("Voltar") . "</a></button>\n";
		include 'perfil.inc';		
}

include( "cabecalho.php" );
include "menu.inc";
ListaDisciplina($disid, $id, $tipo, $menu, $ra, $visitante);
include 'rodape.inc';

?>
