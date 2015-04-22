<?php
session_start();
include( "buscasessao.php" );
$linha = BuscaSessao($disid);
$tipo = $linha["professor"];
$id = $linha["usuid"];

function GravaDados($disid, $recurso) {
	
	include( "./connectdb.php" );
	
	$SQL = "DELETE FROM menu WHERE disid = $disid";
    $aQResult = mysql_query( $SQL, $aDBLink );
    
	$RecursoDisp[0] = "Agenda";
	$RecursoDisp[1] = "Atividades";
	$RecursoDisp[2] = "Avaliacao";
	$RecursoDisp[3] = "Avisos";
	//$RecursoDisp[4] = "Bibliografia";
	$RecursoDisp[5] = "ControleFrequencia";
	$RecursoDisp[6] = "FormacaoEquipes";
	$RecursoDisp[7] = "Forum";
	//$RecursoDisp[8] = "Links";
	$RecursoDisp[9] = "MaterialApoio";
	$RecursoDisp[10] = "Mensagens";
	$RecursoDisp[11] = "Orientacao";
	$RecursoDisp[12] = "PlanoEnsino";
	$RecursoDisp[13] = "PlanoAula";
	$RecursoDisp[14] = "TextosResumos";
	
	for ($i=0; $i<=16; $i++) {
		$indice = array_search($RecursoDisp[$i], $recurso);
		if ($indice === FALSE) {
			$SQL = "INSERT INTO menu VALUES (null, $disid, '" . $RecursoDisp[$i] . "')";
    		$aQResult = mysql_query( $SQL, $aDBLink );
		}
	}
	
	echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
	
	mysql_close($aDBLink);
	
}

include( "cabecalho.php" );

include( "menu.inc" );

include 'dadosdis.inc';

echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-wrench' aria-hidden='true'></span>&nbsp;" . _("Configura&ccedil;&atilde;o de recursos") . "</h3></div>";

echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
echo "<h3 class='panel-title'>" . _("Sele&ccedil;&atilde;o de recursos") . "</h3></div>";
echo "<div class='panel-body'>";
 				
if (isset($recursos)) {
	GravaDados($disid, $recurso);
}
 				
include( "./connectdb.php" );
 			
$SQL = "SELECT nome FROM menu WHERE disid = '$disid'" ;
$QResult = mysql_query( $SQL, $aDBLink );

$i=0;
while ($lin = mysql_fetch_array($QResult)) {
	$linha[$i] = $lin["nome"];
	$i++;
}
    		
echo '<form method="post" action="menuconfig.php">';
 				
if (array_search("Agenda", $linha) !== FALSE) {
	echo '<input type="checkbox" name="recurso[]" value="Agenda">Agenda</input>';
} else { 
	echo '<input type="checkbox" name="recurso[]" value="Agenda" checked>Agenda</input>';
}
if (array_search("Atividades", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="Atividades">Atividades</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="Atividades" checked>Atividades</input>';
}
if (array_search("Avaliacao", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="Avaliacao">Avalia&ccedil;&atilde;o</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="Avaliacao" checked>Avalia&ccedil;&atilde;o</input>';
}
if (array_search("Avisos", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="Avisos">Avisos</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="Avisos" checked>Avisos</input>';
}
//if (array_search("Bibliografia", $linha) !== FALSE) {
//	echo '<br><input type="checkbox" name="recurso[]" value="Bibliografia">Bibliografia</input>';
//} else {
//	echo '<br><input type="checkbox" name="recurso[]" value="Bibliografia" checked>Bibliografia</input>';
//}
if (array_search("ControleFrequencia", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="ControleFrequencia">Controle de Frequ&ecirc;ncia</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="ControleFrequencia" checked>Controle de Frequ&ecirc;ncia</input>';
}
if (array_search("FormacaoEquipes", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="FormacaoEquipes">Forma&ccedil;&atilde;o de Equipes</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="FormacaoEquipes" checked>Forma&ccedil;&atilde;o de Equipes</input>';
}
if (array_search("Forum", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="Forum">Forum</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="Forum" checked>Forum</input>';
}
if (array_search("TextosResumos", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="TextosResumos">Hipertextos</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="TextosResumos" checked>Hipertextos</input>';
} 
//if (array_search("Links", $linha) !== FALSE) {
//	echo '<br><input type="checkbox" name="recurso[]" value="Links">Links</input>';
//} else {
//	echo '<br><input type="checkbox" name="recurso[]" value="Links" checked>Links</input>';
//}
if (array_search("MaterialApoio", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="MaterialApoio">Material de Apoio</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="MaterialApoio" checked>Material de Apoio</input>';
}
if (array_search("Mensagens", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="Mensagens">Mensagens</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="Mensagens" checked>Mensagens</input>';
}
if (array_search("Orientacao", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="Orientacao">Orienta&ccedil;&atilde;o de Projetos</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="Orientacao" checked>Orienta&ccedil;&atilde;o de Projetos</input>';
}
if (array_search("PlanoEnsino", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="PlanoEnsino">Plano de Ensino</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="PlanoEnsino" checked>Plano de Ensino</input>';
}
if (array_search("PlanoAula", $linha) !== FALSE) {
	echo '<br><input type="checkbox" name="recurso[]" value="PlanoAula">Planos de Aula</input>';
} else {
	echo '<br><input type="checkbox" name="recurso[]" value="PlanoAula" checked>Planos de Aula</input>';
}				
mysql_close($aDBLink);
		
?>
 			
<input type="hidden" name="recursos" value="1"></input>
<input type="hidden" name="disid" value="<?php echo $disid; ?>"></input>
<br><br><input type="submit" class="btn btn-default" value="Enviar" /></fieldset>
</form> 				
 		
<?php 

echo "</div></div>";
include 'rodape.inc';

?>