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
			echo "<a href='#' " . CriaLink("agenda.php", "pAction=INSERT") . "><button type='button' class='btn btn btn-default'>" .
			_("Incluir novos compromissos") . "</button></A><br><br>\n";
		}
		
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Agenda") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");

		$sql = "SELECT id, texto, data FROM agenda WHERE disid = '$disid' ORDER BY 3";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			if ($tipo == 1) {	
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" .
				_("Altera dados do compromisso") . "<br><br>\n";
			}
			
			echo "<table class='table'><thread><tr>\n" ;
		
			if ($tipo == 1) {	
				echo "<th></th><th></th>\n";
			}
		
			echo "<th>Data</th><th>Compromisso</th></tr></thread><tbody>\n";	

			echo "<form action='agenda.php' id='deleteForm' name='deleteForm' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='DELETE'>\n";

			while ($linha = mysql_fetch_array($result)) {
				echo "<tr>";
				if ($tipo == 1) {
					echo "<td width='5%' align='right'>\n";
					if ( empty( $selall ) ) {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
					}
					echo "</td><td width='5%'><a href='#' id='textLink' onClick='abrirPag(" . '"agenda.php", "pAction=UPDATE&ageid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>\n";
				}
				ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $linha["data"], $regs);
				$data = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
				echo "<td width='10%' nowrap>" . $data . "</td>\n";
				echo "<td>" . $linha["texto"] . "</td></tr>\n";
			}
			
			echo "</tbody></table>\n";
			
		} else {
			echo "<p class='lead'>N&atilde;o h&aacute; compromissos registrados ...</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		mysql_close($dblink);
		
		if ($tipo == 1) {
			echo "<table><tr valign='top'>\n" ;
			echo "<td><input type='submit' class='btn btn-danger' name='enviar' value='" . _("Excluir") . "'></form></td>\n" ;
			echo "<td><form action='agenda.php' id='selall' method='POST'>\n" ;
			echo "<input type='hidden' name='selall' value='1'>\n";
			echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'>\n";
			echo "</form></td>\n" ;
			echo "<td><form action='agenda.php' id='selall' method='POST'>\n";
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
			foreach ($eliminar as $ageid => $valor) {	
				if ($valor == 'on') {
					$SQL = "DELETE FROM agenda WHERE id = '$ageid'" ;
					$result = mysql_query( $SQL, $dblink );
				}
			}
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
		}
		return;
	}
	
	function IncluiDados($disid, $texto, $data, $detalhe) {
		
		include( "./connectdb.php" );
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $data, $regs);
		$data = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		$sql = "INSERT INTO agenda VALUES (null, '$disid', '$texto', '$data', '$detalhe')";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		
		include 'rss.inc';
		
		return;
	}
	
	function AlteraDados($ageid, $texto, $data, $detalhe) {
		
		include( "./connectdb.php" );
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $data, $regs);
		$data = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		$sql = "UPDATE agenda SET texto = '$texto', data = '$data', detalhe = '$detalhe' WHERE id = $ageid";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		
		include 'rss.inc';
		
		return;
	}
	
	function Formulario($texto, $data, $detalhe) {
		if (!empty($data)) {
			ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $data, $regs);
			$data = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
		}
		echo "<a href='agenda.php'><button type='button' class='btn btn btn-default'>" .
		_("Compromissos agendados") . "</button></A><br><br>\n";
		
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Atividades") . "</h3></div>";
		echo "<div class='panel-body'>";
				
		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br><br>";
		echo "<p><label for='data'>(*) " . _("Data") . "</label>\n";
		echo "<input type='text' name='data' id='data' value='$data' size=10 maxlength=10 class='form-control datepicker' required></p>\n";
		echo "<p><label for='texto'>(*) " . _("Compromisso") . "</label>\n";
		echo "<input type='text' name='texto' value='$texto' size=60 maxlength=90 class='form-control' required></p>\n";
		echo "<p><label for='detalhe'>" . _("Detalhes sobre o compromisso") . "<br></label></p>\n";
		echo "<textarea name='detalhe' rows='20' class='form-control'>$detalhe</textarea>\n";
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
	
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-calendar' aria-hidden='true'></span>&nbsp;" . _("Agenda") . "</h3></div>";

	if ($pAction == "DELETE") {
		ExcluiDados($eliminar);
		ListaDados($selall, $disid, $tipo);
	} elseif ($pAction == "INSERT" or $pAction == "INSERTED") {
		if ($pAction == "INSERTED") {
			IncluiDados($disid, $texto, $data, $detalhe);
		}
		echo "<form action='agenda.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario(null, null, null);
	} elseif ($pAction == "UPDATE" or $pAction == "UPDATED") {
		if ($pAction == "UPDATED") {
			AlteraDados($ageid, $texto, $data, $detalhe);
			ListaDados($selall, $disid, $tipo);
		} else {
			echo "<form action='agenda.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
			echo "<input type='hidden' name='ageid' value='$ageid'>\n";
			include 'connectdb.php';
			$sql = "SELECT texto, data, detalhe FROM agenda WHERE id='$ageid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			Formulario($linha["texto"], $linha["data"], $linha["detalhe"]);
			mysql_close($dblink);
		}	
	} else {
		ListaDados($selall, $disid, $tipo);
	}
	
	include 'rodape.inc';

?>