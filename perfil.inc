<?php
		include( "./connectdb.php" );

		$sql = "SELECT id, email, nome, endereco, cidade, cep, uf, telefone, pais, profissao, ";
		$sql = $sql . "decode(senha, 'mypas') as senha, foto, experiencia, educacao, hobby, professor FROM usuario WHERE id = '$perfil'";

		$result = mysql_query( $sql, $dblink ) or die(mysql_error());			
		$linha = mysql_fetch_array($result);
		
		$usuario = $linha["id"];
		$email = $linha["email"];
		$nome = $linha["nome"];
		$endereco = $linha["endereco"];
		$cep = $linha["cep"];
		$cidade = $linha["cidade"];
		$telefone = $linha["telefone"];
		$uf = $linha["uf"];
		$pais = $linha["pais"];
		$profissao = $linha["profissao"];
		$educacao = $linha["educacao"];
		$experiencia = $linha["experiencia"];
		$hobby = $linha["hobby"];
		$foto = $linha["foto"];
		$professor = $linha["professor"];

		echo "<div class='jumbotron'>\n";

		echo "<p><h1>$nome</h1></p>\n";
		echo "<p><a href='mailto:$email'>$email</a></p></div>\n";

		if ($tipo == 1 or $perfil == $usuid) {
			
			if (!empty($endereco)) {
				echo "<p class='lead'><strong>" . _("Endere&ccedil;o") . ":</strong>$endereco\n";
			}
			if (!empty($cidade)) {
				echo "<p class='lead'><strong>" . _("Cidade") . ":</strong>$cidade\n";
			}
			if (!empty($cep)) {
				echo "<p class='lead'><strong>" . _("CEP") . ":</strong>$cep\n";
			}
			if (!empty($uf)) {
				echo "<p class='lead'><strong>" . _("Estado") . ":</strong>$uf\n";
			}
			if (!empty($telefone)) {
				echo "<p class='lead'><strong>" . _("Telefone") . ":</strong>$telefone\n";
			}
			if (!empty($pais)) {
				echo "<p class='lead'><strong>" . _("Pa&iacute;s") . ":</strong>$pais\n";
			}
			
		}
		
		if (!empty($profissao)) {
			echo "<p class='lead'><strong>" . _("Profiss&atilde;o") . ":</strong>$profissao\n";
		}
		if (!empty($educacao)) {
			echo "<p class='lead'><strong>" . _("Forma&ccedil;&atilde;o acad&ecirc;mica") . ":</strong></p>\n";
			echo "<p class='lead'>" . nl2br($educacao) . "</p>\n";
		}
		if (!empty($experiencia)) {
			echo "<p class='lead'><strong>" . _("Experi&ecirc;cia profissional") . ":</strong></p>\n";
			echo "<p class='lead'>" . nl2br($experiencia) . "</p>\n";
		}
		if (!empty($hobby)) {
			echo "<p class='lead'><strong>" . _("Outros interesses") . ":</strong></p>\n";
			echo "<p class='lead'>" . nl2br($hobby) . "</p>\n";
		}
	
		$sql = "SELECT usuid, endereco, nome FROM usulinks WHERE usuid = '$usuario'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		
		if ( mysql_num_rows($result) > 0 ) {
			print("<tr><td width='100%' colspan='2'>&nbsp;</td></tr>\n");
			echo "<tr><td width='100%' colspan='2'><i>" . _("Refer&ecirc;ncias") . ":</td></tr>\n";
			print("<tr><td width='100%' colspan='2'>&nbsp;</td></tr>\n");
			while ($linha = mysql_fetch_array($result)) {
				print("<tr><td width='100%' colspan='2'><a href = '" . $linha["endereco"] . "' target='_blank'>" . $linha["nome"] . 
				"</a></td></tr>\n");
			}
		}

		$sql = "SELECT usuid, endereco, nome FROM usulinks WHERE usuid = '$id'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		if ( mysql_num_rows($result) > 0 ) {
			echo "<p class='lead'><strong>" . _("Refer&ecirc;ncias") . ":</strong></p>\n";
			while ($linha = mysql_fetch_array($result)) {
				echo "<p class='lead'><a href = '" . $linha["endereco"] . "' target='_blank'>" . $linha["nome"] . "</a></p>\n";
			}
		}
		
		if ($tipo == 1 or $perfil == $usuid) {
			
			$sql = "SELECT d.nome, a.timei, a.timef, a.intervalo, a.clicks,
			((unix_timestamp(a.timef) - unix_timestamp(a.timei)) / 60) as tempo FROM acesso a INNER JOIN disciplina d ON d.id = a.disid WHERE a.usuid = '$perfil'
			AND d.endid = '$endid' ORDER BY 3 DESC";
			
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		
			if ( mysql_num_rows($result) > 0 ) {
				
				echo "<div class='page-header'><h3>" . _("Relat&oacute;rio de acesso") . "</h3></div>";
				
				echo "<div class='row'><div class='col-md-12'>";
					
				echo "<table class='table table-striped'><thread><tr><th>" . _("Disciplina") . "</th><th>" . _("Hora de entrada") .
				"</th><th>" . _("Hora de sa&iacute;da") . "</th><th>" . _("Tempo de conex&atilde;o") . "</th><th>Clicks</th><th>" . 
				_("Intervalo entre clicks") . "</th></tr></thread><tbody>";				
				
				while ($linha = mysql_fetch_array($result)) {
					print("<tr><td>" . $linha["nome"] . "</td>\n");
					$regs = array();
					ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})",$linha["timei"], $regs);
					$timei = $regs[3] . "-" . $regs[2] . "-" . $regs[1] . " " . $regs[4] . ":" . $regs[5];
					print("<td>$timei</td>\n");
					$regs = array();
					ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})",$linha["timef"], $regs);
					$timef = $regs[3] . "-" . $regs[2] . "-" . $regs[1] . " " . $regs[4] . ":" . $regs[5];
					print("<td>$timef</td>\n");
					print("<td align='center'>" . number_format($linha["tempo"], 2) . "</td>\n");
					print("<td align='center'>" . $linha["clicks"] . "</td>");
					print("<td align='center'>" . number_format($linha["tempo"] / $linha["clicks"],2) . "</td></tr>");
				}
				print("</tbody></table>");
			} else {
				echo "<div class='alert alert-warning' role='alert'><strong>" . 
				_("N&atilde;o h&aacute; registros de acesso ...") . "</string></div>";
			}
			
			echo "</div></div>";
		}
				
		mysql_close($dblink);
?>