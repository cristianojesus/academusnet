<?php
session_start();

foreach ($_POST as $key => $value) {
	$$key = $value;
}

function passo1() {

	$instituicao = $_SESSION["instituicao"];
	unset($_SESSION["instituicao"]);
	$usuario = $_SESSION["usuario"];
		
	echo "<p class='lead'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span> " . 
	_("Voc&ecirc; &eacute; respons&aacute;vel por todo e qualquer dado que fornecer ao Academusnet.") . "&nbsp;" .
	_("Seja prudente, pois o uso indevido dos recursos deste sistema acarretar&aacute; nas penas legais cab&iacute;veis.") . "</p>\n";

	echo "<p class='lead'><span class='glyphicon glyphicon-bullhorn' aria-hidden='true'></span> " . 
	_("Passo 1: selecione as institui&ccedil;&otilde;es que voc&ecirc; possui v&iacute;nculo.") . "&nbsp;\n";

	if ($usuario == "professor") {
		// echo _("Caso sua institui&ccedil;&atilde;o n&atilde;o esteja na lista, selecione Academusnet ou clique") . " <a href='cadastro.php?passo=6'>" . _("aqui") . "</a>.</p>\n";
		echo _("Caso sua institui&ccedil;&atilde;o n&atilde;o esteja na lista, n&atilde;o se preocupe, voc&ecirc; poder&aacute; cadastr&aacute;-la depois.") . "&nbsp;" . 
		_("Se preferir") . ", <strong>" . _("use a institui&ccedil;&atilde;o gen&eacute;rica Academusnet.") . "</strong></p>\n";
	} else {
		echo _("Caso sua institui&ccedil;&atilde;o n&atilde;o esteja na lista, solicite a algum representante ou professor que entre em contato conosco.") . 
		"&nbsp;" . _("Consulte tamb&eacute;m") . " <a href='matricula.php'><span class='label label-primary'>" . _("cursos independentes") . "</span></a> " . 
		_("dispon&iacute;veis e") . " <a href='visitante.php'><span class='label label-primary'>" .
		_("cursos abertos") . "</span></a> " . _("a visitantes.") . "</p>\n";
	}
	
	echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Institui&ccedil;&atilde;o") . "</h3></div>";
	echo "<div class='panel-body'>";

	$sql = "SELECT id, nome FROM enderecos ORDER BY 2";
	
	include "connectdb.php";
	
	$qEnderecos = mysql_query($sql);
	
	if (mysql_num_rows($qEnderecos) == 0) {
		echo "<p class='lead'>" . _("N&atilde;o h&aacute; institui&ccedil;&otilde;es cadastradas.") . "</p>\n";
	} else {
		echo "<form id='instituicao' action='cadastro.php' method='POST'>\n";
		while ($aEnderecos = mysql_fetch_array($qEnderecos)) {
			echo "<INPUT type='hidden' name='passo' value='2'>";
			echo "<p class='lead'><INPUT type='checkbox' name='instituicao[" . $aEnderecos["id"] . "]'";
			if ($instituicao[$aEnderecos["id"]] == "on") {
				echo "CHECKED>\n";
			} else {
				echo ">\n";
			}
			echo $aEnderecos["nome"] . "</p>\n";
		}
		echo "<br><p><INPUT type='submit' class='btn btn-default' name='enviar' value='Passo 2 >>'></p></form>\n";
	}
	
	echo "</div></div>";
	
	mysql_close($aDBLink);

}

function passo2_aluno() {

	$instituicao = $_SESSION["instituicao"];
	$usuario = $_SESSION["usuario"];
	$ra = $_SESSION["ra"];
	unset($_SESSION["ra"]);

	echo "<p class='lead'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span> " .
	_("Aviso importante: voc&ecirc; &eacute; respons&aacute;vel por todo e qualquer dado que fornecer ao Academusnet.") . "&nbsp;" .
	_("Seja prudente, pois o uso indevido dos recursos deste sistema acarretar&aacute; nas penas legais cab&iacute;veis.") . "</p>\n";
	
	$sql = "SELECT nome as instituicao, id as endid FROM enderecos ";

	$i = 1;
	$hainstituicao = 0;
	
	foreach ($instituicao as $endid => $valor) {
		if ($valor == 'on') {
			$hainstituicao = 1;
			if ($i == 1) {
				$sql .= "WHERE id = '$endid' ";
				$i++;
			} else {
				$sql .= "OR id = '$endid' ";
			}
		}
	}
	
	$sql .= "ORDER BY 1";

	include "connectdb.php";
	
	$qEnderecos = mysql_query($sql);
	
	if (mysql_num_rows($qEnderecos) == 0 or $hainstituicao == 0) {
		
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Institui&ccedil;&atilde;o") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma institui&ccedil;&atilde;o ...") . "</strong></div>" ;
		echo "<br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=1")' . "'><button type='button' class='btn btn btn-default'><< " . 
		_("Passo 1") . "</button></a></p>\n";
		
	} else {
		
		echo "<br><p class='lead'><span class='glyphicon glyphicon-bullhorn' aria-hidden='true'></span> " .
		_("Passo 2: Informe seu Registro de Aprendiz (RA). Ele deve ter sido fornecido pela institui&ccedil;&atilde;o ou pelo professor.") . "</p>\n";

		echo "<br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=1")' . "'><button type='button' class='btn btn btn-default'><< " . 
		_("Passo 1") . "</button></a></p>\n";
		
		echo "<br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Institui&ccedil;&atilde;o") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		$i = 0;
		
		while ($aEnderecos = mysql_fetch_array($qEnderecos)) {
			
			if ($i == 0) {
				echo "<h1>" . _("Institui&ccedil;&atilde;o") . ": " . $aEnderecos["instituicao"] . "</h1>\n";
			} else {
				if ($aEnderecos["instituicao"] != $aEnderecos["instituicao"]) {
					echo "<h1>" . _("Institui&ccedil;&atilde;o") . ": " . $aEnderecos["instituicao"] . "</h1>\n";
				}
			}
			
			echo "<br><form id='ra' action='cadastro.php' method='POST'>\n";
			echo "<p class='lead'>" . _("Registro de aprendiz") . ":&nbsp;<INPUT type='text' name='ra[" . $aEnderecos["endid"] . "]' value='" . 
			$ra[$aEnderecos["endid"]] . "' size='30' maxlength='30' alt='ra' class='form-control'></p>\n";
			
		}
		
		echo "<INPUT type='hidden' name='passo' value='3'";
		echo "<p><INPUT type='submit' class='btn btn-default' name='enviar' value='" . _("Passo 3") . " >>'></p>\n";
		echo "</form>\n";
		$i++;
	}
	mysql_close($aDBLink);
	
	echo "</div></div>";

}

function passo2_professor() {

	$instituicao = $_SESSION["instituicao"];
	$curso = $_SESSION["curso"];
	unset($_SESSION["curso"]);

	echo "<p class='lead'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span> " .
	_("Aviso importante: voc&ecirc; &eacute; respons&aacute;vel por todo e qualquer dado que fornecer ao Academusnet.") . "&nbsp;" .
	_("Seja prudente, pois o uso indevido dos recursos deste sistema acarretar&aacute; nas penas legais cab&iacute;veis.") . "</p>\n";
	
	$sql = "SELECT DISTINCT e.nome as instituicao, c.id as curid, c.nome as curso FROM curso c INNER JOIN enderecos e ON c.endid = e.id 
	LEFT JOIN usuend ue ON e.id = ue.endid ";
	
	$i = 1;
	$hainstituicoes = 0;
	
	foreach ($instituicao as $endid => $valor) {
		if ($valor == 'on') {
			$hainstituicoes = 1;
			if ($i == 1) {
				$sql .= "WHERE e.id = '$endid' ";
				$i++;
			} else {
				$sql .= "OR e.id = '$endid' ";
			}
		}
	}
	
	$sql .= "ORDER BY 1,3";

	include "connectdb.php";
	
	$qUsuEnd = mysql_query($sql) or die(mysql_error($qUsuEnd));

	$hacursos = 0;
		
	while ($aUsuEnd = mysql_fetch_array($qUsuEnd)) {
		if(!empty($aUsuEnd["curid"])) {
			$hacursos = 1;
		}
	}

	unset($aUsuEnd);

	$qUsuEnd = mysql_query($sql) or die(mysql_error($qUsuEnd));
	
	if (mysql_num_rows($qUsuEnd) == 0 or $hainstituicoes == 0) {
		
		//echo "<p<b>" . _("Selecione ao menos uma institui&ccedil;&atilde;o.") . " </b><p>" . _("Clique") . 
		//" <a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=1&usuario=' . $usuario . '")' . "'>" . _("aqui") . "</a> " . _("para voltar.") . "</p>\n";
		
		echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Curso") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo "<p class='lead'>" . _("Voc&ecirc; optou por cadastrar a institui&ccedil;&atilde;o depois.") . "&nbsp;" .
		_("Com isso voc&ecirc; ganhar&aacute; atributo de Administrador e dever&aacute; tamb&eacute;m cadastrar os cursos. Concorda com esse procedimento?") . "</p>";
		echo "<br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=1&usuario=' . $usuario . '")' . "'><button type='button' class='btn btn btn-default'><< " . 
		_("Passo 1") . "</a></button>\n";
		echo "<a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=4&usuario=' . $usuario . '")' . "'><button type='button' class='btn btn btn-default'>" .
		_("Continuar") . " >></a></button>\n";
		
	} else {
		
		echo "<br><p class='lead'><span class='glyphicon glyphicon-bullhorn' aria-hidden='true'></span> " .
		_("Passo 2: Professor, selecione o curso que voc&ecirc; possui v&iacute;nculo (pode ser selecionado mais de um).") . "&nbsp;" .
		_("Caso seu curso n&atilde;o esteja na lista, n&atilde;o se preocupe. Voc&ecirc; pode cadastr&aacute;-lo depois.") . "</p>\n";
		// <a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=6&usuario=$usuario")' . "'>clique aqui</a>.</p>\n";
		
		echo "<br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=1&usuario=' . $usuario . '")' . "'><button type='button' class='btn btn btn-default'><< " . 
		_("Passo 1") . "</a></button>\n";
		
		echo "<form action='cadastro.php' method='POST'>\n";
		
		echo "<br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Curso") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		$i = 0;
		while ($aUsuEnd = mysql_fetch_array($qUsuEnd)) {
			if ($i == 0) {
				echo "<h1>" . _("Institui&ccedil;&atilde;o") . ": " . $aUsuEnd["instituicao"] . "</h1>\n";
				$instituicaoa = $aUsuEnd["instituicao"];
				$hacursos2 = 0;
			} else {
				if ($aUsuEnd["instituicao"] != $instituicaoa) {
					if ($hacursos2 == 0) {
						echo "<p class='lead'>&nbsp;&nbsp;" . _("N&atilde;o h&aacute; cursos dispon&iacute;veis para esta institui&ccedil;&atilde;o") . "</p>\n";
					}
					echo "<h1>" . _("Institui&ccedil;&atilde;o") . ": " . $aUsuEnd["instituicao"] . "</h1>\n";
					$instituicaoa = $aUsuEnd["instituicao"];
					$hacursos2 = 0;
				}
			}
			if (!empty($aUsuEnd["curid"])) {
				$hacursos2 = 1;
				echo "<br><p class='lead'>&nbsp;&nbsp;<INPUT type='checkbox' name='curso[" . $aUsuEnd["curid"] . "]' ";
				if ($curso[$aUsuEnd["curid"]] == "on") {
					echo "CHECKED>";
				} else {
					echo ">";
				}
				echo $aUsuEnd["curso"] . "<br></p>\n";
			}
			$i++;
		}
		if ($hacursos == 1) {
			echo "<INPUT type='hidden' name='passo' value='4'>";
			echo "<br><p><INPUT type='submit' class='btn btn-default' name='enviar' value='" . _("Passo 3") . " >>'></p></form>\n";
		}
	}
	mysql_close($aDBLink);
	
	echo "</div></div>";
}

function passo3_aluno() {

	echo "<p class='lead'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span> " .
	_("Aviso importante: voc&ecirc; &eacute; respons&aacute;vel por todo e qualquer dado que fornecer ao Academusnet.") . "&nbsp;" .
	_("Seja prudente, pois o uso indevido dos recursos deste sistema acarretar&aacute; nas penas legais cab&iacute;veis.") . "</p>\n";
	
	echo "<br><p class='lead'><span class='glyphicon glyphicon-bullhorn' aria-hidden='true'></span> " . 
	_("Passo 3: caso voc&ecirc; j&aacute; tenha se cadastrado e est&aacute; com problemas, n&atilde;o se cadastre novamente.") . "&nbsp;" . 
	_("Pe&ccedil;a ajuda ao seu professor ou contacte o") . "<a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=8&usuario=' . $usuario . '")' . "'> " . 
	"<span class='label label-primary'>" . _("suporte") . "</span></a>.</p>\n";
	
	echo "<br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=2&usuario=' . $usuario . '")' . "'><button type='button' class='btn btn btn-default'><< " . 
		_("Passo 2") . "</button></a>\n";
	
	echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Identifica&ccedil;&atilde;o") . "</h3></div>";
	echo "<div class='panel-body'>";

	$ra = $_SESSION["ra"];

	$haalunos = 0;
	foreach ($ra as $curid => $valor) {
		if (!empty($valor)) {
			$haalunos = 1;
		}
	}
	if ($haalunos == 0) {
		
		echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Voc&ecirc; deve preencher seu Registro de Aprendiz (RA) ...") . "</strong></div>" ;
		echo "<br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=2&usuario=' . $usuario . '")' . "'><button type='button' class='btn btn btn-default'><< " .
		_("Passo 2") . "</button></a></p>\n";
		echo "</div></div>";
		return;
	}
	
	$hacursos = 0;
	
	include "connectdb.php";
	
	foreach ($ra as $endid => $valor) {
	
		$sql = "SELECT nome as instituicao, id as endid FROM enderecos WHERE id = '$endid'";

		$qEnderecos = mysql_query($sql);
		
		if (mysql_num_rows($qEnderecos) == 0) {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Ocorreu um erro inesperado. ...") . "</strong></div>" ;
			echo "<br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=2&usuario=' . $usuario . '")' . "'><button type='button' class='btn btn btn-default'><< " .
			_("Passo 2") . "</button></a></p>\n";
		} else {

			$i = 0;

			while ($aEnderecos = mysql_fetch_array($qEnderecos)) {
			
				if ($i == 0) {
					echo "<h1>" . _("Institui&ccedil;&atilde;o") . ": " . $aEnderecos["instituicao"] . "</h1>\n";
					$instituicaoa = $aEnderecos["instituicao"];
				} else {
					if ($aEnderecos["instituicao"] != $instituicaoa) {
						echo "<h1>" . _("Institui&ccedil;&atilde;o") . ": " . $aEnderecos["instituicao"] . "</h1>\n";
						$instituicaoa = $aEnderecos["instituicao"];
					}
				}

				$sql = "SELECT DISTINCT a.nome as aluno, c.nome as curso FROM disalu da INNER JOIN aluno a ON a.id = da.aluid 
				INNER JOIN disciplina d ON d.id = da.disid INNER JOIN curso c ON c.id = d.curid WHERE da.aluid = '$valor' AND d.endid = '" . $aEnderecos["endid"] . "'";
			
				$qDisAlu = mysql_query($sql);
		
				if (mysql_num_rows($qDisAlu) == 0) {
					echo "<p class='lead'>" . _("Somente &eacute; poss&iacute;vel cadastrar-se neste sistema caso voc&ecirc; tenha algum v&iacute;nculo com um professor que previamente") . 
					"&nbsp;" . _("tenha cadastrado voc&ecirc; em sua disciplina. Seu Registro de Aprendiz (RA) n&atilde;o foi encontrado em nenhum curso") . "&nbsp;" .
					_("desta institui&ccedil;&atilde;o. Verifique os dados informados nos passos anteriores.") . "&nbsp;" .
					_("Caso tudo esteja realmente certo, pe&ccedil;a ajuda ao seu professor ou contacte o") .  
					" <a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=8&usuario=$usuario")' . "'><span class='label label-primary'>" . _("suporte") . "</span></a>.</p>\n";
				} else {
					$j = 0;
					while ($aDisAlu = mysql_fetch_array($qDisAlu)) {
						if ($j == 0) {
							echo "<p class='lead'>&nbsp;&nbsp;&nbsp;&nbsp;" . $aDisAlu["aluno"] . "</p>\n";
						}
						echo "<p class='lead'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _("Curso") . ": " . $aDisAlu["curso"] . "</p>";

						$sql = "SELECT ra, nome, email FROM usuario u INNER JOIN usuend uec ON uec.usuid = u.id WHERE uec.ra = '$valor' AND uec.endid = '" . 
						$aEnderecos["endid"] . "'";

						$qUsuario = mysql_query($sql);

						if (mysql_num_rows($qUsuario) > 0) {
							echo "<p class='lead'><b>" . _("Possivelmente voc&ecirc; j&aacute; deve estar cadastrado. Confira os dados listados abaixo") . ":</b></p>";
							while ($aUsuario = mysql_fetch_array($qUsuario)) {
								echo "<p><b>" . $aUsuario["ra"] . "&nbsp;&nbsp;" . $aUsuario["nome"] . "&nbsp;&nbsp;" . $aUsuario["email"] . "</b></p>";
							}
							echo _("Se voc&ecirc; esqueceu sua senha, clique") . " <a href='esqueceu.php'>" . _("aqui") . "</a>. " .
							_("Se suas disciplinas n&atilde;o est&atilde;o sendo disponibilizadas pra voc&ecirc;") . ", " .
							_("&eacute; poss&iacute;vel que seu Registro de Aprendiz (RA) esteja errado; acesse o sitema e corrija-o em seu perfil.") . "&nbsp;" .
							_("Se estiver tendo dificuldades,  pe&ccedil;a ajuda ao seu professor ou entre em contato com o") . 
							"<a href=cadastro.php?passo=8><span class='label label-primary'>" . ("suporte") . "</span></a>.</p>";
						} else {
							$hacursos = 1;
						}
						$j++;
					}
				}
			}
		}
	}
	if ($hacursos == 1) {
		echo "<form action='cadastro.php' method='POST'>\n";
		echo "<p>&nbsp;&nbsp;" . _("Voc&ecirc; confirma esses dados? Se afirmativo, avance para a pr&oacute;xima etapa.") . "</p>\n";
		echo "<INPUT type='hidden' name='passo' value='4'>";
		echo "<p><INPUT type='submit' class='btn btn-default' name='enviar' value='" . _("Passo 4") . " >>'></p>\n";
		echo "</form>\n";
	}	
	mysql_close($aDBLink);
	
	echo "</div></div>";
}

function passo4() {

	$instituicao = $_SESSION["instituicao"];
	$usuario = $_SESSION["usuario"];
	$curso = $_SESSION["curso"];
	$ra = $_SESSION["ra"];
	$senha = $_SESSION["senha_password"];
	$email = $_SESSION["email"];
	$nome = $_SESSION["nome"];
	$endereco = $_SESSION["endereco"];
	$cidade = $_SESSION["cidade"];
	$cep = $_SESSION["cep"];
	$uf = $_SESSION["uf"];
	$telefone = $_SESSION["telefone"];
	$pais = $_SESSION["pais"];
	$profissao = $_SESSION["profissao"];
	
	unset($_SESSION["senha_password"]);
	unset($_SESSION["email"]);
	unset($_SESSION["nome"]);
	unset($_SESSION["endereco"]);
	unset($_SESSION["cidade"]);
	unset($_SESSION["cep"]);
	unset($_SESSION["uf"]);
	unset($_SESSION["telefone"]);
	unset($_SESSION["pais"]);
	unset($_SESSION["profissao"]);
	
	echo "<p class='lead'><span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span> " .
	_("Aviso importante: voc&ecirc; &eacute; respons&aacute;vel por todo e qualquer dado que fornecer ao Academusnet.") . "&nbsp;" .
	_("Seja prudente, pois o uso indevido dos recursos deste sistema acarretar&aacute; nas penas legais cab&iacute;veis.") . "</p>\n";

	echo "<br><p class='lead'><span class='glyphicon glyphicon-bullhorn' aria-hidden='true'></span> ";
	
	if ($usuario == "aluno") {
		echo _("Passo 4:");
	} else {
		echo _("Passo 3:");
	}
	
	echo "&nbsp;" . _("forne&ccedil;a seus dados cadastrais. Campos precedidos de asterisco (*) s&atilde;o obrigat&oacute;rios.") . "</p>\n";
	
	if ($usuario == "aluno") {
		echo "<br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=3&usuario=' . $usuario . '")' . "'><button type='button' class='btn btn btn-default'><< " .
		_("Passo 3") . "</button></a></p>\n";
	} else {
		echo "<br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=2&usuario=' . $usuario . '")' . "'><button type='button' class='btn btn btn-default'><< " .
		_("Passo 2") . "</button></a></p>\n";
	}
	
	echo "<br><div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Cadastro") . "</h3></div>";
	echo "<div class='panel-body'>";

	include "connectdb.php";
	
	$haconsistencia = 0;
		
	if (is_array($ra)) {
	
		foreach ($ra as $endid => $valor) {
	
			$sql = "SELECT nome as instituicao, id as endid FROM enderecos WHERE id = '$endid'";

			$qEnderecos = mysql_query($sql);

			//if (mysql_num_rows($qEnderecos) == 0) {
			//	echo "<p>" . _("Ocorreu um erro inesperado. Clique") . "<a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=3&usuario=' . $usuario . '")' . "'> " .
			//	_("aqui") . "</a> " . _("para voltar") . "</p>\n";
			//} else {
			
			$haconsistencia = 1;

			$i = 0;
			
			while ($aEnderecos = mysql_fetch_array($qEnderecos)) {
			
				if ($i == 0) {
					echo "<p class=lead>" . _("Institui&ccedil;&atilde;o") . ": " . $aEnderecos["instituicao"] . "</p>\n";
					$instituicaoa = $aEnderecos["instituicao"];
				} else {
					if ($aEnderecos["instituicao"] != $instituicaoa) {
						echo "<p class=lead>" . _("Institui&ccedil;&atilde;o") . ": " . $aEnderecos["instituicao"] . "</p>\n";
						$instituicaoa = $aEnderecos["instituicao"];
					}
				}

				$sql = "SELECT DISTINCT a.nome as aluno, c.nome as curso, c.id as curid FROM disalu da INNER JOIN aluno a ON a.id = da.aluid 
				INNER JOIN disciplina d ON d.id = da.disid INNER JOIN curso c ON c.id = d.curid WHERE da.aluid = '$valor' AND d.endid = '" . $aEnderecos["endid"] . "'";
		
				$qDisAlu = mysql_query($sql);
	
				if (mysql_num_rows($qDisAlu) == 0) {
					echo "<p class=lead>" . _("Somente &eacute; poss&iacute;vel cadastrar-se neste sistema caso voc&ecirc; tenha algum v&iacute;nculo") . "&nbsp;" .
					_("com um professor que previamente tenha cadastrado voc&ecirc; em sua disciplina.") . "&nbsp;" .
					_("Seu Registro de Aprendiz (RA) n&atilde;o foi encontrado em nenhum curso desta institui&ccedil;&atilde;o.") . "&nbsp;" .
					_("Verifique os dados informados nos passos anteriores. Caso tudo esteja realmente certo, pe&ccedil;a ajuda ao seu professor ou contacte o") .
					" <a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=8&usuario=' . $usuario . '")' . "'><span class='label label-primary'>" . _("suporte") . "</a></p>\n";
				} else {
					$j = 0;
					while ($aDisAlu = mysql_fetch_array($qDisAlu)) {
						if ($j == 0) {
							echo "<p class=lead>&nbsp;&nbsp;&nbsp;&nbsp;" . $aDisAlu["aluno"] . "</p>\n";
						}
						echo "<p class=lead>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _("Curso") . ": " . $aDisAlu["curso"] . "</p>";
						$curso[$aDisAlu["curid"]] = "on";
						$j++;
					}
				}
				$i++;
			}
		}
		
		$_SESSION["curso"] = $curso;
		
	} else {
	
		$sql = "SELECT DISTINCT e.nome as instituicao, c.id as curid, c.nome as curso FROM curso c INNER JOIN enderecos e ON c.endid = e.id 
		LEFT JOIN usuend ue ON e.id = ue.endid ";
	
		$i = 1;
	
		foreach ($instituicao as $endid => $valor) {
			if ($valor == 'on') {
				if ($i == 1) {
					$sql .= "WHERE ( e.id = '$endid' ";
					$i++;
				} else {
					$sql .= "OR e.id = '$endid' ";
				}
			}
		}

		$hacursos;
		$i = 1;

		foreach ($curso as $curid => $valor) {
			if ($valor == 'on') {
				if ($i == 1) {
					$sql .= ") AND ( c.id = '$curid' ";
					$i++;
				} else {
					$sql .= "OR c.id = '$curid' ";
				}
				$hacursos = 1;
			}
		}

		$sql .= ") ORDER BY 1,3";

		if ($hacursos == 0 and $usuario == "aluno") {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma institui&ccedil;&atilde;o ...") . "</strong></div>" ;
			echo "<br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=2&usuario=' . $usuario . '")' . "'><button type='button' class='btn btn btn-default'><< " .
			_("Passo 2") . "</button></a></p>\n";
		} else {
		
			$qUsuEnd = mysql_query($sql);

			//if (mysql_num_rows($qUsuEnd) == 0) {
			//	echo "<p class=lead>" . _("Ocorreu um erro inexperado. Clique") . "<a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=2&usuario=' . $usuario . '")' . "'> " .
			//	_("aqui") . "</a>" . _("para voltar.") . "</a>\n";
			//} else {
		
			$haconsistencia = 1;
	
			$i = 0;
			
			while ($aUsuEnd = mysql_fetch_array($qUsuEnd)) {
				if ($i == 0) {
					echo "<h1>" . _("Institui&ccedil;&atilde;o") . ": " . $aUsuEnd["instituicao"] . "</h1>\n";
					$instituicaoa = $aUsuEnd["instituicao"];
				} else {
					if ($aUsuEnd["instituicao"] != $instituicaoa) {
						echo "<h1>" . _("Institui&ccedil;&atilde;o") . ": " . $aUsuEnd["instituicao"] . "</h1>\n";
						$instituicaoa = $aUsuEnd["instituicao"];
					}
				}
				if (!empty($aUsuEnd["curid"])) {
					echo "<h2>&nbsp;&nbsp;" . $aUsuEnd["curso"] . "</h2><br>\n";
				}
				$i++;
			}
			//}
		}
	}

	if ($haconsistencia == 1) { 
		echo "<form method='post' action='cadastro.php'>\n";
		echo "<p><label for='usuario'>(*) Usu&aacute;rio (<i>login</i>)</label>\n";
		echo "<input type='text' id='id' name='id' size=40 maxlength=40 value='$id' class='form-control' autofocus required /></p>\n";
		echo "<p><label for='email'>(*) e-mail</label>\n";
		echo "<input type='text' id='email' name='email' size=40 maxlength=40 value='$email' class='form-control' autofocus required /></p>\n";
		echo "<p><label for='email'>(*) Repita o e-mail</label>\n";
		echo "<input type='text' id='rmail' name='remail' size=40 maxlength=40 value='$remail' class='form-control' autofocus required /></p>\n";
		echo "<p><label for='senha'>(*) Senha</label>\n";
		echo "<input type='password' id='senha' name='senha' size=10 maxlength=40 value='$senha' class='form-control' autofocus required /></p>\n";
		echo "<p><label for='rsenha'>(*) Repita a Senha</label>\n";
		echo "<input type='password' id='rsenha' name='rsenha' size=10 maxlength=40 value='$rsenha' class='form-control' autofocus required /></p>\n";
		echo "<p><label for='nome'>(*) Nome</label>\n";
		echo "<input type='text' id='nome' name='nome' size=50 maxlength=60 value='$nome' class='form-control' autofocus required /></p>\n";
		echo "<p><label for='endereco'>Endere&ccedil;o</label>\n";
		echo "<input type='text' id='endereco' name='endereco' size=50 maxlength=60 value='$endereco' class='form-control' /></p>\n";
		echo "<p><label for='telefone'>Telefone</label>\n";
		echo "<input type='text' id='telefone' name='telefone' size=20 maxlength=20 value='$telefone' class='form-control' /></p>\n";
		echo "<p><label for='cidade'>Cidade</label>\n";
		echo "<input type='text' id='cidade' name='cidade' size=40 maxlength=40 value='$cidade' class='form-control' /></p>\n";
		echo "<p><label for='cep'>CEP</label>\n";
		echo "<input type='text' id='cep' name='cep' size=9 maxlength=9 value='$cep' class='form-control' /></p>\n";
		echo "<p><label for='uf'>Estado</label>\n";
		echo "<input type='text' id='uf' name='uf' size=2 maxlength=2 value='$uf' class='form-control' /></p>\n";
		echo "<p><label for='pais'>Pa&iacute;s</label>\n";
		echo "<input type='text' id='pais' name='pais' size=20 maxlength=20 value='$pais' class='form-control' /></p>\n";
		echo "<p><label for='profissao'>Profiss&atilde;o</label>\n";
		echo "<input type='text' id='profissao' name='profissao' size=50 maxlength=60 value='$profissao' class='form-control' /></p>\n";
		echo "<input type=hidden name=passo value='5'>\n";
		echo "<input type='submit' class='btn btn-default' name='enviarusu' value='Enviar'></fieldset>\n";
		echo "</form><br><br>\n";	
	}
	mysql_close($aDBLink);
	
	echo "</div></div>";
}

?>

<!DOCTYPE html>

<?php echo "<html lang=" . substr($language,0,5) . ">"; ?>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Academusnet</title>
<meta name="generator" content="Academusnet" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/bootstrap-theme.min.css" rel="stylesheet">
<link href="jquery-ui/css/humanity/jquery-ui.css" rel="stylesheet">
<link href="jquery-ui/jstree/themes/classic/style.css" rel="stylesheet">
<link href="css/styles.css" rel="stylesheet">
<link href="styles.css" rel="stylesheet">
<link href="jquery-ui/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet">
<link href="shortcut icon" href="favicon.ico" type="image/x-icon">

<style type='text/css'>
body {
	background-color: #eee;
}\n
</style>

<script type="text/JavaScript">
function abrirPag(site, variaveis) {
	var endereco = site;
	var parametro = variaveis;
	var objArquivo = document.getElementById("arquivo");
	var objDados = document.getElementById("dados");
	objArquivo.value = endereco;
	objDados.value = parametro;
	document.forms['academusnet'].submit()
}
</script>

<script src="jquery-ui/js/jquery.js"></script>
<script src="jquery-ui/js/jquery-ui.min.js"></script>
<script src="jquery-ui/jstree/jquery.jstree.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/scripts.js"></script>
<script src="jquery-ui/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>

<?php
if (substr($_SESSION["locale"],0,5) != "en_US") {
	echo "<script src='jquery-ui/bootstrap-datepicker/dist/locales/bootstrap-datepicker." .
	strtr(substr($_SESSION["locale"], 0, 5), "_", "-") . ".min.js' charset='UTF-8'></script>";
}
?>

</head>

<body>

	<header class="navbar navbar-default navbar-static-top" role="banner">
		<div class="container">
			<div class="navbar-header">
				<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
        				<span class="sr-only">Toggle navigation</span>
        				<span class="icon-bar"></span>
        				<span class="icon-bar"></span>
        				<span class="icon-bar"></span>
      			</button>
      			<a href="../index.php" class="navbar-brand"><img src="../images/logo.png" style="margin: -12px 0px"></a>
      			<a href="login.php" class="navbar-brand">Login</a>
    		</div>
  		</div>
	</header>

	<form name='academusnet' action='academusnet.php' method='post'>
		<input type="hidden" id="arquivo" name="arquivo" value="">
		<input type="hidden" id="dados" name="dados" value="">
	</form>

	<div class="container">
		<div class="row">
			<div class='col-md-12' id='leftCol'>

<?php

if (empty($passo)) {

	if (empty($passo)) {
		unset($_SESSION["instituicao"]);
		unset($_SESSION["usuario"]);
	}

	echo "<h1>" . _("Registrar-se como") . ":</h1><br>";
	
	echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Perfil") . "</h3></div>";
	echo "<div class='panel-body'>";

	echo "<div class='row'>";
	echo "<div class='col-md-6' align=center><br><h1>" . _("Professor") . "</h1><br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=1&usuario=professor"' . ")'>
	<span style='font-size:180pt;' class='glyphicon glyphicon-apple'></a></span></div>";
	echo "<div class='col-md-6' align=center><br><h1>" . _("Aluno") . "</h1><br><a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=1&usuario=aluno"' . ")'>
	<span style='font-size:180pt;' class='glyphicon glyphicon-education'></span></div></div>";
	
	echo "<br><br></div></div>";
	
} elseif ($passo == 1) {
	if (!empty($usuario)) {
		$_SESSION["usuario"] = $usuario;
	}
	passo1();
} elseif ($passo == 2) {
	if (!empty($instituicao)) {
		$_SESSION["instituicao"] = $instituicao;
	}
	if ($_SESSION["usuario"] == "aluno") {
		passo2_aluno();
	} else {
		passo2_professor();
	}
} elseif ($passo == 3) {
	if (!empty($ra)) {
		$_SESSION["ra"] = $ra;
	}
	passo3_aluno();
} elseif ($passo == 4) {
	if (isset($curso)) {
		$_SESSION["curso"] = $curso;
	}
	passo4();
} elseif ($passo == 5) {
	include "connectdb.php";
	$sql = "SELECT id FROM usuario WHERE id = '$id'";
	$qUsuario = mysql_query($sql, $aDBLink) or die(mysql_error());
	if (mysql_num_rows($qUsuario) > 0) {
		$_SESSION["id"] = trim($id);
		$_SESSION["senha_password"] = trim($senha);
		$_SESSION["email"] = $email;
		$_SESSION["nome"] = $nome;
		$_SESSION["endereco"] = $endereco;
		$_SESSION["cidade"] = $cidade;
		$_SESSION["cep"] = $cep;
		$_SESSION["uf"] = $uf;
		$_SESSION["telefone"] = $telefone;
		$_SESSION["pais"] = $pais;
		$_SESSION["profissao"] = $profissao;
		echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Usu&aacute;rio informado j&aacute; existe") . "... </strong></div><br><br>
		<a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=4&usuario=' . $usuario . '")' . "'><button type='button' class='btn btn btn-default'>
		<< " . _("Passo 4") . "</button></a><br><br>";
	} else {
		if ($email != $remail or $senha != $rsenha) {
			$_SESSION["id"] = trim($id);
			$_SESSION["senha_password"] = trim($senha);
			$_SESSION["email"] = $email;
			$_SESSION["nome"] = $nome;
			$_SESSION["endereco"] = $endereco;
			$_SESSION["cidade"] = $cidade;
			$_SESSION["cep"] = $cep;
			$_SESSION["uf"] = $uf;
			$_SESSION["telefone"] = $telefone;
			$_SESSION["pais"] = $pais;
			$_SESSION["profissao"] = $profissao;
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Confirma&ccedil;&atilde;o de senha ou email n&atilde;o est&aacute; correta...") . "</strong></div><br><br>
			<a href='#' onClick='abrirPag(" . '"cadastro.php", "passo=4&usuario=' . $usuario . '")' . "'><button type='button' class='btn btn btn-default'>
			<< " . _("Passo 4") . "</button></a><br><br>";
		} else {
			$instituicao = $_SESSION["instituicao"];
			$curso = $_SESSION["curso"];
			$ra = $_SESSION["ra"];
			if ($_SESSION["usuario"] == "aluno") {
				$aluno = 1;
				$professor = 0;
			} else {
				$aluno = 0;
				$professor =1;
			}
			$datac = date("Y-m-d");
			$sql = "INSERT INTO usuario (id, email, nome, endereco, cidade, cep, uf, telefone, pais, profissao, senha, datac, aluno, professor)
			VALUES ('$id', '$email', '$nome', '$endereco', '$cidade', '$cep', '$uf', '$telefone', '$pais', '$profissao', encode('$senha', 'mypas'), 
			'$datac', '$aluno', '$professor')";
	
			mysql_query($sql, $aDBLink) or die(mysql_error());
	
			if ($aluno == 1) {		
				foreach ($ra as $endid => $valor) {
					if (!empty($valor)) {
						$sql = "INSERT INTO usuend VALUES ('$id', '$endid', '$valor', 1)";
						mysql_query($sql, $aDBLink) or die(mysql_error());
					}
				}
			} else {
				foreach ($instituicao as $endid => $valor) {
					if ($valor == 'on') {
						$sql = "INSERT INTO usuend VALUES ('$id', '$endid', '', 1)";
						mysql_query($sql, $aDBLink) or die(mysql_error());
					}
				}
			}
			foreach ($curso as $curid => $valor) {
				if ($valor == 'on') {
					$sql = "INSERT INTO usucur VALUES ('$id', '$curid')";
					mysql_query($sql, $aDBLink) or die(mysql_error());
				}
			}
	
			$str_mensagem = _("Bem vindo ao Academusnet") . " (http://www.academusnet.pro.br)\n\n" . _("Data do Cadastro") . ": $datac\n\n" . _("Usuario") . ": $id\n" . _("Senha") . 
			":$senha\n" . _("RA") . ":$ra\nemail: $email\n" . _("Nome") . ": $nome\n" . _("Endereco") . ": $endereco\n" . _("Cidade") . ": $cidade\n" . _("CEP") . ": $cep\n" . 
			_("Estado") . ": $uf\n" . _("Telefone") . ": $telefone\n" . _("Pais") . ": $pais\n" . _("Profissao") . ": $profissao\n\n" . 
			_("Qualquer duvida na utilizacao do sistema envie um email para") . " suporte@academusnet.pro.br.\n\n" . _("Bom trabalho") . "!!\n\n";
	    
			mail($email,"[Academusnet] " . _("Dados de Cadastramento"),$str_mensagem,"Content-type:text;\nFrom:suporte@academusnet.pro.br\n","-rsuporte@academusnet.pro.br");
			
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Cadastramento realizado com sucesso") . "...</strong></div>";
			
			echo "<br><div class='panel panel-default'>\n<div class='panel-heading'>";
			echo "<h3 class='panel-title'>" . _("Cadastro") . "</h3></div>";
			echo "<div class='panel-body'>";
			
			echo "<br><p class='lead'>" . _("Um email foi enviado para a sua caixa postal com os seus dados cadastrais e senha. Guarde-o com cuidado para evitar esquecimento.") . "&nbsp;";
			echo _("A partir de agora voc&ecirc; j&aacute; pode acessar o Academusnet. Efetue seu") . " <a href='login.php'><span class='label label-primary'>" . 
			_("login") . "</a></span>.</p></div></div>";
		}
	}
	mysql_close($aDBLink);
	
} elseif ($passo == 6 or $passo == 8) {

	if ($passo == 6) {
		echo "<br><p class='lead'>" . _("Preencha o formul&aacute;rio abaixo para solicitar o cadastramento de uma nova institui&ccedil;&atilde;o ou curso.") . "&nbsp;";
		echo _("Forne&ccedil;a todos os dados necess&aacute;rios tais como nome, endere&ccedil;o completo e p&aacute;gina de Internet") . ", "; 
		echo _("al&eacute;m do nome dos cursos. Todos os campos que precedem de asterisco (*) s&atilde;o obrigat&oacute;rios. Dentro de muito em breve") . ", ";
		echo _("entraremos em contato.") . "</p><br>\n";
	} else {
		echo "<br><p class='lead'>" . _("Preencha o formul&aacute;rio abaixo para solicitar ajuda. Forne&ccedil;a todos os dados necess&aacute;rios.") . "&nbsp;"; 
		echo _("Todos os campos que precedem de asterisco (*) s&atilde;o obrigat&oacute;rios. Dentro de muito em breve, entraremos em contato.") . "</p><br>\n";
	}

	echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Ajuda") . "</h3></div>";
	echo "<div class='panel-body'>";
	
	echo "<form method='post' action='cadastro.php'>\n";
	
	if ($passo == 6) {
		echo "<input type='hidden' name='passo' value='7'>";
	} else {
		echo "<input type='hidden' name='passo' value='9'>";
	}
	
	echo "<p><label for='nome'>(*) " . _("Nome completo") . "</label>\n";
	echo "<input type='nome' id='nome' name='nome' size=40 maxlength=40 class='form-control' /></p>\n";
	echo "<p><label for='email'>(*) E-Mail</label>\n";
	echo "<input type='nome' id='email' name='email' size=40 maxlength=40 class='form-control' /></p>\n";
	echo "<p><label for='remail'>(*) " . _("Repita o e-mail") . "</label>\n";
	echo "<input type='nome' id='remail' name='remail' size=40 maxlength=40 class='form-control' /></p>\n";
	
	if ($passo == 6) {
		echo "<p><label for='telefone'>" . _("Telefone para contato") . "</label>\n";
		echo "<input type='telefone' id='telefone' name='telefone' size=20 maxlength=20 class='form-control' /></p>\n";
		echo "<p><label for='vinculo'>(*) " . _("V&iacute;nculo com a institui&ccedil;&atilde;o") . "</label>\n";
		echo "<input type='vinculo' id='vinculo' name='vinculo' size=40 maxlength=40 class='form-control' /></p>\n";
		echo "<p><label for='dados'>(*) " . _("Dados da institui&ccedil;&atilde;o") . "</label><br>\n";
		echo "<textarea name='dados' rows='10' id='dados' class='form-control' /></textarea></p>\n";
	} else {
		echo "<p><label for='dados'>(*) " . _("D&uacute;vidas") . "</label><br>\n";
		echo "<textarea name='dados' rows='10' id='dados' class='form-control' /></textarea></p>\n";
	}
	echo "<p><input type='submit' class='btn btn-default' name='enviarusu' value='" . _("Enviar") . "'></p></form>";
	
	echo "</div></div>";
		
} elseif ($passo == 7 or $passo == 9) {
		
		if ($passo == 7) {
			$str_mensagem = _("Solicitacao de Cadastramento de Instituicao") . "\n\n" . _("Nome") . ": $nome\nemail: $email\n" . 
			_("Telefone") . ": $telefone\n" . _("Vinculo") . ": $vinculo\n\n" . _("Dados") . ":\n\n$dados\n\n";
		} else {
			$str_mensagem = _("Solicitacao de Ajuda") . "\n\n" . _("Nome") . ": $nome\nemail: $email\n\n" . _("Dados") . ":\n\n$dados\n\n";
		}
		
		mail($email,"[Academusnet] " . _("Solicitacao de Cadastramento de Instituicao"),$str_mensagem,"Content-type:text;\nFrom: suporte@academusnet.pro.br\n");
		
		echo "<div class='alert alert-success' role='alert'><strong>" . _("Solicita&ccedil;&atilde;o enviada com sucesso") . "...</strong></div>";
		
		echo "<br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Cadastro") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo "<p class='lead'>" . _("Aguarde, dentro de muito em breve faremos contato. Obrigado pelo seu interesse nos servi&ccedil;os do Academusnet. Retorne ao") .  
		" <a href='login.php'><span class='label label-primary'>" . _("in&iacute;co") . "</a></span></p></div></div>";
}

echo "<br><br><br>";

include 'rodape.inc';

?>
