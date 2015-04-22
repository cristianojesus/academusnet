<?php
	session_start();
	include "buscasessao.php";
	$linha = BuscaSessao($disid);
	$usuid = $linha["usuid"];
	$tipo = $linha["professor"];
	if ($tipo != 1) {
		echo "Sess&atilde;o Expirada. Fa&ccedil;a um novo <a href='login.php'>login</a> ...";
		exit;
	}

function IncluiDados( $endid, $disid, $usuid, $curid, $nome, $objetivo, $cargah, $faltas, $sigla, 
$agenda, $avisos, $bibliografia, $conteudo, $links, $material, $minicursos, $planoensino, $avaliacao, $professores, $aulas, $datai, $dataf, $atividade) {

	include( "./connectdb.php" );

	if ($endid == 0 or $curid == 0 or empty($nome) or empty($objetivo) or empty($cargah) or  
	($agenda != 'on' and  $avisos != 'on' and $bibliografia != 'on' and $conteudo != 'on' and $links != 'on' and $minicursos != 'on' and $avaliacao != 'on' and $professores != 'on' 
	and $aulas != 'on')) {
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("H&aacute; campos n&atilde;o preenchidos ...") . "</strong></div>"; 
	} else {
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $datai, $regsi);
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $dataf, $regsf);
		$datai = $regsi[3] . "-" . $regsi[2] . "-" . $regsi[1];
		$dataf = $regsf[3] . "-" . $regsf[2] . "-" . $regsf[1];
		$objetivo = addslashes($objetivo);
		$sql = "INSERT INTO disciplina VALUES (null, '$usuid', '$endid', '$curid', '$nome', '$cargah', '$objetivo', '$faltas', '$sigla', '$datai', '$dataf', 0, 0, now() )";
		$result = mysql_query( $sql, $dblink ) or die("Disciplina: " . mysql_error());
		$sql = "SELECT LAST_INSERT_ID() as disidn";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		$disidn = $linha["disidn"];
		
		if ($agenda == 'on') {
			$sql = "SELECT * FROM agenda WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_array($result)) {
				$texto = addslashes($linha['texto']);
				$sql = "INSERT INTO agenda (id, disid, texto, data, detalhe) VALUES (null, $disidn,'$texto','" . $linha['data'] . "','" . $linha['detalhe'] . "')";
				$resulta = mysql_query( $sql, $dblink ) or die("Agenda: " . mysql_error());
			}
		}
		
		if ($avisos == 'on') {
			$sql = "SELECT * FROM aviso WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_array($result)) {
				$titulo = addslashes($linha["titulo"]);
				$texto = addslashes($linha['texto']);
				$sql = "INSERT INTO aviso VALUES (null, $disidn,'" . $titulo . "','" . $texto . "','" . $linha['datav'] . "')";
				$resulta = mysql_query( $sql, $dblink ) or die("Aviso: " . mysql_error());
			}
		}

		if ($atividade == 'on') {
			$sql = "SELECT * FROM teste WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_array($result)) {
				$tesid = $linha["id"];
				$texto = addslashes($linha["texto"]);
				$sql = "INSERT INTO teste VALUES (null,'" . $texto . "','" . $linha['data'] . "','" . $linha['tipo'] . "','" . $linha['desarq'] . "','" . $linha['status'] . "','" . $linha['avaliacao'] . "','$disidn')";
				$resulta = mysql_query( $sql, $dblink ) or die("Atividade: " . mysql_error());

				$sql = "SELECT LAST_INSERT_ID() as tesidn";
				$resultb = mysql_query( $sql, $dblink ) or die("Atividade: " . mysql_error());
				$linhat = mysql_fetch_array($resultb);
				$tesidn = $linhat["tesidn"];

				$sql = "SELECT * FROM tesque WHERE tesid = '$tesid'";
				$resultc = mysql_query( $sql, $dblink ) or die(mysql_error());
				while ($linhaq = mysql_fetch_array($resultc)) {
					$sql = "INSERT INTO tesque VALUES ('$tesidn', '" . $linhaq['queid'] . "','" . $linhaq['texto'] . "','" . $linhaq['tipo'] . "','" . $linhaq['valor'] . "','" . $linha['resposta'] . "', null)";
					$resultc = mysql_query( $sql, $dblink ) or die("Atividade: " . mysql_error());
				}

			}
		}
		
		if ($bibliografia == 'on') {
			$sql = "SELECT * FROM disbib WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_array($result)) {
				$sql = "INSERT INTO disbib VALUES ('$disidn', '" . $linha["bibid"] . "')";
				$resulta = mysql_query( $sql, $dblink ) or die("Bibliografia: " . mysql_error());
			}
		}
		
		if ($links == 'on') {
			$sql = "SELECT * FROM disweb WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_array($result)) {
				$sql = "INSERT INTO disweb VALUES ('$disidn','" . $linha["webid"] . "')";
				$resulta = mysql_query( $sql, $dblink ) or die("Webteca: " . mysql_error());
			}
		}
		
		if ($material == 'on') {
			$sql = "SELECT * FROM dismat WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_array($result)) {
				$sql = "INSERT INTO dismat VALUES ('$disidn','" . $linha["matid"] . "')";
				$resulta = mysql_query( $sql, $dblink ) or die("Material: " . mysql_error());
			}
		}
		
		if ($minicursos == 'on') {
			$sql = "SELECT * FROM disead WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_array($result)) {
				$sql = "INSERT INTO disead VALUES ($disidn,'" . $linha["eadid"] . "')";
				$resulta = mysql_query( $sql, $dblink ) or die("Disead: " . mysql_error());
			}
		}

		if ($planoensino == 'on') {
			$sql = "SELECT * FROM planoensino WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_array($result)) {
				$ementa = addslashes($linha['ementa']);
				$objetivos = addslashes($linha['objetivos']);
				$conteudo = addslashes($linha['conteudo']);
				$metodologia = addslashes($linha['metodologia']);
				$aval = addslashes($linha['avaliacao']);
				$recursos = addslashes($linha['recursos']);
				$bibliografiab = addslashes($linha['bibliografiab']);
				$bibliografiac = addslashes($linha['bibliografiac']);
				$sql = "INSERT INTO planoensino VALUES ($disidn,'" . $linha['cargahorsem'] . "','" . $linha['cargahortot'] . "','$ementa',
				'$objetivos','$conteudo','$metodologia','$aval', '$recursos','$bibliografiab','$bibliografiac')";
				$resulta = mysql_query( $sql, $dblink ) or die("Plano: " . mysql_error());
			}
		}
		
		if ($avaliacao == 'on') {
			$sql = "SELECT * FROM avaliacao WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_array($result)) {
				$texto = addslashes($linha['texto']);
				$sql = "INSERT INTO avaliacao VALUES (null, $disidn, '$texto','" . $linha['peso'] . "','" . $linha['periodo'] . "','" . 
				$linha['sigla'] . "','" . $linha['tipoaval'] . "')";
				$resulta = mysql_query( $sql, $dblink ) or die("Avaliacao: " . mysql_error());
			}
		}
		
		if ($professores == 'on') {
			$sql = "SELECT * FROM disusu WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_array($result)) {
				$sql = "INSERT INTO disusu VALUES ($disidn,'" . $linha['usuid'] . "')";
				$resulta = mysql_query( $sql, $dblink ) or die("Disusu: " . mysql_error());
			}
		}
		
		if ($aulas == 'on') {
			
			$sql = "SELECT * FROM plano WHERE disid = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			
			while ($linha = mysql_fetch_array($result)) {
				
				$planid = $linha['id'];
				$texto = addslashes($linha['texto']);
				$objetivos = addslashes($linha['objetivos']);
				$conteudos = addslashes($linha['conteudos']);
				$metodologia = addslashes($linha['metodologia']);
				$atividades = addslashes($linha['atividades']);
				$recursos = addslashes($linha['recursos']);
				$leituraobr = addslashes($linha['leituraobr']);
				$leiturarec = addslashes($linha['leiturarec']);
				$comentario = addslashes($linha['comentario']);
				
				$sql = "INSERT INTO plano VALUES (null,'" . $linha['data'] . "', $disidn,'" . $linha['aula'] . "','$texto','$objetivos','$conteudos',
				'$metodologia','$atividades', '$leituraobr','$leiturarec','$comentario')";
				$resulta = mysql_query( $sql, $dblink ) or die("Aulas: " . mysql_error());
				
				$sql = "SELECT LAST_INSERT_ID() as planidn";
				$resulta = mysql_query( $sql, $dblink ) or die(mysql_error());
				$linhap = mysql_fetch_array($resulta);
				$planidn = $linhap["planidn"];
				
				$sql = "SELECT * FROM planmat WHERE planid = '$planid'";
				$resulta = mysql_query( $sql, $dblink ) or die(mysql_error());
				while ($linhapm = mysql_fetch_array($resulta)) {
					$matid = $linhapm['matid'];
					$sql = "INSERT INTO planmat VALUES ('$planidn', '$matid')";
					$resultb = mysql_query( $sql, $dblink ) or die(mysql_error());
				}
				
				$sql = "SELECT * FROM planweb WHERE planid = '$planid'";
				$resulta = mysql_query( $sql, $dblink ) or die(mysql_error());
				while ($linhaw = mysql_fetch_array($resulta)) {
					$webid = $linhaw['webid'];
					$sql = "INSERT INTO planweb VALUES ('$planidn', '$webid')";
					$resultb = mysql_query( $sql, $dblink ) or die(mysql_error());
				}

				$sql = "SELECT * FROM planead WHERE planid = '$planid'";
				$resulta = mysql_query( $sql, $dblink ) or die(mysql_error());
				while ($linhae = mysql_fetch_array($resulta)) {
					$eadid = $linhae['eadid'];
					$sql = "INSERT INTO planead VALUES ('$planidn', '$eadid')";
					$resultb = mysql_query( $sql, $dblink ) or die(mysql_error());
				}
				
			}
			
		}
		
		echo  "<span id='textSuc'><img src='images/important.png' width=16 height=16 border=0>&nbsp;Inclus&atilde;o realizada com sucesso ...</span><br><br>" ;
		
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

		$('.datepicker').datepicker({
			format: '<?php echo $_SESSION["data_formato"];?>',                
                language: 'pt-BR'
		});

	});

	</script>
	
<?php

include 'dadosdis.inc';
	
echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-duplicate' aria-hidden='true'></span>&nbsp;" . _("Duplica&ccedil;&atilde;o de ambiente") . "</h3></div>";

echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
echo "<h3 class='panel-title'>" . _("Duplica&ccedil;&atilde;o de ambiente") . "</h3></div>";
echo "<div class='panel-body'>";

if ($pAction == "INSERT") {
	IncluiDados( $endid, $disid, $usuid, $curid, strip_tags($nome), strip_tags($objetivo), strip_tags($cargah), strip_tags($faltas), strip_tags($sigla), 
	$agenda, $avisos, $bibliografia, $conteudo, $links, $material, $minicursos, $planoensino, $avaliacao, $professores, $aulas, $datai, $dataf, $atividade);
}

$nome = '';
$objetivo = $linhad['objetivo'];
$cargah = $linhad['cargah'];
$faltas = $linhad['faltas'];
$sigla = $linhad['sigla'];
$agenda = 'off';
$atividade = 'off';
$avisos = 'off';
$bibliografia = 'off';
$links = 'off';
$material = 'off';
$minicursos = 'off';
$planoensino = 'off';
$avaliacao = 'off';
$professores = 'off';
$aulas = 'off';
$datai = '';
$dataf = '';

include( "connectdb.php" );

echo "<form action='copiadis.php' method=post>\n";
echo "<p><label for='endid'>" . _("Institui&ccedil;&atilde;o") . "</label></p>\n";
echo "<select onchange='submit()' id='textNormal' name='endid' class='form-control'>\n";
$sql = "SELECT e.id, e.nome FROM usuend ue INNER JOIN enderecos e ON ue.endid = e.id WHERE ue.usuid = '$usuid' ORDER BY 2";
$result = mysql_query( $sql, $dblink ) or die(mysql_error());
if ( mysql_num_rows($result) > 0) {
	echo "<option value=0>" . _("Selecione uma institui&ccedil;&atilde;o") . "</option>";
	while($linha = mysql_fetch_array($result)) {
		echo "<option value=" . $linha["id"];
		if ($endid == $linha["id"]) {
			echo " SELECTED";
		}
		echo ">" . $linha["nome"] . "</option>";
	}
}
echo "</select></form>";

echo "<br><form action='copiadis.php' method=post>\n";
echo "<p><label for='curid'>" . _("Curso") . "</label></p>\n";
echo "<select onchange='submit()' id='textNormal' name='curid' class='form-control'>\n";
$sql = "SELECT DISTINCT c.id, c.nome FROM usucur uc INNER JOIN curso c ON uc.curid = c.id WHERE uc.usuid = '$usuid' AND c.endid = '$endid' ORDER BY 2";
$result = mysql_query( $sql, $dblink ) or die(mysql_error());
if ( mysql_num_rows($result) > 0) {
	echo "<option value=0>" . _("Selecione um curso") . "</option>";
	while($linha = mysql_fetch_array($result)) {
		echo "<option value=" . $linha["id"];
		if ($curid == $linha["id"]) {
			echo " SELECTED";
		}
		echo ">" . $linha["nome"] . "</option>";
	}
}
echo "</select>";
echo "<input type=hidden name=endid value=$endid>";
echo "</form>";

mysql_close($dblink);

echo "<br><br><form action='copiadis.php' method='POST'>\n" ;
echo "<p><label for='nome'>(*) " . _("Nome da nova disciplina") . "</label>\n";
echo "<input type='text' name='nome' value='$nome' size=60 maxlength=60 class='form-control'></p>\n";
echo "<p><label for='cargah'>(*) " . _("Carga Hor&aacute;ria") . "</label>\n";
echo "<input type='text' name='cargah' value='$cargah' size=6 maxlength=6 class='form-control'></p>\n";
echo "<p><label for='faltas'>" . _("Faltas Poss&iacute;veis") . "</label>\n";
echo "<input type='text' name='faltas' value='$faltas' size=6 maxlength=6 class='form-control'></p>\n";
//echo "<p><label for='sigla'>Sigla</label>\n";
//echo "<input type='text' name='sigla' value='$sigla' size=6 maxlength=6 class='form-control'></p>\n";
echo "<p><label for='datai'>(*) " . _("Data Inicial") . "</label>\n";
echo "<input type='text' name='datai' id='datai' value='$datai' size=10 maxlength=10 class='form-control'></p>\n";
echo "<p><label for='dataf'>(*) " . _("Data Final") . "</label>\n";
echo "<input type='text' name='dataf' id='dataf' value='$dataf' size=10 maxlength=10 class='form-control'></p>\n";
echo "<p><label for='copia'>(*) " . _("Elementos a serem copiados") . "</label></p>\n";
echo "<p><input type='checkbox' name='agenda'>&nbsp;" . _("Agenda") . "</p>\n";
echo "<p><input type='checkbox' name='atividade'>&nbsp;" . _("Atividades") . "</p>\n";
echo "<p><input type='checkbox' name='avaliacao'>&nbsp;" . _("Avalia&ccedil;&atilde;o") . "</p>\n";
echo "<p><input type='checkbox' name='avisos'>&nbsp;" . _("Feed de not&iacute;cias") . "</p>\n";
//echo "<p><input type='checkbox' name='bibliografia' class='form-control'>&nbsp;Bibliografia</p>\n";
echo "<p><input type='checkbox' name='minicursos'>&nbsp;" . _("Hipertextos") . "</p>\n";
//echo "<p><input type='checkbox' name='links' class='form-control'>&nbsp;Links</p>\n";
echo "<p><input type='checkbox' name='material'>&nbsp;" . _("Material de apoio") . "</p>\n";
echo "<p><input type='checkbox' name='planoensino'>&nbsp;" . _("Plano de ensino") . "</p>\n";
echo "<p><input type='checkbox' name='aulas'>&nbsp;" . _("Planos de aula") . "</p>\n";
echo "<p><input type='checkbox' name='professores'>&nbsp;" . _("Professores") . "</p>\n";
echo "<p><label for='objetivo'>(*) " . ("Objetivos") . "</label><br>\n";
echo "<p><textarea name='objetivo' rows='20' class='form-control'>$objetivo</textarea></p>\n";
echo "<p><input type='submit' class='btn btn-default' name='enviarage' value='" . _("Enviar") . "'></p>\n";
echo "<input type=hidden name=endid value=$endid>\n";
echo "<input type=hidden name=curid value=$curid>\n";
echo "<input type=hidden name=usuid value=$usuid>\n";
echo "<input type=hidden name=disid value=$disid>\n";
echo "<input type=hidden name=pAction value=INSERT>\n";
echo "</form>\n";

echo "</div></div>";

include 'rodape.inc';

?>
