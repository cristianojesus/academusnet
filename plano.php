<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	if ($tipo == 0 and $pAction != "VIEW") {
		$pAction = "";
	}
	
	function ListaDados($selall, $disid, $tipo) {
		
		if ($tipo == 1) {
			echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=INSERT"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Incluir novos planos de aula") . "</button></A>\n";
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Planos de aula") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");

		if ($tipo == 1) {
			$sql = "SELECT id, DATE_FORMAT(data, '%d/%m/%Y') as dataf, data, aula, texto, DATE_FORMAT(datav, '%d/%m/%Y') as datav FROM plano WHERE disid = '$disid' ORDER BY 3";
		} else {
			$sql = "SELECT id, DATE_FORMAT(data, '%d/%m/%Y') as dataf, data, aula, texto FROM plano WHERE disid = '$disid' and datav <= CURDATE() ORDER BY 3";
		}
		
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			if ($tipo == 1) {	
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" . _("Altera o plano de aula") . "\n";
				echo "<br><span class='glyphicon glyphicon-file' aria-hidden='true'></span>&nbsp;" . _("Anexa material de apoio") . "\n";
				//echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;Anexa links\n" ;
				echo "<br><span class='glyphicon glyphicon-bookmark' aria-hidden='true'></span>&nbsp;" . _("Anexa hipertextos") . "\n";
				echo "<br><span class='glyphicon glyphicon-edit' aria-hidden='true'></span>&nbsp;" . ("Anexa atividades") . "\n";
				echo "<br><span class='glyphicon glyphicon-comment' aria-hidden='true'></span>&nbsp;" . ("F&oacute;rum") . "\n";
				echo "<br><span class='glyphicon glyphicon-knight' aria-hidden='true'></span>&nbsp;" . 
				_("Estrat&eacute;gia educacional") . "\n";
			}
			
			echo "<br><br><table class='table'><thread><tr>\n" ;
		
			if ($tipo == 1) {	
				echo "<th></th><th></th><th></th>\n";
			} else {
				echo "<th></th>";
			}
		
			echo "<th align='center'>" . _("Data") . "</th><th>" . _("Eixo tem&aacute;tico") . "</th>\n";

			if ($tipo == 1) {
				echo "<th>" . _("Visualiza&ccedil;&atilde;o a partir de...") . "</th>";
			}

			echo "</tr></thread><tbody>";
			
			echo "<form action='plano.php' id='deleteForm' name='deleteForm' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='DELETE'>\n";
			
			$seq = 1;

			while ($linha = mysql_fetch_array($result)) {
				echo "<tr>";
				if ($tipo == 1) {
					echo "<td width='5%' nowrap align='right'>\n";
					if ( empty( $selall ) ) {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
					}
					echo "</td><td width='5%' nowrap><a href='#' onClick='abrirPag(" . '"plano.php", "pAction=UPDATE&planid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>\n";
					echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=LIST_FILES&planid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-file' aria-hidden='true'></span></a>\n";
					//echo "<a href='#' id='textLink' onClick='abrirPag(" . '"plano.php", "pAction=LIST_LINKS&planid=' . $linha["id"] . '")' . "'>\n";
					//echo "<img src='images/internet.png' width='16' height='16' border='0'></a>\n";
					echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=LIST_EAD&planid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-bookmark' aria-hidden='true'></span></a>\n";
					echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=LIST_TES&planid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a>\n";
					echo "<a href='#' onClick='abrirPag(" . '"forum.php", "planid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-comment' aria-hidden='true'></span></a>\n";
					echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=BLOOM&planid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-knight' aria-hidden='true'></span></a></td>\n";
				}
				echo "<td align='center' width='5%' nowrap>$seq</td>\n";
				echo "<td align='center' width='10%'>" . $linha["dataf"] . "</td>\n<td>";
				echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=VIEW&planid=' . $linha["id"] . '")' . "'>\n";
				echo $linha["texto"] . "</a></td>\n";
				if ($tipo == 1) {
					echo "<td align='center' width='10%'>" . $linha["datav"] . "</td></tr>\n";
				} else {
					echo "</tr>";
				}
				$seq++;
			}
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; planos de aula registrados") . "...</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		echo "</tbody></table>\n";
		
		mysql_close($dblink);
		
		if ($tipo == 1) {
			echo "<table><tr valign='top'>\n" ;
			echo "<td><input type='submit' class='btn btn-danger' name='enviar' value='" . _("Excluir") . "'></form></td>\n" ;
			echo "<td><form action='plano.php' id='selall' method='POST'>\n" ;
			echo "<input type='hidden' name='selall' value='1'>\n";
			echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Marcar todos") . "'>\n" ;
			echo "</form></td>\n" ;
			echo "<td><form action='plano.php' id='selall' method='POST'>\n";
			echo "<input type='hidden' name='selall' value='0'>\n";
			echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Desmarcar todos") . "'>\n" ;
			echo "</form></td></tr></table>\n" ;
		}
		
		echo "</div></div>";

		return;
	
	}
	
	function ListaArquivos($selall, $planid, $disid, $pAction, $usuid) {
		
		if ($pAction == "LIST_FILES") {
			echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=GET_FILES&planid=' . $planid . '&disid=' . $disid . '"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Associar material de apoio") . "</button></A>\n";
			echo "<a href='#' onClick='abrirPag(" . '"plano.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Planos de aula") . "</button></A>\n";
		} else {
			echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=LIST_FILES&planid=' . $planid . '&disid=' . $disid . '"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Material de apoio associado") . "</button></A>\n";
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Planos de aula e material de apoio") . "</h3></div>";
		echo "<div class='panel-body'>";

		include("./connectdb.php");

		if ($pAction == "LIST_FILES") {
			$sql = "SELECT m.id, m.assid, m.texto, m.link, m.tamanho, m.tipo, a.descricao FROM material m INNER JOIN planmat pm ON pm.matid = m.id 
			LEFT JOIN assunto a ON m.assid = a.id WHERE pm.planid = '$planid' ORDER BY 7, 3";
		} elseif ($pAction == "GET_FILES") {
			$sql = "SELECT m.id, m.assid, m.texto, m.link, m.tamanho, m.tipo, a.descricao FROM material m LEFT JOIN assunto a ON m.assid = a.id
			WHERE m.usuid = '$usuid' AND m.id NOT IN (SELECT matid FROM planmat WHERE planid = '$planid') ORDER BY 7, 3";
		}
		
		$result = mysql_query($sql, $dblink) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {			
			
			if ($pAction == "LIST_FILES") {
				echo "<form action='plano.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='planid' value='$planid'>\n";
				echo "<input type='hidden' name='pAction' value='MISS_FILES'>\n";
			} elseif ($pAction == "GET_FILES") {
				echo "<form action='plano.php' method='POST'>\n";
				echo "<input type='hidden' name='planid' value='$planid'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTEN_FILES'>\n";
			}			
			
			echo "<br><br><table class='table'><thread><tr>\n" ;
			echo "<th></th><th>" . _("Nome") . "</th>" /* <th align='center'>" . _("Tamanho") . " (MB)</th><th>" . _("Tipo") . "</th>"*/;
			echo "<th align='center'>" . ("Assunto") . "</th></tr></thread><tbdoy>\n";
			
			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td align='right' width=5% nowrap>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td>\n";
				//if (!empty($linha["link"])) {
				echo "<td><a href='" . $linha["link"] . "' target='_blank'>";
				//} else {
				//	echo "<td><a href='arquivos/" . $linha["texto"] . "' target='_blank'>";
				//}
				echo $linha["texto"] . "</td>";
				//if (!empty($linha["link"])) {
				//echo "</a></td><td align='center'>---</td><td>Link</td>";
				//} else {
				//	echo "</a><td align='center'>" . number_format($linha["tamanho"] / 1024000, 2,',','.') . "</td>\n";
				//	echo "<td>" . $linha["tipo"] . "</td>\n";
				//}
				if (!empty($linha["descricao"])) {
					echo "<td align='center' width=10%>" . $linha["descricao"] . "</td></tr>\n";
				} else {
					echo "<td align='center' width=10%>---</td></tr>\n";
				}
			}
			
			echo "</tbody></table>\n";

		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; material registrado") . "...</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		mysql_close($dblink);
		
		echo "<table><tr valign='top'>\n" ;
		if ($pAction == "LIST_FILES") {
			echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Desassociar") . "'></form></td>\n" ;
		} elseif ($pAction == "GET_FILES") {
			echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Associar") . "'></form></td>\n" ;
		}
		echo "<td><form action='plano.php' id='selall' method='POST'>\n" ;
		echo "<input type='hidden' name='planid' value='$planid'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Marcar todos") . "'>\n" ;
		echo "</form></td>\n" ;
		echo "<td><form action='plano.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='planid' value='$planid'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Desmarcar todos") . "'>\n" ;
		echo "</form></td></tr></table>\n" ;
				
		echo "</div></div>";

		return;
	
	}
	
	function AssociarArquivos($planid, $assdes) {
		include( "./connectdb.php" );
		foreach ($assdes as $matid => $valor) {	
			if ($valor == 'on') {
				$sql = "INSERT INTO planmat VALUES ('$planid', '$matid')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
	if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}

	function DesassociarArquivos($planid, $assdes) {
		include( "./connectdb.php" );
		foreach ($assdes as $matid => $valor) {	
			if ($valor == 'on') {
				$sql = "DELETE FROM planmat WHERE matid = '$matid' AND planid = '$planid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}

	/*function ListaLinks($selall, $planid, $disid, $pAction, $usuid) {
		
		if ($pAction == "LIST_LINKS") {
			echo "<br><a href='#' onClick='abrirPag(" . '"plano.php", "pAction=GET_LINKS&planid=' . $planid . '&disid=' . $disid . '"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Associar links") . "</button></A>\n";
			echo "<br><a href='#' onClick='abrirPag(" . '"plano.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Planos de aula") . "</button></A>\n";
		} else {
			echo "<br><a href='#' onClick='abrirPag(" . '"plano.php", "pAction=LIST_LINKS&planid=' . $planid . '&disid=' . $disid . '"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Planos de aula") . "</button></A>\n";
		}
		

		include("./connectdb.php");

		if ($pAction == "LIST_LINKS") {
			$sql = "SELECT w.id, w.assid, w.texto, w.endereco, a.descricao FROM webteca w INNER JOIN planweb pw ON pw.webid = w.id 
			LEFT JOIN assunto a ON w.assid = a.id WHERE pw.planid = '$planid' ORDER BY 5, 3";
		} elseif ($pAction == "GET_LINKS") {
			$sql = "SELECT w.id, w.assid, w.texto, w.endereco, a.descricao FROM webteca w LEFT JOIN assunto a ON w.assid = a.id
			WHERE w.usuid = '$usuid' AND w.id NOT IN (SELECT webid FROM planweb WHERE planid = '$planid') ORDER BY 5, 3";
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Planos de aula e links") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		$result = mysql_query($sql, $dblink) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {			
			
			if ($pAction == "LIST_LINKS") {
				echo "<form action='plano.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='planid' value='$planid'>\n";
				echo "<input type='hidden' name='pAction' value='MISS_LINKS'>\n";
			} elseif ($pAction == "GET_LINKS") {
				echo "<form action='plano.php' method='POST'>\n";
				echo "<input type='hidden' name='planid' value='$planid'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTEN_LINKS'>\n";
			}			
			
			echo "<br><br><table class='table'><thread><tr>\n" ;
			echo "<th></th><th>" . _("Nome") . "</th><th align='center'>" . _("Assunto") . "</th></tr></thread><tbody>\n";
			
			$tamanho_total = 0;

			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td align='right' width=5% nowrap>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td>\n";
				echo "<td><a href='" . $linha["endereco"] . "' target='_blank'>";
				echo $linha["texto"] . "</a></td>\n";
				if (!empty($linha["descricao"])) {
					echo "<td align='center'>" . $linha["descricao"] . "</td></tr>\n";
				} else {
					echo "<td align='center'>---</td></tr>\n";
				}
			}
			
			echo "</tbody></table>\n";

		} else {
			echo _("N&atilde;o h&aacute; links registrados ...") . "\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		mysql_close($dblink);
		
		echo "<table><tr valign='top'>\n" ;
		if ($pAction == "LIST_LINKS") {
			echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Desassociar") . "'></form></td>\n" ;
		} elseif ($pAction == "GET_LINKS") {
			echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Associar") . "'></form></td>\n" ;
		}
		echo "<td><form action='plano.php' id='selall' method='POST'>\n" ;
		echo "<input type='hidden' name='planid' value='$planid'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Marcar todos") . "'>\n" ;
		echo "</form></td>\n" ;
		echo "<td><form action='plano.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='planid' value='$planid'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Desmarcar todos") . "'>\n" ;
		echo "</form></td></tr></table>\n" ;
		
		echo "/div></div>";

		return;
	
	}
	
	function AssociarLinks($planid, $assdes) {
		include( "./connectdb.php" );
		foreach ($assdes as $webid => $valor) {	
			if ($valor == 'on') {
				$sql = "INSERT INTO planweb VALUES ('$planid', '$webid')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}

	function DesassociarLinks($planid, $assdes) {
		include( "./connectdb.php" );
		foreach ($assdes as $webid => $valor) {	
			if ($valor == 'on') {
				$sql = "DELETE FROM planweb WHERE webid = '$webid' AND planid = '$planid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}*/
	
	function ListaEaD($selall, $planid, $disid, $pAction, $usuid) {
		
		if ($pAction == "LIST_EAD") {
			echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=GET_EAD&planid=' . $planid . '&disid=' . $disid . '"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Associar hipertextos") . "</button></A>\n";
			echo "<a href='#' onClick='abrirPag(" . '"plano.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Planos de aula") . "</button></A>\n";
		} else {
			echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=LIST_EAD&planid=' . $planid . '&disid=' . $disid . '"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Hipertextos associados") . "</button></A>\n";
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Planos de aula e hipertextos") . "</h3></div>";
		echo "<div class='panel-body'>";
		

		include("./connectdb.php");

		if ($pAction == "LIST_EAD") {
			$sql = "SELECT e.id, e.assid, e.texto, a.descricao FROM ead e INNER JOIN planead pe ON pe.eadid = e.id 
			LEFT JOIN assunto a ON e.assid = a.id WHERE pe.planid = '$planid' AND e.eadid IS NULL ORDER BY 4, 3";
		} elseif ($pAction == "GET_EAD") {
			$sql = "SELECT e.id, e.assid, e.texto, a.descricao FROM ead e LEFT JOIN assunto a ON e.assid = a.id
			WHERE e.usuid = '$usuid' AND e.eadid IS NULL AND e.id NOT IN (SELECT eadid FROM planead WHERE planid = '$planid') ORDER BY 3";
		}
		
		$result = mysql_query($sql, $dblink) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {			
			
			if ($pAction == "LIST_EAD") {
				echo "<form action='plano.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='planid' value='$planid'>\n";
				echo "<input type='hidden' name='pAction' value='MISS_EAD'>\n";
			} elseif ($pAction == "GET_EAD") {
				echo "<form action='plano.php' method='POST'>\n";
				echo "<input type='hidden' name='planid' value='$planid'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTEN_EAD'>\n";
			}			
			
			echo "<br><br><table class='table'><thread><tr>\n" ;
			echo "<th></th><th>" . _("Nome") . "</th><th align='center'>" . _("Assunto") ."</th></tr></thread><tbody>\n";
			
			$tamanho_total = 0;

			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td align='right' width=5% nowrap>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td>\n";
				echo "<td>" . $linha["texto"] . "</td>\n";
				if (!empty($linha["descricao"])) {
					echo "<td align='center'>" . $linha["descricao"] . "</td></tr>\n";
				} else {
					echo "<td align='center'>---</td></tr>\n";
				}
			}
			
			echo "</tbody></table>\n";

		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; textos e resumos registrados ...") . "</p>\n";
			mysql_close($dblink);
			echo "</div></div>";
			return;
		}
		
		mysql_close($dblink);
		
		echo "<table><tr valign='top'>\n" ;
		if ($pAction == "LIST_EAD") {
			echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Desassociar") . "'></form></td>\n" ;
		} elseif ($pAction == "GET_EAD") {
			echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Associar") . "'></form></td>\n" ;
		}
		echo "<td><form action='plano.php' id='selall' method='POST'>\n" ;
		echo "<input type='hidden' name='planid' value='$planid'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Marcar todos") . "'>\n" ;
		echo "</form></td>\n" ;
		echo "<td><form action='plano.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='planid' value='$planid'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Desmarcar todos") . "'>\n" ;
		echo "</form></td></tr></table>\n" ;

		echo "</div></div>";
		
		return;
	
	}
	
	function AssociarEaD($planid, $assdes) {
		include( "./connectdb.php" );
		foreach ($assdes as $eadid => $valor) {	
			if ($valor == 'on') {
				$sql = "INSERT INTO planead VALUES ('$planid', '$eadid')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}

	function DesassociarEaD($planid, $assdes) {
		include( "./connectdb.php" );
		foreach ($assdes as $eadid => $valor) {	
			if ($valor == 'on') {
				$sql = "DELETE FROM planead WHERE eadid = '$eadid' AND planid = '$planid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}
	
	function ListaTes($selall, $planid, $disid, $pAction, $usuid) {
	
		if ($pAction == "LIST_TES") {
			echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=GET_TES&planid=' . $planid . '&disid=' . $disid . '"' . ")'>
				<button type='button' class='btn btn btn-default'>" . _("Associar atividades") . "</button></A>\n";
			echo "<a href='#' onClick='abrirPag(" . '"plano.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Planos de aula") . "</button></A>\n";
		} else {
			echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=LIST_TES&planid=' . $planid . '&disid=' . $disid . '"' . ")'>
				<button type='button' class='btn btn btn-default'>" . _("Atividades associadas") . "</button></A>\n";
		}
	
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Planos de aula e atividades") . "</h3></div>";
		echo "<div class='panel-body'>";
	
	
		include("./connectdb.php");
	
		if ($pAction == "LIST_TES") {
			$sql = "SELECT t.id, DATE_FORMAT(t.data, '%d/%m/%Y') as data, t.texto FROM teste t INNER JOIN plantes pt ON pt.tesid = t.id
			WHERE pt.planid = '$planid' ORDER BY 2, 3";
		} elseif ($pAction == "GET_TES") {
			$sql = "SELECT t.id, t.data, t.texto FROM teste t WHERE t.disid = '$disid' AND t.id NOT IN (SELECT tesid FROM plantes WHERE planid = '$planid') ORDER BY 3";
		}
	
		$result = mysql_query($sql, $dblink) or die(mysql_error());
	
		if (mysql_num_rows($result) > 0) {
				
			if ($pAction == "LIST_TES") {
				echo "<form action='plano.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='planid' value='$planid'>\n";
				echo "<input type='hidden' name='pAction' value='MISS_TES'>\n";
			} elseif ($pAction == "GET_TES") {
				echo "<form action='plano.php' method='POST'>\n";
				echo "<input type='hidden' name='planid' value='$planid'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTEN_TES'>\n";
			}			
				
			echo "<table class='table'><thread><tr>\n" ;
			echo "<th></th><th>" . _("Data") . "</th><th align='center'>" . _("Nome") ."</th></tr></thread><tbody>\n";
				
			$tamanho_total = 0;
	
			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td align='right' width=5% nowrap>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td>\n";
				echo "<td>" . $linha["data"] . "</td>\n";
				echo "<td>" . $linha["texto"] . "</td></tr>\n";
			}	
			
			echo "</tbody></table>\n";
	
		} else {
			
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; atividades registradas ...") . "</p>\n";
			mysql_close($dblink);
			echo "</div></div>";
			return;
		}
			
		mysql_close($dblink);
	
		echo "<table><tr valign='top'>\n" ;

		if ($pAction == "LIST_TES") {
			echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Desassociar") . "'></form></td>\n" ;
		} elseif ($pAction == "GET_TES") {
			echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Associar") . "'></form></td>\n" ;
		}
		
		echo "<td><form action='plano.php' id='selall' method='POST'>\n" ;
		echo "<input type='hidden' name='planid' value='$planid'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Marcar todos") . "'>\n" ;
		echo "</form></td>\n" ;
		echo "<td><form action='plano.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='planid' value='$planid'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Desmarcar todos") . "'>\n" ;
		echo "</form></td></tr></table>\n" ;

		echo "</div></div>";
			
		return;
		
	}
	
	function AssociarTes($planid, $assdes) {
		include( "./connectdb.php" );
		foreach ($assdes as $tesid => $valor) {
			if ($valor == 'on') {
				$sql = "INSERT INTO plantes VALUES ('$planid', '$tesid')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}
	
	function DesassociarTes($planid, $assdes) {
		include( "./connectdb.php" );
		foreach ($assdes as $tesid => $valor) {
			if ($valor == 'on') {
				$sql = "DELETE FROM plantes WHERE tesid = '$tesid' AND planid = '$planid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}
	
	function Visualizar($planid, $disid) {

		include( "./connectdb.php" );
		
		echo "<a href='#' id='textLink' onClick='abrirPag(" . '"plano.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Planos de aula") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Planos de aula") . "</h3></div>";
		echo "<div class='panel-body'>";

		$sql = "SELECT DATE_FORMAT(data, '%d/%m/%Y') as dataf, aula, texto, objetivos, conteudos, metodologia, atividades,
		leituraobr, leiturarec, comentario FROM plano WHERE id = '$planid' ORDER BY 1";
		$query =  mysql_query ($sql) or die(mysql_error());
		
		if ( mysql_num_rows($query) > 0) {
			$linha = mysql_fetch_array($query);
			echo "<h2>" . $linha["texto"] . "</h2><br>";
			//echo "<strong>" . _("Sequ&ecirc;ncia") . ": </strong>" . $linha["aula"];
			echo "<strong>" . _("Data") . ": </strong>" . $linha["dataf"];
			echo "<br><br><strong>" . _("T&iacute;tulo") . ": </strong>" . $linha["texto"];
			if (!empty($linha["objetivos"])) {
				echo "<br><br><strong>" . _("Objetivos") . ": </strong><br>" . nl2br($linha["objetivos"]);
			}
			if (!empty($linha["conteudos"])) {
				echo "<br><br><strong>" . _("Conte&uacute;dos") . ": </strong><br>" . nl2br($linha["conteudos"]);
			}
			if (!empty($linha["metodologia"])) {
				echo "<br><br><strong>" . _("Metodologia de Ensino") . ": </strong><br>" . nl2br($linha["metodologia"]);
			}
			
			$sql = "SELECT t.id, DATE_FORMAT(t.data, '%d/%m/%Y') as data, t.texto FROM teste t INNER JOIN plantes pt ON pt.tesid = t.id
			WHERE pt.planid = '$planid' ORDER BY 1, 2";
			$query_ts =  mysql_query ($sql) or die(mysql_error());
					
			if (!empty($linha["atividades"]) or mysql_num_rows($query_ts) > 0) {
				echo "<br><br><strong>" . _("Atividades") . ": </strong><br>" . nl2br($linha["atividades"]);
				while ($linha_ts = mysql_fetch_array($query_ts)) {
					echo "<br><br><a href='#' onClick='abrirPag(" . '"tesdo.php", "planid=' . $planid . '&tesid=' . $linha_ts["id"] . '")' . "'>" . $linha_ts["texto"] . 
					"</a>, <strong>" . _("prazo") . "&nbsp;" . $linha_ts["data"] . "</strong> " . _("(atividade)");
				}
			}
			if (!empty($linha["leituraobr"])) {
				echo "<br><br><strong>" . _("Leitura Obrigat&oacute;ria") . ": </strong><br>" . nl2br($linha["leituraobr"]);
			}
			if (!empty($linha["leiturarec"])) {
				echo "<br><br><strong>" . _("Leitura Recomendada") . ": </strong><br>" . nl2br($linha["leiturarec"]);
			}
			if (!empty($linha["comentario"])) {
				echo "<br><br><strong>" . _("Observa&ccedil;&otilde;es") . ": </strong><br>" . nl2br($linha["comentario"]);
			}
			
			$sql = "SELECT * FROM planbloom WHERE planid = '$planid'";
			$query =  mysql_query ($sql) or die(mysql_error());
			if ( mysql_num_rows($query) > 0) {
				echo "<h3>" . _("A&ccedil;&otilde;es educativas") . "</h3>";
				$linha = mysql_fetch_array($query);
				if (!empty($linha["conhecimento"])) {
					echo "<br><strong>" . _("Conhecimento") . ":</strong><br>" . $linha["conhecimento"];
				}
				if (!empty($linha["compreensao"])) {
					echo "<br><br><strong>" . _("Compreens&atilde;o") . ":</strong><br>" . $linha["compreensao"];
				}
				if (!empty($linha["aplicacao"])) {
					echo "<br><br><strong>" . _("Aplica&ccedil;&atilde;o") . ":</strong><br>" . $linha["aplicacao"];
				}
				if (!empty($linha["analise"])) {
					echo "<br><br><strong>" . _("An&aacute;lise") . ":</strong><br>" . $linha["analise"];
				}
				if (!empty($linha["avaliacao"])) {
					echo "<br><br><strong>" . _("Avalia&ccedil;&atilde;o") . ":</strong><br>" . $linha["avaliacao"];
				}
				if (!empty($linha["sintese"])) {
					echo "<br><br><strong>" . _("S&iacute;ntese") . ":</strong><br>" . $linha["sintese"];
				}
			}
			
			$sql = "SELECT w.texto, w.endereco FROM planweb pw INNER JOIN webteca w ON (w.id = pw.webid) WHERE pw.planid = '$planid'";
			$query_pw =  mysql_query ($sql) or die(mysql_error());
				
			$sql = "SELECT m.texto, m.link FROM planmat pm INNER JOIN material m ON (m.id = pm.matid) WHERE pm.planid = '$planid'";
			$query_pm =  mysql_query ($sql) or die(mysql_error());
				
			$sql = "SELECT e.texto, pe.eadid FROM planead pe INNER JOIN ead e ON (e.id = pe.eadid) WHERE pe.planid = '$planid'";
			$query_pe =  mysql_query ($sql) or die(mysql_error());
			
			if (mysql_num_rows($query_pe) > 0 or mysql_num_rows($query_pw) > 0 or mysql_num_rows($query_pm) > 0) {
				echo "<br><h3>" . _("Outras refer&ecirc;ncias") . "</h3>";
				while ($linha_pw = mysql_fetch_array($query_pw)) {
					echo "<br><a href='" . $linha_pw["endereco"] . "' target='_blank'>" . $linha_pw["texto"] . "</a> (link)";
				}
				while ($linha_pm = mysql_fetch_array($query_pm)) {
					//if (!empty($linha_pm["link"])) {
					echo "<br><a href='" . $linha_pm["link"] . "' target='_blank'>" . $linha_pm["texto"] . "</a> " . _("(arquivo)");
					//} else {
					//	echo "<br><a href='arquivos/" . $linha_pm["texto"] . "' target='_blank'>" . $linha_pm["texto"] . "</a>" . _("(arquivo)");
					//}
				}
				while ($linha_pe = mysql_fetch_array($query_pe)) {
					echo "<br><a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=VIEW&pActionDest=VIEW&eadid=' . $linha_pe["eadid"] . '")' . "'>" .
							$linha_pe["texto"] . "</a> " . _("(texto/resumo)");
				}
			}

		}
		mysql_close($aDBLink);
		
		echo "</div></div>";
	}
	
	function ExcluiDados($eliminar) {
		if (!empty($eliminar)) {
			include( "./connectdb.php" );
			foreach ($eliminar as $planid => $valor) {	
				if ($valor == 'on') {
					$SQL = "DELETE FROM plano WHERE id = '$planid'" ;
					$result = mysql_query( $SQL, $dblink );
				}
			}
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
		}
		return;
	}
	
	function IncluiDados($disid, $data, $aula, $texto, $objetivos, $conteudos, $metodologia, $atividades, $leituraobr, $leiturarec, $comentario, $datav) {
		include( "./connectdb.php" );
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $data, $regs);
		$data = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $datav, $regs);
		$datav = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		$sql = "INSERT INTO plano VALUES 
		(null, '$data', '$disid', '$aula', '$texto', '$objetivos', '$conteudos', '$metodologia', '$atividades', '$leituraobr', '$leiturarec', '$comentario', '$datav')";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function AlteraDados($planid, $data, $aula, $texto, $objetivos, $conteudos, $metodologia, $atividades, $leituraobr, $leiturarec, $comentario, $datav) {
		include( "./connectdb.php" );
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $data, $regs);
		$data = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $datav, $regs);
		$datav = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		$sql = "UPDATE plano SET data = '$data', aula = '$aula', texto = '$texto', objetivos = '$objetivos', conteudos = '$conteudos', metodologia = '$metodologia', 
		atividades = '$atividades', leituraobr = '$leituraobr', leiturarec = '$leiturarec', comentario = '$comentario', datav = '$datav' WHERE id = $planid";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function DefineBloom($planid, $conhecimento, $compreensao, $aplicacao, $analise, $avaliacao, $sintese) {
		if (empty($conhecimento) and empty($compreensao) and empty($aplicacao) and empty($analise) and empty($avaliacao) and empty($sintese)) {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Defina ao menos uma a&ccedil;&atilde;o ...") . "</strong></div>";
			return 0;
		}
		include( "./connectdb.php" );
		$sql = "SELECT * FROM planbloom WHERE planid = '$planid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		if (mysql_num_rows($result) == 0) {
			$sql = "INSERT INTO planbloom VALUES ('$planid', '$conhecimento', '$compreensao', '$aplicacao', '$analise', '$avaliacao', '$sintese')";
		} else {
			$sql = "UPDATE planbloom SET conhecimento = '$conhecimento', compreensao = '$compreensao', aplicacao = '$aplicacao', 
			analise = '$analise', avaliacao = '$avaliacao', sintese = '$sintese' WHERE planid = '$planid'";
		}
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		return 1;
	}
	
	function Formulario($disid, $data, $aula, $texto, $objetivos, $conteudos, $metodologia, $atividades, $leituraobr, $leiturarec, $comentario, $datav) {
		
		echo "<a href='#' onClick='abrirPag(" . '"plano.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
		_("Plano de aula") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Plano de aula") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br><br>";
		
		echo "<p><label for='aula'>(*) " . _("Visualiza&ccedil;&atilde;o a partir de...") . "</label>\n";
		echo "<input type='text' name='datav' id='datav' value='$datav' size=10 maxlength=10 class='form-control datepicker' required></p>\n";
		echo "<p><label for='data'>(*) " . _("Data") . "</label>\n";
		echo "<input type='text' name='data' id='data' value='$data' size=10 maxlength=10 class='form-control datepicker' required></p>\n";
		echo "<p><label for='texto'>(*) " . _("Eixo tem&aacute;tivo") . "</label>\n";
		echo "<input type='text' name='texto' value='$texto' size=60 maxlength=90 class='form-control' required></p>\n";
		echo "<p><label for='objetivos'>" . _("Objetivos") . "<br></label></p>\n";
		echo "<textarea name='objetivos' rows='20' class='form-control'>$objetivos</textarea>\n";
		echo "<p><label for='conteudos'>" . _("Conte&uacute;do") . "<br></label></p>\n";
		echo "<textarea name='conteudos' rows='20' class='form-control'>$conteudos</textarea>\n";
		echo "<p><label for='metodologia'>" . _("Metodologia") . "<br></label></p>\n";
		echo "<textarea name='metodologia' rows='20' class='form-control'>$metodologia</textarea>\n";
		echo "<p><label for='atividades'>" . _("Atividades") . "<br></label></p>\n";
		echo "<textarea name='atividades' rows='20' class='form-control'>$atividades</textarea>\n";
		echo "<p><label for='leituraobr'>" . _("Leituras obrigat&oacute;rias") . "<br></label></p>\n";
		echo "<textarea name='leituraobr' rows='20' class='form-control'>$leituraobr</textarea>\n";
		echo "<p><label for='leiturarec'>" . _("Leituras recomendadas") . "<br></label></p>\n";
		echo "<textarea name='leiturarec' rows='20' class='form-control'>$leiturarec</textarea>\n";
		echo "<p><label for='comentario'>" . _("Coment&aacute;rio") . "<br></label></p>\n";
		echo "<textarea name='comentario' rows='20' class='form-control'>$comentario</textarea>\n";
		echo "<input type='submit' class='btn btn-default' name='enviarage' value='" . _("Enviar") . "'></form>\n";
		
		echo "</div></div>";
	}
	
	function FormBloom($planid, $conhecimento, $compreensao, $aplicacao, $analise, $avaliacao, $sintese, $disid) {
	
		echo "<a href='#' onClick='abrirPag(" . '"plano.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
		_("Planos de aula") . "</button></A>\n";
	
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Plano de aula - Taxonomia de Bloom") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include( "connectdb.php" );
		$sql = "SELECT e.nome as nomee, e.projeto as projetoe, c.nome as nomec, c.projeto as projetoc, d.nome as nomed, d.objetivo, p.texto, p.objetivos FROM
		plano p INNER JOIN disciplina d ON p.disid = d.id INNER JOIN curso c ON c.id = d.curid INNER JOIN enderecos e ON e.id = d.endid WHERE p.id = '$planid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		echo "<h3>" . $linha["nomee"] . "</h3>";
		if (!empty($linha["projetoe"])) {
			echo "<blockquote><p><strong><em>" . _("Projeto did&aacute;tico pedag&oacute;gico institucional") . ": </strong>" . $linha["projetoe"] . "</em></p></blockquote>";
		}
		echo "<h3>" . $linha["nomec"] . "</h3>";
		if (!empty($linha["projetoc"])) {
			echo "<blockquote><p><strong><em>" . _("Projeto pedag&oacute;gico de curso") . ": </strong>" . $linha["projetoc"] . "</em></p></blockquote>";
		}
		echo "<h3>" . $linha["nomed"] . "</h3>";
		if (!empty($linha["objetivo"])) {
			echo "<blockquote><p><strong><em>" . _("Objetivos da disciplina") . ": </strong>" . $linha["objetivo"] . "</em></p></blockquote>";
		}
		echo "<h3>" . $linha["texto"] . "</h3>";
		if (!empty($linha["objetivos"])) {
			echo "<blockquote><p><strong><em>" . _("Objetivos da aula") . ": </strong>" . $linha["objetivos"] . "</em></p></blockquote>";
		}
	
		echo "<p><label for='conhecimento'>" . _("Conhecimento") . "<br></label></p>\n";
		echo "<textarea name='conhecimento' rows='20' class='form-control'>$conhecimento</textarea>\n";
		echo "<p><label for='compreensao'>" . _("Compreens&atilde;o") . "<br></label></p>\n";
		echo "<textarea name='compreensao' rows='20' class='form-control'>$compreensao</textarea>\n";
		echo "<p><label for='aplicacao'>" . _("Aplica&ccedil;&atilde;o") . "<br></label></p>\n";
		echo "<textarea name='aplicacao' rows='20' class='form-control'>$aplicacao</textarea>\n";
		echo "<p><label for='analise'>" . _("An&aacute;lise") . "<br></label></p>\n";
		echo "<textarea name='analise' rows='20' class='form-control'>$analise</textarea>\n";
		echo "<p><label for='avaliacao'>" . _("Avalia&ccedil;&atilde;o") . "<br></label></p>\n";
		echo "<textarea name='avaliacao' rows='20' class='form-control'>$avaliacao</textarea>\n";
		echo "<p><label for='sintese'>" . _("S&iacute;ntese") . "<br></label></p>\n";
		echo "<textarea name='sintese' rows='20' class='form-control'>$sintese</textarea>\n";
		echo "<input type='submit' class='btn btn-default' name='enviar' value='" . _("Enviar") . "'></form>\n";
			
		echo "</div></div>";
	}
	
	include( "cabecalho.php" );
	
	include( "menu.inc" );
	
?>
		
<script type="text/javascript">

	$(function(){

		$('.datepicker').datepicker({
			format: '<?php echo $_SESSION["data_formato"];?>',                
			language: 'pt-BR'
		});

		$('#Confirmar').click(function () {
			document.deleteForm.submit();
			modal.modal('hide');
		});
			
		$('form#deleteForm').submit(function(e){
			e.preventDefault();
			$('#deleteConfirmModal').modal('show');
		});

	});

</script>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">

				<?php
				echo "<h4 class='modal-title' id='deleteLabel'>" . _("Notifica&ccedil;&atilde;o de exclus&atilde;o") . "</h4>";
				echo "</div>";
				echo "<div class='modal-body'>";
				echo "<p>" . _("Voc&ecirc; optou por excluir um compromisso.") . "</p>";
				echo "<p>" . _("Se esta &eacute; a a&ccedil;&atilde;o que deseja executar, ");
				echo _("por favor confirme sua escolha ou cancele a opera&ccedil;&atilde;o para retornar.") . "</p>";
				echo "</div>";
				echo "<div class='modal-footer'>";
				echo "<button type='button' class='btn btn-success' data-dismiss='modal' id='Cancelar'>" . _("Cancelar") . "</button>";
				echo "<button type='button' class='btn btn-danger' id='Confirmar'>" . _("Confirmar") . "</button>";
				?>
                	
            </div>
        </div>
    </div>
</div>
	
<?php
	
	include 'dadosdis.inc';
		
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-tasks' aria-hidden='true'></span>&nbsp;" . _("Planos de aula") . "</h3></div>";

	if ($pAction == "DELETE") {
		ExcluiDados($eliminar);
		ListaDados($selall, $disid, $tipo);
	} elseif ($pAction == "INSERT" or $pAction == "INSERTED") {
		if ($pAction == "INSERTED") {
			IncluiDados($disid, $data, $aula, $texto, $objetivos, $conteudos, $metodologia, $atividades, $leituraobr, $leiturarec, $comentario, $datav);
		}
		echo "<form action='plano.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario($disid, null, null, null, null, null, null, null, null);
	} elseif ($pAction == "UPDATE" or $pAction == "UPDATED") {
		if ($pAction == "UPDATED") {
			AlteraDados($planid, $data, $aula, $texto, $objetivos, $conteudos, $metodologia, $atividades, $leituraobr, $leiturarec, $comentario, $datav);
			ListaDados($selall, $disid, $tipo);
		} else {
			echo "<form action='plano.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
			echo "<input type='hidden' name='planid' value='$planid'>\n";
			include 'connectdb.php';
			$sql = "SELECT DATE_FORMAT(data, '%d/%m/%Y') as data, aula, texto, objetivos, conteudos, metodologia, atividades, leituraobr, leiturarec, comentario, 
			DATE_FORMAT(datav, '%d/%m/%Y') as datav FROM plano WHERE id='$planid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			Formulario($disid, $linha["data"], $linha["aula"], $linha["texto"], $linha["objetivos"], $linha["conteudos"], $linha["metodologia"], $linha["atividades"],
			$linha["leituraobr"], $linha["leiturarec"], $linha["comentario"], $linha["datav"]);
			mysql_close($dblink);
		}
	} elseif ($pAction == "LIST_FILES" or $pAction == "GET_FILES") {
		ListaArquivos($selall, $planid, $disid, $pAction, $id);
	} elseif ($pAction == "GOTTEN_FILES") {
		AssociarArquivos($planid, $assdes);
		ListaArquivos($selall, $planid, $disid, "LIST_FILES", $id);
	} elseif ($pAction == "MISS_FILES") {
		DesassociarArquivos($planid, $assdes);
		ListaArquivos($selall, $planid, $disid, "LIST_FILES", $id);
	//} elseif ($pAction == "LIST_LINKS" or $pAction == "GET_LINKS") {
	//	ListaLinks($selall, $planid, $disid, $pAction, $id);
	//} elseif ($pAction == "GOTTEN_LINKS") {
	//	AssociarLinks($planid, $assdes);
	//	ListaLinks($selall, $planid, $disid, "LIST_LINKS", $id);
	//} elseif ($pAction == "MISS_LINKS") {
	//	DesassociarLinks($planid, $assdes);
	//	ListaLinks($selall, $planid, $disid, "LIST_LINKS", $id);
	} elseif ($pAction == "LIST_EAD" or $pAction == "GET_EAD") {
		ListaEaD($selall, $planid, $disid, $pAction, $id);
	} elseif ($pAction == "GOTTEN_EAD") {
		AssociarEaD($planid, $assdes);
		ListaEaD($selall, $planid, $disid, "LIST_EAD", $id);
	} elseif ($pAction == "MISS_EAD") {
		DesassociarEaD($planid, $assdes);
		ListaEaD($selall, $planid, $disid, "LIST_EAD", $id);
	} elseif ($pAction == "LIST_TES" or $pAction == "GET_TES") {
		ListaTes($selall, $planid, $disid, $pAction, $id);
	} elseif ($pAction == "GOTTEN_TES") {
		AssociarTes($planid, $assdes);
		ListaTes($selall, $planid, $disid, "LIST_TES", $id);
	} elseif ($pAction == "MISS_TES") {
		DesassociarTes($planid, $assdes);
		ListaTes($selall, $planid, $disid, "LIST_TES", $id);
	} elseif ($pAction == "VIEW") {
		Visualizar($planid, $disid);
	} elseif ($pAction == "BLOOM" or $pAction == "BLOOMED") {
		if ($pAction == "BLOOMED") {
			$bloomed = DefineBloom($planid, $conhecimento, $compreensao, $aplicacao, $analise, $avaliacao, $sintese);
		}
		if ($pAction == "BLOOM" or !$bloomed) {
			include 'connectdb.php';
			$sql = "SELECT * FROM planbloom WHERE planid = '$planid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			echo "<form action='plano.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='BLOOMED'>\n";
			echo "<input type='hidden' name='planid' value='$planid'>\n";
			FormBloom($planid, $linha["conhecimento"], $linha["compreensao"], $linha["aplicacao"], $linha["analise"], $linha["avaliacao"], $linha["sintese"], $disid);
			mysql_close($dblink);
		} else {
			ListaDados($selall, $disid, $tipo);
		}
	} else {
		ListaDados($selall, $disid, $tipo);
	}
	
	include 'rodape.inc';

?>
