<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	function ListaDados($disid, $usuid, $forid, $pAction, $tipo, $planid) {
		
		if ($pAction == "LIST") {
			echo "<a href='#' onClick='abrirPag(" . '"forum.php", "pAction=INSERT&planid=' . $planid . '&forid=' . $forid . '&disid=' . $disid . '"' . ")'>" .
			"<button type='button' class='btn btn btn-default'>" . _("Responder ao t&oacute;pico") . "</button></A>\n";
			echo "<a href='#' onClick='abrirPag(" . '"forum.php", "planid=' . $planid . '&disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("F&oacute;rum") . "</button></A><br>\n";
		} else {
			if (!empty($planid)) {
				echo "<a href='#' onClick='abrirPag(" . '"plano.php", "disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
				_("Planos de aula") . "</button></A>\n";
			}
			echo "<a href='#' onClick='abrirPag(" . '"forum.php", "pAction=INSERT&planid=' . $planid . '&disid=' . $disid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
			_("Incluir novo t&oacute;pico") . "</button></A>\n";
		}
		
		if (!empty($planid)) {
			include 'connectdb.php';
			$sql = "SELECT DATE_FORMAT(data, '%d/%m/%Y') as data, texto FROM plano WHERE id = '$planid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			echo "<h2>" . $linha["texto"] . "&nbsp;(" . $linha["data"] . ")</h2>";
			mysql_close($dblink);
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("F&oacute;rum") . "</h3></div>";
		echo "<div class='panel-body'>";

		include("./connectdb.php");

		if ($pAction == "LIST") {
			
			$sql = "SELECT f.titulo, f.mensagem, u.nome, DATE_FORMAT(f.time, '%d/%m/%Y %H:%i') as data FROM forum f INNER JOIN usuario u ON (u.id = f.usuid) WHERE f.id = '$forid'";
			
			$result = mysql_query($sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			echo "<h2>" . $linha["titulo"] . "</h2><i>" . $linha["nome"] . ", " . $linha["data"] . "</i><br><br><span id='lead'>" . nl2br($linha["mensagem"]) . "</span><br>";
			
			$sql = "SELECT f.id, f.forid, f.titulo, DATE_FORMAT(f.time, '%d/%m/%Y %H:%i') as data, f.time, f.mensagem, u.id as usuid, u.nome 
			FROM forum f INNER JOIN usuario u ON (u.id = f.usuid) WHERE disid = '$disid' AND forid = '$forid'";

			if (!empty($planid) ) {
				$sql .= " AND f.planid = '$planid'";
			} else {
				$sql .= " AND f.planid IS NULL";
			}
			 
			$sql .= " ORDER BY 4 DESC";
			
		} else {
			$sql = "SELECT f.id, f.forid, f.titulo, DATE_FORMAT(f.time, '%d/%m/%Y %H:%i') as data, f.time, f.mensagem, u.id as usuid, u.nome 
			FROM forum f INNER JOIN usuario u ON (u.id = f.usuid) 
			WHERE disid = '$disid' AND forid IS NULL";
			
			if (!empty($planid) ) {
				$sql .= " AND f.planid = '$planid'";
			} else {
				$sql .= " AND f.planid IS NULL";
			}

			$sql .= " ORDER BY 4 DESC";
		}
		
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {

			if ($pAction == "LIST") {
				echo "<br><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span>&nbsp;" . _("Exclui a mensagem") . "\n" ;
				echo "<br><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" . _("Altera a mensagem") . "\n" ;				
			} else {
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" . _("Altera o t&oacute;pico") . "\n" ;
				echo "<br><span class='glyphicon glyphicon-plus-sign' aria-hidden='true'></span>&nbsp;" . _("Responde ao t&oacute;pico") . "\n" ;
				echo "<br><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span>&nbsp;" . _("Exclui o t&oacute;pico") . "\n" ;
			}
			
			echo "<br><br><table class='table'>\n" ;
		
			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td align='right' valign='top' nowrap>";
				if ($linha["usuid"] == $usuid or $tipo == 1) {
					echo "<a href='#' onClick='abrirPag(" . '"forum.php", "pAction=UPDATE&planid=' . $planid . '&forid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>\n";
					if ($pAction != "LIST") {
						echo "<a href='#' onClick='abrirPag(" . '"forum.php", "pAction=LIST&planid=' . $planid . '&forid=' . $linha["id"] . '")' . "'>\n";
						echo "<span class='glyphicon glyphicon-plus-sign' aria-hidden='true'></span></a>\n";
					}
					echo "<a href='#' id='delete' onClick='abrirPag(" . '"forum.php", "pAction=DELETE&planid=' . $planid . '&foridp=' . 
					$linha["forid"] . '&forid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></a></td>";
				}
				echo "<td valign='top'>" . $linha["data"] . "</td>\n";
				echo "<td valign='top'>" . $linha["nome"] . "</a><br>\n";
				if ($pAction == "LIST") {
					echo "<strong>" . $linha["titulo"] . "</strong><br>" . $linha["mensagem"] . "</td></tr>\n";
				} else {
					echo "<a href='#' onClick='abrirPag(" . '"forum.php", "pAction=LIST&planid=' . $planid . '&forid=' . $linha["id"] . '")' . "'>" .
					$linha["titulo"] . "</a><br>" . nl2br($linha["mensagem"]) . "</td></tr>\n";
				}
			}

		} else {
			if ($pAction != "LIST") {
			//	echo "<p class='lead'><br>" . _("N&atilde;o h&aacute; respostas registradas ...") . "</p>\n";
			//} else {
				echo "<p class='lead'>" . _("N&atilde;o h&aacute; t&oacute;picos registrados ...") . "</p>\n";
			}
			mysql_close($dblink);
			echo "</div></div>";
			return;
		}
		
		echo "</table>\n";
		
		echo "</div></div>";
		
		mysql_close($dblink);
		
		return;
	
	}
	
	function ExcluiDados($forid) {
		include( "./connectdb.php" );
		$SQL = "DELETE FROM forum WHERE id = '$forid'" ;
		$result = mysql_query( $SQL, $dblink );
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function IncluiDados($disid, $usuid, $titulo, $mensagem, $forid, $planid) {
		include( "./connectdb.php" );
		if (!empty($forid)) {
			$sql = "UPDATE forum SET time = now() WHERE id = '$forid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			if (!empty($planid)) {
				$sql = "INSERT INTO forum VALUES (null, '$disid', '$titulo', '$mensagem', now(), '$usuid', '$forid', '$planid')";
			} else {
				$sql = "INSERT INTO forum VALUES (null, '$disid', '$titulo', '$mensagem', now(), '$usuid', '$forid', null)";
			}
		} else {
			if (!empty($planid)) {
				$sql = "INSERT INTO forum VALUES (null, '$disid', '$titulo', '$mensagem', now(), '$usuid', null, '$planid')";
			} else {
				$sql = "INSERT INTO forum VALUES (null, '$disid', '$titulo', '$mensagem', now(), '$usuid', null, null)";
			}
		}
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function AlteraDados($forid, $titulo, $mensagem) {
		include( "./connectdb.php" );
		$sql = "SELECT forid FROM forum WHERE id = '$forid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		if (!empty($linha["forid"])) {
			$sql = "UPDATE forum SET time = now() WHERE id = '" . $linha["forid"] . "'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		}
		$sql = "UPDATE forum SET titulo = '$titulo', mensagem = '$mensagem', time = now() WHERE id = $forid";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		mysql_close($dblink);
		return;
	}
	
	function Formulario($titulo, $mensagem, $forid, $planid) {
		if (empty($forid)) {
			echo "<a href='forum.php'><button type='button' class='btn btn btn-default'>" . 
			_("F&oacute;rum") . "</button></A><br>\n";
		} else {
			echo "<a href='#' onClick='abrirPag(" . '"forum.php", "pAction=LIST&planid=' . $planid . '&forid=' . $forid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("F&oacute;rum") . "</button></A>\n";
		}
		
		if (!empty($planid)) {
			include 'connectdb.php';
			$sql = "SELECT DATE_FORMAT(data, '%d/%m/%Y') as data, texto FROM plano WHERE id = '$planid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			echo "<h2>" . $linha["texto"] . "&nbsp;(" . $linha["data"] . ")</h2>";
			mysql_close($dblink);
		}
		
		if (!empty($forid)) {
			include 'connectdb.php';
			$sql = "SELECT f.titulo, f.mensagem, u.nome, DATE_FORMAT(f.time, '%d/%m/%Y %H:%i') as data FROM forum f INNER JOIN usuario u ON (u.id = f.usuid) WHERE f.id = '$forid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			echo "<br><h3>" . $linha["titulo"] . "</h3><i>" . $linha["nome"] . ", " . $linha["data"] . "</i><br><br><span id='lead'>" . nl2br($linha["mensagem"]) . "</span><br>";
			mysql_close($dblink);
		}		
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("F&oacute;rum") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br><br>";
		echo "<p><label for='data'>(*) Titulo</label>\n";
		echo "<input type='text' name='titulo' id='titulo' value='$titulo' size=60 maxlength=90 class='form-control' autofocus required></p>\n";
		echo "<p><label for='detalhe'>(*) Mensagem<br></label><br>\n";
		echo "<textarea name='mensagem' rows='20' autofocus required class='form-control'>$mensagem</textarea></p>\n";
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
	
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-comment' aria-hidden='true'></span>&nbsp;" . _("F&oacute;rum de Discuss&atilde;o") . "</h3></div>";
	
	if ($pAction == "DELETE") {
		echo "<br><br><strong>Confirma exclus&atilde;o?</strong> 
		<a href='#' onClick='abrirPag(" . '"forum.php", "pAction=DELETEY&planid=' . $planid . 'foridp=' . $foridp . '&forid=' . $forid . '")' . "'>[Sim] </a>
		<a href='#' onClick='abrirPag(" . '"forum.php", "planid=' . $planid . '&disid=' . $disid . '"' . ")'>[N&atilde;o]</a><br><br>\n";
		if (empty($foridp)) {
			ListaDados($disid, $id, null, null, $tipo, $planid);
		} else {
			ListaDados($disid, $id, $foridp, "LIST", $tipo, $planid);
		}
	} elseif ($pAction == "DELETEY") {
		ExcluiDados($forid);
		if (empty($foridp)) {
			ListaDados($disid, $id, null, null, $tipo, $planid);
		} else {
			ListaDados($disid, $id, $foridp, "LIST", $tipo, $planid);
		}	
	} elseif ($pAction == "INSERT" or $pAction == "INSERTED") {
		if ($pAction == "INSERTED") {
			IncluiDados($disid, $id, $titulo, $mensagem, $forid, $planid);
			if (!empty($forid)) {
				ListaDados($disid, $id, $forid, "LIST", $tipo, $planid);
			} else {
				ListaDados($disid, $id, null, null, $tipo, $planid);
			}
		} else {
			echo "<form action='forum.php' method='POST'>\n" ;
			echo "<input type='hidden' name='disid' value='$disid'>\n";
			echo "<input type='hidden' name='planid' value='$planid'>\n";
			echo "<input type='hidden' name='forid' value='$forid'>\n";
			echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
			Formulario(null, null, $forid, $planid);
		}
	} elseif ($pAction == "UPDATE" or $pAction == "UPDATED") {
		if ($pAction == "UPDATED") {
			AlteraDados($forid, $titulo, $mensagem);
			ListaDados($disid, $id, $foridp, $pActionDest, $tipo, $planid);
		} else {
			echo "<form action='forum.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
			echo "<input type='hidden' name='planid' value='$planid'>\n";
			echo "<input type='hidden' name='disid' value='$disid'>\n";
			echo "<input type='hidden' name='forid' value='$forid'>\n";
			include 'connectdb.php';
			$sql = "SELECT titulo, mensagem, forid FROM forum WHERE id='$forid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			if (!empty($linha["forid"])) {
				echo "<input type='hidden' name='pActionDest' value='LIST'>\n";
				echo "<input type='hidden' name='planid' value='$planid'>\n";
				echo "<input type='hidden' name='foridp' value='" . $linha["forid"] . "'>\n";
			}
			Formulario($linha["titulo"], $linha["mensagem"], $forid, $planid);
			mysql_close($dblink);
		}
	} elseif ($pAction == "LIST") {
		ListaDados($disid, $id, $forid, $pAction, $tipo, $planid);	
	} else {
		ListaDados($disid, $id, null, null, $tipo, $planid);
	}
	
	include 'rodape.inc';

?>