<?php
$file_endereco = "<a href='#' onClick='abrirPag(" . '"endereco.php", "pAction=SELECT"' . ")'>";
$file_conteudo = "<a href='#' onClick='abrirPag(" . '"eaddis.php", "pAction=SELECT"' . ")'>";
$file_questoes = "<a href='#' onClick='abrirPag(" . '"questoes.php", ""' . ")'>";
$file_assuntos = "<a href='#' onClick='abrirPag(" . '"assunto.php", ""' . ")'>";
$file_bibliografia = "<a href='#' onClick='abrirPag(" . '"bibliografia.php", ""' . ")'>";
$file_links = "<a href='#' onClick='abrirPag(" . '"links.php", ""' . ")'>";
$file_material = "<a href='#' onClick='abrirPag(" . '"material.php", ""' . ")'>";
$file_restore = "<a href='#' onClick='abrirPag(" . '"restore.php", ""' . ")'>";
$file_cursos = "<a href='#' onClick='abrirPag(" . '"endcur.php", ""' . ")'>";
$file_disciplinas = "<a href='#' onClick='abrirPag(" . '"disciplina.php", ""' . ")'>";
$file_estudantes = "<a href='#' onClick='abrirPag(" . '"estudante.php", ""' . ")'>";

?>

<div class="col-md-3" id="leftCol">

<div class="well">

<ul class="nav nav-stacked" id="sidebar">

<?php
if ($tipo == 1) echo "<li>$file_assuntos " . _("Assuntos") . "</a></li>\n";
//if ($tipo == 1) echo "<li>$file_bibliografia " . _("Bibliografia") . "</a></li>\n";
echo "<li>$file_cursos " . _("Cursos") . "</a></li>\n";
if ($tipo == 1) echo "<li>$file_disciplinas " . _("Disciplinas") . "</a></li>\n";
if ($tipo == 1) echo "<li>$file_estudantes " . _("Estudantes") . "</a></li>\n";
if ($tipo == 1) echo "<li>$file_conteudo " . _("Hipertextos") . "</a></li>\n";
echo "<li>$file_endereco " . _("Institui&ccedil;&otilde;es") . "</a></li>\n";
//if ($tipo == 1) echo "<li>$file_links Links</a></li>\n";
if ($tipo == 1) echo "<li>$file_material " . _("Material de Apoio") . "</a></li>\n";
if ($tipo == 1) echo "<li>$file_questoes " . _("Quest&otilde;es") . "</a></li>\n";
if ($tipo == 1) echo "<li>$file_restore Restore</a></li>\n";

$menu=1; //usado para identificar o menu que deve ser exibido na exibição do perfil

?>

</ul>

</div></div>

<div class="col-md-9">