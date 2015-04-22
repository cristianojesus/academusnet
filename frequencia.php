<?php
	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	if ($tipo == 0) {
		$pAction = "";
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

	function ListaDados($disid) {

		include( "./connectdb.php" );
		
		echo "<a href='#' id='textLink' onClick='abrirPag(" . '"frequencia.php", "pAction=REPORT&report=0' . '"' . ")'><button type='button' class='btn btn btn-default'>" .
		_("Relat&oacute;rio de faltas") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Controle de frequ&ecirc;ncia") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		$sql = "SELECT p.id, p.texto, DATE_FORMAT(p.data, '%d-%m-%Y') dataf, COUNT(f.id) faltas FROM plano p LEFT JOIN frequencia f ON (f.planid = p.id) 
		WHERE p.disid = '$disid' GROUP BY 1 ORDER BY data, 2";
		
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {
			
			echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" . _("Registra faltas") . "<br>\n";
			
			echo  "<br><br><table class='table'><thread><tr>\n" ;
			echo "<th>" . _("Data") . "</th><th></th><th>" . _("Aula") . "</th></tr></thread><tbody>\n";

			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td width='5%'nowrap>" . $linha["dataf"] . "</td>\n";
				echo "<td width='5%' nowrap><a href='#' onClick='abrirPag(" . '"frequencia.php", "pAction=UPDATE&planid=' . $linha["id"] . '"' . ")'>
				<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>\n<td>";
				if ($linha["faltas"]) {
					echo "<a href='#' onClick='abrirPag(" . '"frequencia.php", "pAction=REPORT&report=1&disid=' . $disid . '&planid=' . 
					$linha["id"] . '"' . ")'>" . $linha["texto"] . "</a></td>\n";
				} else {
					echo $linha["texto"] . "</td>\n";
				}
				echo "</tr>\n";
			}
			
			echo "</tbody></table>\n";
			
		} else {
			
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; aulas registradas") . "...</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return 0;
			
		}
		
		mysql_close($dblink);
		
		echo "</div></div>";
		
		return;
	}
	
	function Relatorio($disid, $faltas, $report, $planid, $estid, $tipo){
		
		include 'connectdb.php';
		
		if ($tipo == 1) {
			echo "<a href='#' onClick='abrirPag(" . '"frequencia.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Controle de frequ&ecirc;ncia") . "</button></A>\n";
		}
				
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Relat&oacute;rio de Faltas") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		if (!empty($planid)) {
			$sql = "SELECT texto, DATE_FORMAT(data, '%d-%m-%Y') as data FROM plano WHERE id = '$planid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			echo "<strong>" . $linha["texto"] . " (" . $linha["data"] . ")</strong><br><br>";
		}
		
		if (!empty($estid)) {
			$sql = "SELECT nome FROM aluno WHERE id = '$estid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			echo "<strong>" . $linha["nome"] . "</strong><br><br>";
		}
		
		if ($report == 0) {
			$sql = "SELECT a.id, a.nome, SUM(f.faltas) faltas, da.ativo FROM aluno a INNER JOIN disalu da ON (da.aluid = a.id) INNER JOIN plano p ON (p.disid = da.disid) 
			LEFT JOIN frequencia f ON (f.aluid = a.id AND f.planid = p.id) WHERE da.disid = '$disid' GROUP BY 1 ORDER BY 2";
		} elseif ($report == 1) {
			$sql = "SELECT f.aluid, a.nome FROM frequencia f INNER JOIN aluno a ON (a.id = f.aluid) WHERE f.planid = '$planid' AND f.faltas > 0";
		} elseif ($report == 2) {
			$sql = "SELECT p.texto, DATE_FORMAT(p.data, '%d-%m-%Y') dataf, f.faltas FROM frequencia f INNER JOIN plano p ON (p.id = f.planid) 
			WHERE aluid = '$estid' AND p.disid = '$disid' ORDER BY p.data";
		}

		$result = mysql_query( $sql, $dblink ) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {

			echo "<table class='table'><thread><tr>\n" ;
			
			if ($report == 0) {
				echo "<th>" . _("Registro") . "</th><th>" . _("Nome") . "</th><th align='center'>" . _("Faltas") . "</th>\n";
			} elseif ($report == 1) {
				echo "<th>" . _("Registro") . "</th><th>" . _("Nome") . "</th>\n";
			} elseif ($report == 2) {
				echo "<th>" . _("Aula") . "</th><th>" . _("Faltas") . "</th>\n";
			}
			
			echo "</tr></thread><tbody>";
			
			$total = 0;
			
			while ($linha = mysql_fetch_array($result)) {
				if ($report == 0) {
					if ($linha["ativo"]) {
						echo "<tr><td>" . $linha["id"] . "</td>\n";
						echo "<td>\n";
						if ($linha["faltas"] > 0) {
							echo "<a href='#' onClick='abrirPag(" . '"frequencia.php", "pAction=REPORT&report=2&estid=' . 
							$linha["id"] . '&disid=' . $disid . '"' . ")'>" . $linha["nome"] . "</a>\n";
						} else {
							echo $linha["nome"];
						}
						echo "</td><td align='center'>" . $linha["faltas"] . "</td></tr>\n";
					}
				} elseif ($report == 1) {
					echo "<tr><td>" . $linha["aluid"] . "</td>\n";
					echo "<td>" . $linha["nome"] . "</td></tr>\n";
				} elseif ($report == 2) {
					echo "<tr><td>" . $linha["texto"] . " (" . $linha["dataf"] . ")</td>\n";
					echo "<td>" . $linha["faltas"] . "</td></tr>\n";
					$total += $linha["faltas"];
				}
			}
			echo "</tbody></table>\n";
			if ($total != 0) {
				echo "<br><strong>&nbsp;&nbsp;" . _("Total de faltas") . ": $total</strong>";
			}
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; faltas registradas") . "...</p>\n";
		}
		
		mysql_close($dblink);
		
		echo "</div></div>";
		
		return;

	}

	function AlteraDados($planid, $faltas) {
		include( "./connectdb.php" );
		foreach ($faltas as $aluid => $valor) {
			if ($valor == "") {
				$sql = "DELETE FROM frequencia WHERE aluid = '$aluid' AND planid = '$planid'";
			} else {
				$sql = "SELECT * FROM frequencia WHERE planid = '$planid' AND aluid = '$aluid'";
				$result = mysql_query($sql, $dblink) or die(mysql_error());
				if ( mysql_num_rows($result) > 0) {
					$sql = "UPDATE frequencia SET faltas = '$valor' WHERE planid = '$planid' AND aluid = '$aluid'";
				} else {
					$sql = "INSERT INTO frequencia VALUES (null, '0', '$aluid', '$valor', '$planid')";
				}
			}
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		}
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
	}

	function Frequencia($planid, $disid) {

		include( "./connectdb.php" );
		
		$sql = "SELECT texto, DATE_FORMAT(data, '%d-%m-%Y') as data FROM plano WHERE id = '$planid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		
		echo "<a href='#' onClick='abrirPag(" . '"frequencia.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Controle de frequ&ecirc;ncia") . "</button></A>\n";
	
		echo "<br><br><h2>" . $linha["texto"] . " (" . $linha["data"] . ")</h2>";

		$sql = "SELECT a.id, a.nome, f.faltas, d.ativo ";
		$sql = $sql . "FROM aluno a INNER JOIN disalu d ON (a.id = d.aluid) ";
		$sql = $sql . "LEFT JOIN frequencia f ON (a.id = f.aluid AND f.planid = '$planid') WHERE d.disid = '$disid' ORDER BY 2";
		
		$result = mysql_query($sql, $dblink) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {
			echo "<form action='frequencia.php' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='UPDATED'>";
			echo "<input type='hidden' name='planid' value='$planid'>";
			echo "<input type='hidden' name='disid' value='$disid'>";
			echo "<br><br><table class='table'><thread><tr>\n" ;
			echo "<th>" . _("Registro") . "</th><th>" . _("Nome") . "</th><th>" . _("Faltas") . "</th></tr></thread><tbody>\n";
			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td>" . $linha["id"] . "</td>";
				echo "<td>" . $linha["nome"] . "</td>";
				echo "<td><input type='text' size=6 maxlength=6 class='form-control' name='faltas[" . $linha["id"] . "]' value='" . $linha["faltas"] . "'";
				if (!$linha["ativo"]) {
					echo " DISABLED></td></tr>";
				} else {
					echo "></td></tr>";
				}
			}
			echo "</tbody></table>\n";
			echo "<input type='submit' class='btn btn-default' name='enviar' value='" . _("Enviar") . "'>\n" ;
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; estudantes registrados") . "...</p></td></tr>\n";
		}
		
		echo "</div></div>";
		
		mysql_close($dblink);
		return;
	}
	
	include( "cabecalho.php" );
	
	include( "menu.inc" );
	
	include 'dadosdis.inc';
	
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-ok-circle' aria-hidden='true'></span>&nbsp;" . _("Controle de frequ&ecirc;ncia") . "</h3></div>";
	
	include 'connectdb.php';

	if ($tipo == 1) {
		if ($pAction == "UPDATE") {				
			Frequencia($planid, $disid);
		} elseif ($pAction == "UPDATED") {
			AlteraDados($planid, $faltas);
			ListaDados($disid);
		} elseif ($pAction == "REPORT") {
			Relatorio($disid, $linhad["faltas"], $report, $planid, $estid, $tipo);
		} else {		
			ListaDados($disid);		
		}
	} else {
		Relatorio($disid, $linhad["faltas"], 2, $planid, $ra, $tipo);
	}
	
	mysql_close($dblink);

	include 'rodape.inc';
	
?>