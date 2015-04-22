<?php
	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
?>

<?php

	include( "cabecalho.php" );
	
	echo "<div class='col-md-12'>";
	
	include 'dadosdis.inc';
	
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-star-empty' aria-hidden='true'></span>&nbsp;" . _("Matr&iacute;cula") . "</h3></div>";
	
	if (empty($pAction)) {
		unset($disid);
	}
	
	include 'connectdb.php';
	
	if ($pAction == "ENROLLED") {
		
		if ($email != $remail or $senha != $rsenha) {
			
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Confirma&ccedil;&atilde;o de senha ou email n&atilde;o est&aacute; correta...") . "</strong></div>";
			$pAction = "ENROLL";
			
		} else {

			if (empty($id) or $id='visitante') {
			
				$sql = "INSERT INTO usuario (id, email, nome, endereco, cidade, cep, uf, telefone, pais, profissao, senha, datac, aluno, professor)
				VALUES ('$usuid', '$email', '$nome', '$endereco', '$cidade', '$cep', '$uf', '$telefone', '$pais', '$profissao', encode('$senha', 'mypas'), now(), '1', '0')";
				$result = mysql_query($sql, $dblink);	
				
			} else {
				
				$sql = "SELECT nome FROM usuario WHERE id = '$id'";
				$result = mysql_query($sql, $dblink);
				$linha = mysql_fetch_array($result);
				$nome = $linha["nome"];
				
				$sql = "SELECT ra FROM usuend WHERE usuid = '$id' AND endid = '$endid'";
				$result = mysql_query($sql, $dblink);
				if (mysql_num_rows($result) > 0) {
					$linha = mysql_fetch_array($result);
					$ra = $linha["ra"];
				} else {
					$ra = mt_rand(10101010,90909090);
				}
	
			}
	
			if ($result or (!empty($id) and $id != "visitante")) {
				
				$sql = "INSERT INTO aluno VALUES ('$ra', '$usuid', '$nome', null, '$endid', '0')";
				mysql_query($sql, $aDBLink);
				$sql = "INSERT INTO disalu VALUES ('$disid', '$ra', '0')";
				mysql_query($sql, $aDBLink);
				
				echo  "<div class='alert alert-success' role='alert'><strong>" . 
				_("Matr&iacute;cula realizada com sucesso. Aguarde confirma&ccedil;&atilde;o do professor.") . "</strong></div>";
	
				if (empty($id)) {
					$str_mensagem = gettext("Bem vindo ao Academusnet") . " (http://www.academusnet.pro.br)\n\n" . gettext("Data do Cadastro") . ": " . date("Y-m-d") . 
					"\n\n" . gettext("Usuario") . ": $usuid\n" . gettext("Senha") . ": $senha\n" . gettext("RA") . ":$ra\nemail: $email\n" . gettext("Nome") . ": $nome\n" . 
					gettext("Endereco") . ": $endereco\n" . gettext("Cidade") . ": $cidade\nCEP: $cep\n" . gettext("Estado") . ": $uf\n" . gettext("Telefone") . ": $telefone\n" . 
					gettext("Pais") . ": $pais\n" . gettext("Profissao") . ": $profissao\n\n" . gettext("Acesse o sistema via o link") . " http://www.academusnet.pro.br/lms/login.php. " .
					gettext ("O curso sera liberado assim que o professor responsavel aprovar a matricula. Qualquer duvida na utilizacao do sistema	envie um email para") .
					" suporte@academusnet.pro.br.\n\n" . gettext("Bom trabalho") . "!!\n\n";
					mail($email,"[Academusnet] Dados de Cadastramento",$str_mensagem,"Content-type:text;\nFrom: suporte@academusnet.pro.br\n");
				}
				
				$str_mensagem = gettext("Caro professor") . ",\n\n" . gettext("uma nova solicitacao de matricula aguarde sua aprovacao. Data do Cadastro") . ": " . date("Y-m-d") . "\n\n" . 
				gettext("Usuario") . ": $usuid\nRA:$ra\nemail: $email\n" . gettext("Nome") . ": $nome\n" . gettext("Profissao") . ": $profissao\n\n" . 
				gettext("Acesse o sistema via o link") . " http://www.academusnet.pro.br/lms/login.php. " . 
				gettext("Qualquer duvida na utilizacao do sistema envie um email para") . " suporte@academusnet.pro.br.\n\n" . gettext("Bom trabalho") . "!!\n\n";
				mail($profmail,"[Academusnet] Solicitacao de matricula",$str_mensagem,"Content-type:text;\nFrom: suporte@academusnet.pro.br\n");
				
				mysql_close($dblink);
				
				include 'rodape.inc';
				
				exit;
				
			} else {
				echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Usu&aacute;rio (login de acesso) j&aacute; existe ...") . "</strong></div>";
				$pAction = "ENROLL";
			}

		}
		
	} 
	
	if ($pAction == "ENROLL") {
		
		if ($id != "visitante") {
			
			echo "<br>Confirma a matr&iacute;cula? <a href='#' onClick='abrirPag(" . '"matricula.php", "pAction=ENROLLED&curid=' . $curid . 
			'&endid=' . $endid . "&disid=" . $disid . '"' . ")'>SIM</a> <a href='principal.php' id='textLink'>N&Atilde;O</a><br>";
			
		} else {

			if (empty($ra)) {
				$i = 0;
				while ($i == 0) {
					$ra = mt_rand(10101010,90909090);
					$sql = "SELECT * FROM usuend WHERE endid = '$endid' AND ra = '$ra'";
					$result = mysql_query($sql, $aDBLink) or die(mysql_error());
					if (mysql_num_rows($result) > 0) {
						$i = 0;
					} else {
						$i = 1;
					}
				}
			}
		
			$id = "";
		
			echo "<form method='post' action='matricula.php'>\n";
			echo "<p><label for='usuario'>(*) " . _("Usu&aacute;rio") . " (<i>login</i>)</label>\n";
			echo "<input type='text' id='id' name='usuid' size=40 maxlength=40 value='$usuid' class='form-control' autofocus required /></p>\n";
			echo "<p><label for='email'>(*) e-mail</label>\n";
			echo "<input type='text' id='email' name='email' size=40 maxlength=40 value='$email' class='form-control' autofocus required /></p>\n";
			echo "<p><label for='email'>(*) " . _("Repita o e-mail") . "</label>\n";
			echo "<input type='text' id='rmail' name='remail' size=40 maxlength=40 value='$remail' class='form-control' autofocus required /></p>\n";
			echo "<p><label for='senha'>(*) " . _("Senha") . "</label>\n";
			echo "<input type='password' id='senha' name='senha' size=10 maxlength=40 value='$senha' class='form-control' autofocus required /></p>\n";
			echo "<p><label for='rsenha'>(*) " . _("Repita a Senha") . "</label>\n";
			echo "<input type='password' id='rsenha' name='rsenha' size=10 maxlength=40 value='$rsenha' class='form-control' autofocus required /></p>\n";
			echo "<p><label for='nome'>(*) " . _("Registro") . "</label>\n";
			echo "<input type='text' id='ra' name='ra' size=30 maxlength=30 value='$ra' class='form-control' autofocus required /></p>\n";
			echo "<p><label for='nome'>(*) " . _("Nome") . "</label>\n";
			echo "<input type='text' id='nome' name='nome' size=60 maxlength=60 value='$nome' class='form-control' autofocus required /></p>\n";
			echo "<p><label for='endereco'>" . _("Endere&ccedil;o") . "</label>\n";
			echo "<input type='text' id='endereco' name='endereco' size=60 maxlength=60 value='$endereco' class='form-control' /></p>\n";
			echo "<p><label for='telefone'>" . _("Telefone") . "</label>\n";
			echo "<input type='text' id='telefone' name='telefone' size=20 maxlength=20 value='$telefone' class='form-control' /></p>\n";
			echo "<p><label for='cidade'>" . _("Cidade") . "</label>\n";
			echo "<input type='text' id='cidade' name='cidade' size=40 maxlength=40 value='$cidade' class='form-control' /></p>\n";
			echo "<p><label for='cep'>" . _("CEP") . "</label>\n";
			echo "<input type='text' id='cep' name='cep' size=9 maxlength=9 value='$cep' class='form-control' /></p>\n";
			echo "<p><label for='uf'>" . _("Estado") . "</label>\n";
			echo "<input type='text' id='uf' name='uf' size=2 maxlength=2 value='$uf' class='form-control' /></p>\n";
			echo "<p><label for='pais'>" . _("Pa&iacute;s") . "</label>\n";
			echo "<input type='text' id='pais' name='pais' size=20 maxlength=20 value='$pais' class='form-control' /></p>\n";
			echo "<p><label for='profissao'>(*) " . _("Profiss&atilde;o") . "</label>\n";
			echo "<input type='text' id='profissao' name='profissao' size=60 maxlength=60 value='$profissao' class='form-control' autofocus required /></p>\n";
			echo "<input type=hidden name='pAction' value='ENROLLED'>\n";
			echo "<input type=hidden name='curid' value='$curid'>\n";
			echo "<input type=hidden name='endid' value='$endid'>\n";
			echo "<input type=hidden name='disid' value='$disid'>\n";
			echo "<input type='submit' class='btn btn-default' name='enviarusu' value='" . _("Enviar") . "'></fieldset>\n";
			echo "</form><br><br>\n";
			
		}
		
	} else {
	
		$sql = "SELECT d.id as disid, d.nome as nomed, u.nome as nomeu, e.nome as nomee, e.id as endid, c.nome as nomec, c.id as curid FROM disciplina d INNER JOIN usuario u 
		ON (u.id = d.usuid) INNER JOIN enderecos e ON (e.id = d.endid) INNER JOIN curso c ON (c.id = d.curid) 
		WHERE d.matricula = '1' AND d.datai <= CURDATE() AND d.dataf >= CURDATE()";
		
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
		
			echo "<table class='table'><thread><tr>\n" ;
			echo "<th>" . _("Curso/Disciplina") . "</th><th class='text-center'>" . _("Investimento") . "</th></tr><tbody>\n";
		
			while ($linha = mysql_fetch_array($result)) {				
				echo "<tr><td><span><a href='#' onClick='abrirPag(" . '"matricula.php", "pAction=ENROLL&curid=' . $linha["curid"] . 
				'&endid=' . $linha["endid"] . "&disid=" . $linha["disid"] . '"' . ")'>" . $linha["nomed"] . "</a></span><br>" . $linha["nomee"] . "<br>" . _("Curso de") . 
				"&nbsp;" . $linha["nomec"] . "<br>" . _("Prof.(a)") . "&nbsp;" . $linha["nomeu"] . "</td><td align='center'>" . _("Gratu&iacute;to") . "</td></tr>";
			}
		
			echo "</tbody></table>";
		
		} else {
			echo "<p class='lead'>N&atilde;o h&aacute; cursos ou disciplinas com matr&iacute;culas abertas no momento ...</p>\n";
		}
		
	}
	
	mysql_close($dblink);
	
	include 'rodape.inc';
	
?>