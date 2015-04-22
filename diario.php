<?php
	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	if ($tipo == 0) {
		echo _("&Aacute;rea restrita");
		exit;
	}
	
	function Relatorio($datai, $dataf, $disid) {
		
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

		$sql = "SELECT nome FROM disciplina WHERE id = $disid";
			
		$result = mysql_query($sql, $dblink) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {
			
			$linha = mysql_fetch_array($result);
			$nome = $linha["nome"];
				
			if (checkdate( (integer) $mesi, (integer) $diai, (integer) $anoi) and checkdate( (integer) $mesf, (integer) $diaf, (integer) $anof)) {
				echo _("Per&iacute;odo de") . " $datai a $dataf<br><br>\n";
				$sql = "SELECT texto, data, objetivos, comentario FROM plano WHERE disid = '$disid' AND data >= '$dataii' AND data <= '$dataff' ORDER BY 2";
			} else {
				$sql = "SELECT texto, data, objetivos, comentario FROM plano WHERE disid = '$disid' ORDER BY 2";
			}
			
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());

			if ( mysql_num_rows($result) > 0) {
				
				echo "<br><br><h2>" . _("Di&aacute;rio de classe") . " - $nome</h2><br><br>\n";
			
				echo "<u>" . _("Registro de mat&eacute;ria") . "</u><br>";
					
				while ($linha = mysql_fetch_array($result)) {

					ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})",$linha["data"], $regs);
					$data=($regs[3]."-".$regs[2]."-".$regs[1]);
					$texto = $linha["texto"];
					$comentario = nl2br($linha["comentario"]);
					$objetivos = nl2br($linha["objetivos"]);
					
					print("<br><b>$data&nbsp;&nbsp;$texto</b><br><br>\n");
					
					if (!empty($objetivos)) {
						echo "$objetivos<br><br>";
					}
					
					if (!empty($comentario)) {
						echo "$comentario<br><br>";
					}					
				
					$sql = "SELECT a.id, a.nome, f.faltas, p.data FROM plano p LEFT JOIN frequencia f ON (f.planid = p.id) RIGHT JOIN aluno a ON (f.aluid = a.id) 
					INNER JOIN disalu da ON (da.aluid = a.id AND da.disid = p.disid) WHERE p.disid = '$disid' AND p.data = '" . $linha["data"] . "' and f.faltas > 0 ORDER BY 4, 2";

					$resultf = mysql_query( $sql, $dblink ) or die(mysql_error());

					if ( mysql_num_rows($resultf) > 0) {
			
						echo _("Estudantes ausentes") . "<br><br>";

						while ($linhaf = mysql_fetch_array($resultf)) {
				
							$ra = $linhaf["id"];
							$nome = $linhaf["nome"];
							ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})",$linhaf["data"], $regs);
							$data=($regs[3]."-".$regs[2]."-".$regs[1]);
							$faltas = $linhaf["faltas"];
					
							echo "$data&nbsp;&nbsp;$ra&nbsp;&nbsp;$nome&nbsp;&nbsp;$faltas " . _("falta(s)") . "<br>\n";
						}
					}
				}
			} else {
				echo "<br><br>" . _("N&atilde;o h&aacute; aulas registradas ...");
			}
			
			if (checkdate( (integer) $mesi, (integer) $diai, (integer) $anoi) and checkdate( (integer) $mesf, (integer) $diaf, (integer) $anof)) {
				$sql = "SELECT a.id, a.nome, SUM(f.faltas) as faltas FROM plano p LEFT JOIN frequencia f ON (f.planid = p.id) RIGHT JOIN aluno a ON (f.aluid = a.id) 
				INNER JOIN disalu da ON (da.aluid = a.id AND da.disid = p.disid) WHERE p.disid = $disid AND p.data >= '$dataii' AND p.data <= '$dataff' GROUP BY 1 ORDER BY 2";
			} else {
				$sql = "SELECT a.id, a.nome, SUM(f.faltas) as faltas FROM plano p LEFT JOIN frequencia f ON (f.planid = p.id) RIGHT JOIN aluno a ON (f.aluid = a.id) 
				INNER JOIN disalu da ON (da.aluid = a.id AND da.disid = p.disid) WHERE p.disid = $disid GROUP BY 1 ORDER BY 2";
			}

			$result = mysql_query( $sql, $dblink ) or die(mysql_error());

			if ( mysql_num_rows($result) > 0) {
			
				echo "<br><u>" . _("Controle de frequ&ecirc;ncia - sumariza&ccedil;&atilde;o") . "</u><br><br>";

				while ($linha = mysql_fetch_array($result)) {
					if ($linha["faltas"] > 0) {
						$ra = $linha["id"];
						$nome = $linha["nome"];
						$faltas = $linha["faltas"];
						echo "$ra&nbsp;&nbsp;$nome&nbsp;&nbsp;$faltas " . _("falta(s)") . "<br>\n";
					}
				}
			}
			
		}
		
		mysql_close($dblink);
	}

	include( "cabecalho.php" );
	
	if ($pAction != "REPORT") {
		include( "menu.inc" );
	} else {
		echo "</div>";
	}

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
	
echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-paperclip' aria-hidden='true'></span>&nbsp;" . _("Di&aacute;rio de classe") . "</h3></div>";

echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
echo "<h3 class='panel-title'>" . _("Duplica&ccedil;&atilde;o de ambiente") . "</h3></div>";
echo "<div class='panel-body'>";

if ($pAction == "REPORT") {
	Relatorio($datai, $dataf, $disid);
} else {
	echo "<p class='lead'>" . _("Se o per&iacute;odo n&atilde;o for informado, lista tudo.") . "</p><br>";
	echo "<form action='diario.php' target='_blank' method='POST'>\n";
	echo "<input type='hidden' name='pAction' value='REPORT'>";
	echo "<input type='hidden' name='disid' value='$disid'>";
	echo "<input type='hidden' name='usuid' value='$usuid'>";
	echo "<div class='row'><div class='col-lg-2'>";
	echo "<p class='lead'>" . _("Per&iacute;odo") . "</div><div class='col-lg-2'><input type='text' class='form control datepicker' name='datai' id='datai' value='$datai' size='10'></div>
	<div class='col-lg-2'><input type='text' class='form-control datepicker' name='dataf' id='dataf' value='$dataf' size='10'></p></div></div>";
	echo "<br><input type='submit' class='btn btn-default' name='enviar' value='" . _("Enviar") . "'></form>";
}

echo "</div></div>";

include 'rodape.inc';
	
?>
