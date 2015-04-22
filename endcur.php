<?php
	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	//$tipo = $_SESSION["tipo"];
	$id = $linha["usuid"];
	
	function ListaDados($endid, $usuid, $tipo, $pAction, $selall) {
		
		include 'connectdb.php';

		if ($pAction == "GET") {
			if ($usuid == "admin") {
				$sql = "SELECT c.id, c.nome FROM curso c WHERE c.endid = '$endid' ORDER BY 2";							
			} else {
				$sql = "SELECT DISTINCT c.id, c.nome FROM curso c 
				WHERE c.endid = '$endid' AND c.id NOT IN (SELECT curid FROM usucur WHERE usuid = '$usuid') ORDER BY 2";
			}			
		} else {
			$sql = "SELECT c.id, c.nome FROM curso c INNER JOIN usucur uc ON (uc.curid = c.id)
			WHERE c.endid = '$endid' AND uc.usuid = '$usuid' ORDER BY 2";
		}
		
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			if ($tipo == 1) {
				echo "<br><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" .
				_("Altera dados do curso") . "<br><br>\n";
			}
			
			if (empty($pAction)) {
				echo "<form action='endcur.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='MISS'>\n";
			} elseif ($pAction == "GET") {
				echo "<form action='endcur.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTEN'>\n";
			}
			echo "<input type='hidden' name='endid' value='$endid'>\n";
		
			echo "<table class='table'><thread><tr>\n";
			echo "<th></th>";
			if ($tipo == 1) {
				echo "<th></th>";
			}
			echo "<th>" . _("Curso") . "</th><th>" . _("Administrador(es)") . "</th><th>" . _("&Uacute;ltimo acesso") . "</th>";
			echo "</tr></thread><tbody>\n";
			
			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='eliassdes[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='eliassdes[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td>";
				
				if($tipo == 1) {
					$sql = "SELECT * FROM curadmin WHERE usuid = '$usuid' AND curid = '" . $linha["id"] . "'";
					$resultu = mysql_query($sql, $dblink ) or die(mysql_error());
					if (mysql_num_rows($resultu) > 0) {
						echo "<td><a href='#' onClick='abrirPag(" . '"endcur.php", "pActionDest=' . $pAction . '&pAction=UPDATE&endid=' . $endid .
						'&curid=' . $linha["id"] . '")' . "'>\n";
						echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>\n";
					} else {
						echo "<td></td>";
					}
				}
				
				echo "<td>" . $linha["nome"] . "</td><td>\n";
				
				$sql = "SELECT u.nome FROM curadmin ca INNER JOIN usuario u ON (u.id = ca.usuid) WHERE ca.curid = '" . $linha["id"] . "'";
				$resultu = mysql_query($sql, $dblink ) or die(mysql_error());
				$administradores = mysql_num_rows($resultu); 
				$i = 1;
				while ($linhau = mysql_fetch_array($resultu)) {
					echo $linhau["nome"];
					if ($i < $administradores) {
						echo ", ";
					}
					$i++;
				}
				
				echo "</td>\n";
				
				$sql = "SELECT timef FROM acesso a INNER JOIN disciplina d ON a.disid = d.id WHERE d.curid = '" . $linha["id"] . "' ORDER BY 1 DESC LIMIT 1";
				$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
				$linhaa = mysql_fetch_array($resulta);
				echo "<td>" . $linhaa["timef"] . "</td></tr>";
			}
			
			echo "</tbody></table>\n";
			
			echo "<table>\n" ;

			if (empty($pAction)) {
				echo "<tr><td><input class='btn btn-danger' id='enviar' name='enviar' type='submit' value='" . _("Desassociar") . "'></form></td>\n";
			} elseif ($pAction == "GET") {
				echo "<td><input class='btn btn-default' id='enviar' name='enviar' type='submit' value='" . _("Associar") . "'></td>\n";
				if ($tipo == 1) {
					echo "<td><input class='btn btn-danger' id='enviar' name='enviar' type='submit' value='" . _("Excluir") . "'></td>\n";
				}
				echo "</form>";
			}
			
			echo "<td><form action='endcur.php' id='selall' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='$pAction'>\n
					<input type='hidden' name='curid' value='$curid'>\n
					<input type='hidden' name='selall' value='1'>\n";
			echo "<button class='btn btn-default' type='submit'>" . _("Marcar todos") . "</button></form></td>\n";
			echo "<td><form action='endcur.php' id='selall' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='$pAction'>\n
					<input type='hidden' name='curid' value='$curid'>\n
					<input type='hidden' name='selall' value='0'>\n";
			echo "<button class='btn btn-default' type='submit'>" . _("Desmarcar todos") . "</button></form></td></tr></table><br>\n";

		} else {
			
			if ($pAction == "GET") {
				echo "<p class='lead'>" . _("N&atilde;o h&aacute; cursos dispon&iacute;veis") . "...\n</p>";
			} else {
				echo "<p class='lead'>" . _("N&atilde;o h&aacute; cursos associados") . "...\n</p>";
			}
			
		}
		
		mysql_close($dblink);
				
	}
	
	function ListaAdmin($endid, $curid, $usuid, $selall, $pAction) {
		
		include 'connectdb.php';
		
		if ($pAction == "LISTADMIN") {
			$sql = "SELECT u.nome, u.id FROM curadmin ca INNER JOIN usuario u ON (ca.usuid = u.id) WHERE ca.curid = '$curid' ORDER BY 1";
		} else {
			$sql = "SELECT u.nome, u.id FROM usucur uc INNER JOIN usuario u ON (uc.usuid = u.id) WHERE uc.curid = '$curid' AND
			u.professor = '1' AND uc.usuid NOT IN (SELECT usuid FROM curadmin WHERE curid = '$curid') ORDER BY 1";
		}
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		
		if (mysql_num_rows($result) > 0) {
			
			if ($pAction == "GETADMIN") {
				echo "<form action='endcur.php' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTENADMIN'>\n";
				echo "<input type='hidden' name='endid' value='$endid'>\n";
				echo "<input type='hidden' name='curid' value='$curid'>\n";
			} else {
				echo "<form action='endcur.php' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='DELETEADMIN'>\n";
				echo "<input type='hidden' name='endid' value='$endid'>\n";
				echo "<input type='hidden' name='curid' value='$curid'>\n";
			}
			
			echo "<br><table width='100%' class='table'><thread><tr>\n";
			echo "<th></th>";
			echo "<th>" . _("Nome") . "</th><th>" . _("&Uacute;ltimo acesso") . "</th></tr>";
			echo "</thread><tbody>\n";
			
			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='eliassdes[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='eliassdes[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td>";
				
				echo "<td>" . $linha["nome"] . "</td>";
				
				$sql = "SELECT timef FROM acesso WHERE usuid = '" . $linha["id"] . "' ORDER BY 1 DESC LIMIT 1";
				$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
				$linhaa = mysql_fetch_array($resulta);
				echo "<td>" . $linhaa["timef"] . "</td></tr>";
			}
			
			echo "</tbody></table>";
			
			echo "<table>\n" ;
			
			if ($pAction == "LISTADMIN") {
				echo "<tr><td><input class='btn btn-danger' type='submit' value='" . _("Desassociar") . "'></form></td>\n";
			} else {
				echo "<td><input class='btn btn-default' type='submit' value='" . _("Associar") . "'></form></td>\n";
			}
				
			echo "<td><form action='endcur.php' id='selall' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='$pAction'>\n
			<input type='hidden' name='curid' value='$curid'>\n
			<input type='hidden' name='selall' value='1'>\n";
			echo "<button class='btn btn-default' type='submit'>" . _("Marcar todos") . "</button></form></td>\n";
			echo "<td><form action='endcur.php' id='selall' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='$pAction'>\n
			<input type='hidden' name='curid' value='$curid'>\n
			<input type='hidden' name='selall' value='0'>\n";
			echo "<button class='btn btn-default' type='submit'>" . _("Desmarcar todos") . "</button></form></td></tr></table><br>\n";
			
		} else {
			echo "<p>" . _("Sem administradores associados.") . "</p>";
		}
		
		mysql_close($dblink);
	}
	
	function IncluiDados($usuid, $endid, $nome, $projeto) {
		if ($endid) {
			include( "./connectdb.php" );
			$sql = "INSERT INTO curso VALUES (null, '$nome', '', '$endid', '$usuid', '$projeto')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$sql = "SELECT LAST_INSERT_ID() as curid";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			$curid = $linha["curid"];
			$sql = "INSERT INTO usucur VALUES ('$usuid', '$curid')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$sql = "INSERT INTO curadmin VALUES ('$usuid', '$curid')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
			return "INSERTED";
		} else {
			echo  "<br><div class='alert alert-danger' role='alert'><strong>" . _("Opera&ccedil;&atilde;o n&atilde;o realizada ...") . "&nbsp;" .
			_("Selecione corretamente a institui&ccedil;&atilde;o") . "</strong></div>" ;
			return "INSERT";
		}
	}
	
	function Desassociar($eliassdes, $usuid) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $curid => $valor) {	
			if ($valor == 'on') {
				$sql = "UPDATE disciplina SET curid = NULL WHERE curid = '$curid' AND usuid = '$usuid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$sql = "DELETE FROM usucur WHERE curid = '$curid' AND usuid = '$usuid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<br><div class='alert alert-danger' role='alert'><strong>" . _("Opera&ccedil;&atilde;o n&atilde;o realizada ...") . "&nbsp;" . 
			_("Selecione corretamente o curso") . "</strong></div>";
		}
		mysql_close($dblink);
		return;
	}
	
	function Associar($eliassdes, $usuid) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $curid => $valor) {	
			if ($valor == 'on') {
				$sql = "INSERT INTO usucur VALUES ('$usuid', '$curid')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<br><div class='alert alert-danger' role='alert'><strong>" . _("Opera&ccedil;&atilde;o n&atilde;o realizada ...") . 
			_("Selecione corretamente uma refer&ecirc;ncia.") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}
	
	function ExcluiDados($eliassdes, $usuid, $endid) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $curid => $valor) {	
			if ($valor == 'on') {
				$sql = "SELECT * FROM endadmin WHERE usuid = '$usuid' AND endid = '$endid'";
				$result = mysql_query( $sql, $aDBLink ) or die(mysql_error());
				if (mysql_num_rows($result) == 0) {
					$sql = "SELECT * FROM curadmin WHERE usuid = '$usuid' AND curid = '$curid'";
					$result = mysql_query( $sql, $aDBLink ) or die(mysql_error());
					if (mysql_num_rows($result) == 0) {
						echo "<br><div class='alert alert-danger' role='alert'><strong>" .
						_("Voc&ecirc; n&atilde;o &eacute; administrador do curso nem da institui&ccedil;&atilde;o. Exclus&atilde;o n&atilde;o permitida...") . "</strong></div>\n";
						mysql_close($dblink);
						return "DELETE";
					}
				}
				$sql = "UPDATE disciplina SET curid = NULL WHERE curid = '$curid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$sql = "DELETE FROM curadmin WHERE curid = '$curid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$sql = "DELETE FROM curso WHERE id = '$curid' AND usuid = '$usuid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo  "<br><div class='alert alert-danger' role='alert'><strong>" . _("Opera&ccedil;&atilde;o n&atilde;o realizada ...") . 
			_("Selecione corretamente o curso") . "</strong></div>" ;
		}
		mysql_close($dblink);
		return "DELETED";
	}
	
	function ExcluiAdmin($eliassdes, $curid, $endid, $id) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $usuid => $valor) {
			if ($valor == 'on') {
				$sql = "SELECT * FROM endadmin WHERE usuid = '$id' AND endid = '$endid'";
				$result = mysql_query( $sql, $aDBLink ) or die(mysql_error());
				if (mysql_num_rows($result) == 0) {
					$sql = "SELECT * FROM curadmin WHERE usuid = '$id' AND curid = '$curid'";
					$result = mysql_query( $sql, $aDBLink ) or die(mysql_error());
					if (mysql_num_rows($result) == 0) {
						echo "<br><div class='alert alert-danger' role='alert'><strong>" .
						_("Voc&ecirc; n&atilde;o &eacute; administrador do curso nem da institui&ccedil;&atilde;o. Opera&ccedil;&atilde;o n&atilde;o permitida...") . "</strong></div>\n";
						mysql_close($dblink);
						return "DELETEADMIN";
					}
				}
				$sql = "DELETE FROM curadmin WHERE curid = '$curid' AND usuid = '$usuid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function IncluiAdmin($eliassdes, $curid, $endid, $id) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $usuid => $valor) {
			if ($valor == 'on') {
				$sql = "SELECT * FROM endadmin WHERE usuid = '$id' AND endid = '$endid'";
				$result = mysql_query( $sql, $aDBLink ) or die(mysql_error());
				if (mysql_num_rows($result) == 0) {
					$sql = "SELECT * FROM curadmin WHERE usuid = '$id' AND curid = '$curid'";
					$result = mysql_query( $sql, $aDBLink ) or die(mysql_error());
					if (mysql_num_rows($result) == 0) {
						echo "<br><div class='alert alert-danger' role='alert'><strong>" .
						_("Voc&ecirc; n&atilde;o &eacute; administrador do curso nem da institui&ccedil;&atilde;o. Exclus&atilde;o n&atilde;o permitida...") . "</strong></div>\n";
						mysql_close($dblink);
						return "GETADMIN";
					}
				}
				$sql = "INSERT INTO curadmin VALUES ('$usuid', '$curid')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return "GOTTENADMIN";
	}
	
	function AlteraDados($curid, $nome, $projeto) {
		include( "./connectdb.php" );
		$sql = "UPDATE curso SET nome = '$nome', projeto = '$projeto' WHERE id = '$curid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function Formulario($sigla, $nome, $projeto) {
		echo "<p><label for='nome'>(*) Nome</label>\n";
		echo "<input type='text' name='nome' id='nome' value='$nome' size=60 maxlength=60 class='form-control' required autofocus></p>\n";
		echo "<p><label for='projeto'>" . _("Projeto pedag&oacute;gico de curso") . "<br></label></p>\n";
		echo "<textarea name='projeto' rows='20' class='form-control'>$projeto</textarea>\n";
		echo "<p><button class='btn btn-default' type='submit'>" . _("Enviar") . "</button></form></p>\n";
	}
	
	include( "cabecalho.php" );
	
	include( "menup.inc" );
?>

	<script type="text/javascript">

		$(function(){

			$('#ConfirmarExc').click(function () {
				document.deleteForm.submit();
				modal.modal('hide');
			});

			$('form#deleteForm').submit(function(e){
				var btn = $(document.activeElement).val();
				e.preventDefault();
				if (btn != '<?php echo _("Associar") ?>') {
					$('#deleteConfirmModal').modal('show');
				} else {
					document.deleteForm.submit();
				}
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
					echo "<p>" . _("Voc&ecirc; optou por excluir um curso ou se desassociar dele.") . "</p>";
					echo "<p>" . _("Todos os seus dados que est&atilde;o vinculados ao curso serão eliminados. "); 
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

	echo "<div class='jumbotron'>";
	
	include( "connectdb.php" );

	$sql = "SELECT nome,endid,faltas FROM disciplina WHERE id = '$disid'" ;
	$sql2 = "SELECT nome,email,professor FROM usuario WHERE id = '$id'" ;
	
	$result2 = mysql_query( $sql2, $dblink );
	
	if ( mysql_num_rows($result2) > 0) {
		$linhau = mysql_fetch_array($result2);
		if ($linhau["professor"] == 1) {
			echo "<p><h2>" . _("Professor(a)") . "&nbsp;";
		} else {
			echo "<p><h2>";
		}
		echo $linhau["nome"] . "</h2></p>";
	}

	mysql_close($dblink);

	echo "<br><h2 class='blog-post-title'><span class='glyphicon glyphicon-book' aria-hidden='true'></span>&nbsp;" . _("Cursos") . "</h2>";

	echo "</div>";
	
	if ($pAction == "MISS") {
		Desassociar($eliassdes, $id);
	} elseif ($pAction == "DELETED") {
		$pAction = ExcluiDados($eliassdes, $id, $endid);
	} elseif ($pAction == "GOTTEN") {
		if ($enviar == "Associar") {
			Associar($eliassdes, $id);
		} else {
			$pAction = ExcluiDados($eliassdes, $id, $endid);
		}
	} elseif ($pAction == "UPDATED") {
		AlteraDados($curid, $nome, $projeto);
	} elseif ($pAction == "GOTTENADMIN") {
		IncluiAdmin($eliassdes, $curid, $endid, $id);
	} elseif ($pAction == "DELETEADMIN") {
		ExcluiAdmin($eliassdes, $curid, $endid, $id);
	} elseif ($pAction == "INSERTED") {
		$pAction = IncluiDados($id, $endid, $nome, $projeto);
	}
	
	if ($pAction == "INSERT" or $pAction == "INSERTED") {
		echo "<a href='#' " . CriaLink("endcur.php", "endid=$endid") . "><button type='button' class='btn btn btn-default'>" . 
		_("Cursos associados") . "</button></A>\n";
		echo "<a href='#' " . CriaLink("endcur.php", "pAction=GET&endid=$endid") . "><button type='button' class='btn btn btn-default'>" .
		_("Cursos cadastrados") . "</button></a>\n";
	} elseif ($pAction == "GET" or $pAction == "GOTTEN") {
		echo "<a href='#' " . CriaLink("endcur.php", "endid=$endid") . "><button type='button' class='btn btn btn-default'>" . _("Cursos associados") . "</button></A>\n";
		if ($tipo == 1) {
			echo "<a href='#' " . CriaLink("endcur.php", "pAction=INSERT&endid=$endid") . "><button type='button' class='btn btn btn-default'>" .
			_("Incluir novos cursos") . "</button></a>\n";
		}
	} elseif ($pAction == "UPDATE") {
		echo "<a href='#' " . CriaLink("endcur.php", "pAction=GET&endid=$endid") . "><button type='button' class='btn btn btn-default'>" .
		_("Cursos cadastrados") . "</button></a>\n";
		echo "<a href='#' " . CriaLink("endcur.php", "endid=$endid") . "><button type='button' class='btn btn btn-default'>" .
		_("Cursos associados") . "</button></A>\n";
		echo "<a href='#' " . CriaLink("endcur.php", "pAction=INSERT&endid=$endid") . "><button type='button' class='btn btn btn-default'>" .
		_("Incluir novos cursos") . "</button></a>\n";
	} elseif ($pAction == "LISTADMIN" or $pAction == "GETADMIN" or $pAction == "DELETEADMIN" or $pAction == "GOTTENADMIN") {
		echo "<a href='#' " . CriaLink("endcur.php", "endid=$endid") . "><button type='button' class='btn btn btn-default'>" .
		_("Cursos associados") . "</button></A>\n";
		echo "<a href='#' " . CriaLink("endcur.php", "pAction=GETADMIN&endid=$endid&curid=$curid") . "><button type='button' class='btn btn btn-default'>" .
		_("Definir administradores") . "</button></A>\n";
	} else {
		echo "<a href='#' " . CriaLink("endcur.php", "pAction=GET&endid=$endid") . "><button type='button' class='btn btn btn-default'>" .
		_("Cursos cadastrados") . "</button></a>\n";
		if ($tipo == 1) {
			echo "<a href='#' " . CriaLink("endcur.php", "pAction=INSERT&endid=$endid") . "><button type='button' class='btn btn btn-default'>" .
			_("Incluir novos cursos") . "</button></a>\n";
			echo "<a href='#' " . CriaLink("endcur.php", "pAction=LISTADMIN&endid=$endid&curid=$curid") . "><button type='button' class='btn btn btn-default'>" .
			_("Administradores") . "</button></a>\n";
		}
	}
	
	echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Cursos") . "</h3></div>";
	echo "<div class='panel-body'>";

	echo "<form action='endcur.php' id='instituicao' method=post>\n";
	echo "<input type='hidden' name='pAction' value='$pAction'>\n";
	   				
	if ($pAction == "INSERT" or $pAction == "INSERTED") {
		echo "<p class='lead'>Asterisco (*) indica campo obrigat&oacute;rio</p>";
		echo "<p><label for='endid'>(*) " . _("Institui&ccedil;&atilde;o" ) . "</label>\n";
	} else {
		if ($pAction != "UPDATE") {
			echo "<p><label for='endid'>" . _("Institui&ccedil;&atilde;o" ) . "</label>\n";
		}
	}
	
	if ($pAction != "UPDATE") {

		include( "connectdb.php" );
	
		echo "<select onchange='submit()' name='endid' id='endid' class='form-control' required autofocus>\n";
		
		if ($id == "admin") {
			$sql = "SELECT e.id, e.nome, e.projeto FROM enderecos e ORDER BY 2";
		} else {
			$sql = "SELECT e.id, e.nome, e.projeto FROM usuend ue INNER JOIN enderecos e ON ue.endid = e.id WHERE ue.usuid = '$id' ORDER BY 2";
		}
		
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		if ( mysql_num_rows($result) > 0) {
			echo "<option value=0>" . _("Selecione uma institui&ccedil;&atilde;o") . "</option>";
	   		while($linha = mysql_fetch_array($result)) {
				echo "<option value=" . $linha["id"];
				if ($endid == $linha["id"]) {
	   				echo " SELECTED";
	   			}
				echo ">" . $linha["nome"] . "</option>";
			}
		}
				
		echo "</select></p></form>";
		
		if (!empty($endid)) {
			$sql = "SELECT e.projeto FROM enderecos e WHERE id = '$endid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			if (!empty($linha['projeto'])) {
				echo "<strong><em>" . _("Projeto did&aacute;tico pedag&oacute;gico institucional") . ": </strong>" . $linha["projeto"] . "</em><br><br>";
			}
		}
	
		mysql_close($dblink);
		
	} else {
		include( "connectdb.php" );
		$sql = "SELECT e.nome, e.projeto FROM curso c INNER JOIN enderecos e ON e.id = c.endid WHERE c.id = '$curid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		echo "<h2>" . $linha["nome"] . "</h2><br>";
		if (!empty($linha["projeto"])) {
			echo "<strong><em>" . _("Projeto did&aacute;tico pedag&oacute;gico institucional") . ": </strong>" . $linha["projeto"] . "</em><br><br>";
		}
		mysql_close($dblink);
	}
	
	if ($pAction == "LISTADMIN" or $pAction == "GETADMIN" or $pAction == "GOTTENADMIN" or $pAction == "DELETEADMIN") {
		include( "connectdb.php" );
		echo "<form action='endcur.php' id='curso' method=post>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='endid' value='$endid'>\n";
		echo "<p><label for='curid'>" . _("Curso") . "</label>\n";
		echo "<select onchange='submit()' name='curid' id='curid' class='form-control' autofocus>\n";
		if ($id == "admin") {
			$sql = "SELECT DISTINCT c.id, c.nome FROM curso c WHERE c.endid = '$endid' ORDER BY 2";
		} else {
			$sql = "SELECT DISTINCT c.id, c.nome FROM usucur uc INNER JOIN curso c ON uc.curid = c.id WHERE uc.usuid = '$id' AND c.endid = '$endid' ORDER BY 2";
		}
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		if ( mysql_num_rows($result) > 0) {
			echo "<option value=0>" . _("Selecione um curso") . "</option>";
			while($linha = mysql_fetch_array($result)) {
				echo "<option value=" . $linha["id"];
				if ($curid == $linha["id"]) {
					echo " SELECTED";
				}
				echo ">" . $linha["nome"] . "</option>";
			}
		}
		echo "</select></p>";
		echo "</form>";
		mysql_close($dblink);
	}
	   				
	if ($pAction == "INSERT" or $pAction == "INSERTED") {
		echo "<form action='endcur.php' method='POST'>\n" ;
		echo "<input type='hidden' name='endid' value='$endid'>\n";
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario();
	} elseif ($pAction == "GET") {
		ListaDados($endid, $id, $tipo, $pAction, $selall);
	} elseif ($pAction == "MISS" or ($pAction == "GOTTEN" and $enviar == "Associar")) {
		ListaDados($endid, $id, $tipo, null, $selall);
	} elseif ($pAction == "DELETED" or ($pAction == "GOTTEN" and $enviar != "Associar")) {
		ListaDados($endid, $id, $tipo, "GET", $selall);
	} elseif ($pAction == "UPDATE") {
		echo "<form action='endcur.php' method='POST'>\n" ;
		echo "<input type='hidden' name='endid' value='$endid'>\n";
		echo "<input type='hidden' name='curid' value='$curid'>\n";
		echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
		echo "<input type='hidden' name='pActionDest' value='$pActionDest'>\n";
		include 'connectdb.php';
		$sql = "SELECT sigla, nome, projeto FROM curso WHERE id = '$curid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result) or die(mysql_error());
		Formulario($linha["sigla"], $linha["nome"], $linha["projeto"]);
		mysql_close($dblink);
	} elseif ($pAction == "UPDATED") {
		ListaDados($endid, $id, $tipo, $pActionDest, $selall);
	} elseif ($pAction == "LISTADMIN" or $pAction == "GETADMIN") {
		ListaAdmin($endid, $curid, $id, $selall, $pAction);
	} elseif ($pAction == "GOTTENADMIN" or $pAction == "DELETEADMIN") {
		ListaAdmin($endid, $curid, $id, $selall, "LISTADMIN");
	} else {
		ListaDados($endid, $id, $tipo, null, $selall);
	}
	
	echo "</div></div>";
	
	include 'rodape.inc';
	
?>
