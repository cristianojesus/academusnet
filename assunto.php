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
	
	function ListaDados($selall, $usuid) {
		
		echo "<a href='#' onClick='abrirPag(" . '"assunto.php", "pAction=INSERT"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Incluir novos assuntos") . "</button></A><br><br>\n";
		
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Assunto") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");

		$sql = "SELECT id, descricao FROM assunto WHERE usuid = '$usuid' ORDER BY 2";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {

			echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" .
			_("Altera dados do assunto") . "\n";
			
			echo "<form action='assunto.php' id='deleteForm' name='deleteForm' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='DELETE'>\n";
			
			echo "<br><table class='table'><thread><tr>\n" ;
			echo "<th width=5%></th><th width=5%></th><th width=90%>" . _("Descri&ccedil;&atilde;o") . "</th></tr></thread><tbody>\n";	

			while ($linha = mysql_fetch_array($result)) {
				$assid = $linha["id"];
				echo "<tr><td>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td><td><a href='#' onClick='abrirPag(" . '"assunto.php", "pAction=UPDATE&assid=' . $linha["id"] . '")' . "'>\n";
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>\n";
				echo "<td>" . $linha["descricao"] . "</td></tr>\n";
			}
			
			echo "</tbody></table>\n";
			
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; assuntos registrados") . "...</p>\n";
			mysql_close($dblink);
			return;
		}
		
		mysql_close($dblink);
		
		echo "<table><tr valign='top'>\n" ;
		echo "<td><button type='submit' class='btn btn-danger' name='enviar'>" . _("Excluir") . "</button></form></td>\n" ;
		echo "<td><form action='assunto.php' id='selall' method='POST'>\n" ;
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Marcar todos") . "</button>\n" ;
		echo "</form></td>\n" ;
		echo "<td><form action='assunto.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Desmarcar todos") . "</button>\n" ;
		echo "</form></td></tr></table><br>\n" ;

		return;
	
	}
	
	function ExcluiDados($eliminar) {
		if (!empty($eliminar)) {
			include( "./connectdb.php" );
			foreach ($eliminar as $assid => $valor) {	
				if ($valor == 'on') {
					$SQL = "DELETE FROM assunto WHERE id = '$assid'" ;
					if (!$QResult = mysql_query( $SQL, $dblink )) {
						echo "<br><br><div class='alert alert-danger' role='alert'><strong>" .
						_("O assunto est&aacute; sendo usado.") .
						"&nbsp;" . _("Exclus&atilde;o interrompida ...") . "</strong></div><br>" ;
						return;
					}
				}
			}
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" .
			_("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
			mysql_close($dblink);
		}
		return;
	}
	
	function IncluiDados($usuid, $descricao) {
		include( "./connectdb.php" );
		$sql = "INSERT INTO assunto VALUES (null, '$descricao', '$usuid')";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		mysql_close($dblink);
		return;
	}
	
	function AlteraDados($assid, $descricao) {
		include( "./connectdb.php" );
		$sql = "UPDATE assunto SET descricao = '$descricao' WHERE id = $assid";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		mysql_close($dblink);
		return;
	}
	
	function Formulario($descricao) {
		echo "<a href='assunto.php'><button type='button' class='btn btn btn-default'>" . _("Assuntos cadastrados") . "</button></A><br><br>\n";
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Assunto") . "</h3></div>";
		echo "<div class='panel-body'>";
		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br>";
		echo "<br><br><p><label for='texto'>(*)" . _("T&iacute;tulo") . "</label>\n";
		echo "<input type='text' name='descricao' value='$descricao' size=60 maxlength=90 class='form-control' required autofocus></p>\n";
		echo "<button type='submit' name='enviarage' class='btn btn btn-default'>" . _("Enviar") . "</button></form><br>\n";
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
					echo "<p>" . _("Voc&ecirc; optou por excluir assuntos.") . "</p>";
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
	
	echo "<br><h2 class='blog-post-title'><span class='glyphicon glyphicon-tag' aria-hidden='true'></span>&nbsp;" . _("Assuntos") . "</h2>";
	
	echo "</div>";
	
	if ($pAction == "DELETE") {
		ExcluiDados($eliminar);
		ListaDados($selall, $id);
	} elseif ($pAction == "INSERT" or $pAction == "INSERTED") {
		if ($pAction == "INSERTED") {
			IncluiDados($id, $descricao);
		}
		echo "<form action='assunto.php' method='POST'>\n";
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario(NULL);
	} elseif ($pAction == "UPDATE" or $pAction == "UPDATED") {
		if ($pAction == "UPDATED") {
			AlteraDados($assid, $descricao);
			ListaDados($selall, $id);
		} else {
			echo "<form action='assunto.php' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
			echo "<input type='hidden' name='assid' value='$assid'>\n";
			include 'connectdb.php';
			$sql = "SELECT descricao FROM assunto WHERE id='$assid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			Formulario($linha["descricao"]);
			mysql_close($dblink);
		}	
	} else {
		ListaDados($selall, $id);
	}
	
	echo "</div></div>";
	
	include 'rodape.inc';

?>
