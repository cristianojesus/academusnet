<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];

	if ($tipo == 0) {
		die("Sess&atilde;o Expirada. Fa&ccedil;a um novo <a href='login.php'>login</a> ...");
	}
	
	function ListaDados($selall, $queid, $tesid) {

		include("./connectdb.php");

		$sql = "SELECT id, texto, resposta FROM alternativa WHERE queid = '$queid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			echo "<br><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" . _("Altera dados da alternativa") . "\n" ;
			echo "<br><br><table class='table'><thread><tr>\n" ;
			echo "<th></th><th></th><th>Alternativas</th><th>Resposta</th></tr></thread><tbody>\n";

			echo "<form action='alternativas.php' id='deleteForm' name='deleteForm' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='DELETE'>\n";
			echo "<input type='hidden' name='queid' value='$queid'>\n";
			echo "<input type='hidden' name='tesid' value='$tesid'>\n";

			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td width='5%' align='right'>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td><td width='10%' align='left'><a href='#'
				onClick='abrirPag(" . '"alternativas.php", "pAction=UPDATE&queid=' . $queid . '&altid=' . $linha["id"] . '&tesid=' . $tesid .  '")' . "'>\n";
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>\n";
				echo "<td>" . $linha["texto"] . "</td>\n";
				if ($linha["resposta"] == "0") {
					echo "<td align='center'>" . _("N&atilde;o") . "</td>\n";
				} else {
					echo "<td align='center'>" . _("Sim") . "</td>\n";
				}
				echo "</tr>\n";
			}
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; alternativas registradas") . "...</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		echo "</tbody></table>\n";
		
		mysql_close($dblink);
		
		echo "<table><tr valign='top'>\n" ;
		echo "<td><button type='submit' class='btn btn-default' name='enviar'>" . _("Excluir") . "</button></form></td>\n";
		echo  "<td><form id='selall' action='alternativas.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='queid' value='$queid'>\n";
		echo "<input type='hidden' name='tesid' value='$tesid'>\n";
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Marcar todos") . "</button>\n";
		echo  "</form></td>\n" ;
		echo  "<td><form id='selall' action='alternativas.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='queid' value='$queid'>\n";
		echo "<input type='hidden' name='tesid' value='$tesid'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Desmarcar todos") . "</button>\n";
		echo  "</form></td></tr>\n" ;
		echo "</table>\n";

		return;
	
	}
	
	function ExcluiDados($eliminar) {
		if (!empty($eliminar)) {
			include( "./connectdb.php" );
			foreach ($eliminar as $altid => $valor) {	
				if ($valor == 'on') {
					$sql = "DELETE FROM alternativa WHERE id = '$altid'" ;
					$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				}
			}
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
			mysql_close($dblink);
		}
		return;
	}
	
	function IncluiDados($queid, $texto, $resposta) {
		if (empty($texto)) {
			echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" . 
			_("Campos obrigat&oacute;rios n&atilde;o foram preenchidos. Os dados n&atilde;o foram inclu&iacute;dos ...") . "</strong></div><br>";
		} else {		
			include( "./connectdb.php" );
			$sql = "INSERT INTO alternativa VALUES (null, '$queid', '$texto', '$resposta')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
			mysql_close($dblink);
		}
		return;
	}
	
	function AlteraDados($altid, $texto, $resposta) {
		include( "./connectdb.php" );
		$sql = "UPDATE alternativa SET texto = '$texto', resposta = '$resposta' WHERE id = '$altid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		mysql_close($dblink);
		return;
	}
	
	function Formulario($queid, $texto, $resposta) {
		
		include( "./connectdb.php" );
		$sql = "SELECT id, texto, resposta FROM alternativa WHERE queid = '$queid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		if (mysql_num_rows($result) > 0) {
			echo "<blockquote><ul>";
			while ($linha = mysql_fetch_array($result)) {
				echo "<li>" . $linha["texto"];
				if ($linha["resposta"]) {
					echo "&nbsp;(" . _("alternativa correta") . ")";
				}
			}
			echo "</ul></blockquote><br>";
		}
		mysql_close($dblink);
		
		echo _("Asterisco (*) indica campo obrigat&oacute;rio");
				
		echo "<br><br><p><label for='texto'>(*) " . _("Quest&atilde;o") . "&nbsp" . 
		_("(Obs: Caso seja necess&aacute;rio, utilize o") . "&nbsp<a href='http://www.codecogs.com/latex/eqneditor.php?lang=pt-br' target=_blank'>" . 
		_("Editor on line de equa&ccedil;&otilde;es") . " LaTex</a>)<br></label>\n";
		echo "<textarea id='texto' name='texto' class='form-control'>$texto</textarea></p>";
		echo "<p><label for='resposta'>" . _("&Eacute; a alternativa correta?") . "</label>\n";

		echo "<select name='resposta' class='form-control'>\n";
		if ($resposta == 0) {
			echo "<option value='0' selected>" . _("N&atilde;o") . "</option>\n";
			echo "<option value='1'>" . _("Sim") . "</option>";
		} else {
			echo "<option value='0'>" . _("N&atilde;o") . "</option>\n";
			echo "<option value='1' selected>" . "Sim" . "</option>";
		}
		echo "</select></p>";
		echo "<input type='submit' name='enviar' class='btn btn btn-default' value='" . _("Enviar") . "'></form>\n";
	}
	
	include( "cabecalho.php" );
	
	if (empty($disid)) {
		include( "menup.inc" );
	} else {
		include( "menu.inc" );
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
						echo "<p>" . _("Voc&ecirc; optou por excluir alternativas.") . "</p>";
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
		
	echo "<br><h2 class='blog-post-title'><span class='glyphicon glyphicon-check' aria-hidden='true'></span>&nbsp;" . _("Alternativas") . "</h2>";
		
	echo "</div>";
	
	include 'connectdb.php';
	$sql = "SELECT texto FROM questoes WHERE id = '$queid'";
	$result = mysql_query($sql, $dblink) or die(mysql_error());
	$linha = mysql_fetch_array($result);
	
	if (empty($disid)) {
		echo "<a href='questoes.php'><button type='button' class='btn btn btn-default'>" . _("Quest&otilde;es dispon&iacute;veis") . "</button></A>\n";
	} else {
		echo "<a href='#' id='textLink' onClick='abrirPag(" . '"prova.php", "pAction=LIST&tesid=' . $tesid . '"' . ")'><button type='button' class='btn btn btn-default'>" 
		. _("Quest&otilde;es") . "</button></a>\n";
	}
	if ($pAction == "INSERT" or $pAction == "INSERTED" or $pAction == "UPDATE") {
		echo "<a href='#' onClick='abrirPag(" . '"alternativas.php", "queid=' . $queid . '&tesid=' . $tesid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
		_("Alternativas dispon&iacute;veis") . "</button></A>\n";
	} else {
		echo "<a href='#' onClick='abrirPag(" . '"alternativas.php", "queid=' . $queid . '&tesid=' . $tesid .  '&pAction=INSERT"' . ")'>
		<button type='button' class='btn btn btn-default'>" . _("Incluir novas alternativas") . "</button></A>\n";
	}
	
	echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Alternativas") . "</h3></div>";
	echo "<div class='panel-body'>";
	
	echo "<h3><strong>" . _("Quest&atilde;o") . "</strong>: <br><br>" . $linha["texto"] . "</h3><br>";

	if ($pAction == "DELETE") {
		ExcluiDados($eliminar);
		ListaDados($selall, $queid, $tesid);
	} elseif ($pAction == "INSERT") {
		echo "<form action='alternativas.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		echo "<input type='hidden' name='queid' value='$queid'>\n";
		echo "<input type='hidden' name='tesid' value='$tesid'>\n";
		Formulario($queid, null, null);
	} elseif ($pAction == "UPDATE") {
		echo "<form action='alternativas.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
		echo "<input type='hidden' name='queid' value='$queid'>\n";
		echo "<input type='hidden' name='altid' value='$altid'>\n";
		echo "<input type='hidden' name='tesid' value='$tesid'>\n";
		include 'connectdb.php';
		$sql = "SELECT texto, resposta FROM alternativa WHERE id='$altid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		Formulario($altid, $linha["texto"], $linha["resposta"]);
	} elseif ($pAction == "INSERTED" or $pAction == "UPDATED") {
		if ($pAction == "UPDATED") {
			AlteraDados($altid, $texto, $resposta);
			ListaDados($selall, $queid, $tesid);
		} else {
			IncluiDados($queid, $texto, $resposta);
			echo "<form action='alternativas.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
			echo "<input type='hidden' name='queid' value='$queid'>\n";
			echo "<input type='hidden' name='tesid' value='$tesid'>\n";
			Formulario($queid, null, null);
		}	
	} else {
		ListaDados($selall, $queid, $tesid);
	}
	
	include_once "ckeditor/ckeditor.php";
	$CKEditor = new CKEditor();
	$CKEditor->basePath = 'ckeditor/';
	$CKEditor->replace("texto");
	
	mysql_close($dblink);
	
	echo "</div></div>";
	
	include 'rodape.inc';

?>
