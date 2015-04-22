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
	
	function ListaDados($selall, $disid, $tipo) {
		
		echo "<a href='#' id='textLink' onClick='abrirPag(" . '"equipe.php", "pAction=INSERT"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Criar grupos de trabalho") . "</button></A>\n";

		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Equipes de trabalho") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");

		$sql = "SELECT id, nome FROM equipes WHERE disid = '$disid' ORDER BY 1";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;Altera o nome da equipe\n" ;
			echo "<br><span class='glyphicon glyphicon-tags' aria-hidden='true'></span>&nbsp;Membros da equipe\n" ;

			echo "<br><br><table class='table'><thread><tr>\n" ;
		
			echo "<th></th><th></th><th>" . _("Nome") . "</th></tr></thread><tbody>\n";	

			echo "<form action='equipe.php' id='deleteForm' name='deleteForm' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='DELETE'>\n";

			while ($linha = mysql_fetch_array($result)) {
				echo "<tr>";
				echo "<td width='5%' nowrap>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td><td width='5%' nowrap><a href='#' id='textLink' onClick='abrirPag(" . '"equipe.php", "pAction=UPDATE&equid=' . $linha["id"] . '")' . "'>\n";
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></a>\n";
				echo "<a href='#' id='textLink' onClick='abrirPag(" . '"equipe.php", "pAction=LIST&equid=' . $linha["id"] . '")' . "'>\n";
				echo "<span class='glyphicon glyphicon-tags' aria-hidden='true'></a></td>\n";
				echo "<td>" . $linha["nome"] . "</td></tr>\n";
			}
			
			echo "</tbody></table>\n";
			
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; equipes registradas") . "...</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		mysql_close($dblink);
		
		echo "<table><tr valign='top'>\n" ;
		echo "<td><input type='submit' class='btn btn-danger' name='enviar' value='" . _("Excluir") . "'></form></td>\n" ;
		echo "<td><form action='equipe.php' id='selall' method='POST'>\n" ;
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'>\n";
		echo "</form></td>\n" ;
		echo "<td><form action='equipe.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<input class='btn btn-default' type='submit' value='" . _("Desmarcar todos") . "'>\n" ;
		echo "</form></td></tr></table>\n" ;

		echo "</div></div>";
		
		return;
	
	}
	
	function ListaMembros($selall, $equid, $disid, $pAction) {
		
		echo "<a href='equipe.php' id='textLink'><button type='button' class='btn btn btn-default'>" . ("Equipes de trabalho") . "</button></A>\n";
		
		if ($pAction == "LIST") {
			echo "<a href='#' id='textLink' onClick='abrirPag(" . '"equipe.php", "pAction=GET&disid=' . $disid . '&equid=' . $equid . '"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Definir membros") . "</button></A>\n";
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Equipes de trabalho") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");
		
		if ($pAction == "LIST") {
			$sql = "SELECT a.id, a.nome FROM aluno a INNER JOIN equialu ea ON (ea.aluid = a.id) INNER JOIN disalu da ON (da.aluid = a.id) WHERE ea.equid = '$equid' ORDER BY 2";
		} else {	
			$sql = "SELECT a.id, a.nome, da.ativo FROM aluno a INNER JOIN disalu da ON (da.aluid = a.id) WHERE da.disid = '$disid' 
			AND a.id NOT IN (SELECT ea.aluid FROM equipes e INNER JOIN disciplina d ON (d.id = e.disid) INNER JOIN equialu ea ON (ea.equid = e.id) WHERE e.disid = '$disid') ORDER BY 2";
		}
		
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			echo "<table class='table'><thread><tr>\n" ;
	
			echo "<th width='5%' nowrap></th><th>" . _("Nome") . "</th></tr></thread><tbody>\n";

			if ($pAction == "GET") {
				echo "<form action='equipe.php' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTEN'>\n";
			} else {
				echo "<form action='equipe.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='MISS'>\n";
			}
			echo "<input type='hidden' name='equid' value='$equid'>\n";
			echo "<input type='hidden' name='disid' value='$disid'>\n";

			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td align='right'>\n";
				echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]'";
				if (!$linha["ativo"] and $pAction != "LIST") {
					echo "DISABLED";
				}
				if ( empty( $selall ) ) {
					echo ">\n";
				} else {
					echo " CHECKED>\n";
				}
				echo "<td>" . $linha["nome"] . "</td></tr>\n";
			}
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; membros registrados") . "...</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		echo "</tbody></table>\n";
		
		mysql_close($dblink);
		
		echo "<table><tr valign='top'>\n" ;
		if ($pAction == "GET") {
			echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Associar") . "'></form></td>\n" ;
		} else {
			echo "<td><input type='submit' class='btn btn-danger' name='enviar' value='" . _("Desassociar") . "'></form></td>\n" ;
		}
		echo "<td><form action='equipe.php' id='selall' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='equid' value='$equid'>\n";
		echo "<input type='hidden' name='disid' value='$disid'>\n";
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'>\n";
		echo "</form></td>\n" ;
		echo "<td><form action='equipe.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='equid' value='$equid'>\n";
		echo "<input type='hidden' name='disid' value='$disid'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<input class='btn btn-default' type='submit' value='" . _("Desmarcar todos") . "'>\n" ;
		echo "</form></td></tr></table>\n" ;
		
		echo "</div></div>";

		return;
	
	}

	function ExcluiDados($eliminar, $aluid) {
		include( "./connectdb.php" );
		foreach ($eliminar as $equid => $valor) {
			if ($valor == 'on') {
				if (!empty($aluid)) {
					$sql = "SELECT * FROM equialu WHERE equid = '$equid'";
					$result = mysql_query( $sql, $dblink ) or die(mysql_error());
					if (mysql_num_rows($result) > 0) {
						$sql = "SELECT * FROM equialu WHERE aluid = '$aluid' AND equid = '$equid'" ;
						$result = mysql_query( $sql, $dblink ) or die(mysql_error());
						if (mysql_num_rows($result) == 0) {
							echo  "<div class='alert alert-danger' role='alert'><strong>" . 
							_("Voc&ecirc; n&atilde;o pode alterar um grupo de trabalho do qual n&atilde;o faz parte ...") . "</strong></div>" ;
							return;
						}
					}
				}
				$SQL = "DELETE FROM equipes WHERE id = '$equid'" ;
				$result = mysql_query( $SQL, $dblink );
			}
		}
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function IncluiDados($disid, $nome, $aluid) {
		include( "./connectdb.php" );
		$sql = "INSERT INTO equipes VALUES (null, '$disid', '$nome')";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		if (!empty($aluid)) {
			$sql = "SELECT LAST_INSERT_ID() as equid";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			$equid = $linha["equid"];
			$sql = "INSERT INTO equialu VALUES ('$equid', '$aluid')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		}
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function AlteraDados($equid, $nome, $aluid) {
		include( "./connectdb.php" );
		if (!empty($aluid)) {
			$sql = "SELECT * FROM equialu WHERE equid = '$equid'" ;
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			if (mysql_num_rows($result) > 0) {
				$sql = "SELECT * FROM equialu WHERE aluid = '$aluid' AND equid = '$equid'" ;
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				if (mysql_num_rows($result) == 0) {
					echo  "<div class='alert alert-danger' role='alert'><strong>" . 
					_("Voc&ecirc; n&atilde;o pode alterar um grupo de trabalho do qual n&atilde;o faz parte ...") . "</strong></div>" ;
					return;
				}
			}
		}
		$sql = "UPDATE equipes SET nome = '$nome' WHERE id = '$equid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function Associar($assdes, $equid, $aluid) {
		include( "./connectdb.php" );
		if (!empty($aluid)) {
			$sql = "SELECT * FROM equialu WHERE equid = '$equid'" ;
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			if (mysql_num_rows($result) > 0) {
				$sql = "SELECT * FROM equialu WHERE aluid = '$aluid' AND equid = '$equid'" ;
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				if (mysql_num_rows($result) == 0) {
					echo  "<div class='alert alert-danger' role='alert'><strong>" . 
					_("Voc&ecirc; n&atilde;o pode alterar um grupo de trabalho do qual n&atilde;o faz parte ...") . "</strong></div>" ;
					return;
				}
			}
		}
		foreach ($assdes as $aluid => $valor) {	
			if ($valor == 'on') {
				$sql = "INSERT INTO equialu VALUES ('$equid', '$aluid')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos um estudante ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}
	
	function Desassociar($assdes, $equid, $aluid) {
		include( "./connectdb.php" );
		if (!empty($aluid)) {
			$sql = "SELECT * FROM equialu WHERE aluid = '$aluid' AND equid = '$equid'" ;
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			if (mysql_num_rows($result) == 0) {
				echo  "<div class='alert alert-danger' role='alert'><strong>" . 
					_("Voc&ecirc; n&atilde;o pode alterar um grupo de trabalho do qual n&atilde;o faz parte ...") . "</strong></div>" ;
				return;
			}
		}
		foreach ($assdes as $aluid => $valor) {	
			if ($valor == 'on') {
				$sql = "DELETE FROM equialu WHERE aluid = '$aluid' AND equid = '$equid'" ;
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos um estudante ...") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}
	
	function Formulario($nome) {
		echo "<a href='equipe.php' id='textLink'><button type='button' class='btn btn btn-default'>" . ("Equipes de trabalho") . "</button></A>\n";

		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Equipes de trabalho") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br><br>";
		
		echo "<p><label for='texto'>(*) Nome da Equipe</label>\n";
		echo "<input type='text' name='nome' value='$nome' size=60 maxlength=400 class='form-control' autofocus required></p>\n";
		echo "<input type='submit' class='btn btn-default' name='enviarage' value='" . _("Enviar") . "'></form>\n";
		
		echo "</div></div>";
		
		return;
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
					echo "<p>" . _("Voc&ecirc; optou por excluir uma equipe.") . "</p>";
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
		
		echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-share' aria-hidden='true'></span>&nbsp;" . _("Equipes de trabalho") . "</h3></div>";

	if ($pAction == "DELETE") {
		ExcluiDados($eliminar, $ra);
		ListaDados($selall, $disid, $tipo);
	} elseif ($pAction == "INSERT" or $pAction == "INSERTED") {
		if ($pAction == "INSERTED") {
			IncluiDados($disid, $nome, $ra);
		}
		echo "<form action='equipe.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario(null);
	} elseif ($pAction == "UPDATE" or $pAction == "UPDATED") {
		if ($pAction == "UPDATED") {
			AlteraDados($equid, $nome, $ra);
			ListaDados($selall, $disid, $tipo);
		} else {
			echo "<form action='equipe.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
			echo "<input type='hidden' name='equid' value='$equid'>\n";
			include 'connectdb.php';
			$sql = "SELECT nome FROM equipes WHERE id='$equid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			Formulario($linha["nome"]);
			mysql_close($dblink);
		}
	} elseif ($pAction == "LIST" or $pAction == "GET") {
		ListaMembros($selall, $equid, $disid, $pAction);
	} elseif ($pAction == "GOTTEN") {
		Associar($assdes, $equid, $ra);
		ListaMembros($selall, $equid, $disid, "LIST");
	} elseif ($pAction == "MISS") {
		Desassociar($assdes, $equid, $ra);
		ListaMembros($selall, $equid, $disid, "LIST");
	} else {
		ListaDados($selall, $disid, $tipo);
	}
	
	include 'rodape.inc';

?>