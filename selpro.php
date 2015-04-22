<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	function ListaDados($selall, $disid, $tipo, $usuid, $pAction) {
		
		include 'connectdb.php';
		
		if ($pAction == 'GET') {
			echo "<a href='#' onClick='abrirPag(" . '"selpro.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Professores associados") . "</button></a><br><br>";
		} else {
			echo "<a href='#' id='textLink' onClick='abrirPag(" . '"selpro.php", "pAction=GET' . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Associar novos professores") . "</button></a>";
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Professor") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		$sql = "SELECT endid FROM disciplina WHERE id = '$disid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		
		$endid = $linha["endid"];
		
		if ($pAction == "GET") {
			$sql = "SELECT DISTINCT u.id, u.nome, u.email FROM usuend ue INNER JOIN usuario u ON ue.usuid = u.id LEFT JOIN disusu du 
			ON u.id = du.usuid AND du.disid = $disid WHERE u.professor = 1 AND ue.endid = '$endid' AND u.id <> '$usuid' AND du.usuid IS NULL";
		} else {
			$sql = "SELECT u.id, u.nome, u.email FROM disusu du INNER JOIN usuario u ON du.usuid = u.id WHERE du.disid = '$disid'";
		}
		
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {
			
			echo "<table class='table'><thread><tr>\n";
			echo "<th width=5%></th><th align='left'>" . _("Nome") . "</th></tr></thread><tbody>\n";

			if ($pAction == "GET") {
				echo "<form action='selpro.php' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTEN'>\n";
				echo "<input type='hidden' name='disid' value='$disid'>\n";
			} else {			
				echo "<form action='selpro.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='MISS'>\n";
				echo "<input type='hidden' name='disid' value='$disid'>\n";
			}

			while ($linha = mysql_fetch_array($result)) {
				
				echo "<tr><td width='5%' nowrap align='right'>\n";
					
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='assdes[" . $linha["id"] . "]' CHECKED\n>";
				}
					
				echo "</td><td align='left'><a href='#' onClick='abrirPag(" . '"selpro.php", "pAction=SELECT&disid=' . $disid .
				'&perfil=' . $linha["id"] . '")' . "'>" . $linha["nome"] . "</a></td></tr>\n";					
			}
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; outros professores associados a essa disciplina") . "...\n</p>";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		echo "</tbody></table>\n";
		
		mysql_close($dblink);
		
		echo "<table><tr valign='top'>\n";
		
		if ($pAction == "GET") {
			echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Associar") . "'></form></td>\n";
		} else {
			echo "<td><input type='submit' class='btn btn-danger' name='enviar' value='" . _("Desassociar") . "'></form></td>\n";
		}

		echo "<td><form action='selpro.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='disid' value='$disid'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'>\n";
		echo "</form></td>\n";
		echo "<td><form action='selpro.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='disid' value='$disid'>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<input class='btn btn-default' type='submit' value='" . _("Desmarcar todos") . "'>\n" ;
		echo "</form></td></tr></table>\n";
		
		echo "</div></div>";

		return;
	
	}
	
	function ListaRelacoes($perfil, $disid, $pAction, $tipo) {

		if ($pAction == "SELECT") {
			echo "<a href='#' onClick='abrirPag(" . '"selpro.php", "pAction=GET&disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Professores cadastrados") . "</button></a><br><br>";
		} else {
			echo "<a href='#' onClick='abrirPag(" . '"selpro.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Professores associados") . "</button></a><br><br>";
		}
		
		include 'perfil.inc';		
	}
	
	function Desassociar($disid, $assdes) {

		include( "./connectdb.php" );

		foreach ($assdes as $professorid => $valor) {
			if ($valor == 'on') {
				$sql = "DELETE FROM disusu WHERE usuid = '$professorid' AND disid = '$disid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
	
		mysql_close($dblink);
	
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>";
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos um professor ...") . "</strong></div>";
		}

	}

	function Associar($disid, $assdes) {
		
		include( "./connectdb.php" );

		foreach ($assdes as $professorid => $valor) {
			if ($valor == 'on') {
				$sql = "INSERT INTO disusu VALUES ('$disid', '$professorid' )";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
	
		mysql_close($dblink);
	
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>";
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos um professor ...") . "</strong></div>";
		}

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
				echo "<p>" . _("Voc&ecirc; optou por desassociar um professor do seu ambiente.") . "</p>";
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
		
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-apple' aria-hidden='true'></span>&nbsp;" . _("Professores") . "</h3></div>";
	
	if ($pAction == "SELECT") {
		ListaRelacoes($perfil,$disid, $pAction, $tipo);
	} elseif ($pAction == "GOTTEN") {
		Associar($disid, $assdes);
		ListaDados($selall, $disid, $tipo, $id, $pAction);
	} elseif ($pAction == "MISS") {
		Desassociar($disid, $assdes);
		ListaDados($selall, $disid, $tipo, $id, $pAction);
	} else {
		ListaDados($selall, $disid, $tipo, $id, $pAction);
	}
	
	include 'rodape.inc';

?>
