<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];

	if ($tipo == 0) {
		echo _("Sess&atilde;o Expirada. Fa&ccedil;a um novo") . "&nbsp;<a href='login.php'>login</a> ...";
		exit;
	}
	
	function ListaDados($selall, $usuid, $assid, $bloom) {
		
		echo "<a href='#' onClick='abrirPag(" . '"questoes.php", "pAction=INSERT"' . ")'>
		<button type='button' class='btn btn btn-default'>" . _("Incluir novas quest&otilde;es") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Quest&otilde;es") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");

		$sql = "SELECT q.id, q.assid, q.texto, q.tipo, a.descricao, q.bloom FROM questoes q LEFT JOIN assunto a ON q.assid = a.id WHERE q.usuid = '$usuid'";

		if (!empty($assid) and $assid != 0) {
			$sql .= " AND q.assid = '$assid'";
		}
		
		if (!empty($bloom) and $bloom != 0) {
			$sql .= " AND q.bloom = '$bloom'";
		}
		
		$sql .= " ORDER BY 5, 3";
		
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0 or !empty($assid) or !empty($bloom)) {
			
			echo "<form action='questoes.php' method='POST'>\n";
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
			echo "<input type='hidden' name='disid' value='$disid'></p></form>\n";
			
			echo "<form action='questoes.php' method='POST'>\n";
			echo "<p><label for='bloom'>" . _("Compet&ecirc;ncia") . "</label>\n";
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
			echo "<input type='hidden' name='disid' value='$disid'></p></form>\n";
			
			if (mysql_num_rows($result) > 0) {
			
				echo "<br><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" . _("Altera dados da quest&atilde;o") . "\n";
				echo "<br><span class='glyphicon glyphicon-th-list' aria-hidden='true'></span>&nbsp;" . _("Inclus&atilde;o de alternativas") . "\n";
			
				echo "<br><br><small><table class='table table-condensed table'><thread><tr>\n";
				echo "<th width='5%'></th><th width='5%'></th><th width='55%'>" . _("Quest&otilde;es") . "</th></tr></thread></tbody>\n";	

				echo "<form action='questoes.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='DELETE'>\n";
				
			} else {
				echo "<p class='lead'>" . _("N&atilde;o h&aacute; quest&otilde;es registradas") . "...</p>\n";
				mysql_close($dblink);
				echo "</div></div>";
				return;
			}

			while ($linha = mysql_fetch_array($result)) {
				$queid = $linha["id"];
				echo "<tr><td width='5%' align='right'>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td><td width='5%' nowrap><a href='#' onClick='abrirPag(" . '"questoes.php", 
				"pAction=UPDATE&queid=' . $linha["id"] . '")' . "'>\n";
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>\n";
				if ($linha["tipo"] == "A") {
					echo "<a href='#' onClick='abrirPag(" . '"alternativas.php", "queid=' . $linha["id"] . '")' . "'>
					<span class='glyphicon glyphicon-th-list' aria-hidden='true'></span></a></td>";	
				} else {
					echo "</td>";
				}
				echo "<td width='55%'>" . $linha["texto"] . "<br><span class='label label-info'>\n";
				if ($linha["tipo"] == "A") {
					echo _("Alternativas") . "\n";
				} elseif ($linha["tipo"] == "Q") {
					echo _("Arquivo") . "\n";
				} else {
					echo _("Descritiva") . "\n";
				}
				echo "</span>";
				if (!empty($linha["descricao"])) {
					echo "&nbsp;<span class='label label-info'>" . $linha["descricao"] . "</span>\n";
				}
				
				echo "&nbsp;<span class='label label-info'>";
				
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
					default:
						echo "---";
						break;
				}
				
				echo "</span></tr>";
				
			}
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; quest&otilde;es registradas") . "...</p>\n";
			mysql_close($dblink);
			echo "</div></div>";
			return;
		}
		
		echo "</tbody></table></small>\n";
		
		mysql_close($dblink);
		
		echo "<table><tr valign='top'>\n" ;
		echo "<td><button type='submit' class='btn btn-danger' name='enviar'>" . _("Excluir") . "</button></form></td>\n";
		echo  "<td><form id='selall' action='questoes.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Marcar todos") . "</button>\n";
		echo  "</form></td>\n" ;
		echo  "<td><form id='selall' action='questoes.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Desmarcar todos") . "</button>\n";
		echo  "</form></td></tr>\n" ;
		echo "</table>\n";
		
		echo "</div></div>";

		return;
	
	}
	
	function ExcluiDados($eliminar) {
		if (!empty($eliminar)) {
			include( "./connectdb.php" );
			foreach ($eliminar as $queid => $valor) {	
				if ($valor == 'on') {
					$sql = "DELETE FROM questoes WHERE id = '$queid'" ;
					$result = mysql_query( $sql, $dblink );
				}
			}
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
			mysql_close($dblink);
		}
		return;
	}
	
	function IncluiDados($usuid, $assid, $texto, $tipoq, $resposta, $assunto, $bloom) {
		
		if (empty($texto)) {
			echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" . 
			_("Campos obrigat&oacute;rios n&atilde;o foram preenchidos. Os dados n&atilde;o foram inclu&iacute;dos ...") . "</strong></div><br>";
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
			$sql = "INSERT INTO questoes VALUES (null, '$assid', '$texto', '$tipoq', '$resposta', '$usuid', '$bloom')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
			mysql_close($dblink);
		}
		return;
	}
	
	function AlteraDados($assid, $texto, $tipoq, $resposta, $assunto, $bloom, $usuid, $queid) {
		
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
	
		$sql = "UPDATE questoes SET assid = $assid, texto = '$texto', tipo = '$tipoq', resposta = '$resposta', bloom = '$bloom' WHERE id = '$queid'";
		
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		mysql_close($dblink);
		return;
	}
	
	function Formulario($assid, $texto, $tipoq, $resposta, $bloom, $usuid) {
		
		echo "<a href='questoes.php'><button type='button' class='btn btn btn-default'>" . _("Quest&otilde;es dispon&iacute;veis") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Quest&otilde;es") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo _("Asterisco (*) indica campo obrigat&oacute;rio");

		echo "<br><br><p><label for='texto'>(*) " . _("Quest&atilde;o") . "&nbsp" . 
		_("(Obs: Caso seja necess&aacute;rio, utilize o") . "&nbsp<a href='http://www.codecogs.com/latex/eqneditor.php?lang=pt-br' target=_blank'>" . 
		_("Editor on line de equa&ccedil;&otilde;es") . " LaTex</a>)<br></label>\n";
		echo "<textarea id='texto' name='texto' class='form-control' autofocus required>$texto</textarea></p>";
		
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
		echo "</select>\n";
			
		mysql_close($aDBLink);
				
		if ($assid == "NA") {
			echo "<br><input type='text' name='assunto' size=60 maxlength=69 class='form-control'></p>\n";
		}
		
		echo "<p><label for='tipo'>" . _("Tipo") . "</label>\n";

		echo "<select name='tipoq' class='form-control'>\n";
		if ($tipoq == 'A') {
			echo "<option value='A' selected>" . _("Alternativas") . "</option>\n";
			echo "<option value='Q'>" . _("Arquivo") . "</option>";
			echo "<option value='D'>" . _("Descritiva") . "</option>\n";			
		} elseif ($tipoq == 'Q') {
			echo "<option value='A'>" . _("Alternativas") . "</option>\n";
			echo "<option value='Q' selected>" . ("Arquivo") . "</option>";
			echo "<option value='D'>" . ("Descritiva") . "</option>\n";
		} else {
			echo "<option value='A'>" . _("Alternativas") . "</option>\n";
			echo "<option value='Q'>" . ("Arquivo") . "</option>";
			echo "<option value='D' selected>" . ("Descritiva") . "</option>\n";			
		}
		echo "</select></p>\n";
		
		echo "<p><label for='bloom'>" . _("Habilidades, segundo Taxonomia de Bloom") . "</label>\n";
		
		echo "<select name='bloom' class='form-control'>\n";
		
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
		
		echo "<p><label for='resposta'>" . _("Resposta ou Coment&aacute;rio") . "</label><br>\n";
		echo "<textarea id='resposta' name='resposta' cols='90' rows='20' class='form-control'>$resposta</textarea></p>";
		echo "<input type='submit' name='enviar' class='btn btn btn-default' value='" . _("Enviar") . "'></form>\n";
		
		echo "</div></div>";
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
						echo "<p>" . _("Voc&ecirc; optou por excluir quest&otilde;es.") . "</p>";
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
		
	echo "<br><h2 class='blog-post-title'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span>&nbsp;" . _("Quest&otilde;es") . "</h2>";
		
	echo "</div>";

	if ($pAction == "DELETE") {
		ExcluiDados($eliminar);
		ListaDados($selall, $id, $assid, $bloom);
	} elseif ($pAction == "INSERT") {
		echo "<form action='questoes.php' method='POST'>\n";
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario(null, null, null, null, null, $id);
	} elseif ($pAction == "UPDATE") {
		echo "<form action='questoes.php' method='POST'>\n";
		echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
		echo "<input type='hidden' name='queid' value='$queid'>\n";
		include 'connectdb.php';
		$sql = "SELECT assid, texto, tipo, resposta, bloom FROM questoes WHERE id='$queid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		Formulario($linha["assid"], $linha["texto"], $linha["tipo"], $linha["resposta"], $linha["bloom"], $id);
	} elseif ($pAction == "INSERTED" or $pAction == "UPDATED") {
		if (!empty($enviar)) {
			if ($pAction == "UPDATED") {
				AlteraDados($assid, $texto, $tipoq, $resposta, $assunto, $bloom, $id, $queid);
				ListaDados($selall, $id, $assid, null);
			} else {
				IncluiDados($id, $assid, $texto, $tipoq, $resposta, $assunto, $bloom);
				echo "<form action='questoes.php' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
				Formulario(null, null, null, null, null, $id);
			}
		} else {
			if ($pAction == "INSERTED") {
				echo "<form action='questoes.php' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
			} else {
				echo "<form action='questoes.php' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
				echo "<input type='hidden' name='queid' value='$queid'>\n";
			}
			if (!empty($texto) or !empty($resposta)) {
				include 'connectdb.php';
				$sql = "INSERT INTO questoes VALUES (null, null, '$texto', '$tipoq', '$resposta', '$id', '$bloom')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$sql = "SELECT LAST_INSERT_ID() as queid";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$linha = mysql_fetch_array($result);
				$queid = $linha["queid"];
				$sql = "SELECT texto, resposta FROM questoes WHERE id = '$queid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$linha = mysql_fetch_array($result);
				$texto = $linha["texto"];
				$resposta = $linha["resposta"];
				$sql = "DELETE FROM questoes WHERE id = '$queid'";
				$result = mysql_query($sql, $dblink) or die(mysql_error());
			}
			Formulario($assid, $texto, $tipoq, $resposta, $bloom, $id);
		}		
	} else {
		ListaDados($selall, $id, $assid, $bloom);
	}
	
	include_once "ckeditor/ckeditor.php";
	$CKEditor = new CKEditor();
	$CKEditor->basePath = 'ckeditor/';
	$CKEditor->replace("texto");
	$CKEditor->replace("resposta");
	
	mysql_close($dblink);
	
	include 'rodape.inc';

?>
