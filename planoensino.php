<?php
	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];

	function ExcluiDados($disid) {
		include( "./connectdb.php" );
		$sql = "DELETE FROM planoensino WHERE disid = '$disid'" ;
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
	}

	function ManutencaoDados($pAction, $disid, $cargahorsem, $cargahortot, $ementa, $objetivos, $conteudo, $metodologia, $avaliacao, $recursos, 
	$bibliografiab, $bibliografiac) {

		include( "./connectdb.php" );

		if (empty($cargahorsem) or empty($cargahortot) or empty($ementa) or empty($objetivos) or empty($conteudo) or empty($metodologia) or empty($avaliacao) 
		or empty($recursos) or empty($bibliografiab) or empty($bibliografiac) ) {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . 
			_("H&aacute; campos n&atilde;o preenchidos. Os dados n&atilde;o foram inclu&iacute;dos ...") . "</strong></div>" ;
		} else {
			if ($pAction == "INSERTED") {
				$sql = "INSERT INTO planoensino VALUES ($disid, '$cargahorsem', '$cargahortot', '$ementa', '$objetivos', 
				'$conteudo', '$metodologia', '$avaliacao', '$recursos', '$bibliografiab', '$bibliografiac')";
			} else {
				$sql = "UPDATE planoensino SET cargahorsem = '$cargahorsem', cargahortot = '$cargahortot', ementa = 
				'$ementa', objetivos = '$objetivos', conteudo = '$conteudo', metodologia = '$metodologia', 
				avaliacao = '$avaliacao', recursos = '$recursos', bibliografiab = '$bibliografiab', bibliografiac = '$bibliografiac' WHERE disid = '$disid'";
			}
			
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());

			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
				
			mysql_close($dblink);
			return 1;

		}
		mysql_close($dblink);

	}

	include( "cabecalho.php" );
	
	include( "menu.inc" );
	
?>
		
	<script type="text/javascript">

	$(function(){

		$('#Confirmar').click(function () {
			abrirPag("planoensino.php", "pAction=DELETE&disid=<?php echo $disid;?>");
			modal.modal('hide');
		});
			
		$('#hrefExc').click(function(e){
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
					echo "<p>" . _("Voc&ecirc; optou por excluir o plano de ensino.") . "</p>";
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
		
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-book' aria-hidden='true'></span>&nbsp;" . _("Plano de ensino") . "</h3></div>";
	
	include( "./connectdb.php" );
	
	$sql = "SELECT d.curid, c.endid FROM disciplina d INNER JOIN curso c ON c.id = d.curid WHERE d.id = '$disid'";
	$result = mysql_query( $sql, $dblink ) or die(mysql_error());
	$linha = mysql_fetch_array($result);
	
	$endid = $linha["endid"];
	$curid = $linha["curid"];
	
	$sql = "SELECT * FROM endadmin WHERE endid = '$endid' AND usuid = '$id'";
	$result = mysql_query( $sql, $dblink ) or die(mysql_error());
	if ( mysql_num_rows($result) == 0) {
		$sql = "SELECT * FROM curadmin WHERE curid = '$curid' AND usuid = '$id'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		if ( mysql_num_rows($result) == 0) {
			$coordenador = 0;
		} else {
			$coordenador = 1;
		}
	} else {
		$coordenador = 1;
	}
	
	mysql_close($dblink);	
	
	if ($tipo != 1 and $pAction != "SELECT") {
		$pAction = "";
	}

	if ($pAction == "UPDATED" or $pAction == "INSERTED") {

		ManutencaoDados($pAction, $disid, strip_tags($cargahorsem), strip_tags($cargahortot), strip_tags($ementa), 
		strip_tags($objetivos), strip_tags($conteudo), strip_tags($metodologia), strip_tags($avaliacao), strip_tags($recursos), 
		strip_tags($bibliografiab),	strip_tags($bibliografiac));

	} elseif ($pAction == "DELETE") {

		ExcluiDados($disid);
	
	}

	if (empty($pAction) or $pAction == "SELECT") {

		include( "./connectdb.php" );

		$sql = "SELECT * FROM planoensino WHERE disid = '$disid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {
			
			if ($tipo == 1) {
				echo "<a href='#' onClick='abrirPag(" . '"planoensino.php", "pAction=SELECT&disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
				_("Gerar PDF") . "</button></a>\n";
				echo "<a href='#' onClick='abrirPag(" . '"planoensino.php", "pAction=UPDATE&disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
				_("Alterar plano de ensino") . "</button></a>\n";
				echo "<a href='#' id='hrefExc'><button type='button' class='btn btn btn-default'>" . _("Excluir plano de ensino") . "</button></a>\n";
			}
			
			echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
			echo "<h3 class='panel-title'>" . _("Plano de ensino") . "</h3></div>";
			echo "<div class='panel-body'>";

			$linha = mysql_fetch_array($result);

			$cargahorsem = $linha["cargahorsem"];
			$cargahortot = $linha["cargahortot"];
			$ementa = $linha["ementa"];
			$objetivos = $linha["objetivos"];
			$conteudo = $linha["conteudo"];
			$metodologia = $linha["metodologia"];
			$avaliacao = $linha["avaliacao"];
			$recursos = $linha["recursos"];
			$bibliografiab = $linha["bibliografiab"];
			$bibliografiac = $linha["bibliografiac"];

			if (!empty($cargahorsem)) {
				echo "<strong>Carga Hor&aacute;ria Semanal</strong>\n";
				echo "<br>$cargahorsem\n";
			}
			if (!empty($cargahortot)) {
				echo "<br><br><strong>Carga Hor&aacute;ria Total</strong>\n";
				echo "<br>$cargahortot\n";
			}
			if (!empty($objetivos)) {
				echo "<br><br><strong>Objetivos</strong>\n";
				echo "<br>" . nl2br($objetivos) . "\n";
			}
			if (!empty($ementa)) {
				echo "<br><br><strong>Ementa</strong>\n";
				echo "<br>" . nl2br($ementa) . "\n";
			}
			if (!empty($conteudo)) {
				echo "<br><br><strong>Conte&uacute;do Program&aacute;tico</strong>\n";
				echo "<br>" . nl2br($conteudo) . "\n";
			}
			if (!empty($metodologia)) {
				echo "<br><br><strong>Metodologia de Ensino</strong>\n";
				echo "<br>" . nl2br($metodologia) . "\n";
			}
			if (!empty($avaliacao)) {
				echo "<br><br><strong>Crit&eacute;rio de Avalia&ccedil;&atilde;o</strong>\n";
				echo "<br>" . nl2br($avaliacao) . "\n";
			}
			if (!empty($recursos)) {
				echo "<br><br><strong>Recursos Tem&aacute;ticos</strong>\n";
				echo "<br>" . nl2br($recursos) . "\n";
			}
			if (!empty($bibliografiab)) {
				echo "<br><br><strong>Bibliografia B&aacute;sica</strong>\n";
				echo "<br>" . nl2br($bibliografiab) . "\n";
			}
			if (!empty($bibliografiac)) {
				echo "<br><br><strong>Bibliografia Complementar</strong>\n";
				echo "<br>" . nl2br($bibliografiac) . "<br><br>\n";
			}
			if ($pAction == "CONFIRM") {
				echo  "<form action='planoensino.php?pAction=DELETE&disid=$disid' method='POST'>\n";
				echo "<input type='submit' name='enviar' class='btn btn-default' value='" . _("Confirmar exclus&atilde;o") . "...'></form>\n";
			}

		} else {
			
			if ($tipo == 1) {
				echo "<a href='#' onClick='abrirPag(" . '"planoensino.php", "pAction=INSERT&disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
				_("Incluir plano de ensino") . "</button></a>\n";
			}
			
			echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
			echo "<h3 class='panel-title'>" . _("Plano de ensino") . "</h3></div>";
			echo "<div class='panel-body'>";
			
			echo "<p class='lead'>" . _("Plano de ensino n&atilde;o foi informado") . "...</p>\n";
		}
		
		mysql_close($dblink);
		
		if ($pAction == "SELECT") {
			include 'plano.inc';
		}

	}

	if ($pAction == "UPDATE" or $pAction == "INSERT" or $pAction == "UPDATED" or $pAction == "INSERTED") {
		
		echo "<a href='#' onClick='abrirPag(" . '"planoensino.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
		_("Plano de ensino") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Plano de ensino") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		if ($pAction == "UPDATE" or $pAction == "UPDATED") {
			include 'connectdb.php';
			$sql = "SELECT * FROM planoensino WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			$cargahorsem = $linha["cargahorsem"];
			$cargahortot = $linha["cargahortot"];
			$ementa = $linha["ementa"];
			$objetivos = $linha["objetivos"];
			$conteudo = $linha["conteudo"];
			$metodologia = $linha["metodologia"];
			$avaliacao = $linha["avaliacao"];
			$recursos = $linha["recursos"];
			$bibliografiab = $linha["bibliografiab"];
			$bibliografiac = $linha["bibliografiac"];
			mysql_close($dblink);
		}

		echo  "<form action='planoensino.php' method='POST'>\n" ;
		echo "<p><label for='cargahorsem'>" . _("Carga hor&aacute;ria semanal") . "</label>\n";
		echo "<input type='text' name='cargahorsem' value='$cargahorsem' size=5 maxlength=5 class='form-control' autofocus></p>\n";
		echo "<p><label for='cargahortot'>" . _("Carga hor&aacute;ria total") . "</label>\n";
		echo "<input type='text' name='cargahortot' value='$cargahortot' size=5 maxlength=5 class='form-control' autofocus></p>\n";
		echo "<p><label for='objetivos'>" . _("Objetivos") . "</label><br>\n";
		echo "<textarea name='objetivos' rows='20' class='form-control' autofocus>$objetivos</textarea></p>\n";
		echo "<p><label for='ementa'>" . _("Ementa") . "</label><br>\n";
		if ($coordenador) {
			echo "<textarea name='ementa' rows='20' class='form-control' autofocus>$ementa</textarea></p>\n";
		} else {
			echo "<textarea name='ementa' rows='20' class='form-control' autofocus disabled>$ementa</textarea></p>\n";
		}
		echo "<p><label for='conteudo'>" . _("Conte&uacute;do program&aacute;tico") . "</label><br>\n";
		echo "<textarea name='conteudo' rows='20' class='form-control' autofocus>$conteudo</textarea></p>\n";
		echo "<p><label for='metodologia'>" . _("Metodologia de ensino") . "</label><br>\n";
		echo "<textarea name='metodologia' rows='20' class='form-control' autofocus>$metodologia</textarea></p>\n";
		echo "<p><label for='avaliacao'>" . _("Crit&eacute;rio de avalia&ccedil;&atilde;o") . "</label><br>\n";
		echo "<textarea name='avaliacao' rows='20' class='form-control' autofocus>$avaliacao</textarea></p>\n";
		echo "<p><label for='recursos'>" . _("Recursos tem&aacute;ticos") . "</label><br>\n";
		echo "<textarea name='recursos' rows='20' class='form-control' autofocus>$recursos</textarea></p>\n";
		echo "<p><label for='bibliografiab'>" . _("Bibliografia b&aacute;sica") . "</label><br>\n";
		if ($coordenador) {
			echo "<textarea name='bibliografiab' rows='20' class='form-control' autofocus>$bibliografiab</textarea></p>\n";
		} else {
			echo "<textarea name='bibliografiab' rows='20' class='form-control' autofocus disabled>$bibliografiab</textarea></p>\n";
		}
		echo "<p><label for='bibliografiac'>" . _("Bibliografia complementar") . "</label><br>\n";
		if ($coordenador) {
			echo "<textarea name='bibliografiac' rows='20' class='form-control' autofocus>$bibliografiac</textarea></p></fieldset>\n";
		} else {
			echo "<textarea name='bibliografiac' rows='20' class='form-control' autofocus disabled>$bibliografiac</textarea></p></fieldset>\n";
		}
		if ($pAction == "UPDATE" or $pAction == "UPDATED") {
			echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
		} else {
			echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		}
		echo "<input type='hidden' name='disid' value='$disid'>\n";
		echo "<input type='submit' class='btn brn-default' name='enviar' value='" . _("Enviar") . "'></form>\n";
	}
	
	echo "</div></div>";
	
	include 'rodape.inc';
	
	
?>