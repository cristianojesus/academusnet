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
	
	if (empty($equid)) {
		$sql = "SELECT ea.equid FROM equialu ea INNER JOIN equipes e ON (e.id = ea.equid) WHERE e.disid = '$disid' AND ea.aluid = '$ra'";
		$query =  mysql_query ($sql) or die(mysql_error());
		if (mysql_num_rows($query) > 0) {
			$linhaea = mysql_fetch_array($query);
			$equid = $linhaea["equid"];
		} else {
			$equid = "";
		}
	}
	
	mysql_close($dblink);
	
	if ($tipo == 0 and $pAction != "VIEW") {
		$pAction = "";
	}
	
	function ListaDados($selall, $disid, $tipo, $ra, $equid) {
		
		if ($tipo == 1) {
			echo "<a href='#' onClick='abrirPag(" . '"reunioes.php", "pAction=INSERT_UN&disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Agendar reuni&atilde;o de orienta&ccedil;&atilde;o individual") . "</button></A>\n";
			echo "<a href='#' onClick='abrirPag(" . '"reunioes.php", "pAction=INSERT_GR&disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Agendar reuni&atilde;o de orienta&ccedil;&atilde;o em grupo") . "</button></A>\n";
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Reuni&otilde;es de orienta&ccedil;&atilde;o") . "</h3></div>";
		echo "<div class='panel-body'>";

		include("./connectdb.php");

		$sql = "SELECT r.id, r.aluid, r.dataage as data, DATE_FORMAT(r.dataage, '%d/%m/%Y') as dataage, DATE_FORMAT(r.horaage, '%H:%i') as horaage, 
		r.descricao, r.equid, a.nome as nomea, e.nome as nomee FROM reunioes r LEFT JOIN aluno a ON (a.id = r.aluid) 
		LEFT JOIN equipes e ON (e.id = r.equid) WHERE r.disid = '$disid' ORDER BY 4,5";
		
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {

			if ($tipo == 1) {	
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" .
				_("Altera dados da reuni&atilde;o") . "<br><br>\n";
			}
			
			echo "<table class='table'><thread><tr>\n" ;
		
			if ($tipo == 1) {	
				echo "<th></th><th></th>\n";
			}
		
			echo "<th>" . _("Data") . "</th><th>" . _("Hor&aacute;rio") . "</th><th>" . _("Descri&ccedil;&atilde;o") . "</th><th>" . _("Nome") . "</th></tr></thread><tbody>\n";	

			echo "<form action='reunioes.php' id='deleteForm' name='deleteForm' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='DELETE'>\n";

			while ($linha = mysql_fetch_array($result)) {
				if ($tipo == 0) {
					if ($linha["aluid"] != $ra and $linha["equid"] != $equid) {
						continue;
					}
				}
				echo "<tr>";
				if ($tipo == 1) {
					echo "<td width='5%' align='right'>\n";
					if ( empty( $selall ) ) {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
					}
					echo "</td><td width='5%' nowrap><a href='#' onClick='abrirPag(" . '"reunioes.php", "pAction=UPDATE&reuid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>\n";
				}
				echo "<td>" . $linha["dataage"] . "</td>\n";
				echo "<td>" . $linha["horaage"] . "</td>\n";
				echo "<td><a href='#' onClick='abrirPag(" . '"reunioes.php", "pAction=VIEW&reuid=' . $linha["id"] . '")' . "'>" . $linha["descricao"] . "</a></td>\n";
				if (!empty($linha["nomea"])) {
					echo "<td>" . $linha["nomea"] . "</td></tr>\n";
				} else {
					echo "<td>" . $linha["nomee"] . "<br>\n";
					$sql = "SELECT a.nome FROM equialu ea INNER JOIN aluno a ON (a.id = ea.aluid) WHERE equid = '" . $linha["equid"] . "' ORDER BY 1";
					$resulte = mysql_query($sql, $dblink) or die(mysql_error());
					$i = 1;
					while ($linhae = mysql_fetch_array($resulte)) {
						echo $linhae["nome"];
						if ($i < mysql_num_rows($resulte)) {
							echo ", ";
						}
						$i++;
					}
					echo "</td></tr>\n";
				}
			}
			
			echo "</tbody></table>\n";
			
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; reuni&otilde;es agendadas") . "...\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}		
		
		mysql_close($dblink);
		
		if ($tipo == 1) {
			echo "<table><tr valign='top'>\n" ;
			echo "<td><input type='submit' class='btn btn-danger' name='enviar' value='" . _("Excluir") . "'></form></td>\n" ;
			echo "<td><form action='reunioes.php' id='selall' method='POST'>\n" ;
			echo "<input type='hidden' name='selall' value='1'>\n";
			echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'>\n";
			echo "</form></td>\n" ;
			echo "<td><form action='reunioes.php' id='selall' method='POST'>\n";
			echo "<input type='hidden' name='selall' value='0'>\n";
			echo "<input class='btn btn-default' type='submit' value='" . _("Desmarcar todos") . "'>\n" ;
			echo "</form></td></tr></table>\n" ;
		}

		echo "</div></div>";
		
		return;
	
	}
	
	function ExcluiDados($eliminar) {
		if (!empty($eliminar)) {
			include( "./connectdb.php" );
			foreach ($eliminar as $reuid => $valor) {	
				if ($valor == 'on') {
					$SQL = "DELETE FROM reunioes WHERE id = '$reuid'" ;
					$result = mysql_query( $SQL, $dblink );
				}
			}
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
		}
		return;
	}
	
	function IncluiDados($disid, $aluid, $dataage, $horaage, $descricao, $comentarios, $pendencias, $equid) {
		if (empty($aluid)) {
			$aluid = "null";
		}
		if (empty($equid)) {
			$equid = "null";
		}
		include( "./connectdb.php" );
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $dataage, $regs);
		$dataage = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		$sql = "INSERT INTO reunioes VALUES (null, '$disid', ";
		if ($aluid == "null") {
			$sql .= "null, ";
		} else {
			$sql .= "'$aluid', ";	
		}
		$sql .= "'$dataage', '$horaage', '$descricao', '$comentarios', '$pendencias', '$equid')";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function AlteraDados($reuid, $dataage, $horaage, $descricao, $comentarios, $pendencias) {
		include( "./connectdb.php" );
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $dataage, $regs);
		$dataage = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		$sql = "UPDATE reunioes SET dataage = '$dataage', horaage = '$horaage', descricao = '$descricao', comentarios = '$comentarios', pendencias = '$pendencias' WHERE id = $reuid";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function Visualizar($reuid, $disid) {

		include( "./connectdb.php" );
		
		echo "<a href='#' onClick='abrirPag(" . '"reunioes.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Reuni&otilde;es de orienta&ccedil;&atilde;o") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Reuni&otilde;es de orienta&ccedil;&atilde;o") . "</h3></div>";
		echo "<div class='panel-body'>";

		$sql = "SELECT r.id, r.aluid, r.dataage as data, DATE_FORMAT(r.dataage, '%d/%m/%Y') as dataage, DATE_FORMAT(r.horaage, '%H:%i') as horaage, 
		r.descricao, r.equid, r.comentarios, r.pendencias, a.nome as nomea, e.nome as nomee, r.equid FROM reunioes r LEFT JOIN aluno a ON (a.id = r.aluid) 
		LEFT JOIN equipes e ON (e.id = r.equid) WHERE r.id = '$reuid' ORDER BY 2";
		
		$query =  mysql_query ($sql) or die(mysql_error());
		
		if ( mysql_num_rows($query) > 0) {
			$linha = mysql_fetch_array($query);
			echo "<strong>" . _("Data") . ": </strong>" . $linha["dataage"];
			echo "<br><br><strong>" . _("Hor&aacute;rio") . ": </strong>" . $linha["horaage"];
			echo "<br><br><strong>" . _("Descri&ccedil;&atilde;o") . ": </strong>" . $linha["descricao"];
			if (!empty($linha["nomea"])) {
				echo "<br><br><strong>" . _("Estudante") . ": </strong><br>" . $linha["nomea"];
			} else {
				echo "<br><br><strong>" . _("Equipe") . ": </strong><br><br>" . $linha["nomee"];
			}
			$sql = "SELECT a.nome FROM equialu ea INNER JOIN aluno a ON (a.id = ea.aluid) WHERE ea.equid = '" . $linha["equid"] . "'";
			$query_e =  mysql_query ($sql) or die(mysql_error());
			while ($linha_e = mysql_fetch_array($query_e)) {
				echo "<br>" . $linha_e["nome"];
			}
			if (!empty($linha["comentarios"])) {
				echo "<br><br><strong>" . _("Coment&aacute;rios") . ": </strong><br>" . nl2br($linha["comentarios"]);
			}
			if (!empty($linha["pendencias"])) {
				echo "<br><br><strong>" . _("Pend&ecirc;ncias") . ": </strong><br>" . nl2br($linha["pendencias"]);
			}
		}
		
		echo "</div></div>";
		
		mysql_close($aDBLink);

	}
	
	function Formulario($disid, $aluid, $equid, $dataage, $horaage, $descricao, $comentarios, $pendencias, $pAction) {
		
		echo "<a href='#' onClick='abrirPag(" . '"reunioes.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Reuni&otilde;es de orienta&ccedil;&atilde;o") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Reuni&otilde;es de orienta&ccedil;&atilde;o") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br><br>";
		
		include( "./connectdb.php");
		if (!empty($aluid) or $pAction == "INSERT_UN") {
			$sql = "SELECT a.id, a.nome, da.ativo FROM aluno a INNER JOIN disalu da ON (da.aluid = a.id) WHERE da.disid = '$disid' ORDER BY 2";
			$result = mysql_query($sql, $dblink) or die(mysql_error());
			if (mysql_num_rows($result) > 0) {
				echo "<p><label for='aluid'>(*) " . _("Estudante") . "</label><br><select name='aluid' class='form-control'>";
				while($linha = mysql_fetch_array($result)) {
					if ($linha["ativo"]) {
						echo "<option value='" . $linha["id"];
						if ($linha["id"] == $aluid) {
							echo "' selected>";
						} else {
							echo "'>";
						}
						echo $linha["nome"] . "</option>\n";
					}
				}
			}
			echo "</select></p>\n";
		} elseif ($pAction == "INSERT_GR") {
			$sql = "SELECT e.id, e.nome FROM equipes e WHERE disid = '$disid' ORDER BY 2";
			$result = mysql_query($sql, $dblink) or die(mysql_error());
			if (mysql_num_rows($result) > 0) {
				echo "<p><label for='equid'>(*) " . _("Equipe") . "</label><br><select name='equid' class='form-control'>";
				while($linha = mysql_fetch_array($result)) {
					echo "<option value='" . $linha["id"];
					if ($linha["id"] == $equid) {
						echo "' selected>";
					} else {
						echo "'>";
					}
					echo $linha["nome"] . "</option>\n";
				}
				echo "</select>\n";
			} else {
				echo "<p class='lead'>" . _("N&atilde;o h&aacute; equipes registradas...") . "</p>";
				echo "</div></div>";
				return;
			}
			
		}
		mysql_close($dblink);
		echo "<p><label for='dataage'>(*) " . _("Data") . "</label>\n";
		echo "<input type='text' name='dataage' id='dataage' value='$dataage' size=10 maxlength=10 class='form-control datepicker' required></p>\n";
		echo "<p><label for='horaage'>(*) Hor&aacute;rio (hh:mm)</label>\n";
		echo "<input type='text' name='horaage' id='horaage' value='$horaage' size=5 maxlength=5 class='form-control' required></p>\n";
		echo "<p><label for='descricao'>(*) Descri&ccedil;&atilde;o</label>\n";
		echo "<input type='text' name='descricao' value='$descricao' size=60 maxlength=80 class='form-control' required></p>\n";
		echo "<p><label for='comentarios'>Coment&aacute;rios<br></label><br>\n";
		echo "<textarea name='comentarios' rows='20' class='form-control'>$comentarios</textarea></p>\n";
		echo "<p><label for='pendencias'>Pend&ecirc;ncias<br></label><br>\n";
		echo "<textarea name='pendencias' rows='20' class='form-control'>$pendencias</textarea></p>\n";
		echo "<input type='submit' class='btn btn-default' name='enviarage' value='Enviar'></form>\n";
		
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
						echo "<p>" . _("Voc&ecirc; optou por excluir uma reuni&atilde;o.") . "</p>";
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
		
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-calendar' aria-hidden='true'></span>&nbsp;" . 
	_("Orienta&ccedil;&atilde;o de atividades supervisionadas") . "</h3></div>";
	
	if ($pAction == "DELETE") {
		ExcluiDados($eliminar);
		ListaDados($selall, $disid, $tipo, $ra, $equid);
	} elseif ($pAction == "INSERT_UN" or $pAction == "INSERT_GR" or $pAction == "INSERTED") {
		if ($pAction == "INSERTED") {
			IncluiDados($disid, $aluid, $dataage, $horaage, $descricao, $comentarios, $pendencias, $equid);
		}
		echo "<form action='reunioes.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario($disid, null, null, null, null, null, null, null, $pAction);
	} elseif ($pAction == "UPDATE" or $pAction == "UPDATED") {
		if ($pAction == "UPDATED") {
			AlteraDados($reuid, $dataage, $horaage, $descricao, $comentarios, $pendencias);
			ListaDados($selall, $disid, $tipo, $ra, $equid);
		} else {
			echo "<form action='reunioes.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
			echo "<input type='hidden' name='reuid' value='$reuid'>\n";
			include 'connectdb.php';
			$sql = "SELECT aluid, equid, DATE_FORMAT(dataage, '%d/%m/%Y') as dataage, DATE_FORMAT(horaage, '%H:%i') as horaage, descricao, comentarios, pendencias 
			FROM reunioes WHERE id='$reuid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			Formulario($disid, $linha["aluid"], $linha["equid"], $linha["dataage"], $linha["horaage"], $linha["descricao"], $linha["comentarios"], $linha["pendencias"], null);
			mysql_close($dblink);
		}
	} elseif ($pAction == "VIEW") {
		Visualizar($reuid, $disid);
	} else {
		ListaDados($selall, $disid, $tipo, $ra, $equid);
	}
	
	include 'rodape.inc';

?>
