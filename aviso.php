<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	if ($tipo == 0) {
		$pAction = "";
	}
	
	function ListaDados($selall, $disid, $tipo) {
		
		if ($tipo == 1) {
			echo "<a href='#' id='textLink' onClick='abrirPag(" . '"aviso.php", "pAction=INSERT"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Compartilhar not&iacute;cias") . "</button></A>\n";
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Feed de not&iacute;cias") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");

		$sql = "SELECT id, titulo, texto, DATE_FORMAT(datav, '%Y-%m-%d %H:%i:%s') as datav FROM aviso WHERE disid = '$disid' ORDER BY 3";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			if ($tipo == 1) {
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" . _("Altera o conte&uacute;do compartilhado") . "\n" ;
			}
			
			echo "<br><br><table class='table'><thread><tr>\n" ;
			
			if ($tipo == 1) {
				echo "<th width=5% nowrap></th><th width=5% nowrap></th>\n";
			}
			
			
			echo "<th>Publica&ccedil;&atilde;o</th><th>Aviso</th></tr></thread></tbody>\n";

			echo "<form action='aviso.php' id='deleteForm' name='deleteForm' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='DELETE'>\n";

			while ($linha = mysql_fetch_array($result)) {
				echo "<tr>\n";
				if ($tipo == 1) {
					echo "<td align='right'>\n";
					if ( empty( $selall ) ) {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
					}
					echo "</td><td><a href='#' onClick='abrirPag(" . '"aviso.php", "pAction=UPDATE&avid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>\n";
				}
				if (!empty($linha["datav"])) {
					echo "<td align='center'>" . $linha["datav"] . "</td>\n";
				} else {
					echo "<td align='center'>---</td>\n";
				}
				echo "<td><b>" . $linha["titulo"] . "</b><br>" . $linha["texto"] . "</td></tr>\n";
			}
			
			echo "</tbody></table>\n";
			
		} else {
			echo "<p class='lead'>N&atilde;o h&aacute; avisos ou not&iacute;cias registrados ...</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		mysql_close($dblink);
		
		if ($tipo == 1) {
			echo "<table><tr valign='top'>\n" ;
			echo "<td><input type='submit' class='btn btn-danger' name='enviar' value='" . _("Excluir") . "'></form></td>\n" ;
			echo "<td><form action='aviso.php' id='selall' method='POST'>\n" ;
			echo "<input type='hidden' name='selall' value='1'>\n";
			echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'>\n";
			echo "</form></td>\n" ;
			echo "<td><form action='aviso.php' id='selall' method='POST'>\n";
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
			foreach ($eliminar as $avid => $valor) {	
				if ($valor == 'on') {
					$SQL = "DELETE FROM aviso WHERE id = '$avid'" ;
					$result = mysql_query( $SQL, $dblink );
				}
			}
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
		}
		return;
	}
	
	function IncluiDados($disid, $titulo, $texto, $datav) {
		
		if (empty($texto) or empty($titulo)) {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Campos n&atilde;o preenchidos. Os dados n&atilde;o foram alterados ...") . "</strong></div>" ;
			return;
		} else {			
			include( "./connectdb.php" );
			$sql = "INSERT INTO aviso VALUES (null, '$disid', '$titulo', '$texto', now())";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
			
			include 'rss.inc';
			
			return;
		}
	}
	
	function AlteraDados($avid, $titulo, $texto) {
		if (empty($texto) or empty($titulo)) {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Campos n&atilde;o preenchidos. Os dados n&atilde;o foram alterados ...") . "</strong></div>" ;
			return;
		} else {
			
			include( "./connectdb.php" );
			$sql = "UPDATE aviso SET titulo = '$titulo', texto = '$texto', datav = now() WHERE id = $avid";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
			
			include 'rss.inc';
			
			return;
		}
	}
	
	function Formulario($titulo, $texto) {
		
		echo "<br><a href='aviso.php' id='textLink'><button type='button' class='btn btn btn-default'>" . _("Conte&uacute;do compartilhado") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Feed de not&iacute;cias") . "</h3></div>";
		echo "<div class='panel-body'>";		

		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br><br>";
		
		echo "<p><label for='titulo'>(*) " . _("T&iacute;tulo") . "</label>\n";
		echo "<input type='text' name='titulo' value='$titulo' size=60 maxlength=90 class='form-control' autofocus required></p>\n";
		echo "<p><label for='texto'>(*) " . _("Aviso/Not&iacute;cia") . "</label>\n";
		echo "<textarea name='texto' rows='20' class='form-control'>$texto</textarea></p>\n";
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
				echo "<p>" . _("Voc&ecirc; optou por excluir um conte&uacute;do compartilhado.") . "</p>";
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
	
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-share' aria-hidden='true'></span>&nbsp;" . _("Feed de not&iacute;cias") . "</h3></div>";

	if ($pAction == "DELETE") {
		ExcluiDados($eliminar);
		ListaDados($selall, $disid, $tipo);
	} elseif ($pAction == "INSERT" or $pAction == "INSERTED") {
		if ($pAction == "INSERTED") {
			IncluiDados($disid, $titulo, $texto);
		}
		echo "<form action='aviso.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario(null);
	} elseif ($pAction == "UPDATE" or $pAction == "UPDATED") {
		if ($pAction == "UPDATED") {
			AlteraDados($avid, $titulo, $texto);
			ListaDados($selall, $disid, $tipo);
		} else {
			echo "<form action='aviso.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
			echo "<input type='hidden' name='avid' value='$avid'>\n";
			include 'connectdb.php';
			$sql = "SELECT texto, titulo FROM aviso WHERE id='$avid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			Formulario($linha["titulo"], $linha["texto"]);
			mysql_close($dblink);
		}	
	} else {
		ListaDados($selall, $disid, $tipo);
	}
	
	include_once "ckeditor/ckeditor.php";
	$CKEditor = new CKEditor();
	$CKEditor->basePath = 'ckeditor/';
	$CKEditor->replace("texto");
	
	include 'rodape.inc';

?>
