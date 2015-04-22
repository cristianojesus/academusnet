<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	include( "./connectdb.php" );
	$sql = "SELECT ue.ra FROM usuend ue INNER JOIN disciplina d ON (ue.endid = d.endid) WHERE d.id = '$disid' AND ue.usuid = '$id'";
	$query =  mysql_query ($sql) or die(mysql_error());
	if (mysql_num_rows($query) > 0) {
		$linha = mysql_fetch_array($query);
		$ra = $linha["ra"];
	}
	
	$sql = "SELECT ea.equid FROM equialu ea INNER JOIN equipes e ON (e.id = ea.equid) WHERE e.disid = '$disid' AND ea.aluid = '$ra'";
	$query =  mysql_query ($sql) or die(mysql_error());
	if (mysql_num_rows($query) > 0) {
		$linhaea = mysql_fetch_array($query);
		$equid = $linhaea["equid"];
	} else {
		$equid = "";
	}
	
	mysql_close($dblink);
	
	if ($tipo == 0 and $pAction != "SHOW") {
		$pAction = "";
	}
	
	function ListaDados($selall, $disid, $tipo, $aluid, $equid) {
		
		if ($tipo == 1) {
			echo "<a href='#' onClick='abrirPag(" . '"prova.php", "pAction=INSERT"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Incluir novas atividades") . "</button></A>\n";
			echo "<a href='#' onClick='abrirPag(" . '"prova.php", "pAction=BLOOMALL"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Relat&oacute;rio geral") . "</button></A><br><br>\n";
		}

		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Atividades") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");

		if ($tipo == 1) {
			$sql = "SELECT id, texto, DATE_FORMAT(data, '%d/%m/%Y') as dataf, data, tipo, status, avaliacao, DATE_FORMAT(datav, '%d/%m/%Y') as datav 
			FROM teste WHERE disid = '$disid' ORDER BY data";
		} else {
			$sql = "SELECT id, texto, DATE_FORMAT(data, '%d/%m/%Y') as dataf, data, tipo, status, avaliacao
			FROM teste WHERE disid = '$disid' AND datav <= CURDATE() ORDER BY data";
		}
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			if ($tipo == 1) {	
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" . _("Alterar a atividade") . "\n" ;
				echo "<br><span class='glyphicon glyphicon-question-sign' aria-hidden='true'></span>&nbsp;" . _("Quest&otilde;es") . "\n" ;
			}
			echo "<br><span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span>&nbsp;" . _("Visualizar a atividade");
			if ($tipo == 1) {
				echo "<br><span class='glyphicon glyphicon-ok' aria-hidden='true'></span>&nbsp;" . _("Corrigir a atividade");
			}			
			echo "<br><span class='glyphicon glyphicon-edit' aria-hidden='true'></span>&nbsp;" . _("Fazer a atividade");
			if ($tipo == 1) {
				echo "<br><span class='glyphicon glyphicon-list-alt' aria-hidden='true'></span>&nbsp;" .
				_("Relat&oacute;rio avaliativo") . "<br>\n";
			}
			
			echo "<br><br><table class='table'><thread><tr>\n" ;
		
			if ($tipo == 1) {	
				echo "<th></th>\n";
			}
		
			echo "<th width='10%'></th><th width='10%'>" . _("Data") . "</th>";
			
			if ($tipo == 1) {
				echo "<th width='10%'>" . _("Visualiza&ccedil;&atilde;o a partir de...") . "</th>";
			}
			
			echo "<th>" . _("Atividade") . "</th>\n";
			
			if ($tipo == 0) {
				echo "<th align='center' width='10%'>" . _("Nota") . "</th>";
			}
			
			echo "</tr></thread><tbody>";

			echo "<form action='prova.php' id='deleteForm' name='deleteForm' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='DELETE'>\n";

			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td align='right' nowrap>\n";
				if ($tipo == 1) {
					if ( empty( $selall ) ) {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
					}
					echo "</td><td nowrap><a href='#' onClick='abrirPag(" . '"prova.php", "pAction=UPDATE&tesid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>\n";
					echo "<a href='#' onClick='abrirPag(" . '"prova.php", "pAction=LIST&tesid=' . $linha["id"] . '")' . "'>
					<span class='glyphicon glyphicon-question-sign' aria-hidden='true'></span></a>\n";
				}
				if ($tipo == 1 or $linha["status"] == 2) {
					echo "<a href='#' onClick='abrirPag(" . '"prova.php", "pAction=SHOW&tesid=' . $linha["id"] . '")' . "'>
					<span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span></a>";
				}
				if ($tipo == 1) {
					echo "<a href='#' onClick='abrirPag(" . '"prova.php", "pAction=VIEW&tesid=' . $linha["id"] . '")' . "'>
					<span class='glyphicon glyphicon-ok' aria-hidden='true'></span></a>";
				}
				if ($linha["status"] == 1) {
					if ( date("Y-m-d") <= $linha["data"]) {
						echo "<a href='#' onClick='abrirPag(" . '"tesdo.php", "tesid=' . $linha["id"] . '")' . "'>
						<span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a>";
					}
				}
				if ($tipo == 1) {
					echo "<a href='#' onClick='abrirPag(" . '"prova.php", "pAction=BLOOM&tesid=' . $linha["id"] . '")' . "'>
					<span class='glyphicon glyphicon-list-alt' aria-hidden='true'></span></a>";
				}
				echo "</td><td>" . $linha["dataf"] . "</td>\n";
				if ($tipo == 1) {
					echo "<td>" . $linha["datav"] . "</td>";
				}
				echo "<td>" . $linha["texto"] . "<br><strong><span class='label label-info'>";
				if($linha["status"]==1) {
					if ( date("Y-m-d") <= $linha["data"]) {
						echo _("Em andamento");
					} else {
						echo _("Em corre&ccedil;&atilde;o");
					}
				} elseif ($linha["status"]==0) {
					echo _("Em corre&ccedil;&atilde;o");
				} else {
					echo _("Corrigido");
				}
				
				echo "</span>&nbsp;<span class='label label-info'>";
				
				if($linha["avaliacao"]==0) {
					echo _("Individual");
				} else {
					echo _("Em equipe");
				}
				
				echo "</span></strong>";
				
				if ($tipo == 0) {
					if ((date("Y-m-d") > $linha["data"]) or $linha["status"]==2) {
						if ($linha["avaliacao"] == 1) {
							$sql = "SELECT SUM(d.valor) as valord FROM tesque tq INNER JOIN descritiva d ON (d.queid = tq.queid) 
							WHERE tq.tesid = '" . $linha["id"] . "' AND (d.aluid = '$aluid' OR d.equid = '$equid') GROUP BY d.aluid, d.tesid";
						} else {
							$sql = "SELECT SUM(d.valor) as valord FROM tesque tq INNER JOIN descritiva d ON (d.queid = tq.queid) 
							WHERE tq.tesid = '" . $linha["id"] . "' AND d.aluid = '$aluid' GROUP BY d.aluid, d.tesid";
						}
						$result_d = mysql_query($sql, $dblink ) or die(mysql_error());
						$linhad = mysql_fetch_array($result_d);
						if ($linha["avaliacao"] == 1) {
							$sql = "SELECT SUM(c.valor) as valorc FROM tesque tq INNER JOIN correcao c ON (c.queid = tq.queid) 
							WHERE tq.tesid = '" . $linha["id"] . "' AND (c.aluid = '$aluid' OR c.equid = '$equid') GROUP BY c.aluid, c.tesid";
						} else {
							$sql = "SELECT SUM(c.valor) as valorc FROM tesque tq INNER JOIN correcao c ON (c.queid = tq.queid) 
							WHERE tq.tesid = '" . $linha["id"] . "' AND c.aluid = '$aluid' GROUP BY c.aluid, c.tesid";
						}
						$result_c = mysql_query($sql, $dblink ) or die(mysql_error());
						$linhac = mysql_fetch_array($result_c);
						if ($linha["avaliacao"] == 1) {
							$sql = "SELECT SUM(ta.valor) as valorta FROM tesque tq INNER JOIN testearq ta ON (ta.queid = tq.queid) 
							WHERE tq.tesid = '" . $linha["id"] . "' AND (ta.aluid = '$aluid' OR ta.equid = '$equid') GROUP BY ta.aluid, ta.tesid";
						} else {
							$sql = "SELECT SUM(ta.valor) as valorta FROM tesque tq INNER JOIN testearq ta ON (ta.queid = tq.queid) 
							WHERE tq.tesid = '" . $linha["id"] . "' AND ta.aluid = '$aluid' GROUP BY ta.aluid, ta.tesid";
						}
						$result_ta = mysql_query($sql, $dblink ) or die(mysql_error());
						$linhata = mysql_fetch_array($result_ta);
						echo "<td align='center'>" . number_format($linhad["valord"] + $linhac["valorc"] + $linhata["valorta"],1) . "\n";
					} else {
						echo "<td>";
					}
				}
				echo "</td></tr>\n";
			}
			
			echo "</tbody></table>\n";
			
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; atividades registradas") . "...</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		mysql_close($dblink);
		
		if ($tipo == 1) {
			echo "<table><tr valign='top'>\n" ;
			echo "<td><input type='submit' class='btn btn-danger' name='enviar' value='" . _("Excluir") . "'></form></td>\n" ;
			echo "<td><form action='prova.php' id='selall' method='POST'>\n" ;
			echo "<input type='hidden' name='selall' value='1'>\n";
			echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'>\n";
			echo "</form></td>\n" ;
			echo "<td><form action='prova.php' id='selall' method='POST'>\n";
			echo "<input type='hidden' name='selall' value='0'>\n";
			echo "<input class='btn btn-default' type='submit' value='" . _("Desmarcar todos") . "'>\n" ;
			echo "</form></td></tr></table>\n" ;
		}
		
		echo "</div></div>";

		return;
	
	}
	
	function ListaAtividades($tesid, $disid, $tipo) {
		
		echo "<a href='prova.php'><button type='button' class='btn btn btn-default'>" . _("Atividades") . "</button></A><br><br>\n";
		
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Atividades") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");
		
		$sql = "SELECT avaliacao FROM teste WHERE id = '$tesid' ORDER BY data";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		$avaliacao = $linha["avaliacao"];
		
		$sql = "SELECT a.id, a.nome FROM aluno a INNER JOIN disalu da ON (da.aluid = a.id) WHERE da.disid = '$disid' ORDER BY 2";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			echo "<table class='table'><thread><tr>\n" ;
	
			echo "<th>Registro</th><th>Nome</th><th align='center'>Nota</th></tr></thread><tbody>\n";	

			while ($linha = mysql_fetch_array($result)) {
				
				if ($avaliacao == 1) {
					$sql = "SELECT ea.equid FROM equialu ea INNER JOIN equipes e ON (e.id = ea.equid) WHERE e.disid = '$disid' AND ea.aluid = '" . $linha["id"] . "'";
					$query =  mysql_query ($sql) or die(mysql_error());
					if (mysql_num_rows($query) > 0) {
						$linhaea = mysql_fetch_array($query);
						$equid = $linhaea["equid"];
					} else {
						$equid = "0";
					}
				} else {
					$equid = "0";
				}
				
				echo "<tr>\n";
				echo "<td>" . $linha["id"] . "</td>\n";
				echo "<td>";
				
				$nota = 0;
				$prova = 0;
				
				$sql = "SELECT SUM(d.valor) as valord FROM tesque tq INNER JOIN descritiva d ON (d.queid = tq.queid) 
				WHERE tq.tesid = '$tesid' AND d.tesid = '$tesid' AND (d.aluid ='" . $linha["id"] . "' OR d.equid = '$equid') GROUP BY d.aluid, d.tesid";
				$result_d = mysql_query($sql, $dblink ) or die(mysql_error());
				
				if (mysql_num_rows($result_d) > 0) {
					$linhad = mysql_fetch_array($result_d);
					$nota = $linhad["valord"];
					$prova = 1;
				}

				$sql = "SELECT SUM(c.valor) as valorc FROM tesque tq INNER JOIN correcao c ON (c.queid = tq.queid) 
				WHERE tq.tesid = '$tesid' AND (c.aluid ='" . $linha["id"] . "' OR c.equid = '$equid') GROUP BY c.aluid, c.tesid";					
				$result_c = mysql_query($sql, $dblink ) or die(mysql_error());

				if (mysql_num_rows($result_c) > 0) {
					$linhac = mysql_fetch_array($result_c);
					$nota += $linhac["valorc"];
					$prova = 1;
				}
					
				$sql = "SELECT SUM(ta.valor) as valorta FROM tesque tq INNER JOIN testearq ta ON (ta.queid = tq.queid) 
				WHERE ta.tesid = '$tesid' AND (ta.aluid ='" . $linha["id"] . "' OR ta.equid = '$equid') GROUP BY ta.aluid, ta.tesid";						
				$result_ta = mysql_query($sql, $dblink ) or die(mysql_error());

				if (mysql_num_rows($result_ta) > 0) {
					$linhata = mysql_fetch_array($result_ta);
					$nota += $linhata["valorta"];
					$prova = 1;
				}
				
				if (!$prova) {
					echo $linha["nome"] . "</td><td></td></tr>\n";
				} else {
					echo "<a href='#' id='textLink' onClick='abrirPag(" . '"prova.php", "pAction=VERIFY&tesid=' . 
					$tesid . '&aluid=' . $linha["id"] . '&equid=' . $equid . '"' . ")'>" . $linha["nome"] . "</a></td>\n";
					echo "<td align='center'>" . number_format($linhad["valord"] + $linhac["valorc"] + $linhata["valorta"],1) . "</td></tr>\n";
				}

			}
			
			echo "</tbody></table>\n";
			
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; atividades registradas") . "...</p>\n";
			mysql_close($dblink);
			echo "</div></div>";
			return;
		}
		
		mysql_close($dblink);
		
		echo "</div></div>";
		
		return;
	
	}
	
	function ListaQuestoes($selall, $tesid, $usuid, $assid, $bloom, $pAction) {
		
		if ($pAction == "GET") {
			echo "<a href='#' id='textLink' onClick='abrirPag(" . '"prova.php", "pAction=LIST&tesid=' . $tesid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Quest&otilde;es") . "</button></A>\n";
		} else {
			echo "<a href='prova.php' id='textLink'><button type='button' class='btn btn btn-default'>" . _("Atividades") . "</button></A>\n";
		}
		if ($pAction == "LIST") {
			echo "<a href='#' id='textLink' onClick='abrirPag(" . '"prova.php", "pAction=GET&tesid=' . $tesid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Associar novas quest&otilde;es") . "</button></A>\n";
			echo "<a href='#' id='textLink' onClick='abrirPag(" . '"prova.php", "pAction=CREATE&tesid=' . $tesid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Criar novas quest&otilde;es") . "</button></A>\n";
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Quest&otilde;es") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");

		if ($pAction == "LIST") {
			$sql = "SELECT q.id, q.assid, q.texto as textoq, tq.texto as textotq, q.tipo, a.descricao, tq.valor, q.bloom FROM tesque tq INNER JOIN questoes q ON (tq.queid = q.id) 
			LEFT JOIN assunto a ON q.assid = a.id WHERE tq.tesid = '$tesid'";
		} elseif ($pAction == "GET") {
			$sql = "SELECT  q.id, q.assid, q.texto as textoq, q.tipo, a.descricao, q.bloom FROM questoes q LEFT JOIN assunto a ON (q.assid = a.id) 
			WHERE q.usuid = '$usuid' AND q.id NOT IN (SELECT queid FROM tesque WHERE tesid = '$tesid')";
		}
		
		if (!empty($assid) and $assid != 0) {
			$sql .= " AND q.assid = '$assid'";
		}
		
		if (!empty($bloom) and $bloom != 0) {
			$sql .= " AND q.bloom = '$bloom'";
		}
		
		$sql .= " ORDER BY 3";
		
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			echo "<form action='prova.php' method='POST'>\n";
			echo "<p><label for='assunto'>" . _("Assunto") . "</label>\n";
			echo "<select onchange='submit()' name='assid' class='form-control'>";
			echo "<option value='0' selected>" . _("Todos") . "</option>\n";
			$sql = "SELECT id, descricao FROM assunto WHERE usuid = '$usuid' ORDER BY 2";
			$resulta = mysql_query($sql, $dblink) or die(mysql_error());
			if (mysql_num_rows($resulta) > 0) {
				while($linha = mysql_fetch_array($resulta)) {
					echo "<option value='" . $linha["id"];
					if ($linha["id"] == $assid) {
						echo "' selected>";
					} else {
						echo "'>";
					}
					echo $linha["descricao"] . "</option>\n";
				}
			}
			echo "</select>\n";
			echo "<input type='hidden' name='pAction' value='$pAction'>\n";
			echo "<input type='hidden' name='tesid' value='$tesid'></p></form>\n";
						
			echo "<form action='prova.php' method='POST'>\n";
			echo "<p><label for='bloom'>" . _("Habilidade, segundo Taxonomia de Bloom") . "</label>\n";
			echo "<select onchange='submit()' name='bloom' class='form-control'>";
			for ($i = 0; $i < 7; $i++) {
				echo "<option value='$i'";
				if ($bloom == $i) {
					echo " selected>";
				} else {
					echo ">";
				}
				switch ($i) {
					case 0:
						echo _("Todos");
						break;
					case 1:
						echo _("Conhecimento");
						break;
					case 2:
						echo _("Compreens&atilde;o");
						break;
					case 3:
						echo _("Aplica&ccedil;&atilde;o");
						break;
					case 4:
						echo _("An&aacute;lise");
						break;
					case 5:
						echo _("Avalia&ccedil;&atilde;o");
						break;
					case 6:
						echo _("S&iacute;ntese");
						break;
				}
				echo "</option>";
			}
			echo "</select>\n";
			echo "<input type='hidden' name='pAction' value='$pAction'>\n";
			echo "<input type='hidden' name='assid' value='$assid'>\n";
			echo "<input type='hidden' name='tesid' value='$tesid'></p></form>\n";
			
			echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" . _("Altera dados da quest&atilde;o") . "\n";
			echo "<br><span class='glyphicon glyphicon-th-list' aria-hidden='true'></span>&nbsp;" . _("Inclus&atilde;o de alternativas") . "\n";
			
			echo "<br><br><small><table class='table table-condensed table'><thread><tr>\n";
			echo "<th width='5%'></th><th width='5%'></th><th>" . _("Quest&otilde;es") . "</th>";
			
			if ($pAction == "LIST") {
				echo "<th width='10%'>" . _("Valor") . "</th>";
			}
			
			echo "</tr></thread></tbody>\n";	
			
			if ($pAction == "GET") {
				echo "<form action='prova.php' method='POST'>\n";
				echo "<input type='hidden' name='tesid' value='$tesid'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTEN'>\n";
			} elseif ($pAction == "LIST")  {
				echo "<form action='prova.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='tesid' value='$tesid'>\n";
				echo "<input type='hidden' name='pAction' value='MISS'>\n";
			}

			while ($linha = mysql_fetch_array($result)) {				
				$queid = $linha["id"];
				if (!empty($linha["textotq"])) {
					$texto = $linha["textotq"];
				} else {
					$texto = $linha["textoq"];
				}
				echo "<tr><td align='right'>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]' CHECKED\n>";
				}
				if ($pAction == "GET") {
					echo "</td><td nowrap><a href='#' onClick='abrirPag(" . '"prova.php", "pAction=GEDIT&tesid=' . $tesid . '&queid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>";
				} else {
					echo "</td><td nowrap><a href='#' onClick='abrirPag(" . '"prova.php", "pAction=EDIT&tesid=' . $tesid . '&queid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>";
				}
				if ($linha["tipo"] == "A") {
					echo "<a href='#' onClick='abrirPag(" . '"alternativas.php", "queid=' . $linha["id"] . '&tesid=' . $tesid . '")' . "'>
					<span class='glyphicon glyphicon-th-list' aria-hidden='true'></span></a></td>";	
				} else {
					echo "</td>";
				}
				
				echo "<td>$texto<br><span class='label label-info'>\n";
				
				if ($linha["tipo"] == "A") {
					echo _("Alternativas") . "\n";
				} elseif ($linha["tipo"] == "Q") {
					echo _("Arquivo") . "\n";
				} else {
					echo _("Descritiva") . "\n";
				}
				
				echo "</span>&nbsp;<span class='label label-info'>\n";
				
				if (!empty($linha["descricao"])) {
					echo $linha["descricao"];
				}
				
				echo "</span>&nbsp;<span class='label label-info'>\n";
				
				switch ($linha["bloom"]) {
					case 1:
						echo _("Conhecimento");
						break;
					case 2:
						echo _("Compreens&atilde;o");
						break;
					case 3:
						echo _("Aplica&ccedil;&atilde;o");
						break;
					case 4:
						echo _("An&aacute;lise");
						break;
					case 5:
						echo _("Avalia&ccedil;&atilde;o");
						break;
					case 6:
						echo _("S&iacute;ntese");
						break;
				}
				
				echo "</span></td>";
				
				if ($pAction == "LIST") {
					echo "<td width=10% nowrap>" . $linha["valor"] . "</td></tr>";
				}
				
			}
			
			echo "</tbody></table></small>\n";
			
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; quest&otilde;es registradas") . "...</p>\n";
			mysql_close($dblink);
			echo "</div></div>";
			return;
		}
		
		mysql_close($dblink);
		
		echo "<table><tr valign='top'>\n" ;
		
		if ($pAction == "GET") {
			echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Associar") . "'></form></td>\n";
		} elseif ($pAction == "LIST") {
			echo "<td><input type='submit' class='btn btn-danger' name='enviar' value='" . _("Desassociar") . "'></form></td>\n" ;
		}
		echo "<td><form action='prova.php' id='selall' method='POST'>\n" ;
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='tesid' value='$tesid'>\n";
		echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'>\n";
		echo "</form></td>\n" ;
		echo "<td><form action='prova.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='tesid' value='$tesid'>\n";
		echo "<input class='btn btn-default' type='submit' value='" . _("Desmarcar todos") . "'>\n" ;
		echo "</form></td></tr></table>\n" ;
		
		echo "</div></div>";

		return;
		
	}
	
	function ListaBloom($tesid, $disid) {
	
		echo "<a href='prova.php'><button type='button' class='btn btn btn-default'>" . _("Atividades") . "</button></A><br><br>\n";
	
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Atividades") . "</h3></div>";
		echo "<div class='panel-body'>";
	
		include("./connectdb.php");
	
		$sql = "SELECT texto, avaliacao FROM teste WHERE id = '$tesid' ORDER BY data";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		$avaliacao = $linha["avaliacao"];
		
		echo "<p class='lead'>" . $linha["texto"] . "</p>";
	
		$sql = "SELECT a.id, a.nome FROM aluno a INNER JOIN disalu da ON (da.aluid = a.id) WHERE da.disid = '$disid' ORDER BY 2";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
	
		if (mysql_num_rows($result) > 0) {
			
			echo "<br><p><strong>" . _("Legenda:") . "</strong><br>";
			echo "<em>[1] " . _("Conhecimento") . "<br>";
			echo "[2] " . _("Compreens&atilde;o") . "<br>";
			echo "[3] " . _("Aplica&ccedil;&atilde;o") . "<br>";
			echo "[4] " . _("An&aacute;lise") . "<br>";
			echo "[5] " . _("Avalia&ccedil;&atilde;o") . "<br>";
			echo "[6] " . _("S&iacute;ntese") . "</p></em>";

			echo "<table class='table'><thread><tr>\n" ;
	
			echo "<th>" . _("Registro") . "</th><th>" . _("Nome") . "</th><th class='text-center'>[1]</th><th class='text-center'>[2]</th>
			<th class='text-center'>[3]</th><th class='text-center'>[4]</th><th class='text-center'>[5]</th><th class='text-center'>[6]</th><th>" . 
			_("Nota") . "</th></tr></thread><tbody>\n";
			
			$notaTotal = 0;
			$conhecimento = 0;
			$compreensao = 0;
			$aplicacao = 0;
			$analise = 0;
			$avaliacao = 0;
			$sintese = 0;
			$num = 0;
	
			while ($linha = mysql_fetch_array($result)) {
	
				if ($avaliacao == 1) {
					$sql = "SELECT ea.equid FROM equialu ea INNER JOIN equipes e ON (e.id = ea.equid) WHERE e.disid = '$disid' AND ea.aluid = '" . $linha["id"] . "'";
					$query =  mysql_query ($sql) or die(mysql_error());
					if (mysql_num_rows($query) > 0) {
						$linhaea = mysql_fetch_array($query);
						$equid = $linhaea["equid"];
					} else {
						$equid = "";
					}
				} else {
					$equid = "";
				}
				
				$aluid = $linha["id"];
				$total = 0;
	
				echo "<tr>\n";
				echo "<td>" . $linha["id"] . "</td>\n";
				echo "<td>" . $linha["nome"] . "</td><td align='center'>";

				$bloom = 1;
				while ($bloom <= 6) {
					
					if ($avaliacao == 1) {
						$sql = "SELECT SUM(d.valor) as valord, SUM(tq.valor) as pontosd FROM tesque tq INNER JOIN descritiva d ON (d.queid = tq.queid) 
						INNER JOIN questoes q ON (tq.queid = q.id) WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND (d.aluid = '$aluid' OR d.equid = '$equid') 
						GROUP BY d.aluid, d.tesid";
					} else {
						$sql = "SELECT SUM(d.valor) as valord, SUM(tq.valor) as pontosd FROM tesque tq INNER JOIN descritiva d ON (d.queid = tq.queid) 
						INNER JOIN questoes q ON (tq.queid = q.id) WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND d.aluid = '$aluid' GROUP BY d.aluid, d.tesid";
					}
					
					$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
					$linhaa = mysql_fetch_array($resulta);
					$valord = $linhaa["valord"];

					if ($avaliacao == 1) {
						$sql = "SELECT SUM(c.valor) as valorc, SUM(tq.valor) as pontosc FROM tesque tq INNER JOIN correcao c ON (c.queid = tq.queid) 
						INNER JOIN questoes q ON (tq.queid = q.id) WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND (c.aluid = '$aluid' OR c.equid = '$equid') 
						GROUP BY c.aluid, c.tesid";
					} else {
						$sql = "SELECT SUM(c.valor) as valorc, SUM(tq.valor) as pontosc FROM tesque tq INNER JOIN correcao c ON (c.queid = tq.queid) 
						INNER JOIN questoes q ON (tq.queid = q.id) WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND c.aluid = '$aluid' GROUP BY c.aluid, c.tesid";
					}
					
					$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
					$linhaa = mysql_fetch_array($resulta);
					$valorc = $linhaa["valorc"];

					if ($avaliacao == 1) {
						$sql = "SELECT SUM(ta.valor) as valorta, SUM(tq.valor) as pontosta FROM tesque tq INNER JOIN testearq ta ON (ta.queid = tq.queid)
						INNER JOIN questoes q ON (tq.queid = q.id) WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND (ta.aluid = '$aluid' OR ta.equid = '$equid')
						GROUP BY ta.aluid, ta.tesid";
					} else {
						$sql = "SELECT SUM(ta.valor) as valorta, SUM(tq.valor) as pontosta FROM tesque tq INNER JOIN testearq ta ON (ta.queid = tq.queid)
						INNER JOIN questoes q ON (tq.queid = q.id) WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND ta.aluid = '$aluid' GROUP BY ta.aluid, ta.tesid";
					}
					
					$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
					$linhaa = mysql_fetch_array($resulta);
					$valorta = $linhaa["valorta"];
					
					$total += $valord+$valorc+$valorta;
					
					echo number_format($total,1) . "</td><td align='center'>";

					if ($bloom == 1) {
						$conhecimento += $total;
					} elseif ($bloom == 2) {
						$compreensao += $total;
					} elseif ($bloom == 3) {
						$aplicacao += $total;
					} elseif ($bloom == 4) {
						$analise += $total;
					} elseif ($bloom == 5) {
						$avaliacao += $total;
					} else {
						$sintese += $total;
					}
					
					$total = 0;
					
					$bloom++;
					
				}
				
				$nota = 0;
	
				$sql = "SELECT SUM(d.valor) as valord FROM tesque tq INNER JOIN descritiva d ON (d.queid = tq.queid)
				WHERE tq.tesid = '$tesid' AND d.tesid = '$tesid' AND (d.aluid ='" . $linha["id"] . "' OR d.equid = '$equid') GROUP BY d.aluid, d.tesid";
				$result_d = mysql_query($sql, $dblink ) or die(mysql_error());
	
				if (mysql_num_rows($result_d) > 0) {
					$linhad = mysql_fetch_array($result_d);
					$nota = $linhad["valord"];
				}
	
				$sql = "SELECT SUM(c.valor) as valorc FROM tesque tq INNER JOIN correcao c ON (c.queid = tq.queid)
				WHERE tq.tesid = '$tesid' AND (c.aluid ='" . $linha["id"] . "' OR c.equid = '$equid') GROUP BY c.aluid, c.tesid";					
				$result_c = mysql_query($sql, $dblink ) or die(mysql_error());
	
				if (mysql_num_rows($result_c) > 0) {
					$linhac = mysql_fetch_array($result_c);
					$nota += $linhac["valorc"];
				}
					
				$sql = "SELECT SUM(ta.valor) as valorta FROM tesque tq INNER JOIN testearq ta ON (ta.queid = tq.queid)
				WHERE ta.tesid = '$tesid' AND (ta.aluid ='" . $linha["id"] . "' OR ta.equid = '$equid') GROUP BY ta.aluid, ta.tesid";						
				$result_ta = mysql_query($sql, $dblink ) or die(mysql_error());
	
				if (mysql_num_rows($result_ta) > 0) {
					$linhata = mysql_fetch_array($result_ta);
					$nota += $linhata["valorta"];
				}
	
				echo number_format($nota,1) . "</td></tr>\n";
				
				$notaTotal += $nota;
				
				$num++;
	
			}
			
			echo "<tr><td></td><td align='right'><strong>" . _("M&Eacute;DIA:") . "</strong></td><td align='center'><strong>" . number_format($conhecimento/$num,1) . 
			"</strong></td><td align='center'><strong>" . number_format($compreensao/$num,1) . 
			"</strong></td><td align='center'><strong>" . number_format($aplicacao/$num,1) .	"</strong></td><td align='center'><strong>" . number_format($analise/$num,1) . 
			"</strong></td><td align='center'><strong>" . number_format($avaliacao/$num,1) . "</strong></td><td align='center'><strong>" . number_format($sintese/$num,1) .
			"</strong></td><td align='center'><strong>" . number_format($notaTotal/$num,1) . "</strong></td></tr>";
				
			echo "</tbody></table>\n";
			
		}
	
		mysql_close($dblink);
	
		echo "</div></div>";
	
		return;
	
	}
	
	function ListaBloomAll($disid) {
	
		echo "<a href='prova.php'><button type='button' class='btn btn btn-default'>" . _("Atividades") . "</button></A><br><br>\n";
	
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Atividades") . "</h3></div>";
		echo "<div class='panel-body'>";
	
		include("./connectdb.php");
	
		$sql = "SELECT a.id, a.nome FROM aluno a INNER JOIN disalu da ON (da.aluid = a.id) WHERE da.disid = '$disid' ORDER BY 2";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
	
		if (mysql_num_rows($result) > 0) {
				
			echo "<p><strong>" . _("Legenda:") . "</strong><br>";
			echo "<em>[1] " . _("Conhecimento") . "<br>";
			echo "[2] " . _("Compreens&atilde;o") . "<br>";
			echo "[3] " . _("Aplica&ccedil;&atilde;o") . "<br>";
			echo "[4] " . _("An&aacute;lise") . "<br>";
			echo "[5] " . _("Avalia&ccedil;&atilde;o") . "<br>";
			echo "[6] " . _("S&iacute;ntese") . "</p></em>";
	
			echo "<table class='table'><thread><tr>\n" ;
	
			echo "<th>" . _("Registro") . "</th><th>" . _("Nome") . "</th><th class='text-center'>[1]</th><th class='text-center'>[2]</th>
				<th class='text-center'>[3]</th><th class='text-center'>[4]</th><th class='text-center'>[5]</th><th class='text-center'>[6]</th><th>" . 
			_("Nota") . "</th></tr></thread><tbody>\n";
				
			$notaTotal = 0;
			$conhecimento = 0;
			$compreensao = 0;
			$aplicacao = 0;
			$analise = 0;
			$avaliacao = 0;
			$sintese = 0;
			$num = 0;
	
			while ($linha = mysql_fetch_array($result)) {
				
				echo "<tr>\n";
				echo "<td>" . $linha["id"] . "</td>\n";
				echo "<td>" . $linha["nome"] . "</td><td align='center'>";
				
				$aluid = $linha["id"];
	
				$nota = 0;
				
				$bloom = 1;
				while ($bloom <= 6) {
					
					$total = 0;
					$valord = 0;
					$valorc = 0;
					$valorta = 0;
						
					$sql = "SELECT SUM(d.valor) as valord, SUM(tq.valor) as pontosd FROM tesque tq INNER JOIN teste t ON tq.tesid = t.id INNER JOIN descritiva d ON (d.queid = tq.queid)
					INNER JOIN questoes q ON (tq.queid = q.id) WHERE t.disid = '$disid' AND d.aluid = '$aluid' AND q.bloom = '$bloom'";
						
					$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
					$linhaa = mysql_fetch_array($resulta);
					$valord += $linhaa["valord"];
					
					$sql = "SELECT SUM(d.valor) as valord, SUM(tq.valor) as pontosd FROM tesque tq INNER JOIN teste t ON (tq.tesid = t.id) INNER JOIN descritiva d ON (d.queid = tq.queid)
					INNER JOIN questoes q ON (tq.queid = q.id) INNER JOIN equialu ea ON (d.equid = ea.equid) WHERE t.disid = '$disid' AND ea.aluid = '$aluid' AND q.bloom = '$bloom'";
	
					$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
					$linhaa = mysql_fetch_array($resulta);
					$valord += $linhaa["valord"];
					
					$sql = "SELECT SUM(c.valor) as valorc, SUM(tq.valor) as pontosd FROM tesque tq INNER JOIN teste t ON tq.tesid = t.id INNER JOIN correcao c ON (c.queid = tq.queid)
					INNER JOIN questoes q ON (tq.queid = q.id) WHERE t.disid = '$disid' AND c.aluid = '$aluid' AND q.bloom = '$bloom'";
					
					$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
					$linhaa = mysql_fetch_array($resulta);
					$valorc += $linhaa["valorc"];
					
					$sql = "SELECT SUM(c.valor) as valorc, SUM(tq.valor) as pontosc FROM tesque tq INNER JOIN teste t ON tq.tesid = t.id INNER JOIN correcao c ON (c.queid = tq.queid)
					INNER JOIN questoes q ON (tq.queid = q.id) INNER JOIN equialu ea ON (c.equid = ea.equid) WHERE t.disid = '$disid' AND ea.aluid = '$aluid' AND q.bloom = '$bloom'"; 
						
					$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
					$linhaa = mysql_fetch_array($resulta);
					$valorc += $linhaa["valorc"];

					$sql = "SELECT SUM(ta.valor) as valorta, SUM(tq.valor) as pontosta FROM tesque tq INNER JOIN teste t ON tq.tesid = t.id INNER JOIN testearq ta ON (ta.queid = tq.queid)
					INNER JOIN questoes q ON (tq.queid = q.id) WHERE t.disid = '$disid' AND ta.aluid = '$aluid' AND q.bloom = '$bloom'";
						
					$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
					$linhaa = mysql_fetch_array($resulta);
					$valorta += $linhaa["valorta"];
						
					$sql = "SELECT SUM(ta.valor) as valorta, SUM(tq.valor) as pontosta FROM tesque tq INNER JOIN teste t ON tq.tesid = t.id INNER JOIN testearq ta ON (ta.queid = tq.queid)
					INNER JOIN questoes q ON (tq.queid = q.id) INNER JOIN equialu ea ON (ta.equid = ea.equid) WHERE t.disid = '$disid' AND ea.aluid = '$aluid' AND q.bloom = '$bloom'"; 
					
					$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
					$linhaa = mysql_fetch_array($resulta);
					$valorta += $linhaa["valorta"];
						
					$total += $valord+$valorc+$valorta;
						
					echo number_format($total,1) . "</td><td align='center'>";
	
					if ($bloom == 1) {
						$conhecimento += $total;
					} elseif ($bloom == 2) {
						$compreensao += $total;
					} elseif ($bloom == 3) {
						$aplicacao += $total;
					} elseif ($bloom == 4) {
						$analise += $total;
					} elseif ($bloom == 5) {
						$avaliacao += $total;
					} else {
						$sintese += $total;
					}
					
					$nota += $total;
						
					$bloom++;
						
				}
	
				echo number_format($nota,1) . "</td></tr>\n";
	
				$notaTotal += $nota;
				$nota = 0;
					
				$num++;
	
			}
				
			echo "<tr><td></td><td align='right'><strong>" . _("M&Eacute;DIA:") . "</strong></td><td align='center'><strong>" . number_format($conhecimento/$num,1) .
				"</strong></td><td align='center'><strong>" . number_format($compreensao/$num,1) . 
				"</strong></td><td align='center'><strong>" . number_format($aplicacao/$num,1) .	"</strong></td><td align='center'><strong>" . number_format($analise/$num,1) . 
				"</strong></td><td align='center'><strong>" . number_format($avaliacao/$num,1) . "</strong></td><td align='center'><strong>" . number_format($sintese/$num,1) .
				"</strong></td><td align='center'><strong>" . number_format($notaTotal/$num,1) . "</strong></td></tr>";
	
			echo "</tbody></table>\n";
				
		}
	
		mysql_close($dblink);
	
		echo "</div></div>";
	
		return;
	
	}
	
	function ExcluiDados($eliminar) {
		if (!empty($eliminar)) {
			include( "./connectdb.php" );
			foreach ($eliminar as $tesid => $valor) {	
				if ($valor == 'on') {
					$SQL = "DELETE FROM teste WHERE id = '$tesid'" ;
					$result = mysql_query( $SQL, $dblink );
				}
			}
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
		}
		return;
	}
	
	function IncluiDados($disid, $texto, $data, $status, $avaliacao, $datav) {
		include( "./connectdb.php" );
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $data, $regs);
		$data = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $datav, $regs);
		$datav = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		$sql = "INSERT INTO teste VALUES (null, '$texto', '$data', null, null, '$status', '$avaliacao', '$disid', '$datav')";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function AlteraDados($tesid, $texto, $data, $status, $avaliacao, $datav) {
		include( "./connectdb.php" );
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $data, $regs);
		$data = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $datav, $regs);
		$datav = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		$sql = "UPDATE teste SET texto = '$texto', data = '$data', status = '$status', avaliacao = '$avaliacao', datav = '$datav' WHERE id = $tesid";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function AlteraQuestoes($queid, $texto, $resposta) {
	
		include( "./connectdb.php" );
	
		$sql = "UPDATE questoes SET texto = '$texto', resposta = '$resposta' WHERE id = '$queid'";
	
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		mysql_close($dblink);
		return;
	}
	
	function CriaQuestoes($tesid, $usuid, $assid, $texto, $tipoq, $resposta, $assunto, $valor, $bloom) {
		
		include( "./connectdb.php" );
		
		if (empty($texto)) {
			echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" .
			_("Campos obrigat&oacute;rios n&atilde;o foram preenchidos. Os dados n&atilde;o foram inclu&iacute;dos") . "...</strong></div><br>";
			mysql_close($dblink);
			return;
		}
		
		if ($assid != "0" and $assid == "NA") {
			$sql = "INSERT INTO assunto VALUES (null, '$assunto', '$usuid')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$sql = "SELECT LAST_INSERT_ID() as assid";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			$assid = $linha["assid"];
		}
		
		if ($assid == "0" or empty($assid)) {
			$assid = 'null';
		}
		
		$sql = "INSERT INTO questoes VALUES (null, $assid, '$texto', '$tipoq', '$resposta', '$usuid', '$bloom')";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		
		$sql = "SELECT LAST_INSERT_ID() as queid";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		$queid = $linha["queid"];
		
		if(!strpos($valor,".")&&(strpos($valor,","))) {
			$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
		}
		
		$sql = "INSERT INTO tesque (tesid, queid, texto, tipo, valor, resposta, id) VALUES ('$tesid', '$queid', '$texto', '$tipoq', '$valor', '$resposta', null)"; 
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$sql = "SELECT valor, tipo FROM tesque WHERE tesid = '$tesid' AND queid = '$queid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result) or die(mysql_error());
		$sql = "SELECT a.id, a.resposta FROM correcao c INNER JOIN alternativa a ON (a.id = c.altid) WHERE c.queid = '$queid'";
		$resulta = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linhaa = mysql_fetch_array($resulta);
		$sql = "UPDATE correcao SET valor = '" . $linhaa["resposta"] * $linha["valor"] . "' WHERE altid = '" . $linhaa["id"] . "' AND tesid = '$tesid'"	;
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		
		echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		
		mysql_close($dblink);
		return;
	}
	
	function EditaQuestoes($tesid, $queid, $texto, $valor, $resposta) {		
		include( "./connectdb.php" );
		if(!strpos($valor,".")&&(strpos($valor,","))) {
			$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
		}
		$sql = "UPDATE tesque SET texto = '$texto', valor = '$valor', resposta = '$resposta' WHERE tesid = '$tesid' AND queid = '$queid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$sql = "SELECT valor, tipo FROM tesque WHERE tesid = '$tesid' AND queid = '$queid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result) or die(mysql_error());
		$sql = "SELECT a.id, a.resposta FROM correcao c INNER JOIN alternativa a ON (a.id = c.altid) WHERE c.queid = '$queid'";
		$resulta = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linhaa = mysql_fetch_array($resulta);
		$sql = "UPDATE correcao SET valor = '" . $linhaa["resposta"] * $linha["valor"] . "' WHERE altid = '" . $linhaa["id"] . "' AND tesid = '$tesid'"	;
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function Associar($assdes, $tesid) {
		include( "./connectdb.php" );
		foreach ($assdes as $queid => $valor) {
			if ($valor == 'on') {
				$sql = "INSERT INTO tesque VALUES ('$tesid', '$queid', null, null, 0, null, null)";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<br><br><div class='alert alert-success' role='danger'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}

	function Desassociar($assdes, $tesid) {
		include( "./connectdb.php" );
		foreach ($assdes as $queid => $valor) {	
			if ($valor == 'on') {
				$sql = "DELETE FROM tesque WHERE queid = '$queid' AND tesid = '$tesid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<br><br><div class='alert alert-success' role='danger'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}
	
	function GravaCorrecao($campos, $aluid, $tesid, $disid) {
		
		include( "./connectdb.php" );
		
		$sql = "SELECT avaliacao FROM teste WHERE id = '$tesid' ORDER BY data";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		if ($linha["avaliacao"] == 1) {
			$sql = "SELECT ea.equid FROM equialu ea INNER JOIN equipes e ON (e.id = ea.equid) WHERE e.disid = '$disid' AND ea.aluid = '$aluid'";
			$query =  mysql_query ($sql) or die(mysql_error());
			if (mysql_num_rows($query) > 0) {
				$linhaea = mysql_fetch_array($query);
				$equid = $linhaea["equid"];
			} else {
				$equid = "";
			}
		} else {
			$equid = "";
		}

		foreach($campos AS $aKey => $aValue) {
			
			$sql = "SELECT tq.tesid, tq.queid, q.tipo, tq.valor FROM tesque tq INNER JOIN questoes q ON (q.id = tq.queid) WHERE tq.tesid = '$tesid' AND tq.queid = '$aKey'";			
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			
			if (mysql_num_rows($result) > 0) {		

				if (empty($equid)) {
					$equid = 'null';
				}
				
				$linha = mysql_fetch_array($result);

				if ($linha["tipo"] == "D") {
					if(!strpos($aValue,".")&&(strpos($aValue,","))) {
						$aValue=substr_replace($aValue, '.', strpos($aValue, ","), 1);
					}
					$sqlq = "UPDATE descritiva SET valor = '$aValue' WHERE (aluid = '$aluid' OR equid = '$equid') AND tesid = '$tesid' AND queid = '$aKey'";
					$resultd = mysql_query( $sqlq, $dblink ) or die (mysql_error());
					$sqlq = "UPDATE descritiva SET comentario = '" . $campos["comentario_".$aKey] . "' WHERE (aluid = '$aluid' OR equid = '$equid') AND tesid = '$tesid' AND queid = '$aKey'";
					$resultd = mysql_query( $sqlq, $dblink ) or die (mysql_error());
				} elseif ($linha["tipo"] == "A") {
					$sqlc = "UPDATE correcao SET comentario = '" . $campos["comentario_".$aKey] . "' WHERE (aluid = '$aluid' OR equid = '$equid') AND tesid = '$tesid' AND queid = '$aKey'";
					$resultc = mysql_query( $sqlc, $dblink ) or die(mysql_error());
				} elseif ($linha["tipo"] == "Q") {
					if(!strpos($aValue,".")&&(strpos($aValue,","))) {
						$aValue=substr_replace($aValue, '.', strpos($aValue, ","), 1);
					}
					$sqlq = "UPDATE testearq SET valor = '$aValue' WHERE (aluid = '$aluid' OR equid = '$equid') AND tesid = '$tesid' AND queid = '$aKey'";
					$resultq = mysql_query( $sqlq, $dblink ) or die (mysql_error());
					$sqlq = "UPDATE testearq SET comentario = '" . $campos["comentario_".$aKey] . "' WHERE (aluid = '$aluid' OR equid = '$equid') AND tesid = '$tesid' AND queid = '$aKey'";
					$resultq = mysql_query( $sqlq, $dblink ) or die (mysql_error());
				}
			}
		}
		mysql_close($dblink);
		echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		
	}
	
	function Corrigir($tesid, $aluid, $disid) {

		include( "./connectdb.php" );
		
		echo "<a href='#' id='textLink' onClick='abrirPag(" . '"prova.php", "pAction=VIEW&tesid=' . $tesid . '")' . "'><button type='button' class='btn btn btn-default'>" .
		_("Atividades para corrigir") . "</button></a><br><br>";
		
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Corre&ccedil;&atilde;o de Atividades") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		$sql = "SELECT nome FROM aluno WHERE id = '$aluid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {
			$linha = mysql_fetch_array($result);			
			echo "<br><br><strong>" . $linha["nome"] . "</strong>";
		} else {
			echo "<br><br>N&atilde;o h&aacute; estudantes registrados ...";
			return;
		}
		
		$sql = "SELECT texto, avaliacao FROM teste WHERE id = '$tesid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {

			$linha = mysql_fetch_array($result);
			
			if ($linha["avaliacao"] == 1) {
				$sql = "SELECT ea.equid FROM equialu ea INNER JOIN equipes e ON (e.id = ea.equid) WHERE e.disid = '$disid' AND ea.aluid = '$aluid'";
				$query =  mysql_query ($sql) or die(mysql_error());
				if (mysql_num_rows($query) > 0) {
					$linhaea = mysql_fetch_array($query);
					$equid = $linhaea["equid"];
				} else {
					$equid = "";
				}
			} else {
				$equid = "";
			}			
			
			echo "<br><br>" . $linha["texto"];
			
			$sql = "SELECT tq.queid, tq.texto as textotq, q.texto as textoq, q.tipo as tipoq, tq.resposta
			FROM tesque tq INNER JOIN questoes q ON (q.id = tq.queid) WHERE tq.tesid = '$tesid' ORDER BY 2";
			
			$result = mysql_query($sql, $dblink) or die(mysql_error());

			if ( mysql_num_rows($result) > 0) {
				
				echo "<form ENCTYPE='multipart/form-data' action='prova.php' method='POST'>\n";
					
				echo "<input type='hidden' name='pAction' value='VERIFIED'>\n";
				echo "<input type='hidden' name='aluid' value='$aluid'>\n";
				echo "<input type='hidden' name='tesid' value='$tesid'>\n";
				
				$aComentario = array();

				while ($linha = mysql_fetch_array($result)) {
				
					if (!empty($linha["textotq"])) {
						$texto = $linha["textotq"];
					} else {
						$texto = $linha["textoq"];
					}
					
					echo "<br>$texto\n";

					if ($linha["tipoq"] == "A") {
						
						$sql = "SELECT texto, id, resposta FROM alternativa WHERE queid = '" . $linha["queid"] . "' ORDER BY 1" ;

						$resulto = mysql_query( $sql, $dblink );

						if (mysql_num_rows($resulto) > 0) {
							
							echo "<table>";
							
							while ($linhao = mysql_fetch_array($resulto)) {

								$sql = "SELECT id, valor, comentario FROM correcao WHERE tesid = '$tesid' AND queid = " . $linha["queid"] . " AND altid = " . 
								$linhao["id"] . " AND (aluid = '$aluid' OR equid = '$equid')";

								$resultc = mysql_query( $sql, $dblink );

								$linhac = mysql_fetch_array($resultc);

								if (mysql_num_rows($resultc) > 0) {
									echo "<tr><td><input type='radio' name='" . $linha["queid"] . "' value='" . $linhao["id"] . "' checked disabled></td>&nbsp;<td>" . $linhao["texto"];
									if ($linhao["resposta"]) {
										echo " <i>(" . _("alternativa correta") . ")</i>";
										echo "<br><input type='hidden' name='" . $linha["queid"] . "' value='" . $linhao["id"] . "'></td></tr>";
									} else {
										echo "</td></tr>";
									}
									$valor = $linhac["valor"];
									if (empty($linhac["comentario"])) {
										$comentario = $linha["resposta"];
									} else {
										$comentario = $linhac["comentario"];
									}
								} else {
									echo "<tr><td><input type=radio name='" . $linha["queid"] . "' value='" . $linhao["id"] . "' disabled></td>&nbsp;<td>" . 
									$linhao["texto"];
									if ($linhao["resposta"]) {
										echo " <i>(" . _("alternativa correta") . ")</i>";
										echo "<br><input type='hidden' name='" . $linha["queid"] . "' value='" . $linhao["id"] . "'></td></tr>";
									} else {
										echo "</td></tr>";
									}
								}
								
							}
							
							echo "</table>";
								
							echo "<br>" . _("Ponto(s)") . "<input type='text' name='valor_" . $linha["queid"] . "' value='$valor' size='6' class='form-control' disabled>";
								
							echo "<br>" . _("Coment&aacute;rio") . ": <textarea rows=5 name='comentario_" . $linha["queid"] . 
							"' class='form-control'>$comentario</textarea><br>";		

							array_push($aComentario, "comentario_" . $linha["queid"]);
						}
					
					} elseif ($linha["tipoq"] == "Q") {
						
						$sql = "SELECT arquivo, valor, comentario FROM testearq WHERE tesid = $tesid and (aluid = '$aluid' OR equid = '$equid')";
						$resulta = mysql_query($sql, $dblink) or die(mysql_error());

						if (mysql_num_rows($result) > 0) {
							
							$linhaa = mysql_fetch_array($resulta);
							$arquivo = $linhaa["arquivo"];
							
							if (strrpos($arquivo, "http://") !== false or strrpos($arquivo, "https://") !== false or strrpos($arquivo, "ftp://") !== false) {
								echo "<br>" . _("Arquivo") . ": <a href='" . $linhaa["arquivo"] . "' target='_blank' id='textLink'>" . $linhaa["arquivo"] . "</a>";
							} else {
								echo "<br>" . _("Arquivo") . ": <a href='arquivos/" . $linhaa["arquivo"] . "' id='textLink'>" . $linhaa["arquivo"] . "</a>";
							}
							
							if (empty($linhaa["comentario"])) {
								$comentario = $linha["resposta"];
							} else {
								$comentario = $linhaa["comentario"];
							}
							
							echo "<br>" . _("Ponto(s)") . " <input type='text' name='" . $linha["queid"] . "' value='" . $linhaa["valor"] . "' size='6' class='form-control'>";
							
							echo "<br>" . _("Coment&aacute;rio") . ": <textarea rows=5 name='comentario_" . $linha["queid"] . "' 
							class='form-control'>$comentario</textarea><br>";
							
							array_push($aComentario, "comentario_" . $linha["queid"]);
						}
						
					} elseif ($linha["tipoq"] == "D") {

						$sql = "SELECT texto, id, valor, comentario FROM descritiva WHERE tesid = '$tesid' AND (aluid = '$aluid' OR equid = '$equid')
						AND queid = " . $linha["queid"] . " ORDER BY 1";

						$resultd = mysql_query( $sql, $dblink );

						if ( mysql_num_rows($resultd) > 0) {
							$linhad = mysql_fetch_array($resultd);
							echo "<br><textarea rows=15 name='" . $linha["queid"] . "' class='form-control' disabled>" . $linhad["texto"] . "</textarea>";
			 			} else {
							echo "<br><textarea rows=15 name='" . $linha["queid"] . "' class='form-control' disabled></textarea>";
						}
						
						if (empty($linhad["comentario"])) {
							$comentario = $linha["resposta"];
						} else {
							$comentario = $linhad["comentario"];
						}
						
						echo "<br>" . _("Ponto(s)") . " <input type='text' name='" . $linha["queid"] . "' value='" . $linhad["valor"] . "' size='6' class='form-control'>";
						
						echo "<br>" . _("Coment&aacute;rio") . ": <textarea rows=5 name='comentario_" . $linha["queid"] . "' 
						class='form-control'>$comentario</textarea><br>";
						
						array_push($aComentario, "comentario_" . $linha["queid"]);
					}
					
					echo "<hr>";
				}

				if ($tipo <> 1) {
					echo "<input type='submit' class='btn btn-default' name='enviarage' value='Enviar'>";
				} else {
					echo "<input type='submit' class='btn btn-default' name='enviarage' value='Enviar' disabled>";
				}
				
				echo "</form>";

			}
		}
		
		mysql_close($dblink);
		
		echo "</div></div>";
		
		return $aComentario;
	}
	
	function AtualizaBloom($tesid, $post) {
		
		include 'connectdb.php';
		
		$sql = "SELECT * FROM tesbloom WHERE tesid = '$tesid' AND competencia = '1'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		if ( mysql_num_rows($result) > 0) {
			$sql = "UPDATE tesbloom SET liberado = '" . $post["conhecimentoLiberado"] . "',
			                            maximo = '" . $post["conhecimentoMaximo"] . "',
			                            desempenho = '" . $post["conhecimentoDesempenho"] . "',
			                            peso = '" . $post["conhecimentoPeso"] ."' WHERE tesid = '$tesid' AND competencia = '1'";
			
		} else {
			$sql = "INSERT tesbloom VALUES ('$tesid', '1', '" . $post["conhecimentoLiberado"] . "', '" . $post["conhecimentoMaximo"] . "', '" . $post["conhecimentoDesempenho"] .
			"', '" . $post["conhecimentoPeso"] . "')";
		}
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		
		$sql = "SELECT * FROM tesbloom WHERE tesid = '$tesid' AND competencia = '2'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		if ( mysql_num_rows($result) > 0) {
			$sql = "UPDATE tesbloom SET liberado = '" . $post["compreensaoLiberado"] . "',
					                    maximo = '" . $post["compreensaoMaximo"] . "',
					                    desempenho = '" . $post["compreensaoDesempenho"] . "',
					                    peso = '" . $post["compreensaoPeso"] ."' WHERE tesid = '$tesid' AND competencia = '2'";
				
		} else {
			$sql = "INSERT tesbloom VALUES ('$tesid', '2', '" . $post["compreensaoLiberado"] . "', '" . $post["compreensaoMaximo"] . "', '" . $post["compreensaoDesempenho"] .
					"', '" . $post["compreensaoPeso"] . "')";
		}
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		
		$sql = "SELECT * FROM tesbloom WHERE tesid = '$tesid' AND competencia = '3'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		if ( mysql_num_rows($result) > 0) {
			$sql = "UPDATE tesbloom SET liberado = '" . $post["aplicacaoLiberado"] . "',
							            maximo = '" . $post["aplicacaoMaximo"] . "',
							            desempenho = '" . $post["aplicacaoDesempenho"] . "',
							            peso = '" . $post["aplicacaoPeso"] ."' WHERE tesid = '$tesid' AND competencia = '3'";
		
		} else {
			$sql = "INSERT tesbloom VALUES ('$tesid', '3', '" . $post["aplicacaoLiberado"] . "', '" . $post["aplicacaoMaximo"] . "', '" . $post["aplicacaoDesempenho"] .
							"', '" . $post["aplicacaoPeso"] . "')";
		}
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		
		$sql = "SELECT * FROM tesbloom WHERE tesid = '$tesid' AND competencia = '4'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		if ( mysql_num_rows($result) > 0) {
			$sql = "UPDATE tesbloom SET liberado = '" . $post["analiseLiberado"] . "',
									    maximo = '" . $post["analiseMaximo"] . "',
									    desempenho = '" . $post["analiseDesempenho"] . "',
									    peso = '" . $post["analisePeso"] ."' WHERE tesid = '$tesid' AND competencia = '4'";
		
		} else {
			$sql = "INSERT tesbloom VALUES ('$tesid', '4', '" . $post["analiseLiberado"] . "', '" . $post["analiseMaximo"] . "', '" . $post["analiseDesempenho"] .
									"', '" . $post["analisePeso"] . "')";
		}
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		
		$sql = "SELECT * FROM tesbloom WHERE tesid = '$tesid' AND competencia = '5'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		if ( mysql_num_rows($result) > 0) {
			$sql = "UPDATE tesbloom SET liberado = '" . $post["avaliacaoLiberado"] . "',
										maximo = '" . $post["avaliacaoMaximo"] . "',
										desempenho = '" . $post["avaliacaoDesempenho"] . "',
										peso = '" . $post["avaliacaoPeso"] ."' WHERE tesid = '$tesid' AND competencia = '5'";
		
		} else {
			$sql = "INSERT tesbloom VALUES ('$tesid', '5', '" . $post["avaliacaoLiberado"] . "', '" . $post["avaliacaoMaximo"] . "', '" . $post["avaliacaoDesempenho"] .
											"', '" . $post["avaliacaoPeso"] . "')";
		}
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		
		$sql = "SELECT * FROM tesbloom WHERE tesid = '$tesid' AND competencia = '6'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		if ( mysql_num_rows($result) > 0) {
			$sql = "UPDATE tesbloom SET liberado = '" . $post["sinteseLiberado"] . "',
										maximo = '" . $post["sinteseMaximo"] . "',
									    desempenho = '" . $post["sinteseDesempenho"] . "',
										peso = '" . $post["sintesePeso"] ."' WHERE tesid = '$tesid' AND competencia = '6'";
		
		} else {
			$sql = "INSERT tesbloom VALUES ('$tesid', '6', '" . $post["sinteseLiberado"] . "', '" . $post["sinteseMaximo"] . "', '" . $post["sinteseDesempenho"] .
													"', '" . $post["sintesePeso"] . "')";
		}
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		
		mysql_close($dblink);
		echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		
		return;
	}
	
	function Visualizar($tesid, $aluid, $equid, $disid) {

		include( "./connectdb.php" );
		
		echo "<a href='prova.php' id='textLink'><button type='button' class='btn btn btn-default'>" . _("Atividades") . "</button></a><br><br>";
		
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Visualiza&ccedil;&atilde;o de Atividades") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		$sql = "SELECT id, nome FROM aluno WHERE id = '$aluid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {
			$linha = mysql_fetch_array($result);			
			echo "<h3>" . $linha["nome"] . "</h3><br><br>";
		}

		$sql = "SELECT texto, avaliacao FROM teste WHERE id = '$tesid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {

			$linha = mysql_fetch_array($result);
			
			if ($linha["avaliacao"] == 1) {
				$sql = "SELECT ea.equid FROM equialu ea INNER JOIN equipes e ON (e.id = ea.equid) WHERE e.disid = '$disid' AND ea.aluid = '$aluid'";
				$query =  mysql_query ($sql) or die(mysql_error());
				if (mysql_num_rows($query) > 0) {
					$linhaea = mysql_fetch_array($query);
					$equid = $linhaea["equid"];
				} else {
					$equid = "";
				}
			} else {
				$equid = "";
			}			
			
			echo $linha["texto"] . "<br><br>";
			
			$sql = "SELECT tq.queid, tq.valor, tq.texto as textotq, q.texto as textoq, q.tipo as tipoq, tq.resposta
			FROM tesque tq INNER JOIN questoes q ON (q.id = tq.queid) WHERE tq.tesid = '$tesid' ORDER BY 4";
			
			$result = mysql_query($sql, $dblink) or die(mysql_error());

			if ( mysql_num_rows($result) > 0) {

				while ($linha = mysql_fetch_array($result)) {
				
					if (!empty($linha["textotq"])) {
						$texto = $linha["textotq"];
					} else {
						$texto = $linha["textoq"];
					}
					
					echo "$texto\n";
					
					echo "<br><strong>" . _("Ponto(s)") . "</strong>: " . $linha["valor"] . "<br><br>";

					if ($linha["tipoq"] == "A") {
						
						$sql = "SELECT texto, id, resposta FROM alternativa WHERE queid = '" . $linha["queid"] . "' ORDER BY 1" ;

						$resulto = mysql_query( $sql, $dblink );

						if (mysql_num_rows($resulto) > 0) {
							
							echo "<table>";

							while ($linhao = mysql_fetch_array($resulto)) {
								
								echo "<tr>";

								$sql = "SELECT id, valor, comentario FROM correcao 
								WHERE (aluid = '$aluid' OR equid = '$equid') AND altid = " . $linhao["id"] . " AND tesid = '$tesid'";
								
								$resultc = mysql_query( $sql, $dblink ) or die(mysql_error());

								$linhac = mysql_fetch_array($resultc);

								if (mysql_num_rows($resultc) > 0) {
									echo "<td><input type='radio' name='" . $linha["queid"] . "' value='" . $linhao["id"] . "' checked disabled>&nbsp;</td><td>" . $linhao["texto"];
									if ($linhao["resposta"] == 1) {
										echo "<i> (" . _("alternativa correta") . ")</i>";
									}
									echo "</td>";
									$valor = $linhac["valor"];
									if (empty($linhac["comentario"])) {
										$comentario = $linha["resposta"];
									} else {
										$comentario = $linhac["comentario"];
									}
								} else {
									echo "<td><input type=radio name='" . $linha["queid"] . "' value='" . $linhao["id"] . "' disabled>&nbsp;</td><td>" . $linhao["texto"];
									if ($linhao["resposta"] == 1) {
										echo "<i> (" . _("alternativa correta") . ")</i>";
									}
									echo "</td>";
								}
								
								echo "</tr>";
							}
							
							echo "</table>";
								
							echo "<br><strong>" . _("Ponto(s)") . "</strong>: " . 
							"<input type='text' name='valor_" . $linha["queid"] . "' value='$valor' class='form-control' size='6' disabled>";
								
							//echo "<br><br>" . _("Coment&aacute;rio") . ": <textarea rows=5 name='comentario_" . $linha["queid"] . "' 
							//class='form-control' disabled>$comentario</textarea><br>";
							
							if (!empty($comentario)) {
								echo "<br><br><strong>" . _("Coment&aacute;rio") . "</strong>: $comentario";
							}
						}
					
					} elseif ($linha["tipoq"] == "Q") {
						
						$sql = "SELECT arquivo, valor, comentario FROM testearq WHERE tesid = $tesid and (aluid = '$aluid' or equid = '$equid')" ;
						$resulta = mysql_query($sql, $dblink) or die(mysql_error());

						if (mysql_num_rows($result) > 0) {
							
							$linhaa = mysql_fetch_array($resulta);
							$arquivo = $linhaa["arquivo"];
							
							//if (strrpos($arquivo, "http://") !== false) {
							echo _("Arquivo") . ":<a href='" . $linhaa["arquivo"] . "' target='_blank'>" . $linhaa["arquivo"] . "</a>";
							//} else {
							//	echo "<br>" . _("Arquivo") . ":<a href='arquivos/" . $linhaa["arquivo"] . "' id='textLink'>" . $linhaa["arquivo"] . "</a>";
							//}
							
							if (empty($linhaa["comentario"])) {
								$comentario = $linha["resposta"];
							} else {
								$comentario = $linhaa["comentario"];
							}
							
							echo "<br><br><strong>" . _("Ponto(s)") . "</strong>: " . "<input type='text' name='" . $linha["queid"] . "' value='" . $linhaa["valor"] . 
							"' size='6' class='form-control' disabled>";
							
							//echo "<br>" . _("Coment&aacute;rio") . ": <textarea rows=5 name='comentario_" . $linha["queid"] . "' 
							//class='form-control' disabled>$comentario</textarea><br>";
							
							if (!empty($comentario)) {
								echo "<br><br><strong>" . _("Coment&aacute;rio") . "</strong>: $comentario";
							}
						}
						
					} elseif ($linha["tipoq"] == "D") {
						
						$sql = "SELECT texto, id, valor, comentario FROM descritiva 
						WHERE tesid = '$tesid' AND (aluid = '$aluid' OR equid = '$equid') AND queid = " . $linha["queid"] . " ORDER BY 1";
						$resultd = mysql_query( $sql, $dblink );

						if ( mysql_num_rows($resultd) > 0) {
							$linhad = mysql_fetch_array($resultd);
							echo "<textarea rows=15 name='" . $linha["queid"] . "' class='form-control' disabled>" . $linhad["texto"] . "</textarea><br>";
			 			} else {
							echo "<textarea rows=15 name='" . $linha["queid"] . "' class='form-control' disabled></textarea><br>";
						}
						
						if (empty($linhad["comentario"])) {
							$comentario = $linha["resposta"];
						} else {
							$comentario = $linhad["comentario"];
						}
						
						echo "<br><strong>" . _("Ponto(s)") . "</strong>: " . 
						" <input type='text' name='" . $linha["queid"] . "' value='" . $linhad["valor"] . "' size='6' class='form-control' disabled>";
						
						//echo "<br>" . _("Coment&aacute;rio") . ":<br><br><textarea rows=5 name='comentario_" . $linha["queid"] . "' 
						//class='form-control' disabled>$comentario</textarea><br>";
						
						if (!empty($comentario)) {
							echo "<br><br><strong>" . _("Coment&aacute;rio") . "</strong>: $comentario";
						}
						
					}
					
					echo "<hr>";
				}

				echo "</form>";

			}
		}
		
		echo "</div></div>";
		
		mysql_close($dblink);
		return;
	}
	
	function Formulario($texto, $data, $status, $avaliacao, $datav) {
		
		echo "<a href='prova.php' id='textLink'><button type='button' class='btn btn btn-default'>" . _("Atividades") . "</button></a><br><br>";
		
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Atividade") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br><br>";
		echo "<p><label for='data'>(*) " . _("Prazo") . "</label>\n";
		echo "<input type='text' name='data' id='data' value='$data' size=10 maxlength=10 class='form-control datepicker'></p>\n";
		echo "<p><label for='datav'>(*) " . _("Visualiza&ccedil;&atilde;o a partir de") . " ...</label>\n";
		echo "<input type='text' name='datav' id='datav' value='$datav' size=10 maxlength=10 class='form-control datepicker'></p>\n";
		echo "<p><label for='texto'>(*) " . _("Nome") . "</label>\n";
		echo "<input type='text' name='texto' value='$texto' maxlength=120 class='form-control'></p>\n";
		echo "<p><label for='detalhe'>(*) " . _("Estado") . "</label><br>\n";
		echo "<select name='status' class='form-control'><br>\n";
		if ($status == 1) {
			echo "<option value='1' selected>" . _("Em andamento") . "</option>";
			echo "<option value='0'>" . _("Em corre&ccedil;&atilde;o") . "</option>";
			echo "<option value='2'>" . _("Corrigido") . "</option></select></p>";
		} else {
			if ($status == 0) {
				echo "<option value='1' selected>" . _("Em andamento") . "</option>";
				echo "<option value='0'>" . _("Em corre&ccedil;&atilde;o") . "</option>";
				echo "<option value='2'>" . ("Corrigido") . "</option></select></p>";
			} else {
				echo "<option value='1'>" . _("Em andamento") . "</option>";
				echo "<option value='0'>" . _("Em corre&ccedil;&atilde;o") . "</option>";
				echo "<option value='2' selected>" . _("Corrigido") . "</option></select></p>";
			}
		}
		echo "<p><label for='detalhe'>(*) " . _("Avalia&ccedil;&atilde;o") . "<br></label><br>\n";
		echo "<select name='avaliacao' class='form-control'>\n";
		if ($avaliacao == 0) {
			echo "<option value='0' selected>" . _("Invidual") . "</option>";
			echo "<option value='1'>" . _("Em equipe") . "</option></select></p>";
		} else {
			echo "<option value='0'>" . _("Invidual") . "</option>";
			echo "<option value='1' selected>" . _("Em equipe") . "</option></select></p>";
		}						
		
		echo "<input type='submit' class='btn btn-default' name='enviarage' value='" . _("Enviar") . "'></form>\n";
		
		echo "</div></div>";
		
		return;
	}
	
	function FormularioQuestao($tesid, $queid, $textoq, $textotq, $valor, $respostaq, $respostatq, $tipoq, $assid, $bloom, $pAction, $usuid) {
		
		if (!empty($textotq)) {
			$texto = $textotq;
		} else {
			$texto = $textoq;
		}
		
		if (!empty($respostatq)) {
			$resposta = $respostatq;
		} else {
			$resposta = $respostaq;
		}
		
		echo "<a href='#' onClick='abrirPag(" . '"prova.php", "pAction=LIST&tesid=' . $tesid . '")' . "'><button type='button' class='btn btn btn-default'>" 
		. _("Quest&otilde;es") . "</button></a>";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Atividade") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br><br>";

		echo "<p><label for='texto'>(*) " . _("Quest&atilde;o (Obs: Caso seja necess&aacute;rio, utilize o") . "&nbsp
		<a href='http://www.codecogs.com/latex/eqneditor.php?lang=pt-br' id='textLink' target='_blank'>" . 
		_("Editor on line de equa&ccedil;&otilde;es") . "LaTex</a>)<br></label>\n";
		echo "<textarea id='texto' name='texto' cols='90' rows='20' class='form-control' style='width:740px;'>$texto</textarea></p>";
				
		echo "<p><label for='tipo'>" . _("Tipo") . "</label>\n";

		if ($pAction == "CREATE") {
			echo "<select name='tipoq' class='form-control'>\n";
		} else {
			echo "<select name='tipoq' disabled class='form-control'>\n";
		}
		if ($tipoq == 'A') {
			echo "<option value='A' selected>" . _("Alternativas") . "</option>\n";
			echo "<option value='Q'>" . _("Arquivo") . "</option>";
			echo "<option value='D'>" . _("Descritiva") . "</option></select></p>\n";			
		} elseif ($tipoq == 'Q') {
			echo "<option value='A'>" . _("Alternativas") . "</option>\n";
			echo "<option value='Q' selected>" . _("Arquivo") . "</option>";
			echo "<option value='D'>" . _("Descritiva") . "</option></select></p>\n";
		} else {
			echo "<option value='A'>" . _("Alternativas") . "</option>\n";
			echo "<option value='Q'>" . _("Arquivo") . "</option>";
			echo "<option value='D' selected>" . _("Descritiva") . "</option></select></p>\n";			
		}
		
		if ($pAction == "CREATE") {
			
			echo "<p><label for='assunto'>" . ("Assunto") . "</label>\n";
			echo "<select onchange='submit()' name='assid' class='form-control'>";
			echo "<option value='0'";
			if ($assid == "0") {
				echo " selected";
			}
			echo ">---</option>\n";
			echo "<option value='NA'";
			if ($assid == "NA") {
				echo " selected";
			}
			echo ">" . _("Novo assunto") . "</option>\n";
			
			include( "./connectdb.php" );
			
			$sql = "SELECT id, descricao FROM assunto WHERE usuid = '$usuid' ORDER BY 2";
			$result = mysql_query($sql, $dblink) or die(mysql_error());
			if (mysql_num_rows($result) > 0) {
				while($linha = mysql_fetch_array($result)) {
					echo "<option value='" . $linha["id"];
					if ($linha["id"] == $assid) {
						echo "' selected>";
					} else {
						echo "'>";
					}
					echo $linha["descricao"] . "</option>\n";
				}
			}
			echo "</select></p>\n";
			
			mysql_close($aDBLink);
			
		}
				
		echo "<p><label for='bloom'>" . _("Habilidades, segundo Taxonomia de Bloom") . "</label>\n";
		
		if ($pAction == "GEDIT") {
			echo "<select name='bloom' class='form-control' disabled>\n";
		} else {
			echo "<select name='bloom' class='form-control'>\n";
		}
		
		for ($i = 0; $i < 7; $i++) {
			echo "<option value='$i'";
			if ($bloom == $i) {
				echo "selected>";
			} else {
				echo ">";
			}
			
			switch ($i) {
				case 1:
					echo _("Conhecimento");
					break;
				case 2:
					echo _("Compreens&atilde;o");
					break;
				case 3:
					echo _("Aplica&ccedil;&atilde;o");
					break;
				case 4:
					echo _("An&aacute;lise");
					break;
				case 5:
					echo _("Avalia&ccedil;&atilde;o");
					break;
				case 6:
					echo _("S&iacute;ntese");
					break;				
			}
			echo "</option>";
		}
		
		echo "</select></p>\n";
		
		if ($assid == "NA") {
			echo "<br><input type='text' name='assunto' size=60 maxlength=69 class='form-control'></p>\n";
		}

		if ($pAction != "GEDIT") {
			echo "<p><label for='valor'>" . _("Valor") . "</label>\n";
			echo "<input type='text' name='valor' value='$valor' size=6 maxlength=6 class='form-control'></p>\n";
		}
		
		echo "<p><label for='resposta'>" . _("Resposta ou Coment&aacute;rio") . "</label><br>\n";
		echo "<textarea name='resposta' cols='105' rows='20' class='form-control'>$resposta</textarea></p>";
		echo "<input type='submit' class='btn btn-default' name='enviar' value='" . _("Enviar") . "'></form>\n";
		
		echo "</div></div>";
		
		return;
		
	}
	
	function FormularioBloom($tesid) {
	
		echo "<a href='prova.php' id='textLink'><button type='button' class='btn btn btn-default'>" . _("Atividades") . "</button></a><br><br>";

		include 'connectdb.php';
		
		$sql = "SELECT texto FROM teste WHERE id = '$tesid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		
		echo "<p class='lead'>" . $linha["texto"] . "</p>";
		
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Estrat&eacute;gia avaliativa") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		//echo "<table class='table'><thread><tr><th></th><th colspan='2' class='text-center'>" . _("N&uacute;mero de quest&otilde;es") . "</th><th></th><th></th></tr>"; 
		//echo "<tr><th class='text-center'>" . _("Compet&ecirc;ncias") . "</th><th class='text-center'>" . _("Para libera&ccedil;&atilde;o") . 
		//"</th><th class='text-center'>" . _("Limite") . "</th><th class='text-center'>" . _("Desempenho m&iacute;nimo") . 
		//"</th><th class='text-center'>" . _("Peso") . "</th></tr></thread><tbody>";
		
		echo "<table class='table'><thread><tr><th class='lead'>" . _("Compet&ecirc;ncias") . "</th><th class='text-center lead'>" . _("Peso") . "</th></tr></thread><tbody>";
		
		$sql = "SELECT liberado, maximo, desempenho, peso FROM tesbloom WHERE tesid = '$tesid' AND competencia = '1'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		
		//$liberado = $linha["liberado"];
		//$maximo = $linha["maximo"];
		//$desempenho = $linha["desempenho"];
		$peso = $linha["peso"];
		
		echo "<tr><td class='lead'>" . _("Conhecimento") . "</td>";
		/*echo "<td><input type='text' name='conhecimentoLiberado' value='$liberado' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='conhecimentoMaximo' value='$maximo' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='conhecimentoDesempenho' value='$desempenho' placeholder='%' maxlength=4 class='form-control'></td>\n";*/
		echo "<td width='10%'><input type='text' name='conhecimentoPeso' value='$peso' maxlength=4 class='form-control'></td></tr>\n";
		
		$sql = "SELECT liberado, maximo, desempenho, peso FROM tesbloom WHERE tesid = '$tesid' AND competencia = '2'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		
		//$liberado = $linha["liberado"];
		//$maximo = $linha["maximo"];
		//$desempenho = $linha["desempenho"];
		$peso = $linha["peso"];
		
		echo "<tr><td class='lead'>" . _("Compreens&atilde;o") . "</td>";
		/*echo "<td><input type='text' name='compreensaoLiberado' value='$liberado' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='compreensaoMaximo' value='$maximo' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='compreensaoDesempenho' value='$desempenho' placeholder='%' maxlength=4 class='form-control'></td>\n";*/
		echo "<td width='10%'><input type='text' name='compreensaoPeso' value='$peso' maxlength=4 class='form-control'></td></tr>\n";
		
		$sql = "SELECT liberado, maximo, desempenho, peso FROM tesbloom WHERE tesid = '$tesid' AND competencia = '3'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		
		//$liberado = $linha["liberado"];
		//$maximo = $linha["maximo"];
		//$desempenho = $linha["desempenho"];
		$peso = $linha["peso"];
		
		echo "<tr><td class='lead'>" . _("Aplica&ccedil;&atilde;o") . "</td>";
		/*echo "<td><input type='text' name='aplicacaoLiberado' value='$liberado' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='aplicacaoMaximo' value='$maximo' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='aplicacaoDesempenho' value='$desempenho' placeholder='%' maxlength=4 class='form-control'></td>\n";*/
		echo "<td width='10%'><input type='text' name='aplicacaoPeso' value='$peso' maxlength=4 class='form-control'></td></tr>\n";
		
		$sql = "SELECT liberado, maximo, desempenho, peso FROM tesbloom WHERE tesid = '$tesid' AND competencia = '4'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		
		//$liberado = $linha["liberado"];
		//$maximo = $linha["maximo"];
		//$desempenho = $linha["desempenho"];
		$peso = $linha["peso"];
		
		echo "<tr><td class='lead'>" . _("An&aacute;lise") . "</td>";
		/*echo "<td><input type='text' name='analiseLiberado' value='$liberado' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='analiseMaximo' value='$maximo' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='analiseDesempenho' value='$desempenho' placeholder='%' maxlength=4 class='form-control'></td>\n";*/
		echo "<td width='10%'><input type='text' name='analisePeso' value='$peso' maxlength=4 class='form-control'></td></tr>\n";
		
		$sql = "SELECT liberado, maximo, desempenho, peso FROM tesbloom WHERE tesid = '$tesid' AND competencia = '5'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		
		//$liberado = $linha["liberado"];
		//$maximo = $linha["maximo"];
		//$desempenho = $linha["desempenho"];
		$peso = $linha["peso"];
		
		echo "<tr><td class='lead'>" . _("Avalia&ccedil;&atilde;o") . "</td>";
		/*echo "<td><input type='text' name='avaliacaoLiberado' value='$liberado' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='avaliacaoMaximo' value='$maximo' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='avaliacaoDesempenho' value='$desempenho' placeholder='%' maxlength=4 class='form-control'></td>\n";*/
		echo "<td width='10%'><input type='text' name='avaliacaoPeso' value='$peso' maxlength=4 class='form-control'></td></tr>\n";		
		
		$sql = "SELECT liberado, maximo, desempenho, peso FROM tesbloom WHERE tesid = '$tesid' AND competencia = '6'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		
		//$liberado = $linha["liberado"];
		//$maximo = $linha["maximo"];
		//$desempenho = $linha["desempenho"];
		$peso = $linha["peso"];
		
		echo "<tr><td class='lead'>" . _("S&iacute;ntese") . "</td>";
		/*echo "<td><input type='text' name='sinteseLiberado' value='$liberado' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='sinteseMaximo' value='$maximo' maxlength=4 class='form-control'></td>\n";
		echo "<td><input type='text' name='sinteseDesempenho' value='$desempenho' placeholder='%' maxlength=4 class='form-control'></td>\n";*/
		echo "<td width='10%'><input type='text' name='sintesePeso' value='$peso' maxlength=4 class='form-control'></td></tr>\n";
		
		echo "</tbody></table>";
		
		echo "<input type='submit' class='btn btn-default' name='enviar' value='" . _("Enviar") . "'></form>\n";
			
		echo "</div></div>";
		
		mysql_close($dblink);
	
		return;
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
		
		echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span>&nbsp;" . _("Atividades") . "</h3></div>";

	if ($pAction == "DELETE") {
		ExcluiDados($eliminar);
		ListaDados($selall, $disid, $tipo, $ra, $equid);
	} elseif ($pAction == "INSERT" or $pAction == "INSERTED") {
		if ($pAction == "INSERTED") {
			IncluiDados($disid, $texto, $data, $status, $avaliacao, $datav);
		}
		echo "<form action='prova.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario(null, null, null, null, null);
	} elseif ($pAction == "CREATE" or $pAction == "CREATED") {
		if ($pAction == "CREATED" and !empty($enviar)) {
			CriaQuestoes($tesid, $id, $assid, $texto, $tipoq, $resposta, $assunto, $valor, $bloom);
			//ListaQuestoes($selall, $tesid, $id, "LIST");
		}
		echo "<form action='prova.php' method='POST'>\n";
		echo "<input type='hidden' name='pAction' value='CREATED'>\n";
		echo "<input type='hidden' name='tesid' value='$tesid'>\n";
		if (!empty($enviar)) {
			FormularioQuestao($tesid, null, null, null, null, null, null, null, null, null, "CREATE", $id);
		} else {
			FormularioQuestao($tesid, $queid, $texto, null, $valor, $resposta, null, $tipoq, $assid, $bloom, "CREATE", $id);
		}
	} elseif ($pAction == "UPDATE" or $pAction == "UPDATED") {
		if ($pAction == "UPDATED") {
			AlteraDados($tesid, $texto, $data, $status, $avaliacao, $datav);
			ListaDados($selall, $disid, $tipo, $ra, $equid);
		} else {
			echo "<form action='prova.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
			echo "<input type='hidden' name='tesid' value='$tesid'>\n";
			include 'connectdb.php';
			$sql = "SELECT id, texto, DATE_FORMAT(data, '%d/%m/%Y') as dataf, tipo, status, avaliacao, DATE_FORMAT(datav, '%d/%m/%Y') as datav 
			FROM teste WHERE id='$tesid' ORDER BY data";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			Formulario($linha["texto"], $linha["dataf"], $linha["status"], $linha["avaliacao"], $linha["datav"]);
		}
	} elseif ($pAction == "EDIT" or $pAction == "EDITED") {
		if ($pAction == "EDITED") {
			EditaQuestoes($tesid, $queid, $texto, $valor, $resposta);
			ListaQuestoes($selall, $tesid, $id, $assid, $bloom, "LIST");
		} else {
			echo "<form action='prova.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='EDITED'>\n";
			echo "<input type='hidden' name='tesid' value='$tesid'>\n";
			echo "<input type='hidden' name='queid' value='$queid'>\n";
			include 'connectdb.php';
			$sql = "SELECT q.id, q.texto as textoq, tq.texto as textotq, tq.valor, q.resposta as respostaq, tq.resposta as respostatq, q.tipo as tipoq, q.assid, q.bloom
			FROM tesque tq INNER JOIN questoes q ON (tq.queid = q.id) WHERE tq.tesid = '$tesid' AND tq.queid='$queid'";
			$result = mysql_query($sql, $dblink) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			FormularioQuestao($tesid, $linha["id"], $linha["textoq"], $linha["textotq"], $linha["valor"], $linha["respostaq"], 
			$linha["respostatq"], $linha["tipoq"], $linha["assid"], $linha["bloom"]);
			mysql_close($dblink);
		}
	} elseif ($pAction == "GEDIT" or $pAction == "GEDITED") {
		if ($pAction == "GEDITED") {
			AlteraQuestoes($queid, $texto, $resposta);
			ListaQuestoes($selall, $tesid, $id, $assid, $bloom, "GET");
		} else {
			echo "<form action='prova.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='GEDITED'>\n";
			echo "<input type='hidden' name='tesid' value='$tesid'>\n";
			echo "<input type='hidden' name='queid' value='$queid'>\n";
			include 'connectdb.php';
			$sql = "SELECT q.id, q.texto as textoq, q.resposta as respostaq, q.tipo as tipoq, q.assid, q.bloom
			FROM questoes q WHERE q.id='$queid'";
			$result = mysql_query($sql, $dblink) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			FormularioQuestao($tesid, $linha["id"], $linha["textoq"], null, $linha["valor"], $linha["respostaq"],
			null, $linha["tipoq"], $linha["assid"], $linha["bloom"], "GEDIT");
			mysql_close($dblink);
		}
	} elseif ($pAction == "GET" or $pAction == "LIST") {
		ListaQuestoes($selall, $tesid, $id, $assid, $bloom, $pAction);
	} elseif ($pAction == "GOTTEN") {
		Associar($assdes, $tesid);
		ListaQuestoes($selall, $tesid, $id, $assid, $bloom, "LIST");
	} elseif ($pAction == "MISS") {
		Desassociar($assdes, $tesid);
		ListaQuestoes($selall, $tesid, $id, $assid, $bloom, "LIST");
	} elseif ($pAction == "VIEW") {
		ListaAtividades($tesid, $disid, $tipo);
	} elseif ($pAction == "VERIFY") {
		$aComentario = Corrigir($tesid, $aluid, $disid);
	} elseif ($pAction == "VERIFIED") {
		GravaCorrecao($_POST, $aluid, $tesid, $disid);
		ListaAtividades($tesid, $disid, $tipo);
	} elseif ($pAction == "SHOW") {
		Visualizar($tesid, $ra, $equid, $disid);
	} elseif ($pAction == "BLOOM") {
		//echo "<form action='prova.php' method='POST'>\n" ;
		//echo "<input type='hidden' name='pAction' value='BLOOMED'>\n";
		//echo "<input type='hidden' name='tesid' value='$tesid'>\n";
		//FormularioBloom($tesid);
		ListaBloom($tesid, $disid);
	//} elseif ($pAction == "BLOOMED") {
	//	AtualizaBloom($tesid, $_POST);
	//	ListaDados($selall, $disid, $tipo, $ra, $equid);
	} elseif ($pAction == "BLOOMALL") {
		ListaBloomAll($disid);
	} else {
		ListaDados($selall, $disid, $tipo, $ra, $equid);
	}

	include_once "ckeditor/ckeditor.php";
	$CKEditor = new CKEditor();
	$CKEditor->basePath = 'ckeditor/';
	$CKEditor->replace("texto");
	$CKEditor->replace("resposta");
	
	foreach ($aComentario as $valor) {
		$CKEditor->replace($valor);
	}
	
	include 'rodape.inc';

?>
