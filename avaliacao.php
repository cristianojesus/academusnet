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
	mysql_close($dblink);
	
	function ListaDados($selall, $disid, $tipo, $ra) {
		
		if ($tipo == 1) {
			echo "<a href='#' onClick='abrirPag(" . '"avaliacao.php", "pAction=REPORT"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Relat&oacute;rio de notas") . "</button></a>\n";
			echo "<a href='#' onClick='abrirPag(" . '"avaliacao.php", "pAction=INSERT"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Incluir novas avalia&ccedil;&otilde;es") . "</button></A>\n";
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Avalia&ccedil;&atilde;o") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");

		$sql = "SELECT id, texto, peso, periodo, sigla, tipoaval FROM avaliacao WHERE disid = '$disid' ORDER BY 4, 2";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			if ($tipo == 1) {	
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" . _("Altera a avalia&ccedil;&atilde;o") . "\n" ;
				echo "<br><span class='glyphicon glyphicon-tag' aria-hidden='true'></span>&nbsp;Registra notas individualmente\n" ;
				echo "<br><span class='glyphicon glyphicon-tags' aria-hidden='true'></span>&nbsp;Registra notas por grupo\n" ;				
			}			
			
			echo "<br><br><table class='table'><thread><tr>\n" ;
		
			if ($tipo == 1) {	
				echo "<th></th><th></th>\n";
			}
		
			echo /*"<th>Sigla</th>*/"<th>Avalia&ccedil;&atilde;o</th><th class='text-center'>Per&iacute;odo</th><th class='text-center'>Peso</th><th>Tipo</th>\n";

			if ($tipo == 0) {
				echo "<th>Nota</th><th>Total</th>";
			}
			
			echo "</tr></thread><tbody>";

			if ($tipo == 1) {
				echo "<form action='avaliacao.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='DELETE'>\n";
			}
			
			$nota_periodo = 0;
			$nota_total = 0;
			$total = 0;
			$per = 1;

			while ($linha = mysql_fetch_array($result)) {
				
				echo "<tr>";
				
				if (!empty($periodo) and $periodo <> $linha["periodo"]) {
					echo "<tr><td colspan='4' align='right'>Notal Final: ";
					echo "<td align='center' colspan='2'>$nota_total</td></tr>";
					$periodo = $linha["periodo"];
					$nota_total = 0;
					$per++;
				}
				
				if ($tipo == 1) {
					echo "<td width='5%' align='right' nowrap>\n";
					if ( empty( $selall ) ) {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
					}
					echo "</td><td width='5%' align='left' nowrap><a href='#' id='textLink' onClick='abrirPag(" . '"avaliacao.php", "pAction=UPDATE&avalid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>\n";
					echo "<a href='#' onClick='abrirPag(" . '"avaliacao.php", "pAction=LIST&avalid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-tag' aria-hidden='true'></span></a>\n";
					if ($linha["tipoaval"] != 0) {
						echo "<a href='#' id='textLink' onClick='abrirPag(" . '"avaliacao.php", "pAction=GROUP&avalid=' . $linha["id"] . '")' . "'>\n";
						echo "<span class='glyphicon glyphicon-tags' aria-hidden='true'></span></a>\n";
					}
					echo "</td>";
				}
				
				echo /*"<td>" . $linha["sigla"] . "</td>\n*/"<td>";
				
				/* if ($tipo == 1) {
					if ($linha["tipoaval"] == 0) {
						echo "<a href='#' id='textLink' onClick='abrirPag(" . '"avaliacao.php", "pAction=LIST&avalid=' . $linha["id"] . '")' . "'>\n";
					} else {
						echo "<a href='#' id='textLink' onClick='abrirPag(" . '"avaliacao.php", "pAction=GROUP&avalid=' . $linha["id"] . '")' . "'>\n";
					}
				} */
				
				echo $linha["texto"] . "</td>\n";
				echo "<td align='center' width='10%' nowrap>" . $linha["periodo"] . "</td>\n";
				echo "<td align='center' width='10%' nowrap>" . $linha["peso"] . "</td>\n";
				
				if ($linha["tipoaval"] == 0) {
					echo "<td width='10%' nowrap>Individual</td>\n";
				} else {
					echo "<td width='10%' nowrap>Em equipe</td>\n";
				}
				
				if ($tipo == 0) {
					
					$sql = "SELECT nota FROM notas WHERE aluid = '$ra' AND avalid = '" . $linha["id"] . "'";
					$resultn = mysql_query( $sql, $dblink ) or die(mysql_error());
					$linhan = mysql_fetch_array($resultn);	
									
					if (mysql_num_rows($resultn) > 0) {
						echo "<td align='center' width='10%' nowrap>" . number_format($linhan["nota"],2) . "</td>";
						echo "<td align='center' width='10%' nowrap>" . number_format($linhan["nota"] * ($linha["peso"]/10),2) . "</td>";
						$nota_periodo = $linhan["nota"] * ($linha["peso"]/10);
						$nota_total = $nota_total + $nota_periodo;
						$total = $total + $nota_periodo;
					} else {
						echo "<td></td><td></td></tr>";
					}
					
				}
				
				echo "</tr>";
				
			}
			
			if ($tipo == 0) {
				echo "<tr><td colspan='5' align='right'><strong>" . _("Notal Final") . ": </strong></td>";
				echo "<td width='10%' align='center' colspan='2' nowrap><strong>" . number_format($nota_total,2) . "</strong></td></tr>";
				$nota_total = 0;
				echo "<tr><td colspan='5' align='right'><strong>" . _("M&eacute;dia") . ": </strong></td>";
				echo "<td width='10%' align='center' colspan='2' nowrap><strong>" . number_format($total/$per,2) . "</strong></td></tr>";
				$total = 0;
			}
			
			echo "</tbody></table>\n";
			
		} else {
			echo "<p class='lead'>N&atilde;o h&aacute; avalia&ccedil;&otilde;es registradas ...</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		mysql_close($dblink);
		
		if ($tipo == 1) {
			echo "<table><tr valign='top'>\n" ;
			echo "<td><input type='submit' class='btn btn-danger' name='enviar' value='" . _("Excluir") . "'></form></td>\n" ;
			echo "<td><form action='avaliacao.php' id='selall' method='POST'>\n" ;
			echo "<input type='hidden' name='selall' value='1'>\n";
			echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'>\n";
			echo "</form></td>\n" ;
			echo "<td><form action='avaliacao.php' id='selall' method='POST'>\n";
			echo "<input type='hidden' name='selall' value='0'>\n";
			echo "<input class='btn btn-default' type='submit' value='" . _("Desmarcar todos") . "'>\n" ;
			echo "</form></td></tr></table>\n" ;
		}
		
		echo "</div></div>";

		return;
	
	}
	
	function ListaEstudantes($avalid, $disid) {
		
		echo "<a href='#' onClick='abrirPag(" . '"avaliacao.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Itens de avalia&ccedil;&atilde;o") . "</button></A><br>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Avalia&ccedil;&atilde;o") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");
		
		$sql = "SELECT texto, sigla FROM avaliacao WHERE id = '$avalid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);

		echo "<br><h2>" . $linha["texto"];
		
		//if (!empty($linha["sigla"])) {
		//	echo " (" . $linha["sigla"] . ")";
		//}
		
		echo "</h2>";
		
		$sql = "SELECT a.id, a.nome, da.ativo FROM aluno a INNER JOIN disalu da ON (da.aluid = a.id) WHERE da.disid = '$disid' ORDER BY 2";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			echo "<form action='avaliacao.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='FILLED'>\n";
			echo "<input type='hidden' name='disid' value='$disid'>\n";
			echo "<input type='hidden' name='avalid' value='$avalid'>\n";
			
			echo "<br><br><table class='table'><thread><tr>\n" ;
	
			echo "<th>" . _("Registro") . "</th><th>" . _("Nome") . "</th><th align='center' width='10%'>" . _("Nota") . "</th></tr></thread><tbody>\n";	

			while ($linha = mysql_fetch_array($result)) {				
				$sql = "SELECT nota FROM notas WHERE aluid = '" . $linha["id"] . "' AND avalid = '$avalid'";
				$resultn = mysql_query($sql, $dblink ) or die(mysql_error());
				if (mysql_num_rows($resultn) > 0) {
					$linhan = mysql_fetch_array($resultn);
					$nota = $linhan["nota"];
				} else {
					$nota = "";
				}				
				echo "<tr>\n";
				echo "<td>" . $linha["id"] . "</td>\n";
				echo "<td>" . $linha["nome"] . "</td>\n";
				echo "<td align='center'><input type='text' name='nota[" . $linha["id"] . "]' value='$nota' size=6 maxlength=6 class='form-control'";
				if (!$linha["ativo"]) {
					echo " DISABLED></td></tr>\n";
				} else {
					echo "></td></tr>\n";
				}
			}
			
			echo "</tbody></table><input type='submit' class='btn btn-default' name='enviarend' value='" . _("Enviar") . "'>";
			
		} else {
			echo "<br><br>" . _("N&atilde;o h&aacute; estudantes registrados ...") . "\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		echo "</div></div>\n";
		
		mysql_close($dblink);
		
		return;
	
	}
	
	function ListaGrupos($avalid, $disid) {
		
		echo "<a href='#' onClick='abrirPag(" . '"avaliacao.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Itens de avalia&ccedil;&atilde;o") . "</button></A><br>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Avalia&ccedil;&atilde;o") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");
		
		$sql = "SELECT texto, sigla FROM avaliacao WHERE id = '$avalid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);

		echo "<br><h2>" . $linha["texto"];
		
		//if (!empty($linha["sigla"])) {
		//	echo " (" . $linha["sigla"] . ")";
		//}
		
		echo "</h2>";
		
		//$sql = "SELECT id, nome FROM equipes WHERE disid = '$disid' ORDER BY 2";
		
		$sql = "SELECT e.id, e.nome, a.id as ida, a.nome as nomea FROM equipes e INNER JOIN equialu ea ON (ea.equid = e.id) INNER JOIN aluno a ON (ea.aluid = a.id) 
		WHERE e.disid = '$disid' GROUP BY 1 ORDER BY 4";
		
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		
		if (mysql_num_rows($result) > 0) {
			
			echo "<form action='avaliacao.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='FILLED_GROUP'>\n";
			echo "<input type='hidden' name='disid' value='$disid'>\n";
			echo "<input type='hidden' name='avalid' value='$avalid'>\n";
			
			echo "<br><br><table class='table'>\n" ;
	
			echo "<th>" . _("Equipe") . "</th><th align='center' width='10%'>" . _("Nota") . "</th>\n";	

			while ($linha = mysql_fetch_array($result)) {				
				echo "<tr>\n";
				echo "<td>" . $linha["nome"] . "<br>\n";
				$sql = "SELECT a.id, a.nome FROM aluno a INNER JOIN equialu ea ON (ea.aluid = a.id) WHERE ea.equid = '" . $linha["id"] . "' ORDER BY 2";
				$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
				$nome = "";
				while ($linhaa = mysql_fetch_array($resulta)) {
					if (!empty($nome)) {
						echo ",\n";
					}
					echo $linhaa["nome"];
					$nome = $linha["nome"];
					$ra = $linhaa["id"];
				}
				
				$sql = "SELECT nota FROM notas WHERE aluid = '$ra' AND avalid = '$avalid'";
				$resultn = mysql_query($sql, $dblink ) or die(mysql_error());
				if (mysql_num_rows($resultn) > 0) {
					$linhan = mysql_fetch_array($resultn);
					$nota = $linhan["nota"];
				} else {
					$nota = "";
				}
				
				echo "<td align='center'><input type='text' name='nota[" . $linha["id"] . "]' value='$nota' size=6 maxlength=6></td></tr>\n";
			}
			
			echo "</tbody></table><input type='submit' name='enviarend' value='" . _("Enviar") . "'>";
			
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; equipes registradas ...") . "</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}

		echo "</div></div>";
		
		mysql_close($dblink);
		
		return;
	
	}
	
	function RelatorioNotas($disid) {
		
		echo "<a href='#' onClick='abrirPag(" . '"avaliacao.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
		_("Itens de avalia&ccedil;&atilde;o") . "</button></A><br>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Relat&oacute;rio de notas") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include( "./connectdb.php" );
		
		$sql = "SELECT a.id, a.nome FROM aluno a INNER JOIN disalu da ON (da.aluid = a.id) WHERE da.disid = '$disid' ORDER BY 2";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());		

		if ( mysql_num_rows($result) > 0) {
			
			echo "<table width='100%'><thread><tr>\n" ;
			echo "<th width='50%' align='left'>Descri&ccedil;&atilde;o</th>\n";
			echo "<th width='10%' align='center'>Peso</th>\n";
			echo "<th width='10%' align='center'>Periodo</th>\n";
			echo "<th width='10%' align='center'>Nota</th>\n";
			echo "<th width='10%' align='center'>Total</th></tr></thread><tbody>\n";
			
			while ($linha = mysql_fetch_array($result)) {
				
				$nota_periodo = 0;
				$nota_total = 0;
				$total = 0;
				$per = 1;
				
				echo "<tr><td colspan='6'> </td></tr>";
				echo "<tr><td colspan='6' align='left'>" . $linha["id"] . " - " . $linha["nome"] . "</td></tr>";
				echo "<tr><td colspan='6'> </td></tr>";
				
				$sql = "SELECT texto, peso, id, periodo FROM avaliacao WHERE disid = '$disid'";
				$resulta = mysql_query( $sql, $dblink ) or die(mysql_error());
				
				while ($linhaa = mysql_fetch_array($resulta)) {

					if (!empty($periodo) and $periodo <> $linhaa["periodo"]) {
						echo "<tr><td colspan='4' align='right'>Notal Final: ";
						echo "<td width='10%' align='center' colspan='2'>$nota_total</td></tr>";
						$periodo = $linhaa["periodo"];
						$nota_total = 0;
						$per++;
					}
					
					echo "<td width='60%'>" . $linhaa["texto"]. "</td>\n";
					echo "<td width='10%' align='center'>" . $linhaa["peso"] . "</td>\n";
					echo "<td width='10%' align='center'>" . $linhaa["periodo"] . "</td>\n";
					
					$sql = "SELECT nota FROM notas WHERE aluid = '" . $linha["id"] . "' AND avalid = '" . $linhaa["id"] . "'";
					$resultn = mysql_query( $sql, $dblink ) or die(mysql_error());
					$linhan = mysql_fetch_array($resultn);
					
					if ( mysql_num_rows($resultn) > 0) {
						echo "<td width='10%' align='center'>" . number_format($linhan["nota"],2) . "</td>\n";
						echo "<td width='10%' align='center' colspan='2'>" . number_format($linhan["nota"] * ($linhaa["peso"]/10),2) . "</td></tr>\n";
						$nota_periodo = $linhan["nota"] * ($linhaa["peso"]/10);
						$nota_total = $nota_total + $nota_periodo;
						$total = $total + $nota_periodo;
					} else {
						echo "<td></td><td></td></tr>\n";
					}
					
					echo "<TR><TD colspan='7'></TD></TR>\n";
					$periodo = $linhaa["periodo"];
				
				}
				
				echo "<tr><td colspan='4' align='right'><strong>" . _("Notal Final") . ": </strong></td>";
				echo "<td width='10%' align='center' colspan='2'><strong>" . number_format($nota_total,2) . "</strong></td></tr>";
				$periodo = $linhaa["periodo"];
				$nota_total = 0;

				echo "<tr><td colspan='4' align='right'><strong>" . _("M&eacute;dia") . ": </strong></td>";
				echo "<td width='10%' align='center' colspan='2'><strong>" . number_format($total/$per,2) . "</strong></td></tr>";
				$periodo = $linhaa["periodo"];
				$total = 0;

			}
			
			echo "</tbody></table>";

		}

		mysql_close($dblink);

		echo "</div></div>";
		
		return 1;

	}
	
	function ExcluiDados($eliminar) {
		if (!empty($eliminar)) {
			include( "./connectdb.php" );
			foreach ($eliminar as $avalid => $valor) {	
				if ($valor == 'on') {
					$sql = "DELETE FROM avaliacao WHERE id = '$avalid'" ;
					$result = mysql_query( $sql, $dblink );
				}
			}
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
		}
		return;
	}
	
	function IncluiDados($disid, $texto, $peso, $periodo, $sigla, $tipoaval) {
		include( "./connectdb.php" );
		$sql = "INSERT INTO avaliacao VALUES (null, '$disid', '$texto', '$peso', '$periodo', '$sigla', '$tipoaval')";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function AlteraDados($avalid, $texto, $peso, $periodo, $sigla, $tipoaval) {
		include( "./connectdb.php" );
		$sql = "UPDATE avaliacao SET texto = '$texto', peso = '$peso', periodo = '$periodo', sigla = '$sigla', tipoaval = '$tipoaval' WHERE id = $avalid";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function RegistraNotas($avalid, $nota) {

		include( "./connectdb.php" );
		
		foreach ($nota as $aluid => $valor) {
	
			IF (empty($valor)) {
				$sql = "DELETE FROM notas WHERE aluid = '$aluid' AND avalid = $avalid" ;
			} else {
				if(!strpos($valor,".")&&(strpos($valor,","))) {
					$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
				}
				$sql = "REPLACE INTO notas SET nota='$valor', avalid='$avalid', aluid='$aluid'";
			}

			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		}
		mysql_close($dblink);
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
	}
	
	function RegistraNotasGrupos($avalid, $nota) {

		include( "./connectdb.php" );
		
		foreach ($nota as $equid => $valor) {
			
			$sql = "SELECT aluid FROM equialu WHERE equid = '$equid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());

			while ($linha = mysql_fetch_array($result)) {
				IF (empty($valor)) {
					$sql = "DELETE FROM notas WHERE aluid = '" . $linha["aluid"] . "' AND avalid = $avalid" ;
				} else {
					if(!strpos($valor,".")&&(strpos($valor,","))) {
						$valor=substr_replace($valor, '.', strpos($valor, ","), 1);
					}
					$sql = "REPLACE INTO notas SET nota='$valor', avalid='$avalid', aluid='" . $linha["aluid"] . "'";
				}
				$resultn = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		mysql_close($dblink);
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
	}
	
	function Formulario($texto, $peso, $periodo, $sigla, $tipoaval, $disid) {
		
		echo "<a href='#' onClick='abrirPag(" . '"avaliacao.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
		_("Itens de avalia&ccedil;&atilde;o") . "</button></A><br>\n";
		
		echo "<b><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Relat&oacute;rio de notas") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br><br>";

		echo "<p><label for='texto'>(*) Nome</label>\n";
		echo "<input type='text' name='texto' value='$texto' size=60 maxlength=90 class='form-control' autofocus required></p>\n";
		echo "<p><label for='detalhe'>(*) Avalia&ccedil;&atilde;o<br></label><br>\n";
		echo "<select name='tipoaval' class='form-control'>\n";
		if ($tipoaval == 0) {
			echo "<option value='0' selected>Invidual</option>";
			echo "<option value='1'>Em equipe</option></select></p>";
		} else {
			echo "<option value='0'>Invidual</option>";
			echo "<option value='1' selected>Em equipe</option></select></p>";
		}
		//echo "<p><label for='sigla'>Sigla</label>\n";
		//echo "<input type='text' name='sigla' id='sigla' value='$sigla' size=6 maxlength=6 class='ui-widget-content'></p>\n";
		echo "<p><label for='texto'>(*) Per&iacute;odo</label>\n";
		echo "<input type='text' name='periodo' value='$periodo' size=2 maxlength=2 class='form-control' autofocus required></p>\n";
		echo "<p><label for='texto'>(*) Peso</label>\n";
		echo "<input type='text' name='peso' value='$peso' size=4 maxlength=4 class='form-control' autofocus required></p>\n";
		echo "<input type='submit' class='btn btn-default' name='enviarage' value='" . _("Enviar") . "'></form>\n";
		
		echo "</div></div>";
	}
	
	include( "cabecalho.php" );
	
	include( "menu.inc" );
	
	?>
		
		<script type="text/javascript">
	
			$(function(){
	
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
		
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-scale' aria-hidden='true'></span>&nbsp;" . _("Avalia&ccedil;&atilde;o") . "</h3></div>";

	if ($pAction == "DELETE") {
		ExcluiDados($eliminar);
		ListaDados($selall, $disid, $tipo, $ra);
	} elseif ($pAction == "INSERT" or $pAction == "INSERTED") {
		if ($pAction == "INSERTED") {
			IncluiDados($disid, $texto, $peso, $periodo, $sigla, $tipoaval);
		}
		echo "<form action='avaliacao.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario(null, null, null, null, null, $disid);
	} elseif ($pAction == "UPDATE" or $pAction == "UPDATED") {
		if ($pAction == "UPDATED") {
			AlteraDados($avalid, $texto, $peso, $periodo, $sigla, $tipoaval);
			ListaDados($selall, $disid, $tipo, $ra);
		} else {
			echo "<form action='avaliacao.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
			echo "<input type='hidden' name='avalid' value='$avalid'>\n";
			include 'connectdb.php';
			$sql = "SELECT texto, peso, periodo, sigla, tipoaval FROM avaliacao WHERE id='$avalid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			Formulario($linha["texto"], $linha["peso"], $linha["periodo"], $linha["sigla"], $linha["tipoaval"], $disid);
			mysql_close($dblink);
		}
	} elseif ($pAction == "LIST") {
		ListaEstudantes($avalid, $disid);
	} elseif ($pAction == "FILLED_GROUP") {
		RegistraNotasGrupos($avalid, $nota);
		ListaDados($selall, $disid, $tipo, $ra);
	} elseif ($pAction == "GROUP") {
		ListaGrupos($avalid, $disid);
	} elseif ($pAction == "FILLED") {
		RegistraNotas($avalid, $nota);
		ListaDados($selall, $disid, $tipo, $ra);
	} elseif ($pAction == "REPORT") {
		RelatorioNotas($disid);
	} else {
		ListaDados($selall, $disid, $tipo, $ra);
	}
	
	include 'rodape.inc';

?>
