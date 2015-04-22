<script>

	$(function(){   
			
		$('#ConfirmarMenu').click(function () {
			abrirPag("principal.php", "pAction=DELETE&disid=<?php echo $disid;?>");
			modal.modal('hide');
		});

		$('a#hrefDisExc').click(function(e){
			e.preventDefault();
			$('#deleteConfirmModalMenu').modal('show');
		});
		
	});

</script>
	
<div class="modal fade" id="deleteConfirmModalMenu" tabindex="-1" role="dialog" aria-labelledby="deleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				
				<?php 
					echo "<h4 class='modal-title' id='deleteLabel'>" . _("Notifica&ccedil;&atilde;o de exclus&atilde;o") . "</h4>";
           			echo "</div>";
					echo "<div class='modal-body'>";
               		echo "<p>" . _("Voc&ecirc; optou por excluir este ambiente.") . "</p>";
               		echo "<p>" . _("Se esta &eacute; a a&ccedil;&atilde;o que deseja executar, ");
               		echo _("por favor confirme sua escolha ou cancele a opera&ccedil;&atilde;o para retornar.") . "</p>";
           			echo "</div>";
          				echo "<div class='modal-footer'>";
               		echo "<button type='button' class='btn btn-success' data-dismiss='modal' id='Cancelar'>" . _("Cancelar") . "</button>";
               		echo "<button type='button' class='btn btn-danger' id='ConfirmarMenu'>" . _("Confirmar") . "</button>";
               	?>
                	
           	</div>
       	</div>
   	</div>
</div>

<div class="col-md-3" id="leftCol">

<div class="well">

<ul class="nav nav-stacked" id="sidebar">

<?php

$visitante = $_SESSION["visitante"];

echo "<li><a href='detplanprof.php'>" . _("In&iacute;cio") . "</a></li>\n";
if (!$visitante) {
	echo "<li><a href='#' onClick='abrirPag(" . '"estudante.php", "disid=' . $disid . '&pAction=LIST"' . ")'>" . _("Estudantes") . "</a></li>\n";
}

echo "<hr>";

//echo "<li class='divider'></li>";

include( "./connectdb.php" );

$sql = "SELECT nome FROM menu WHERE disid = '$disid'" ;
$query = mysql_query( $sql, $dblink );

$n=0;
while ($line = mysql_fetch_array($query)) {
	$menu[$n] = $line["nome"];
    $n++;
}

mysql_close($dblink);

if (array_search("Agenda", $menu) === FALSE and !$visitante) {
	echo "<li><a href='agenda.php'>" . _("Agenda") . "</a></li>";
}
if (array_search("Atividades", $menu) === FALSE and !$visitante) {
	echo "<li><a href='prova.php'>" . _("Atividades") . "</a></li>";
}
if (array_search("Avaliacao", $menu) === FALSE and !$visitante) {
	echo "<li><a href='avaliacao.php'>" . _("Avalia&ccedil;&atilde;o") . "</a></li>";
}
//if (array_search("Bibliografia", $menu) === FALSE) {
//	echo "<li><a href='#' onClick='abrirPag(" . '"bibliografia.php", "disid=' . $disid . '&pAction=LIST"' . ")'>" . _("Bibliografia") . "</a></li>";
//}
if (array_search("Avisos", $menu) === FALSE and !$visitante and $tipo == 1) {
	echo "<li><a href='aviso.php'>" . _("Compartilhar") . "</a></li>";
}
if (array_search("ControleFrequencia", $menu) === FALSE and !$visitante) {
	echo "<li><a href='frequencia.php'>" . _("Controle de frequ&ecirc;ncia") . "</a></li>";
}
if (array_search("FormacaoEquipes", $menu) === FALSE and !$visitante) {
	echo "<li><a href='equipe.php'>" . _("Equipes de trabalho") . "</a></li>";
}
if (array_search("Forum", $menu) === FALSE and !$visitante) {
	echo "<li><a href='forum.php'>" . _("F&oacute;rum") . "</a></li>";
}
if (array_search("TextosResumos", $menu) === FALSE) {
	echo "<li><a href='#' onClick='abrirPag(" . '"eaddis.php", "disid=' . $disid . '&pAction=SELECT"' . ")'>" . _("Hipertextos") . "</a></li>";
}
//if (array_search("Links", $linha) === FALSE) {
//	echo "<li><a href='#' onClick='abrirPag(" . '"links.php", "disid=' . $disid . '&pAction=LIST"' . ")'>" . _("Links") . "</a></li>";
//}
if (array_search("MaterialApoio", $menu) === FALSE) {
	echo "<li><a href='#' onClick='abrirPag(" . '"material.php", "disid=' . $disid . '&pAction=LIST"' . ")'>" . _("Material de apoio") . "</a></li>";
}
if (array_search("Mensagens", $menu) === FALSE and !$visitante) {
	echo "<li><a href='mensagem.php'>" . _("Mensagens") . "</a></li>";
}
if (array_search("Orientacao", $menu) === FALSE and !$visitante) {
	echo "<li><a href='reunioes.php'>" . _("Orienta&ccedil;&atilde;o") . "</a></li>";
}
if (array_search("PlanoEnsino", $menu) === FALSE) {
	echo "<li><a href='planoensino.php'>" . _("Plano de ensino") . "</a></li>";
}
if (array_search("PlanoAula", $menu) === FALSE) {
	echo "<li><a href='plano.php'>" . _("Planos de aula") . "</a></li>";
}

if ($tipo == 1) {
	echo "<hr>\n";
	//echo "<li role='presentation' class='divider'></li>";
	echo "<li><a href='backup.php'>" . _("Backup") . "</a></li>";
	echo "<li><a href='menuconfig.php'>" . _("Configura&ccedil;&atilde;o") . "</a></li>\n";
	echo "<li><a href='copiadis.php'>" . _("Copiar este ambiente") . "</a></li>";
	echo "<li><a href='diario.php'>" . _("Di&aacute;rio de classe") . "</a></li>\n";
	echo "<li><a href='#' id='hrefDisExc'>" . _("Excluir este ambiente") . "</a></li>\n";
	echo "<li><a href='selpro.php'>" . _("Professores") . "</a></li>\n";
	echo "<li><a href='acesso.php'>" . _("Relat&oacute;rio de acesso") . "</a></li>\n";
	echo "<li><a href='altdisvin.php'>" . _("Transferir esse ambiente") . "</a></li>\n";
}

$menu=2; //usado para identificar o menu que deve ser exibido na exibição do perfil

?>

</ul>

</div></div>

<div class="col-md-9">
