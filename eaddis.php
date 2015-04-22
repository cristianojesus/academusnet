<?php
	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];

	function ListaDados($usuid, $selall, $pAction, $eadid, $disid, $tipo, $assunto_nome) {
	
		include( "./connectdb.php" );
		
		if ($pAction == "SELECT") {
			if (empty($disid)) {
				$sql = "SELECT e.id, e.texto, e.comentario, count(ee.eadid) paginas, a.descricao FROM ead e LEFT JOIN ead ee ON e.id = ee.eadid 
				LEFT JOIN assunto a ON e.assid = a.id WHERE e.usuid = '$usuid' AND e.eadid IS NULL";
			} else {
				$sql = "SELECT e.id, e.texto, e.comentario, count(ee.eadid) paginas, a.descricao FROM ead e LEFT JOIN ead ee ON e.id = ee.eadid 
				INNER JOIN disead de ON de.eadid = e.id LEFT JOIN assunto a ON e.assid = a.id WHERE de.disid = '$disid' AND e.eadid IS NULL";
			}
		} elseif ($pAction == "GET") {
			$sql = "SELECT e.id, e.texto, e.comentario, count(ee.eadid) paginas, a.descricao FROM ead e LEFT JOIN ead ee ON e.id = ee.eadid 
			LEFT JOIN assunto a ON e.assid = a.id WHERE e.usuid = '$usuid' AND e.eadid IS NULL AND e.id NOT IN (SELECT eadid FROM disead WHERE disid = '$disid')";
		} elseif ($pAction == "APPEND") {
			$sql = "SELECT e.eadid, e.id, e.texto, e.comentario, e.pagina FROM ead e INNER JOIN ead ee ON e.eadid = ee.id WHERE e.eadid = '$eadid' ORDER BY e.pagina";
		}
		
		if (!empty($assunto_nome) and $pAction != "APPEND") {
			$sql .= " AND UPPER(a.descricao) LIKE '%" . strtoupper($assunto_nome) . "%'";
		}
		
		if ($pAction != "APPEND") {
			$sql .= "  GROUP BY 1 ORDER BY 2,5";
		}
		
		$result = mysql_query( $sql, $dblink ) or die($pAction . " " . mysql_error());

		if ( mysql_num_rows($result) > 0) {
			
			if ($pAction == "APPEND") {
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" .
				_("Altera dados da p&aacute;gina") . "<br>\n";
			} else {
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" .
				_("Altera dados do t&iacute;tulo") . "\n";
				echo "<br><span class='glyphicon glyphicon-paperclip' aria-hidden='true'></span>&nbsp;" .
				_("Altera lista de p&aacute;ginas") . "\n";
			}
			
			if ($pAction != "APPEND") {
				echo "<form action='eaddis.php' method='POST'>\n";
				echo "<br><p><label>Assunto<br></label>";
				echo "<input type='text' name='assunto_nome' size='30' maxlength='90' class='form-control'>\n";
				echo "<input type='hidden' name='pAction' value='$pAction'>\n";
				echo "<input type='submit' name='enviar' value='Pesquisar' class='btn btn-default'></p></form>\n";
			}
			
			echo "<br><table class='table'><thread><tr>\n" ;

			if ($tipo == 1) {
				echo "<th width=5%></th>";
				echo "<th width=10%></th>";
			}
			
			echo "<th width=75%>T&iacute;tulo</th>\n";
		
			if ($pAction == "SELECT" or $pAction == "GET") {
				echo "<th width=10%>Assunto</th><th align='center'>P&aacute;ginas</th>\n";
			} else if ($pAction == "APPEND") {
				echo "<th width=10%></th></tr></thread><tbody>";
			}

			if ($pAction == "SELECT") {
				echo "<form action='eaddis.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='disid' value='$disid'>\n";
				if (!empty($disid)) {
					echo "<input type='hidden' name='pAction' value='MISS'>\n";
				} else {
					echo "<input type='hidden' name='pAction' value='DELETE'>\n";
				}
			} elseif ($pAction == "GET") {
				echo "<form action='eaddis.php' method='POST'>\n";
				echo "<input type='hidden' name='disid' value='$disid'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTEN'>\n";
			} else {
				echo "<form action='eaddis.php' name='deleteForm' id='deleteForm' method='POST'>\n" ;
				echo "<input type='hidden' name='pAction' value='DELETE'>\n";
				echo "<input type='hidden' name='eadid' value='$eadid'>\n";
			}

			while ($linha = mysql_fetch_array($result)) {

				echo "<tr>\n";
				
				if ($tipo == 1) {
					echo "<td>\n";
					if ( empty( $selall ) ) {
						echo "<input type='checkbox' name='eliassdes[" . $linha["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='eliassdes[" . $linha["id"] . "]' CHECKED\n>";
					}
					echo "</td><td>";
					echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=UPDATE&eadid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>";
					if ($pAction == "SELECT") {
						echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=APPEND&eadid=' . $linha["id"] . '")' . "'>\n";
						echo "<span class='glyphicon glyphicon-paperclip' aria-hidden='true'></span></a>";
					}
					echo "</td>\n";
				}
			
				echo "<td>";
				
				echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=VIEW&pActionDest=' . $pAction . 
				'&disid=' . $disid . '&eadid=' . $linha["id"] . '")' . "'>" . $linha["texto"] . "</a></td>\n";
				
				if ($pAction == "SELECT" or $pAction == "GET") {
					if (!empty($linha["descricao"])) {
						echo "<td>" . $linha["descricao"] . "</td>\n";
					} else {
						echo "<td>---</td>\n";
					}
					echo "<td align='center'>" . ($linha["paginas"]+1) . "</td>\n";
				} else if($pAction == "APPEND") {
					echo "<td>
					<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=UPDATE_UP&pagina=' . $linha["pagina"] . '&eadid=' . $eadid . '")' . "'>
					\n&uarr;</a>&nbsp;&nbsp;
					<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=UPDATE_DOWN&pagina=' . $linha["pagina"] . '&eadid=' . $eadid . '")' . "'>
					\n&darr;</a></td>";
				}
				
				echo "</tr>";
				
			}
			
		} else {
			
			if ($pAction == "APPEND") {
				echo "<p class='lead'>" . _("N&atilde;o h&aacute; p&aacute;ginas cadastradas") . "...\n";
			} else {
				echo "<p class='lead'>" . _("N&atilde;o h&aacute; t&iacute;tulos cadastrados") . "...\n";
			}
			
			mysql_close($dblink);
			
			echo "</div></div>";
			
			return 0;
			
		}
		
		mysql_close($dblink);		
		return 1;
	}
	
	function Visualizar($eadid, $pagina, $disid, $pAction) {
		
		include( "./connectdb.php" );

		if (empty($pagina)) {
			$sql = "SELECT texto as titulo, comentario FROM ead WHERE id = '$eadid'";
		} else {
			$sql = "SELECT e.texto as titulo, ee.comentario, ee.texto as subtitulo FROM ead e INNER JOIN ead ee ON ee.eadid = e.id 
			WHERE ee.eadid = '$eadid' and ee.pagina = '$pagina'";
		}

		$query = mysql_query($sql, $dblink) or die(mysql_error());

		if (mysql_num_rows($query) > 0) {
	
			$linha = mysql_fetch_array($query) or die(mysql_error());
			echo  "<strong><h1>" . $linha["titulo"] . "</h1></strong><br><hr>" ;
		
			$sql = "SELECT MAX(pagina) max_pg, MIN(pagina) min_pg FROM ead WHERE eadid = '$eadid' GROUP BY 'eadid'";
			$query = mysql_query($sql, $dblink) or die(mysql_error());
			$linha_mm = mysql_fetch_array($query);

			echo "<center><a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=VIEW&eadid=' . $eadid . '&pagina=' . $linha_mm["min_pg"] . '")' . "'>\n
			<span class='glyphicon glyphicon-fast-backward' aria-hidden='true'></span></a>";
			if ($pagina > $linha_mm["min_pg"]) {
				echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=VIEW&eadid=' . $eadid . '&pagina=' . ($pagina-1) . '")' . "'>\n";
			} else {
				echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=VIEW&eadid=' . $eadid . '&pagina=' . $linha_mm["min_pg"] . '")' . "'>\n";
			}
			echo "<span class='glyphicon glyphicon-backward' aria-hidden='true'></a>";
			echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=VIEW&eadid=' . $eadid . '")' . "'>\n
			<span class='glyphicon glyphicon-home' aria-hidden='true'></a>";
			if ($pagina < $linha_mm["max_pg"]) {
				echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=VIEW&eadid=' . $eadid . '&pagina=' . ($pagina+1) . '")' . "'>\n";
			} else {
				echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=VIEW&eadid=' . $eadid . '&pagina=' . $linha_mm["max_pg"] . '")' . "'>\n";
			}	
			echo "<span class='glyphicon glyphicon-forward' aria-hidden='true'></a>";
			echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=VIEW&eadid=' . $eadid . '&pagina=' . $linha_mm["max_pg"] . '")' . "'>\n
			<span class='glyphicon glyphicon-fast-forward' aria-hidden='true'></a></center><br>";
			
			if (!empty($pagina)) {
				echo  "<br><strong><h3>" . $linha["subtitulo"] . "</h3></strong><br>" ;
			}

			echo $linha["comentario"];
			
		}

		mysql_close($dblink);
		
		return;
		
	}

	function ExcluiDados($usuid, $eliassdes, $pAction) {
		include( "connectdb.php" );
		if (!empty($eliassdes)) {
			foreach ($eliassdes as $eadid => $valor) {
				if ($valor == 'on') {
					$sql = "DELETE FROM ead WHERE id = '$eadid'" ;
					$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				}
			}
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" .
			_("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		}
		mysql_close($dblink);
	}

	function IncluiDados($usuid, $eadid, $pAction, $texto, $comentario, $assid, $assunto, $disid) {
		include( "./connectdb.php" );
		if (empty($comentario)) {
			echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" .
			_("H&aacute; campos n&atilde;o preenchidos. Os dados n&atilde;o foram inclu&iacute;dos ...") . "</strong></div><br>";
			mysql_close($dblink);
			return 0;
		} else {
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
			
			if (empty($eadid)) {
				$eadid = "null";
			}
			
			$sql = "SELECT MAX(pagina) pg FROM ead WHERE eadid = '$eadid' GROUP BY 'eadid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			if ( mysql_num_rows($result) > 0) {
				$linha = mysql_fetch_array($result);
				$pagina = $linha["pg"] + 1;
			} else {
				$pagina = 1;
			}
			
			$sql = "INSERT INTO ead VALUES (null, '$texto', '$comentario', '$usuid', $eadid, $assid, $pagina)";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			
			$sql = "SELECT LAST_INSERT_ID() as eadid";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			$eadid = $linha["eadid"];
			
			$sql = "INSERT INTO disead VALUES ('$disid', '$eadid')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" .
			_("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		}
		mysql_close($dblink);
		return 1;
	}

	function AlteraDados($eadid, $usuid, $texto, $comentario, $assid, $assunto) {
		if (empty($texto)) {
			echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" .
			_("H&aacute; campos n&atilde;o preenchidos. Os dados n&atilde;o foram inclu&iacute;dos ...") . "</strong></div><br>";
			return 1;
		} else {
			include( "./connectdb.php" );
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
			$sql = "UPDATE ead SET texto = '$texto', comentario = '$comentario', assid = $assid WHERE id = $eadid";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" .
			_("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
			mysql_close($dblink);
			return 0;
		}
	}
	
	function Associar($eliassdes, $disid) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $eadid => $valor) {	
			if ($valor == 'on') {
				$sql = "INSERT INTO disead VALUES ('$disid', '$eadid')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" .
			_("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		} else {
			echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" .
			_("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div><br>";
		}
		mysql_close($dblink);
		return;
	}

	function Desassociar($eliassdes, $disid) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $eadid => $valor) {	
			if ($valor == 'on') {
				$sql = "DELETE FROM disead WHERE eadid = '$eadid' AND disid = '$disid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" .
			_("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		} else {
			echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" .
			_("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div><br>";
		}
		mysql_close($dblink);
		return;
	}

	include( "cabecalho.php" );
	
	if (empty($disid)) {
		include "menup.inc";
	} else {
		include "menu.inc";
	}
	
?>

	<script type="text/javascript">

		$(function(){

			$('#ConfirmarExc').click(function () {
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
					echo "<h4 class='modal-title' id='deleteLabel'>" . _("Notifica&ccedil;&atilde;o") . "</h4>";
					echo "</div>";
					echo "<div class='modal-body'>";
					echo "<p>" . _("Voc&ecirc; optou por excluir um t&iacute;tulo.") . "</p>";
					echo _("Se esta &eacute; a a&ccedil;&atilde;o que deseja executar, ");
					echo _("por favor confirme sua escolha ou cancele a opera&ccedil;&atilde;o para retornar.") . "</p>";
					echo "</div>";
					echo "<div class='modal-footer'>";
					echo "<button type='button' class='btn btn-success' data-dismiss='modal' id='Cancelar'>" . _("Cancelar") . "</button>";
					echo "<button type='button' class='btn btn-danger' id='ConfirmarExc'>" . _("Confirmar") . "</button>";
				?>
				                	
		       </div>
			</div>
		</div>
	</div>

<?php

if (empty($disid)) {

	include( "connectdb.php" );
		
	echo "<div class='jumbotron'>";
	
	$sql = "SELECT nome,email,professor FROM usuario WHERE id = '$id'" ;

	$result = mysql_query( $sql, $dblink );

	if ( mysql_num_rows($result) > 0) {
		$linhau = mysql_fetch_array($result);
		if ($linhau["professor"] == 1) {
			echo "<p><h2>" . _("Professor(a)") . "&nbsp;";
		} else {
			echo "<p><h2>";
		}
		echo $linhau["nome"] . "</h2></p>";
	}
		
	mysql_close($dblink);
		
} else {
		
	include 'dadosdis.inc';
		
}
	
echo "<br><h2 class='blog-post-title'><span class='glyphicon glyphicon-bookmark' aria-hidden='true'></span>&nbsp;" . _("Hipertextos") . "</h2>";

echo "</div>";

if ($tipo == 1) {
	
	if ($pAction == "UPDATE_UP") {
		if ($pagina > 1) {
			include( "./connectdb.php" );
			$sql = "UPDATE ead SET pagina = 0 WHERE eadid = '$eadid' AND pagina = '$pagina'";
			$result = mysql_query( $sql, $dblink ) or die($pAction . " " . mysql_error());
			$sql = "UPDATE ead SET pagina = '$pagina' WHERE eadid = '$eadid' AND pagina = '$pagina'-1";
			$result = mysql_query( $sql, $dblink ) or die($pAction . " " . mysql_error());
			$sql = "UPDATE ead SET pagina = '$pagina'-1 WHERE eadid = '$eadid' AND pagina = '0'";
			$result = mysql_query( $sql, $dblink ) or die($pAction . " " . mysql_error());
			mysql_close($dblink);
		}
		$pAction = "APPEND";
	}
	
	if ($pAction == "UPDATE_DOWN") {
		include( "./connectdb.php" );
		$sql = "SELECT MAX(pagina) pg_max FROM ead WHERE eadid = '$eadid' GROUP BY 'eadid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);					
		if ($pagina < $linha["pg_max"]) {				
			$sql = "UPDATE ead SET pagina = 0 WHERE eadid = '$eadid' AND pagina = '$pagina'";
			$result = mysql_query( $sql, $dblink ) or die($pAction . " " . mysql_error());
			$sql = "UPDATE ead SET pagina = '$pagina' WHERE eadid = '$eadid' AND pagina = '$pagina'+1";
			$result = mysql_query( $sql, $dblink ) or die($pAction . " " . mysql_error());
			$sql = "UPDATE ead SET pagina = '$pagina'+1 WHERE eadid = '$eadid' AND pagina = '0'";
			$result = mysql_query( $sql, $dblink ) or die($pAction . " " . mysql_error());				
		}
		$pAction = "APPEND";
		mysql_close($dblink);
	}
	
	if ($pAction == "DELETE") {
		ExcluiDados($id, $eliassdes, $pAction);
		if (empty($eadid)) {
			$pAction = "SELECT";
		} else {
			$pAction = "APPEND";
		}
	}

	if ($pAction == "INSERTED" and $enviaread == "Enviar") {
		$inclusao = IncluiDados($id, $eadid, $pAction, strip_tags($texto), $comentario, $assid, $assunto, $disid);
		if ($inclusao == 1) {
			$texto = "";
			$comentario = "";
			$assid = '0';
		}
	}
	
	if ($pAction == "MISS") {
		Desassociar($eliassdes, $disid);
		$pAction = "SELECT";
	}
	
	if ($pAction == "GOTTEN") {
		Associar($eliassdes, $disid);
		$pAction = "GET";
	}

	if ($pAction == "UPDATED" and $enviaread == "Enviar") {
		$alteracao = AlteraDados( $eadid, $id, strip_tags($texto), $comentario, $assid, $assunto);
		include( "./connectdb.php" );
		$sql = "SELECT eadid FROM ead WHERE id = '$eadid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		if (mysql_num_rows($result) > 0) {
			$linha = mysql_fetch_array($result);
			$eadid = $linha["eadid"];
		}
		if (empty($eadid)) {
			$pAction = "SELECT";
		} else {				
			$pAction = "APPEND";
		}	
		mysql_close($dblink);
	}
	
	if ($pAction == "SELECT") {
		if (!empty($disid)) {
			echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=GET"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Associar novos t&iacute;tulos") . "</button></A>\n";
		}
		echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=INSERT"' . ")'><button type='button' class='btn btn btn-default'>" .
		_("Incluir novos t&iacute;tulos") . "</button></A>\n";
	} elseif ($pAction == "APPEND")	{
		echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=INSERT&eadid=' . $eadid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
		_("Incluir novas p&aacute;ginas") . "</button></A>\n";
		echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=SELECT"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("T&iacute;tulos associados") . "</button></A><br><br>\n";
	} elseif ($pAction == "GET") {
		echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=SELECT"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("T&iacute;tulos dispon&iacute;veis") . "</button></A><br><br>\n";
	} elseif ($pAction == "VIEW") {
		if ($pActionDest == "VIEW") {
			echo "<br><a href='plano.php'><button type='button' class='btn btn btn-default'>" . _("Plano de aula") . "</button></A><br><br>\n";
		} else {
			echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=SELECT"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("T&iacute;tulos associados") . "</button></A><br><br>\n";
		}
	}
	
	if ($pAction == "INSERT" or $pAction == "INSERTED") {
		if (empty($eadid)) {
			echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=SELECT"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("T&iacute;tulos dispon&iacute;veis") . "</button></A><br><br>\n";
		} else {
			echo "<br><a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=APPEND&eadid=' . $eadid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("P&aacute;ginas dispon&iacute;veis") . "</button></A><br><br>\n";
		}
		echo "<form action='eaddis.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		echo "<input type='hidden' name='eadid' value='$eadid'>\n";
		if ($pAction == "INSERT") {
			$texto = "";
			$comentario = "";
			$assid = "0";
		}
	}

	if ($pAction == "UPDATE" or $pAction == "UPDATED") {
		echo "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=SELECT"' . ")'><button type='button' class='btn btn btn-default'>" .
		_("T&iacute;tulos dispon&iacute;veis") . "</button></A>\n";
		echo "<form id='eaddis' action='eaddis.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
		echo "<input type='hidden' name='eadid' value='$eadid'>\n";
		include( "./connectdb.php" );
		$sql = "SELECT texto, comentario, assid FROM ead WHERE id = '$eadid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		if ( mysql_num_rows($result) > 0) {
			$linha = mysql_fetch_array($result);
			$comentario = $linha["comentario"];
			if ($pAction == "UPDATE") {
				$texto = $linha["texto"];
				$assid = $linha["assid"];
			}
		}
		mysql_close($dblink);
	}		
	
	echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Hipertextos") . "</h3></div>";
	echo "<div class='panel-body'>";
	
	if ($pAction == "INSERT" or $pAction == "INSERTED" or $pAction == "UPDATE" or $pAction == "UPDATED") {

		if (!empty($eadid) and ($pAction == "INSERT" or $pAction == "INSERTED" or $pAction == "UPDATE")) {
			include( "./connectdb.php" );
			$sql = "SELECT texto, eadid FROM ead WHERE id = '$eadid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			if ( mysql_num_rows($result) > 0) {
				$linha = mysql_fetch_array($result);
				echo "<p><h2>" . $linha["texto"] . "</h2></p>";
			}
			mysql_close($dblink);
		}
					
		echo "<p><label for='texto'>(*) " . _("T&iacute;tulo") . "</label>\n";
		echo "<input type='text' name='texto' value='$texto' size=60 maxlength=90 class='form-control'></p>\n";

		if (empty($linha["eadid"])) {
			echo "<p><label for='texto'>Assunto</label>\n";
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
			
			$sql = "SELECT id, descricao FROM assunto WHERE usuid = '$id' ORDER BY 2";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			if ( mysql_num_rows($result) > 0) {
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
			echo "</select>";
		}
		
		mysql_close($dblink);
			
		if ($assid == "NA") {
			echo "<p><label for='assunto'>" . _("Assunto") . "</label>\n";
			echo "<input type='text' name='assunto' size=60 maxlength=69 class='form-control'></p>\n";
		}
			
		// }
		
		echo "<p><label for='comentario'>(*) " . _("Conte&uacute;do") . "</label><br>\n";
		echo "<textarea id='comentario' name='comentario' class='form-control'>$comentario</textarea></p>";
		echo "<p><input type='submit' name='enviaread' id='enviaread' value='" . _("Enviar") . "' class='btn btn-default'></form>\n";
		
	}
	
	if ($pAction == "DELETE" or ($pAction == "UPDATED" and $enviaread == "Enviar") or $pAction == "SELECT" or $pAction == "APPEND") {
		
		if ($pAction == "APPEND") {
		
			include( "./connectdb.php" );	
			$sql = "SELECT texto, comentario FROM ead WHERE id = '$eadid'";
			$query = mysql_query($sql, $dblink) or die(mysql_error());

			if ($linha = mysql_fetch_array($query)) {
				echo "<h2>" . _("T&iacute;tulo") . ":&nbsp;" . $linha["texto"] . "</h2><br>";
				/*if (!empty($linha["comentario"])) {
					$eadid = $linha["id"];
					echo "<script language='JavaScript'>\nfunction popup$eadid() {\n";
					echo "window.open('consmini.php?eadid=$eadid', 'http', 
					'resizable=no,location=no,directories=no,status=no,toolbar=no,menubar=no,scrollbars=yes,width=800,height=600' ) }\n</script>\n";
					echo "<a href='javascript:popup$eadid();'><span class='glyphicon glyphicon-file' aria-hidden='true'></span></h2></a>\n";
				}*/
			}

			mysql_close($dblink);
		
		}
		
	}
	
} else {
	
	echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Hipertextos") . "</h3></div>";
	echo "<div class='panel-body'>";
	
	// echo "<br><a href='detplanprof.php'><button type='button' class='btn btn btn-default'>" . _("Voltar") . "</button></a><br>\n";
}

if ($pAction == "VIEW") {
	Visualizar($eadid, $pagina, $disid, $pActionDest);
}

if ($pAction == "SELECT" or $pAction == "APPEND" or $pAction == "GET") {
	
	$dados = ListaDados($id, $selall, $pAction, $eadid, $disid, $tipo, $assunto_nome);

	echo "</table>";

	if ($tipo == 1) {

		if ($dados == 1) {
			echo  "<table><tr>\n" ;
			if ($pAction == "SELECT" and !empty($disid)) {
				echo "<td><button type='submit' class='btn btn-danger' name='enviar'>" . _("Desassociar") . "</button></form></td>\n";
			} elseif ($pAction == "GET") {
				echo "<td><button type='submit' class='btn btn-default' name='enviar'>" . _("Associar") . "</button></form></td>\n";
			} elseif ($pAction == "SELECT" or $pAction == "APPEND") {
				echo "<td><button type='submit' class='btn btn-danger' name='enviar'>" . _("Excluir") . "</button></form></td>\n";
			}
			echo  "<td><form id='selall' action='eaddis.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='$pAction'>\n";
			echo "<input type='hidden' name='eadid' value='$eadid'>\n";
			echo "<input type='hidden' name='selall' value='1'>\n";
			echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Marcar todos") . "</button>\n";
			echo  "</form></td>\n" ;
			echo  "<td><form id='selall' action='eaddis.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='$pAction'>\n";
			echo "<input type='hidden' name='eadid' value='$eadid'>\n";
			echo "<input type='hidden' name='selall' value='0'>\n";
			echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Desmarcar todos") . "</button>\n";
			echo  "</form></td></tr>\n" ;
			echo "</table>\n";
		}
	}
	
}

include_once "ckeditor/ckeditor.php";
$CKEditor = new CKEditor();
$CKEditor->basePath = 'ckeditor/';
$CKEditor->replace("comentario");

mysql_close($dblink);

echo "</div></div>";

include 'rodape.inc';

?>