<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	function ListaDados($selall, $disid, $tipo, $pAction, $usuid) {
		
		if (empty($pAction)) {
			$pAction = "LIST_RCP";
		}
		
		echo "<a href='#' id='textLink' onClick='abrirPag(" . '"mensagem.php", "pAction=WRITE"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Escrever uma mensagem") . "</button></A>\n";
		echo "<a href='#' id='textLink' onClick='abrirPag(" . '"mensagem.php", "pAction=LIST_SND"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Mensagens enviadas") . "</button></A>\n";
		echo "<a href='#' id='textLink' onClick='abrirPag(" . '"mensagem.php", "pAction=LIST_RCP"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Mensagens recebidas") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Mensagens") . "</h3></div>";
		echo "<div class='panel-body'>";

		include("./connectdb.php");
		
		$sql = "SELECT email FROM usuario WHERE id = '$usuid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				
		if ( mysql_num_rows($result) > 0) {
			$linha = mysql_fetch_array($result);
			$email = $linha["email"];
		}
		
		if ($pAction == "LIST_RCP"){
			$sql = "SELECT id, remetente as pessoas, assunto, DATE_FORMAT(data, '%d/%m/%Y %H:%i') as dataf, data, lido FROM mensagens 
			WHERE disid = '$disid' AND destinatario LIKE '%$email%' ORDER BY 5 DESC";
		} else {
			$sql = "SELECT id, destinatario as pessoas, assunto, DATE_FORMAT(data, '%d/%m/%Y %H:%i') as dataf, data, lido FROM mensagens 
			WHERE usuid = '$usuid' AND disid = '$disid' ORDER BY 3 DESC";
		}
				
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			echo "<br><span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span>&nbsp;Responder a mensagem\n" ;
			echo "<br><span class='glyphicon glyphicon-plane' aria-hidden='true'></span>&nbsp;Encaminhar a mensagem\n" ;
			
			echo "<br><br><table class='table'><thread><tr>\n" ;
		
			echo "<th width='5%' nowrap></th><th width='5%' nowrap></th><th>" . _("De") . "</th><th>" . _("Assunto") . "</th><th>" . _("Data") . "</th></tr></thread><tbody>\n";	

			echo "<form action='mensagem.php' id='deleteForm' name='deleteForm' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='DELETE'>\n";

			while ($linha = mysql_fetch_array($result)) {
				//if ($linha["lido"] == 1 and $pAction == "LIST_RCP") {
				//	$estilo = "textBold2";
				//} else {
				//	$estilo = "textNormal";
				//}
				echo "<tr><td align='right'>\n";
				if ( empty( $selall ) ) {
					echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
				} else {
					echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td><td nowrap><a href='#' id='textLink' onClick='abrirPag(" . '"mensagem.php", "pAction=REPLY&mensid=' . $linha["id"] . '")' . "'>\n";
				echo "<span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span></a>\n";
				echo "<a href='#' id='textLink' onClick='abrirPag(" . '"mensagem.php", "pAction=FORWARD&mensid=' . $linha["id"] . '")' . "'>\n";
				echo "<span class='glyphicon glyphicon-plane' aria-hidden='true'></span></td>\n";
				echo "<td>" . htmlspecialchars($linha["pessoas"]) . "</td>";
				echo "<td><a href='#' id='textLink' onClick='abrirPag(" . '"mensagem.php", "pAction=VIEW&mensid=' . $linha["id"] . '")' . "'>" . $linha["assunto"] . "</td>\n";
				echo "<td>" . $linha["dataf"] . "</td></tr>\n";
			}
			
			echo "</tbody></table>\n";
			
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; mensagens") . "...</p>\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		mysql_close($dblink);
		
		echo "<table><tr valign='top'>\n" ;
		echo "<td><input type='submit' class='btn btn-danger' name='enviar' value='" . _("Excluir") . "'></form></td>\n" ;
		echo "<td><form action='mensagem.php' id='selall' method='POST'>\n" ;
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'>\n";
		echo "</form></td>\n" ;
		echo "<td><form action='mensagem.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<input class='btn btn-default' type='submit' value='" . _("Desmarcar todos") . "'>\n" ;
		echo "</form></td></tr></table>\n" ;

		echo "</div></div>";
		
		return;
	
	}
	
	function Escrever($disid, $usuid, $selall, $pAction, $mensid, $endid) {
		
		echo "<a href='#' id='textLink' onClick='abrirPag(" . '"mensagem.php", "pAction=LIST_RCP"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Mensagens") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Mensagens") . "</h3></div>";
		echo "<div class='panel-body'>";

		include("./connectdb.php");
		
		if ($pAction == "REPLY") {
			$sql = "SELECT remetente, destinatario FROM mensagens WHERE id = '$mensid'";
			$result = mysql_query($sql, $dblink) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			$dest = explode(",", $linha["destinatario"]);
			array_push($dest, $linha["remetente"]);
			for ($i = 0; $i < count($dest); $i++) {
				$dest[$i] = strtoupper(trim($dest[$i]));
			}
		}

		$sql = "SELECT u.id, UPPER(u.nome) as nomeu, UPPER(a.nome) as nome, u.email FROM disalu da INNER JOIN aluno a ON (a.id = da.aluid) INNER JOIN usuend ue ON (ue.ra = da.aluid) 
		INNER JOIN usuario u ON (u.id = ue.usuid) WHERE da.disid = '$disid' and ue.endid = '$endid' ORDER BY 2";
				
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			echo "<span id='lead'>Selecione as pessoas para as quais dever&atilde;o ser enviada a mensagem</span>";
			
			echo "<br><br><table class='table'><thread><tr>\n" ;
		
			echo "<th></th><th>Nome</th><th>email</th></tr></thread>\n";	

			echo "<form action='mensagem.php' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='SEND'>\n";
			
			$sql = "SELECT UPPER(u.nome) as nome, u.email, u.id, u.id as perfil FROM usuario u INNER JOIN disciplina d ON u.id = d.usuid WHERE d.id = '$disid'";
			$resulta = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linhaa = mysql_fetch_array($resulta);
			if (mysql_num_rows($resulta) > 0) {
				$email = trim($linhaa["nome"]) . " <" . trim($linhaa["email"]) . ">";
				echo "<tr><td width='10px' align='right'>\n";
				if ( empty( $selall ) ) {
					if (array_search(strtoupper($email), $dest) === false or $pAction != "REPLY") {
						echo "<input type='checkbox' name='destinatario[" . $linhaa["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='destinatario[" . $linhaa["id"] . "]' CHECKED\n>";
					}
				} else {
					echo "<input type='checkbox' name='destinatario[" . $linhaa["id"] . "]' CHECKED\n>";
				}
				echo "</td><td><strong>" . $linhaa["nome"] . "</strong></td>";
				echo "<td><a href='mailto:" . $linhaa["email"] . "'>" . $linhaa["email"] . "</a></td></tr>\n";
			}
			$sql = "SELECT UPPER(u.nome) as nome, u.email, u.id, u.id as perfil FROM usuario u INNER JOIN disusu du ON du.usuid = u.id WHERE du.disid = '$disid'";					
			$resultb = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linhab = mysql_fetch_array($resultb);
			if (mysql_num_rows($resultb) > 0) {
				$email = trim($linhab["nome"]) . " <" . trim($linhab["email"]) . ">";
				echo "<tr><td width='10px' align='right'>\n";
				if ( empty( $selall ) ) {
					if (array_search(strtoupper($email), $dest) === false or $pAction != "REPLY") {
						echo "<input type='checkbox' name='destinatario[" . $linhab["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='destinatario[" . $linhab["id"] . "]' CHECKED\n>";
					}
				} else {
					echo "<input type='checkbox' name='destinatario[" . $linhab["id"] . "]' CHECKED\n>";
				}
				echo "</td><td><strong>" . $linhab["nome"] . "</strong></td>";
				echo "<td><a href='mailto:" . $linhab["email"] . "'>" . $linhab["email"] . "</a></td></tr>\n";
			}
			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td width='10px' align='right'>\n";
				$email = $linha["nomeu"] . " <" . $linha["email"] . ">";
				if ( empty( $selall ) ) {
					if (array_search(strtoupper($email), $dest) === false or $pAction != "REPLY") {
						echo "<input type='checkbox' name='destinatario[" . $linha["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='destinatario[" . $linha["id"] . "]' CHECKED\n>";
					}
				} else {
					echo "<input type='checkbox' name='destinatario[" . $linha["id"] . "]' CHECKED\n>";
				}
				echo "</td><td>" . $linha["nome"] . "</td>";
				echo "<td><a href='mailto:" . $linha["email"] . "'>" . $linha["email"] . "</a></td></tr>\n";
			}
			
			echo "</tbody></table>\n";
			
		} else {
			echo "N&atilde;o h&aacute; alunos registrados ...\n";
			echo "</div></div>";
			mysql_close($dblink);
			return;
		}
		
		$sql = "SELECT destinatario, remetente, assunto, mensagem, DATE_FORMAT(data, '%D %M %Y %H:%i') as data FROM mensagens WHERE id = '$mensid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		if (mysql_num_rows($result) > 0) {
			$linha = mysql_fetch_array($result);
			if ($pAction == "FORWARD") {
				if (!strstr("Fwd: ")) {
					$assunto = "Fwd: " . $linha["assunto"];
				} else {
					$assunto = $linha["assunto"];
				}
				$mensagem = "\n\n\n\n\n---------- Mensagem encaminhada ----------\n";
				$mensagem .= "From: " . $linha["remetente"] . "\n";
				$mensagem .= "Data: " . $linha["data"] . "\n";
				$mensagem .= "Subject: " . $linha["assunto"] . "\n";
				$mensagem .= "To: " . $linha["destinatario"] . "\n\n";
			} else {
				if (!strstr("Re: ")) {
					$assunto = "Re: " . $linha["assunto"];
				} else {
					$assunto = $linha["assunto"];
				}
				$mensagem = "\n\n\n\n\nIn " . $linha["data"]. ", " . $linha["remetente"] . " wrote:\n\n";
			}
			$mensagem .= $linha["mensagem"];
		} else {
			$assunto = "";
			$mensagem = "";
		}		
		
		mysql_close($dblink);
		
		echo "<p><span id='lead'>Escreva a mensagem nos campos abaixo e acione o bot&atilde;o para transmit&iacute;-la</span></p>";

		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br><br>";
		echo "<p><label for='assunto'>(*) " . _("Assunto") . "</label><br>\n";
		echo "<input type='text' name='assunto' value='$assunto' size=60 maxlength=60 class='form-control' autofocus required></p>\n";
		echo "<p><label for='mensagem'>(*) " . _("Mensagem") . "</label><br>\n";
		echo "<textarea name='mensagem' rows='20' class='form-control' autofocus required>$mensagem</textarea></p>\n";		
		echo "<table><tr valign='top'>\n" ;
		echo "<td><input type='submit' class='btn btn-default' name='enviar' value='" . _("Enviar") . "'></form></td>\n" ;
		echo "<td><form action='mensagem.php' id='selall' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='WRITE'>";
		echo "<input type='hidden' name='selall' value='1'>\n";
		echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Marcar todos") . "'>\n" ;
		echo "</form></td>\n" ;
		echo "<td><form action='mensagem.php' id='selall' method='POST'>\n";
		echo "<input type='hidden' name='pAction' value='WRITE'>";
		echo "<input type='hidden' name='selall' value='0'>\n";
		echo "<input type='submit' class='btn btn-default' name='selecionar' value='" . _("Desmarcar todos") . "'></fieldset>\n" ;
		echo "</form></td></tr></table>\n" ;

		echo "</div></div>";
		
		return;
	
	}
	
	function Visualizar($mensid, $disid) {
		
		echo "<a href='#' id='textLink' onClick='abrirPag(" . '"mensagem.php", "pAction=LIST_RCP"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Mensagens") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Mensagens") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include 'connectdb.php';
		
		$sql = "SELECT id, destinatario, remetente, assunto, mensagem, DATE_FORMAT(data, '%D %M %Y %H:%i') as data FROM mensagens WHERE id = '$mensid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		if (mysql_num_rows($result) > 0) {
			$linha = mysql_fetch_array($result);
			echo "<br><strong>De: </strong>" . $linha["remetente"];
			echo "<br><strong>Para: </strong>" . $linha["destinatario"];
			echo "<br><strong>Data: </strong>" . $linha["data"];
			echo "<br><br><a href='#' onClick='abrirPag(" . '"mensagem.php", "pAction=REPLY&mensid=' . $linha["id"] . '")' . "'>Responder</a>\n";
			echo "<br><a href='#' onClick='abrirPag(" . '"mensagem.php", "pAction=FORWARD&mensid=' . $linha["id"] . '")' . "'>Encaminhar</a>\n";
			echo "<br><br><strong>Assunto: </strong>" . $linha["assunto"];
			echo "<br><br>" . nl2br($linha["mensagem"]);
		}
		$sql = "UPDATE mensagens SET lido = '0' WHERE id = '$mensid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		mysql_close($dblink);
		echo "</div></div>";
		return;
	}
	
	function Enviar($disid, $id, $destinatario, $assunto, $mensagem) {
		
		include( "./connectdb.php" );
		
		if (!strstr($assunto, '[Academusnet]')) {
			$assunto = "[Academusnet] " . $assunto;
		}
		
		$para = "";

		foreach ($destinatario as $destid => $valor) {
			if ($valor == 'on') {
				if (!empty($para)) {
					$para .= ", ";
				}
				$sql = "SELECT upper(nome) as nome, email FROM usuario WHERE id = '$destid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				if ( mysql_num_rows($result) > 0) {
					$linha = mysql_fetch_array($result);
					$para .= $linha["nome"] . " <" . $linha["email"] . ">";		
				}
			}
		}
		
		$sql = "SELECT nome, email FROM usuario WHERE id = '$id'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				
		if ( mysql_num_rows($result) > 0) {
			$linha = mysql_fetch_array($result);
			$de = $linha["nome"] . " <" . $linha["email"] . ">";
		}
			
		$sql = "INSERT INTO mensagens VALUES (NULL, '$para', '$de', '$assunto', now(), '$mensagem', $disid, 1, '$id')" ;
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			
		mail($para,$assunto,$mensagem,"Content-type:text/plain; charset=iso-8859-1;\nFrom:$de\n","-rsuporte@academusnet.pro.br");
				
		mysql_close($dblink);
		
		return;
	}
	
	function ExcluiDados($eliminar) {
		if (!empty($eliminar)) {
			include( "./connectdb.php" );
			foreach ($eliminar as $mensid => $valor) {	
				if ($valor == 'on') {
					$SQL = "DELETE FROM mensagens WHERE id = '$mensid'" ;
					$result = mysql_query( $SQL, $dblink ) or die(mysql_error());
				}
			}
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
		}
		return;
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
		
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-envelope' aria-hidden='true'></span>&nbsp;" . _("Mensagens") . "</h3></div>";

	if ($pAction == "DELETE") {
		ExcluiDados($eliminar);
		$pAction = "";
		ListaDados($selall, $disid, $tipo, $pAction, $id);
	} elseif ($pAction == "SEND") {
		Enviar($disid, $id, $destinatario, $assunto, $mensagem);
		ListaDados($selall, $disid, $tipo, "LIST_RCP", $id);
	} elseif ($pAction == "REPLY" or $pAction == "FORWARD") {
		Escrever($disid, $id, $selall, $pAction, $mensid, $endid);
	} elseif ($pAction == "WRITE") {
		Escrever($disid, $id, $selall, null, null, $endid);
	} elseif ($pAction == "VIEW") {
		Visualizar($mensid, $disid);
	} else {
		ListaDados($selall, $disid, $tipo, $pAction, $id);
	}
	
	include 'rodape.inc';

?>
