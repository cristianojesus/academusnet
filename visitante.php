<?php
	session_start();
	include "connectdb.php";
	$qSessao = mysql_query("DELETE FROM sessao WHERE sessao = '" . session_id() . "'") or die(mysql_error());
	mysql_close($dblink);
	session_unset();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	if ($tipo == 0) {
		$pAction = "";
	}
	
	include( "cabecalho.php" );

	echo "<div class='col-md-12'>";
	
	include 'dadosdis.inc';
		
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-star-empty' aria-hidden='true'></span>&nbsp;" . _("Disciplinas e cursos abertos a visitantes") . "</h3></div>";
	
	include 'connectdb.php';
	
	$sql = "SELECT d.id as disid, d.nome as nomed, u.nome as nomeu, e.nome as nomee, e.id as endid, c.nome as nomec 
	FROM disciplina d INNER JOIN usuario u ON (u.id = d.usuid) INNER JOIN enderecos e ON (e.id = d.endid) 
	INNER JOIN curso c ON (c.id = d.curid) WHERE d.visitante = '1' AND d.datai <= CURDATE() AND d.dataf >= CURDATE() ORDER BY 2";
	$result = mysql_query($sql, $dblink ) or die(mysql_error());

	if (mysql_num_rows($result) > 0) {
		
		echo "<table class='table'><thread><tr><th>" . _("Disciplinas") . "</th></tr></thread><tbody>\n";
		
		while ($linha = mysql_fetch_array($result)) {
			echo "<tr><td><a href='#' id='textLink' onClick='abrirPag(" . '"detplanprof.php", "endid=' . $linha["endid"] . "&disid=" . 
			$linha["disid"] . '"' . ")'>" . $linha["nomed"] . "</a><br>" . $linha["nomee"] . "<br>" . _("Curso de") . "&nbsp;" . 
			$linha["nomec"] . "<br>Prof.(a) " . $linha["nomeu"] . "</td></tr>";
		}
		
		echo "</tbdoy></table>";
		
	} else {
		echo "<p class='lead'>" . _("N&atilde;o h&aacute; cursos ou disciplinas abertos a visitantes no momento") .  "...</p>\n";
	}
	
	mysql_close($dblink);
	
	include 'rodape.inc';
	
?>