<?php
	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	if ($tipo == 0) {
		echo "&Aacute;rea Restrita";
		exit;
	}
	
	function Relatorio($datai, $dataf, $disid, $endid) {
		
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $datai, $regs);
		$anoi = $regs[3];
		$mesi = $regs[2];
		$diai = $regs[1];
		
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $dataf, $regs);
		$anof = $regs[3];
		$mesf = $regs[2];
		$diaf = $regs[1];
		
		$dataii = "$anoi-$mesi-$diai";
		$dataff = "$anof-$mesf-$diaf";
		
		include( "./connectdb.php" );
		
		$sql = "SELECT u.id, UPPER(u.nome) as nomeu, a.nome, u.email, ue.endid FROM disalu da INNER JOIN aluno a ON (a.id = da.aluid) INNER JOIN usuend ue ON (ue.ra = da.aluid) 
		INNER JOIN disciplina d ON (d.id = da.disid) LEFT JOIN usuario u ON (u.id = ue.usuid) WHERE da.disid = '$disid' ORDER BY 2";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		
		if ( mysql_num_rows($result) > 0 ) {
			
			echo "<br><br><table class='table'><thread><tr><th>" . _("Hora de entrada") . "</th><th>" . _("Hora de sa&iacute;da") . "</th><th align='center'>" . _("Tempo de conex&atilde;o") . "</th>
			<th align='center'>Clicks</th><th align='center'>" . _("Intervalo entre clicks") . "</th></tr></thread><tbody>";

			while ($linha = mysql_fetch_array($result)) {
			
				$sql = "SELECT u.nome, a.timei, a.timef, a.intervalo, a.clicks,
				((unix_timestamp(a.timef) - unix_timestamp(a.timei)) / 60) as tempo FROM acesso a INNER JOIN usuario u ON (u.id = a.usuid) INNER JOIN disciplina d ON d.id = a.disid 
				WHERE a.usuid = '" . $linha["id"] . "' AND d.endid = '" . $linha["endid"] . "' AND a.disid = '$disid' ORDER BY 3 DESC";
			
				$resulta = mysql_query( $sql, $dblink ) or die(mysql_error());
		
				if ( mysql_num_rows($resulta) > 0 ) {
					
					$nome = "";
					
					while ($linhaa = mysql_fetch_array($resulta)) {
						if ($nome != $linha["nome"]) {
							$nome = $linha["nome"];
							echo "<tr><td colspan='5'><strong>" . $linha["nome"] . "</strong></td></tr>\n";
						}
						$regs = array();
						ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})",$linhaa["timei"], $regs);
						$timei = $regs[3] . "-" . $regs[2] . "-" . $regs[1] . " " . $regs[4] . ":" . $regs[5];
						echo "<tr><td>$timei</td>\n";
						$regs = array();
						ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})",$linhaa["timef"], $regs);
						$timef = $regs[3] . "-" . $regs[2] . "-" . $regs[1] . " " . $regs[4] . ":" . $regs[5];
						echo "<td>$timef</td>\n";
						echo "<td align='center'>" . number_format($linhaa["tempo"], 2) . "</td>\n";
						echo "<td align='center'>" . $linhaa["clicks"] . "</td>";
						echo "<td align='center'>" . number_format($linhaa["tempo"] / $linhaa["clicks"],2) . "</td></tr>";

					}
				
				}
				
			}
			
		} else {
			echo _("N&atilde;o h&aacute; registros de acesso") . "...<br><br>";
		}

		echo "</tbody></table>";
		
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
	
		});
	
		</script>
		
	<?php
	
	include 'dadosdis.inc';
		
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-pushpin' aria-hidden='true'></span>&nbsp;" . _("Relat&oacute;rio de acesso") . "</h3></div>";
	
	echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Acesso") . "</h3></div>";
	echo "<div class='panel-body'>";
	
	echo "<p class='lead'>" . _("Se o per&iacute;odo n&atilde;o for informado, lista tudo.") . "</p><br>";
	echo "<form action='acesso.php' method='POST'>\n";
	echo "<input type='hidden' name='pAction' value='REPORT'>";
	echo "<input type='hidden' name='disid' value='$disid'>";
	echo "<input type='hidden' name='usuid' value='$usuid'>";
	echo "<div class='row'><div class='col-lg-2'>";
	echo "<p class='lead'>" . _("Per&iacute;odo") . "</div><div class='col-lg-2'><input type='text' class='form control datepicker' name='datai' id='datai' value='$datai' size='10'></div>
	<div class='col-lg-2'><input type='text' class='form-control datepicker' name='dataf' id='dataf' value='$dataf' size='10'></p></div></div>";
	echo "<br><input type='submit' class='btn btn-default' name='enviar' value='" . _("Enviar") . "'></form>";

	if ($pAction == "REPORT") {
		Relatorio($datai, $dataf, $disid, $endid);
	}
	
	echo "</div></div>";
	
	include 'rodape.inc';
	
?>
