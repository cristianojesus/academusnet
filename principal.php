<?php
session_start();

include( "buscasessao.php" );

unset($_SESSION["disid"]);
unset($_SESSION["endid"]);

function AutenticaUsuario($id, $senha) {
	$sql = "SELECT id, nome, professor as tipo FROM usuario WHERE id = '$id' AND decode(senha,'mypas') = '$senha'";
	include "connectdb.php";
	$qUsuario = mysql_query($sql) or die(mysql_error());
	if (mysql_num_rows($qUsuario) > 0) {
		mysql_close($dblink);
		return $qUsuario;
	} else {
		echo "<div class='alert alert-danger' role='alert'>
        <strong>" . _("Usu&aacute;rio inexistente ou senha incorreta.") . "<a href='login.php'>Clique aqui para voltar</a>...</strong></div>";
		mysql_close($dblink);
		exit;
	}
}

function ListaDados($id, $tipo, $tipov) {

	include "connectdb.php";

	echo "<div class='panel panel-default'>\n
 		  <div class='panel-heading'>\n
		  <h3 class='panel-title'>" . _("Ambientes de aprendizagem") . "</h3></div>\n
		  <div class='panel-body'>\n";

	print ("<div id='academusnet' class='academusnet'><ul>\n");

	if ($tipo == 1) {

		if ($id == 'admin') {
			$sql = "SELECT * from disciplina WHERE endid IS NULL";
		} else {
			$sql = "SELECT * from disciplina WHERE endid IS NULL AND usuid = '$id'";
		}
		$result = mysql_query($sql) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			$hainstituicao = 1;
				
			print("<li id='phtml_1'>\n");

			print("<span class='lead'><strong>" . _("Disciplinas sem institui&ccedil;&atilde;o vinculada") . "</strong></span><br>\n");

			if ($id == 'admin') {
				$sql = "SELECT id, nome, usuid, datai, dataf FROM disciplina WHERE endid IS NULL ORDER BY 2";
			} else {
				$sql = "SELECT id, nome, usuid, datai, dataf FROM disciplina WHERE usuid = '$id' AND endid IS NULL ORDER BY 2";
			}
			$query = mysql_query($sql, $dblink) or die(mysql_error());
				
			print("<ul>");
				
			while ($linha = mysql_fetch_array($query)) {
				print("<li id='phtml_2'>\n");
				print("<a href='#' onClick='abrirPag(" . '"detplanprof.php", "&disid=' .
				$linha["id"] . '"' . ")'><button type='button' class='btn btn btn-link'>" . trim($linha["nome"]) . "</button></a>\n");
				//wordwrap(trim($linha["nome"]),50, "<br />\n")
				print("</li>\n");
			}
				
			print("</ul></li>\n");
				
		}

	}

	if ($id == 'admin') {
		$sql = "SELECT id as endid, nome FROM enderecos ORDER BY 2";
	} else {
		$sql = "SELECT ue.endid, e.nome, ue.ra, ue.ativo FROM usuend ue INNER JOIN enderecos e ON e.id = ue.endid INNER JOIN usuario u ON u.id = ue.usuid
		WHERE ue.usuid = '$id' ORDER BY 2";
	}
	
	$qUsuEnd = mysql_query($sql) or die(mysql_error());

	while ($aUsuEnd = mysql_fetch_array($qUsuEnd)) {
		
		$hainstituicao = 1;

		print("<li id='phtml_1'>\n");

		echo "<span class='lead'><strong>" . trim($aUsuEnd["nome"]) . "</span></strong>\n";

		//wordwrap(trim($aUsuEnd["nome"]),60,"<br>\n")
		
		//echo "<a href='#' onClick='abrirPag(" . '"endcur.php", "pAction=SELECT&endid=' . $aUsuEnd["endid"] . '")' . "'>
		//<button type='button' class='btn btn btn-link'>" . _("Cursos") . "</button></a>\n";

		if ($tipo == 0) {
			echo "<a href='#' onClick='abrirPag(" . '"alterara.php", "endid=' . $aUsuEnd["endid"] . '&usuid=' . $id . '")' . "'>
			<button type='button' class='btn btn btn-link'>" . _("Alterar registro") . "</button></a>\n";
		}

		print ("<ul>\n");

		if ($tipo == 1) {

			if ($id == 'admin') {
				$sql = "SELECT * from disciplina WHERE curid IS NULL AND endid = '" . $aUsuEnd["endid"] . "'";
			} else {
				$sql = "SELECT * from disciplina WHERE curid IS NULL AND usuid = '$id' AND endid = '" . $aUsuEnd["endid"] . "'";
			}
			$result = mysql_query($sql) or die(mysql_error());

			if (mysql_num_rows($result) > 0) {

				print ("<li id='phtml_2'>\n");

				echo "<span class='lead'><strong>" . _("Disciplinas sem curso vinculado") . "</strong></span>\n";

				if ($id == 'admin') {
					$sql = "SELECT id, nome, usuid, datai, dataf FROM disciplina WHERE endid = '" . $aUsuEnd["endid"] . "' AND curid IS NULL ORDER BY 2";
				} else {
					$sql = "SELECT id, nome, usuid, datai, dataf FROM disciplina WHERE usuid = '$id' AND endid = '" . $aUsuEnd["endid"] . "' AND curid IS NULL ORDER BY 2";
				}
				$query = mysql_query($sql, $dblink) or die(mysql_error());

				print ("<ul>\n");

				while ($linha = mysql_fetch_array($query)) {
					print ("<li id='phtml_3'>\n");
					print("<a href='#' onClick='abrirPag(" . '"detplanprof.php", "&disid=' .
					$linha["id"] . '"' . ")'><button type='button' class='btn btn btn-link'>" . trim($linha["nome"]) . "</button></a>\n");
					print ("</li>\n");
				}

				print ("</ul></li>\n");

			}
			
		}

		if ($id == 'admin') {
			$sql = "SELECT id as curid, nome FROM curso WHERE endid = '" . $aUsuEnd["endid"] . "' ORDER BY 2";
		} else {
			$sql = "SELECT uc.curid, c.nome FROM usucur uc INNER JOIN curso c ON c.id = uc.curid WHERE c.endid = '" . $aUsuEnd["endid"] . "' AND uc.usuid = '$id' ORDER BY 2";
		}
		
		$qUsuCur = mysql_query($sql) or die(mysql_error());
			
		while ($aUsuCur = mysql_fetch_array($qUsuCur)) {
				
			print ("<li id='phtml_2'>\n");

			print("<span class='lead'>" . trim($aUsuCur["nome"]) . "</span>&nbsp;&nbsp;\n");
				
			if ($tipo == 1 or $tipov == 1) {

				//echo "<a href='#' onClick='abrirPag(" . '"disciplina.php", "pAction=SELECT&endid=' . $aUsuEnd["endid"] . "&curid=" .
				//$aUsuCur["curid"] . '"' . ")'><button type='button' class='btn btn btn-link'>" . _("Disciplinas") . "</button></a><br>\n";
				
				if ($id == 'admin') {
					$sql = "SELECT id, nome, usuid as du, datai, dataf FROM disciplina WHERE endid = " . $aUsuEnd["endid"] . " AND " . 
					"curid = " . $aUsuCur["curid"] . " ORDER BY 2";
				} else {
					$sql = "SELECT DISTINCT id, nome, d.usuid du, d.datai, d.dataf FROM disciplina d LEFT JOIN disusu du ON d.id = du.disid
					WHERE d.usuid = '$id' AND endid = " . $aUsuEnd["endid"] . " AND " . "curid = " . $aUsuCur["curid"] . " ORDER BY 2";
				}
					
			} else {

				$sql = "SELECT d.id, d.nome, d.usuid, d.datai, d.dataf FROM disalu da INNER JOIN disciplina d ON d.id = da.disid
				WHERE da.aluid = '" . $aUsuEnd["ra"] . "' AND d.endid = '" . $aUsuEnd["endid"] . "' AND d.curid = '" . $aUsuCur["curid"] . "' ORDER BY 2";

			}

			$qDisAlu = mysql_query($sql, $dblink) or die(mysql_error());
				
			print("<ul>\n");
				
			while ($aDisAlu = mysql_fetch_array($qDisAlu)) {

				print ("<li id='phtml_3'>\n");

				if (empty($aDisAlu["datai"]) or $aDisAlu["datai"] == '0000-00-00' or $tipo == 1) {
					$acesso = 1;
				}

				if ( date("Y-m-d") >= $aDisAlu["datai"] and date("Y-m-d") <= $aDisAlu["dataf"]) {
					$acesso = 1;
				}

				if (!$aUsuEnd["ativo"] and $id != "admin") {
					$acesso = 0;
				}

				if ($acesso) {
					print("<a href='#' onClick='abrirPag(" . '"detplanprof.php", "endid=' . $aUsuEnd["endid"] . "&disid=" .
					$aDisAlu["id"] . '"' . ")'><button type='button' class='btn btn btn-link'>" . trim($aDisAlu["nome"]) . "</button></a>&nbsp;&nbsp;\n");
				}

				print("</li>\n");
			}
				
			if ($tipo == 1 and $id != 'admin') {
				
				$sql = "SELECT DISTINCT id, nome, d.usuid du, d.datai, d.dataf FROM disciplina d LEFT JOIN disusu du ON d.id = du.disid
				WHERE du.usuid = '$id' AND endid = " . $aUsuEnd["endid"] . " AND " . "curid = " . $aUsuCur["curid"] . " ORDER BY 2";
					
				$qDisAlu = mysql_query($sql, $dblink) or die(mysql_error());
					
				if (mysql_num_rows($qDisAlu) > 0) {
					print ("<li id='phtml_3'>\n");
					echo "<strong>" . _("Ambientes compartilhados") . "</strong><br>";
					print("<ul>\n");
				}
					
				while ($aDisAlu = mysql_fetch_array($qDisAlu)) {
					print ("<li id='phtml_3'>\n");
					print("<a href='#' onClick='abrirPag(" . '"detplanprof.php", "endid=' . $aUsuEnd["endid"] . "&disid=" .
					$aDisAlu["id"] . '"' . ")'><button type='button' class='btn btn btn-link'>" . trim($aDisAlu["nome"]) . "</button></a>&nbsp;&nbsp;\n");
					print("</li>\n");
				}

				if (mysql_num_rows($qDisAlu) > 0) {
					print("</ul></li>\n");
				}
				
				$sql = "SELECT DISTINCT id, nome, d.usuid du, d.datai, d.dataf FROM disciplina d INNER JOIN curadmin cu ON d.curid = cu.curid
						WHERE cu.usuid = '$id' AND d.usuid != '$id' AND d.endid = " . $aUsuEnd["endid"] . " AND " . "d.curid = " . $aUsuCur["curid"] . " ORDER BY 2";
					
				$qDisAlu = mysql_query($sql, $dblink) or die(mysql_error());
					
				if (mysql_num_rows($qDisAlu) > 0) {
					print ("<li id='phtml_3'>\n");
					echo "<strong>" . _("Ambientes sob sua administra&ccedil;&atilde;o") . "</strong><br>";
					print("<ul>\n");
				}
					
				while ($aDisAlu = mysql_fetch_array($qDisAlu)) {
					print ("<li id='phtml_3'>\n");
					print("<a href='#' onClick='abrirPag(" . '"detplanprof.php", "endid=' . $aUsuEnd["endid"] . "&disid=" .
					$aDisAlu["id"] . '"' . ")'><button type='button' class='btn btn btn-link'>" . trim($aDisAlu["nome"]) . "</button></a>&nbsp;&nbsp;\n");
					print("</li>\n");
				}
				
				if (mysql_num_rows($qDisAlu) > 0) {
					print("</ul></li>\n");
				}

			}
				
			print("</ul></li>\n");
				
		}


		print("</ul></li>\n");

	}
	
	echo "</ul></div>";
	
	if (!$hainstituicao) {
		echo "<br><p class='lead'>" . _("Sem institui&ccedil;&otilde;es vinculadas ...") . "</p>";
	}

	print ("</div></div>\n");

	mysql_close($dblink);

	return;
}

function contato($operacao) {

	echo "<button type='button' class='btn btn btn-default'><a href='principal.php'>Voltar</a></button></p>";

	if ($operacao == 1) {
		echo "<p class='lead'>" . _("Preencha o formul&aacute;rio abaixo para solicitar o cadastramento de uma nova institui&ccedil;&atilde;o ou curso.") . 
		_("&Eacute; gratu&iacute;to.") . _("Forne&ccedil;a todos os dados necess&aacute;rios tais como nome, endere&ccedil;o completo e p&aacute;gina de Internet,") .
		_("al&eacute;m do nome dos cursos. Todos os campos que precedem de asterisco (*) s&atilde;o obrigat&oacute;rios.") .
		_("Dentro de muito em breve, entraremos em contato.") . "</p>\n";
	} else {
		echo "<br><p class='lead'>" . _("Preencha o formul&aacute;rio abaixo para solicitar ajuda.") . 
		_("Forne&ccedil;a todos os dados necess&aacute;rios.") .
		_("Todos os campos que precedem de asterisco (*) s&atilde;o obrigat&oacute;rios.") .
		_("Dentro de muito em breve, entraremos em contato.") . "</p>\n";
	}

	echo "<form name='solicitacao' method='post' action='principal.php'>\n";
	echo "<label for='nome'>(*) " . _("Nome completo") . "</label>\n";
	echo "<input type='text' name='nome' maxlength=40 class='form-control' required autofocus>\n";
	echo "<label for='email'>(*) Email</label>\n";
	echo "<input type='email' id='email' name='email' maxlength=40 class='form-control' required autofocus>\n";
	echo "<label for='remail'>(*)" . _("Repita o email") . "</label>\n";
	echo "<input type='email' name='remail' maxlength=40 class='form-control' oninput='check(this)' required autofocus>\n";

	if ($operacao == 1) {
		echo "<label for='telefone'>" . _("Telefone para contato") . "</label>\n";
		echo "<input type='tel' name='telefone' maxlength=20 class='form-control' autofocus>\n";
		echo "<label for='vinculo'>(*) " . _("V&iacute;nculo com a institui&ccedil;&atilde;o") . "</label>\n";
		echo "<input type='text' name='vinculo' maxlength=40 class='form-control' required autofocus>\n";
		echo "<label for='dados'>(*) " . _("Dados da institui&ccedil;&atilde;o") . "</label>\n";
		echo "<textarea name='dados' rows='10' class='form-control' required autofocus/></textarea></p>\n";
		echo "<input type='hidden' name='operacao' value='3'>\n";
	} else {
		echo "<p><label for='dados'>(*) " . _("D&uacute;vidas") . "</label><br>\n";
		echo "<textarea name='dados' rows='10' class='form-control' /></textarea></p>\n";
		echo "<input type='hidden' name='operacao' value='4'>\n";
	}
	echo "<br><button class='btn btn-lg btn-primary' type='submit'>" . _("Enviar") . "</bsutton></form>\n";

}

function ExcluiDados($disid) {
	include "connectdb.php";
	$SQL = "DELETE FROM disciplina WHERE id = '$disid'" ;
	$QResult = mysql_query( $SQL, $dblink ) or die(mysql_error());
	mysql_close($dblink);
}

$data = date("Y-m-d H:i:s");
$sessao = session_id();

if (!empty($_POST["id"])) {
	$id = $_POST["id"];
	$senha = $_POST["senha"];
	$qUsuario = AutenticaUsuario($id, $senha);
	if ($qUsuario) {
		$_SESSION["logok_academusnet"] = 1;
		include "connectdb.php";
		$aUsuario = mysql_fetch_array($qUsuario);
		$_SESSION["nome_usuario"] = $aUsuario["nome"];
		$_SESSION["professor"] = $aUsuario["tipo"];
		$tipo = $aUsuario["tipo"];
		if ($tipo == 2) {
			$tipo = 1;
		}
		$id = $aUsuario["id"];
		$qSessao = mysql_query("SELECT * FROM sessao WHERE sessao = '$sessao'") or die(mysql_error());
		if (mysql_num_rows($qSessao) == 0) {
			$qSessao = mysql_query("INSERT INTO sessao VALUES('$sessao', '$id', '', '$data', '$tipo')" ) or die(mysql_error());
		}
		$result = mysql_query("UPDATE acesso SET timef = now() WHERE usuid = '$id' AND sessao = '" . session_id() . "'") or die(mysql_error());
		mysql_close($dblink);
	}
} else {
	$aSessao = BuscaSessao(0);
	if (is_array($aSessao)) {
		$id = $aSessao["usuid"];
		if (!isset($_SESSION["tipo"])) {
			$tipo = $aSessao["professor"];
			$_SESSION["tipo"] = $aSessao["professor"];
		} else {
			$tipo = $_SESSION["tipo"];
		}
		if ($_SESSION["tipo"] == 2) {
			$_SESSION["tipo"] = 1;
		}
		if ($tipo == 2) {
			$tipo = 1;
		}
	} else {
		echo "<p><br><br>" . _("Devido ao longo tempo de inatividade ou por algum erro durante o acesso ao sistema,") .
		_("a sess&atilde;o foi cancelada por motivos de seguran&ccedil;a.") . " <a href='login.php'>" . _("Clique aqui para sair") . "</a>.</p><br>\n";
		exit;
	}
	include "connectdb.php";
	$qUsuario = mysql_query( "SELECT nome FROM usuario WHERE id = '$id'" ) or die(mysql_error());
	$aUsuario = mysql_fetch_array($qUsuario);
	mysql_close($dblink);
}

if (isset($_GET["tipo"])) {
	$_SESSION["tipov"] = $tipo;
	$tipo = $_GET["tipo"];
	$_SESSION["tipo"] = $tipo;
}

if ($pAction == "DELETE") {
	ExcluiDados($disid);
}

include( "cabecalho.php" );

include "menup.inc";

?>

<script type="text/javascript" charset="utf-8">

		$(function () {
			$("#academusnet")
				.jstree({
			        "themes" : {
			            "theme" : "classic",
						"dots" : false,
						"icons" : false
					},										
					"plugins" : ["themes","html_data"],
					"core" : { "initially_open" : [ "phtml_2" ] }					
				})
				.bind("loaded.jstree", function (event, data) {});
				<?php if ($tipo == 1) echo 'setTimeout(function () { $.jstree._reference("#phtml_1").close_node("#phtml_2"); }, 300);'; ?>
		});

		function check(input) {
		  if (input.value != document.getElementById('email').value) {
		    input.setCustomValidity('Os endere\u00e7os de email devem ser iguais');
		  } else {
		    input.setCustomValidity('');
		  }
		}

	</script>


	<?php

	if (!empty($operacao)) {

		if ($operacao == 1 or $operacao == 2) {
			contato($operacao);
		} else {
			if ($operacao == 3) {
				$str_mensagem = "Solicitacao de Cadastramento de Instituicao e/ou Cursos\n\n" . sprintf(_("Nome: %s"), $nome) .
				"\n" . sprintf(_("email: %s"), $email) . "\n" . sprintf(_("Telefone: %s"), $telefone) . "\n" . sprintf(_("Vinculo: %s"), $vinculo) . "\n\n" . 
				sprintf(_("Dados:%s")) . "\n\n$dados\n\n";
				$assunto = "[Academusnet] " . sprintf(_("Solicitacao de cadastramento de instituicao e/ou cursos"));
			} else {
				$str_mensagem = "Solicitacao de Ajuda\n\n" . sprintf(_("Nome: %s", $nome) . "\n" . sprintf(_("email: %s"), $email) . "\n\n" . 
				sprintf(_("Dados:%s")) . "\n\n$dados\n\n");
				$assunto = "[Academusnet] " . sprintf(_("Solicitacao de ajuda"));
			}

			mail($email,$assunto,$str_mensagem,"Content-type:text;\nFrom: suporte@academusnet.pro.br\n");
			echo "<div class='alert alert-success' role='alert'>" . _("Solicita&ccedil;&atilde;o enviada com sucesso ...") . "</div>\n";
			echo "<p class='lead'>" . _("Aguarde, dentro de muito em breve faremos contato. Obrigado pelo seu interesse nos servi&ccedil;os do Academusnet.") . "</p>\n";
			echo "<button type='button' class='btn btn btn-default'><a href='principal.php'>" . _("Voltar") . "</a></button></p>";

		}

	} else {

		if ($qUsuario) {

			echo "<div class='jumbotron'>\n";

			if ( $tipo == 1) {
				echo "<p><h2>" . _("Professor(a): ") . $aUsuario["nome"] . "</a></h2></p>\n";
				//echo "<p><ul><li>" . _("Para solicitar o cadastramento de novas institui&ccedil&otilde;es e cursos") . ",
				//<a href='#' onClick='abrirPag(" . '"principal.php", "operacao=1"' . ")'>" . _("clique aqui") . "</a>.</li>\n";
				// echo "<li>" . _("Para cadastrar ou importar novos estudantes, clique em [Estudantes]") . ".</li>\n";
				// echo "<li>" . _("Para criar novas disciplinas, clique em [Disciplinas]") . ".</li>\n";
				echo "<p><ul><li>" . _("Para criar disciplinas com base em alguma j&aacute; existente, acesse o ambiente da disciplina e clique em [Copiar]") . "</li>\n";
				echo "<li>" . _("Qualquer d&uacute;vida, entre em contato com o suporte ") . "<a href='#'
				onClick='abrirPag(" . '"principal.php", "operacao=2"' . ")'>" . _("clicando aqui") . ".</a></li></p>\n";
			} else {
				echo "<p><h2>" . $aUsuario["nome"] . "</a></h2></p>\n";
				echo "<p><a href='#' onClick='abrirPag(" . '"perfil.php", "usuid=' . $id . '")' . "'>" . _("Perfil") . "</a>\n";
				echo "&nbsp;<a href='#' onClick='abrirPag(" . '"endereco.php", "pAction=SELECT"' . ")'>" . 
				_("Institui&ccedil;&otilde;es") . "</a>\n";
				echo "&nbsp;<a href='matricula.php'>" . _("Matricular-se") . "</a></p>\n";
				echo "<p>" . _("Somente ser&atilde;o listados aqui as disciplinas em que voc&ecirc; foi incluido como estudante pelos professores") . "&nbsp;" .
				_("ou cursos livres os quais seu pedido de matr&iacute;cula foi aceito.") . "</p>\n";			
			}

			echo "</div>";

			ListaDados($id, $tipo, $_SESSION["tipov"]);

			echo '<br style="clear:both" />';
			echo '<br style="clear:both" />';

		}

	}

	include 'rodape.inc';

	?>
