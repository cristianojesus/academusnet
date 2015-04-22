<?php
	session_start();
	include("buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	//$tipo = $_SESSION["tipo"];
	$id = $linha["usuid"];

	function ListaDados($usuid, $selall, $pAction, $tipo) {

		include( "./connectdb.php" );

		if ($pAction == "SELECT") {
			$SQL = "SELECT DISTINCT e.id, e.nome, e.endereco, e.cidade, e.cep, e.estado, e.pais, ";
			$SQL = $SQL . "e.telefone, e.email, e.url, e.id, e.sigla, ue.usuid, ue.ra ";
			if ($tipo == 2) {
				$SQL = $SQL . "FROM enderecos e INNER JOIN usuend ue ON (e.id = ue.endid) ";
			} else {
				$SQL = $SQL . "FROM enderecos e INNER JOIN usuend ue ON (e.id = ue.endid AND ue.usuid = '$usuid') ";
			}
			$SQL = $SQL . "ORDER BY 2";
		} else {
			if ($pAction == "GET" or $pAction == "UNSELECT" or $pAction == "DELETE") {
				$SQL = "SELECT DISTINCT e.id, e.nome, e.endereco, e.cidade, e.cep, e.estado, e.pais, ";
				$SQL = $SQL . "e.telefone, e.email, e.url, e.id, e.sigla, ue.usuid, ue.ra ";
				if ($tipo == 2) {
					$SQL = $SQL . "FROM enderecos e LEFT JOIN usuend ue ON (e.id = ue.endid) ";
				} else {
					$SQL = $SQL . "FROM enderecos e LEFT JOIN usuend ue ON (e.id = ue.endid AND ue.usuid = '$usuid') ";
				}
				$SQL = $SQL . "WHERE ue.endid IS NULL ORDER BY 2";
			} else {
				$SQL = "SELECT DISTINCT e.id, e.nome, e.endereco, e.cidade, e.cep, e.estado, e.pais, ";
				$SQL = $SQL . "e.telefone, e.email, e.url FROM enderecos e ORDER BY 2";
			}
		}

		$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());

		if ( mysql_num_rows($QResult) > 0) {
			
			if ($tipo == 1) {
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" .
				_("Altera dados da institui&ccedil;&atilde;o") . "\n";
			}

			if ($pAction == "SELECT") {
				echo "<form action='endereco.php' id='deleteForm' name='deleteForm' method='POST'>\n" ;
				echo "<input type='hidden' name='pAction' value='MISS'>\n";
			} else {
				echo "<form action='endereco.php' id='deleteForm' name='deleteForm' method='POST'>\n" ;
				echo "<input type='hidden' name='pAction' value='DELETE'>\n";
			}
			
			echo "<br><table class='table'><thread><tr>\n";
			echo "<th></th><th></th><th>" . _("Institui&ccedil;&atilde;o") . "</th></thread><tbody>\n";

			while ($linha = mysql_fetch_array($QResult)) {

				echo "<tr><td width=5% nowrap>\n";

				if ( $pAction <> "QUERY" ) {
					if ( empty( $selall ) ) {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
					}
				}

				echo "</td>";
				
				$sql = "SELECT * FROM endadmin WHERE usuid = '$usuid' AND endid = '" . $linha["id"] . "'";
				$resultu = mysql_query($sql, $dblink ) or die(mysql_error());
				if (mysql_num_rows($resultu) > 0 or $usuid == "admin") {
					if ($tipo == 1) {
						echo "<td width=5% nowrap><a href='#' onClick='abrirPag(" . '"endereco.php", "pActionDest=' . $pAction . '&pAction=UPDATE&endid=' . $linha["id"] . '")' . "'>\n";
						echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>\n";
					} else {
						echo "<td></td>";
					}
				} else {
					echo "<td></td>";
				}
				
				echo "<td>\n";

				echo $linha["nome"] . "<br>\n";

				if (!empty($linha["endereco"])) {
					echo $linha["endereco"] . "<br>\n";
				}
				if (!empty($linha["cidade"])) {
					echo $linha["cidade"] . " " . $linha["cep"] . " " . $linha["estado"] . "<br>\n";
				}
				if (!empty($linha["telefone"])) {
					echo $linha["telefone"] . "<br>\n";
				}
				if (!empty($linha["email"])) {
					echo $linha["email"] . "<br>\n";
				}
				if (!empty($linha["url"])) {
					if (substr(trim($linha["url"]),0,4) <> "http") {
						$url = "http://" . $linha["url"];
					} else {
						$url = $linha["url"];
					}
					echo "<a href='" . $url . "' target='window' id='textLink'>" . $url . "</a><br>\n";
				}
				
				$sql = "SELECT u.nome FROM endadmin ea INNER JOIN usuario u ON (u.id = ea.usuid) WHERE ea.endid = '" . $linha["id"] . "'";
				$resultu = mysql_query($sql, $dblink ) or die(mysql_error());
				$administradores = mysql_num_rows($resultu);
				if ( $administradores > 1) {
					echo "<br>" . _("Administradores") . ": ";
				} elseif ($administradores == 1) {
					echo "<br>" . _("Administrador") . ": ";
				} else {
					echo "<br>" . _("Administrador: sem administrador definido");
				}
				$i = 1;
				while ($linhau = mysql_fetch_array($resultu)) {
					echo $linhau["nome"];
					if ($i < $administradores) {
						echo ", ";
					}
					$i++;
				}
								
				if ($tipo == 0 and !empty($linha["ra"])) {
					echo "RA: " . $linha["ra"] . "<a href='#' id='textLink'" . CriaLink("alterara.php", "endid=" . $linha["id"] . "&usuid=" 
					. $linha["usuid"]) . ">[" . ("alterar RA") . "]</a><br>\n" ;
				} else {
					echo "</td></tr>";
				}
			}
			echo "</tbody></table>";
		} else {
			if ( $pAction == "GET" ) {
				echo "<p class='lead'>" . _("N&atilde;o h&aacute; institui&ccedil;&otilde;es registradas ...") . "</p>\n";
			} else {
				echo "<p class='lead'>" . _("N&atilde;o h&aacute; institui&ccedil;&otilde;es associadas ...") . "</p>\n";
			}
			mysql_close($aDBLink);
			return 0;
		}
		mysql_close($aDBLink);
		return 1;
	}
	
	function ListaAdmin($endid, $usuid, $selall, $pAction) {
	
		include 'connectdb.php';
	
		if ($pAction == "LISTADMIN") {
			$sql = "SELECT u.nome, u.id FROM endadmin ea INNER JOIN usuario u ON (ea.usuid = u.id) WHERE ea.endid = '$endid' ORDER BY 1";
		} else {
			$sql = "SELECT u.nome, u.id FROM usuend ue INNER JOIN usuario u ON (ue.usuid = u.id) WHERE ue.endid = '$endid' AND
				u.professor = '1' AND ue.usuid NOT IN (SELECT usuid FROM endadmin WHERE endid = '$endid') ORDER BY 1";
		}
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
	
		if (mysql_num_rows($result) > 0) {
				
			if ($pAction == "GETADMIN") {
				echo "<form action='endereco.php' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTENADMIN'>\n";
			} else {
				echo "<form action='endereco.php' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='DELETEADMIN'>\n";
			}
				
			echo "<table width='100%' class='table'><thread><tr>\n";
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
			<input type='hidden' name='endid' value='$endid'>\n
			<input type='hidden' name='selall' value='1'>\n";
			echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'></form></td>\n";
			echo "<td><form action='endcur.php' id='selall' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='$pAction'>\n
			<input type='hidden' name='endid' value='$endid'>\n
			<input type='hidden' name='selall' value='0'>\n";
			echo "<input class='btn btn-default' type='submit' value='" . _("Desmarcar todos") . "'></form></td></tr></table><br>\n";
				
		} else {
			echo "<p>" . _("Sem administradores associados.") . "</p>";
		}
			
		mysql_close($dblink);
	}

	function ExcluiDados($eliminar, $pAction, $usuid) {

		if (!empty($eliminar)) {

			include( "./connectdb.php" );

			foreach ($eliminar as $endid => $valor) {

				if ($valor == 'on') {
					if ($pAction == "DELETE") {
						$sql = "SELECT * FROM endadmin WHERE usuid = '$usuid' AND endid = '$endid'";
						$result = mysql_query( $sql, $aDBLink ) or die(mysql_error());
						if (mysql_num_rows($result) == 0) {
							echo "<br><div class='alert alert-danger' role='alert'><strong>" .
							_("Voc&ecirc; n&atilde;o &eacute; administrador da institui&ccedil;&atilde;o. Exclus&atilde;o n&atilde;o permitida...") . "</strong></div>\n";
						} else {
							$SQL = "SELECT * FROM usuend WHERE usuid <> '$usuid' AND endid = '$endid'";
							$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());
							if (mysql_num_rows($QResult) > 0) {
								echo "<br><div class='alert alert-danger' role='alert'><strong>" . 
								_("H&aacute; outros professores associados a essa institui&ccedil;&atilde;o. Exclus&atilde;o n&atilde;o permitida...") . "</strong></div>\n";
							} else {
								$SQL = "DELETE FROM enderecos WHERE id = '$endid'" ;
								$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());
								echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
							}
						}
					} else {
						$SQL = "DELETE FROM usuend WHERE usuid = '$usuid' AND endid = $endid";
						$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());
						$SQL = "DELETE usucur FROM usucur INNER JOIN curso WHERE usucur.curid = curso.id AND usucur.usuid = '$usuid' AND curso.endid = '$endid'";
						$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());
						$SQL = "DELETE disalu FROM disalu INNER JOIN disciplina WHERE disalu.disid = disciplina.id AND disciplina.usuid = '$usuid' 
						AND disciplina.endid = '$endid'";
						$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());
						$SQL = "DELETE disusu FROM disusu INNER JOIN disciplina WHERE disusu.disid = disciplina.id AND disciplina.usuid = '$usuid' 
						AND disciplina.endid = '$endid'";
						$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());
						$SQL = "UPDATE disciplina SET endid = null, curid = null WHERE usuid = '$usuid' AND endid = $endid";
						$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());
						
						echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
						
					}
				}
			}

			mysql_close($aDBLink);
		}
	}

	function IncluiDados( $usuid, $nome, $endereco, $bairro, $cidade, $estado, $cep, $pais, $telefone, $email, $url, $sigla, $projeto, $endid, $pAction) {

		include( "./connectdb.php" );

		if ($pAction == "GET") {
			$SQL = "SELECT ra FROM usuend WHERE usuid = '$usuid'";
			$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());
			$linha = mysql_fetch_array($QResult);
			$SQL = "INSERT INTO usuend VALUES ('$usuid', '$endid', '" . $linha["ra"] . "', 1)";
			$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());
			mysql_close($aDBLink);
			return 1;
		} else {
			$SQL = "INSERT INTO enderecos VALUES (null, '$nome', '$endereco', '$bairro', '$cidade', '$estado', '$cep', '$pais', '$telefone', '$email', '$url', '$sigla', $projeto )";
			$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());
			$sql = "SELECT LAST_INSERT_ID() as endid";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			$endid = $linha["endid"];
			$sql = "INSERT INTO endadmin VALUES ('$usuid', '$endid')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($aDBLink);
			return 1;
		}
	}

	function AlteraDados( $endid, $usuid, $nome, $endereco, $bairro, $cidade, $estado, $cep, $pais, $telefone, $email, $url, $sigla, $projeto ) {

		include( "./connectdb.php" );

		$SQL = "UPDATE enderecos SET nome = '$nome', ";
		$SQL = $SQL . "endereco = '$endereco', ";
		$SQL = $SQL . "cidade = '$cidade', ";
		$SQL = $SQL . "estado = '$estado', ";
		$SQL = $SQL . "bairro = '$bairro', ";
		$SQL = $SQL . "cep = '$cep', ";
		$SQL = $SQL . "pais = '$pais', ";
		$SQL = $SQL . "telefone = '$telefone', ";
		$SQL = $SQL . "url = '$url', ";
		$SQL = $SQL . "email = '$email', ";
		$SQL = $SQL . "projeto = '$projeto' WHERE id = $endid";

		$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());

		echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;

		mysql_close($aDBLink);

		return 0;

	}
	
	function ExcluiAdmin($eliassdes, $endid) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $usuid => $valor) {
			if ($valor == 'on') {
				$sql = "DELETE FROM endadmin WHERE endid = '$endid' AND usuid = '$usuid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function IncluiAdmin($eliassdes, $endid) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $usuid => $valor) {
			if ($valor == 'on') {
				$sql = "INSERT INTO endadmin VALUES ('$usuid', '$endid')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
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
					echo "<p>" . _("Voc&ecirc; optou por excluir uma institui&ccedil;&atilde;o ou se desassociar dela.") . "</p>";
					echo "<p>" . _("Todos os seus dados que est&atilde;o vinculados a institui&ccedil;&atilde;o ser&atilde;o eliminados. "); 
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

	echo "<br><h2 class='blog-post-title'><span class='glyphicon glyphicon-book' aria-hidden='true'></span>&nbsp;" . _("Institui&ccedil;&otilde;es") . "</h2>";

	echo "</div>";

	
	if ($pAction == "INSERT" or $pAction == "UPDATE") {
		echo "<a href='#' " . CriaLink("endereco.php", "pAction=SELECT") . "><button type='button' class='btn btn btn-default'>" .
		_("Institui&ccedil;&otilde;es associadas") . "</button></A>\n";
		echo "<a href='#' " . CriaLink("endereco.php", "pAction=GET") . "><button type='button' class='btn btn btn-default'>" . 
		_("Institui&ccedil;&otilde;es cadastradas") . "</button></A>\n";
	} elseif ($pAction == "GET" or $pAction == "DELETE") {
		echo "<a href='#' " . CriaLink("endereco.php", "pAction=SELECT") . "><button type='button' class='btn btn btn-default'>" .
		_("Institui&ccedil;&otilde;es associadas") . "</button></A>\n";
		if ($tipo == 1) {
			echo "<a href='#' " . CriaLink("endereco.php", "pAction=INSERT") . "><button type='button' class='btn btn btn-default'>" .
			_("Incluir novas institui&ccedil;&otilde;es") . "</button></a>\n";
		}
	} elseif ($pAction == "LISTADMIN" or $pAction == "GETADMIN" or $pAction == "DELETEADMIN" or $pAction == "GOTTENADMIN") {
		echo "<a href='#' " . CriaLink("endereco.php", "pAction=SELECT") . "><button type='button' class='btn btn btn-default'>" .
		_("Institui&ccedil;&otilde;es associadas") . "</button></A>\n";
		echo "<a href='#' " . CriaLink("endereco.php", "pAction=GETADMIN") . "><button type='button' class='btn btn btn-default'>" .
		_("Definir administradores") . "</button></A>\n";
	} else {
		echo "<a href='#' " . CriaLink("endereco.php", "pAction=GET") . "><button type='button' class='btn btn btn-default'>" .
		_("Institui&ccedil;&otilde;es cadastradas") . "</button></a>\n";
		if ($tipo == 1) {
			echo "<a href='#' " . CriaLink("endereco.php", "pAction=LISTADMIN") . "><button type='button' class='btn btn btn-default'>" .
			_("Administradores") . "</button></a>\n";
		}
	}
	
	echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Institui&ccedil;&otilde;es") . "</h3></div>";
	echo "<div class='panel-body'>";
	
	if ($pAction == "MISS") {
		ExcluiDados($eliminar, $pAction, $id);
		$pAction = "SELECT";
	}
	
	if ($pAction == "DELETE") {
		if ($enviar == "Associar") {
			if (!empty($eliminar)) {
				foreach ($eliminar as $endid => $valor) {
					if ($valor == 'on') {
						$associacao = IncluiDados( $id, "", "", "", "", "", "", "", "", "", "", "", "", $endid, "GET" );
					}
				}
			}
			if ($associacao == 1) {
				echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			}
			$pAction = "SELECT";
		} else {
			ExcluiDados($eliminar, $pAction, $id);
			$pAction = "GET";
		}
	}

	if ($pAction == "INSERT" or $pAction == "INSERTED" or $pAction == "UPDATE" or $pAction == "UPDATED") {

		if ($pAction == "INSERTED") {
			$inclusao = IncluiDados($id, strip_tags($nome), strip_tags($endereco), strip_tags($bairro), strip_tags($cidade), 
			strip_tags($estado), strip_tags($cep), strip_tags($pais), strip_tags($telefone), strip_tags($email), strip_tags($url), $sigla, strip_tags($projeto), "", "" );
			if ($inclusao == 1) {
				$nome = "";
				$endereco = "";
				$cidade = "";
				$estado = "";
				$cep = "";
				$bairro = "";
				$pais = "";
				$telefone = "";
				$email = "";
				$url = "";
				$sigla = "";
				$projeto = "";
			}
		}

		if ($pAction == "UPDATED") {
			$alteracao = AlteraDados( $endid, $id, strip_tags($nome), strip_tags($endereco), strip_tags($bairro), strip_tags($cidade), strip_tags($estado), 
			strip_tags($cep), strip_tags($pais), strip_tags($telefone), strip_tags($email), strip_tags($url), strip_tags($sigla), strip_tags($projeto) );
			$pAction = "SELECT";
		}

		include( "./connectdb.php" );

		if ($pAction == "UPDATE" or $alteracao == 1) {

			echo "<form action='endereco.php' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='UPDATED'\n";
			echo "<input type='hidden' name='endid' value='$endid'>\n";
						
			$SQL = "SELECT nome, endereco, bairro, cidade, estado, cep, pais, telefone, email, url, sigla, projeto ";
			$SQL = $SQL . "FROM enderecos WHERE id = '$endid'";

			$QResult = mysql_query( $SQL, $aDBLink ) or die(mysql_error());

			if ( mysql_num_rows($QResult) > 0) {

				$linha = mysql_fetch_array($QResult);

				$nome = $linha["nome"];
				$endereco = $linha["endereco"];
				$cidade = $linha["cidade"];
				$estado = $linha["estado"];
				$cep = $linha["cep"];
				$bairro = $linha["bairro"];
				$telefone = $linha["telefone"];
				$pais = $linha["pais"];
				$email = $linha["email"];
				$url = $linha["url"];
				$sigla = $linha["sigla"];
				$projeto = $linha["projeto"];
			}

		} elseif ($pAction == "INSERT") {
			
			echo "<br><br><form action='endereco.php' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
					
		}
		
		if ($pAction == "INSERT" or $pAction == "INSERTED" or $pAction == "UPDATE" or ($pAction == "UPDATED" and $alteracao == 1)) {
			echo "<p><label for='nome'>(*) Nome</label>\n";
			echo "<input type='text' name='nome' value='$nome' size=60 maxlength=60 class='form-control' required autofocus></p>\n";
			echo "<p><label for='endereco'>(*) Endere&ccedil;o</label>\n";
			echo "<input type='text' name='endereco' value='$endereco' size=60 maxlength=60 class='form-control' required autofocus></p>\n";
			echo "<p><label for='bairro'>(*) Bairro</label>\n";
			echo "<input type='text' name='bairro' value='$bairro' size=30 maxlength=30 class='form-control' required autofocus></p>\n";
			echo "<p><label for='cidade'>(*) Cidade</label>\n";
			echo "<input type='text' name='cidade' value='$cidade' size=30 maxlength=30 class='form-control' required autofocus></p>\n";
			echo "<p><label for='estado'>(*) Estado</label>\n";
			echo "<input type='text' name='estado' value='$estado' size=2 maxlength=2 class='form-control' required autofocus></p>\n";
			echo "<p><label for='cep'>(*) CEP</label>\n";
			echo "<input type='text' name='cep' value='$cep' size=9 maxlength=9 class='form-control' required autofocus></p>\n";
			echo "<p><label for='pais'>(*) Pais</label>\n";
			echo "<input type='text' name='pais' value='$pais' size=30 maxlength=30 class='form-control' required autofocus></p>\n";
			echo "<p><label for='telefone'>(*) Telefone</label>\n";
			echo "<input type='text' name='telefone' value='$telefone' size=30 maxlength=30 class='form-control' required autofocus></p>\n";
			echo "<p><label for='email'>(*) e-mail</label>\n";
			echo "<input type='text' name='email' value='$email' size=30 maxlength=30 class='form-control' required autofocus></p>\n";
			echo "<p><label for='url'>(*) P&aacute;gina Web</label>\n";
			echo "<input type='text' name='url' value='$url' size=30 maxlength=30 class='form-control' required autofocus></p>\n";
			echo "<p><label for='projeto'>" . _("Projeto did&aacute;tico pedag&oacute;gico institucional") . "<br></label></p>\n";
			echo "<textarea name='projeto' rows='20' class='form-control'>$projeto</textarea>\n";
			echo "<p><input class='btn btn-default' type='submit' value='" . _("Enviar") . "'></form></p>\n";
		}

		mysql_close($aDBLink);

	}

	if ($pAction == "DELETE" or ($pAction == "UPDATED" and $alteracao == 0) or empty($pAction) or $pAction == "SELECT" or $pAction == "GET") {
					
		$dados = ListaDados($id, $selall, $pAction, $tipo);

		if ($dados == 1) {
			echo "<table><tr valign='top'>\n";
			if ($pAction == "SELECT") {
				echo "<tr><td><input type='submit' class='btn btn-danger' name='enviar' id='enviar' value='" . _("Desassociar") . "'></form></td>\n";
			} else {
				echo "<td><input type='submit' class='btn btn-default' name='enviar' id='enviar' value='" . _("Associar") . "'></td>\n";
				if ($tipo == 1) {
					echo "<td><input type='submit' class='btn btn-danger' name='enviar' id='enviar' value='" . _("Excluir") . "'></td>\n";
				}
				echo "</form>";
			}
			echo "<td><form action='endereco.php' id='selall' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='$pAction'>";
			echo "<input type='hidden' name='selall' value='1'>";
			echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'></form></td>\n";
			echo "</form></td>\n" ;
			echo "<td><form action='endereco.php' id='selall' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='$pAction'>";
			echo "<input type='hidden' name='selall' value='0'>";
			echo "<input class='btn btn-default' type='submit' value='" . _("Desmarcar todos") . "'>\n";
			echo "</form></td></tr></table><br>\n" ;
		}
	}
	
	if ($pAction == "LISTADMIN" or $pAction == "GETADMIN" or $pAction == "GOTTENADMIN" or $pAction == "DELETEADMIN") {
		include( "connectdb.php" );
		echo "<br><br><form action='endereco.php' id='instituicao' method='post'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<p><label for='endid'>" . _("Institui&ccedil;&atilde;o") . "</label>\n";
		echo "<select onchange='submit()' name='endid' id='endid' class='form-control' autofocus>\n";
		if ($id == "admin") {
			$sql = "SELECT DISTINCT e.id, e.nome FROM enderecos e ORDER BY 2";
		} else {
			$sql = "SELECT DISTINCT e.id, e.nome FROM usuend ue INNER JOIN enderecos e ON ue.endid = e.id WHERE ue.usuid = '$id' ORDER BY 2";
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
		echo "</select></p>";
		echo "</form>";
		mysql_close($dblink);
	}
	
	if ($pAction == "LISTADMIN" or $pAction == "GETADMIN") {
		ListaAdmin($endid, $id, $selall, $pAction);
	} elseif ($pAction == "GOTTENADMIN") {
		IncluiAdmin($eliassdes, $endid);
		ListaAdmin($endid, $id, $selall, "LISTADMIN");
	} elseif ($pAction == "DELETEADMIN") {
		ExcluiAdmin($eliassdes, $endid);
		ListaAdmin($endid, $id, $selall, "LISTADMIN");
	}
	
	echo "</div></div>";
				
	include 'rodape.inc';

?>