<?php
	session_start();
	
	include("buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];

	if ($pAction == "DELETEY") {
		ExcluiDados( $id );
	}
	
	function ExcluiDados( $id ) {

		include( "./connectdb.php" );

		$sql = "DELETE FROM usuario WHERE id = '$id'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());

		mysql_close($dblink);

		echo "<div class='alert alert-success' role='alert'><strong>" . _("Conta exclu&iacute;da com sucesso ...") . "</strong></div>";
		echo "<a href='login.php'><button type='button' class='btn btn btn-default'>" . _("Voltar") . "</button></a>\n";
		
		exit;

	}

	function AlteraDados($perid, $senha, $rsenha, $email, $remail, $nome, $endereco, $cidade, $cep, $uf, $telefone, $pais, $profissao, $educacao, 
	$experiencia, $hobby, $link, $nomelink) {

		include( "./connectdb.php" );

		$sql = "UPDATE usuario SET senha = encode('$senha','mypas'), email = '$email', nome = '$nome', endereco = '$endereco', cidade = '$cidade', uf = '$uf', 
		telefone = '$telefone', pais = '$pais', cep = '$cep', profissao = '$profissao', experiencia = '$experiencia', educacao = '$educacao', hobby = '$hobby',
		foto = null WHERE id = '$perid'";
		
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		
		$sql = "DELETE FROM usulinks WHERE usuid = '$perid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		
		$quantidade = count($nomelink);
		
		for ($i=0; $i<$quantidade; $i++) {
			if (!empty($link[$i]) and !empty($nomelink[$i])) {
				$sql = "INSERT INTO usulinks VALUES (null, '$perid', '" . strip_tags($link[$i]) . "', '" . strip_tags($nomelink[$i]) . "')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Dados alterados com sucesso ...") . "</strong></div>" ;
		
		mysql_close($dblink);
		
		return 0;

	}
	
	include( "cabecalho.php" );
	
?>
	
	<script>

		$(function(){   
			
			function removeCampo() {
				$(".removerCampo").unbind("click");
				$(".removerCampo").bind("click", function () {
					i=0;
					$(".links p.campoLink").each(function () {
						i++;
					});
					if (i>1) {
						$(this).parent().remove();
					}
				});
			}
			
			removeCampo();
			
			$(".adicionarCampo").click(function () {
				novoCampo = $(".links p.campoLink:first").clone();
				novoCampo.find("input").val("");
				novoCampo.insertAfter(".links p.campoLink:last");
				removeCampo();
			});

			$('#Confirmar').click(function () {
				abrirPag("perfil.php", "pAction=DELETEY&perid=<?php echo $id;?>");
				modal.modal('hide');
			});
			
			$('a#hrefPerExc').click(function(e){
				e.preventDefault();
				$('#deleteConfirmModal').modal('show');
			});

		});

	</script>
	
	</div>
	
	<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
				
					<?php 
						echo "<h4 class='modal-title' id='deleteLabel'>" . _("Notifica&ccedil;&atilde;o de exclus&atilde;o") . "</h4>";
            			echo "</div>";
						echo "<div class='modal-body'>";
                		echo "<p>" . _("Voc&ecirc; optou por excluir sua conta.") . "</p>";
                		echo "<p>" . _("Se esta &eacute; a a&ccedil;&atilde;o que deseja executar, ");
                		echo _("por favor confirme sua escolha ou cancele a opera&ccedil;&atilde;o para retornar.") . "</p>";
            			echo "</div>";
           				echo "<div class='modal-footer'>";
                		echo "<button type='button' class='btn btn-success' data-dismiss='modal' id='Cancelar'>" . _("Cancelar") . "</button>";
                		echo "<button type='button' class='btn btn-danger' id='Confirmar'>" . _("Confirmar") . "</button>";
                	?>
                	
            	</div>
        	</div>
    	</div>
	</div>
	
	<div class="col-md-12">
	
				<?php
				
				if ($pAction == "UPDATED") {
					$alteracao = AlteraDados( $perid, strip_tags($senha), strip_tags($rsenha), strip_tags($email), strip_tags($remail), 
					strip_tags($nome), strip_tags($endereco), strip_tags($cidade), strip_tags($cep), strip_tags($uf), strip_tags($telefone), strip_tags($pais), 
					strip_tags($profissao), strip_tags($educacao), strip_tags($experiencia), strip_tags($hobby), $link, $nomelink);
				}

				include( "./connectdb.php" );

				$sql = "SELECT id, email, nome, endereco, cidade, cep, uf, telefone, pais, profissao, ";
				$sql = $sql . "decode(senha, 'mypas') as senha, foto, experiencia, educacao, hobby FROM usuario WHERE id = '$id'";

				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				
				if (!isset($alteracao)) {
					$alteracao = 0;
				}

				if ($alteracao <> 1) {
					$linha = mysql_fetch_array($result);
					$senha = $linha["senha"];
					$rsenha = $linha["senha"];
					$email = $linha["email"];
					$remail = $linha["email"];
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
				}
				
				echo "<div class='jumbotron'><h1>$nome</h1>\n";
				echo "<p><a href='mailto:$email'>$email</a></p></div>\n";
				
				if ($pAction == "UPDATE") {
					echo "<a href='perfil.php'><button type='button' class='btn btn btn-default'>" . _("Voltar") . "</button></a>\n";
				} else {
					echo "<a href='principal.php'><button type='button' class='btn btn btn-default'>" . _("Voltar") . "</button></a>\n";
				}
				
				echo "<a href='#' " . CriaLink( "perfil.php", "pAction=UPDATE&perid=$id" ) . "><button type='button' class='btn btn btn-default'>" . _("Alterar") . "</button></a>";
				echo "<a href='#' id='hrefPerExc'><button type='button' class='btn btn-danger btn-default'>" . _("Excluir") . "</button></a><br><br>\n";
				
				if ($pAction == "UPDATE" or $alteracao == 1) {
					
					echo "<p class='lead'>" . _("Campos assinalados com asterisco (*) s&atilde;o obrigat&oacute;rios.") . "</p>";
					
					echo "<form method='post' action='perfil.php'>\n";
					echo "<p><label for='senha'>(*) " . _("Senha") . "</label>\n";
					echo "<input type='password' class='form-control' id='senha' name='senha' maxlength=40 value='$senha' autofocus required /></p>\n";
					echo "<p><label for='rsenha'>(*) " . _("Repita a senha") . "</label>\n";
					echo "<input type='password' class='form-control' name='rsenha' maxlength=40 value='$rsenha' 
					class='form-control' oninput='check(this)' required autofocus /></p>\n";
					echo "<p><label for='email'>(*) e-mail</label>\n";
					echo "<input type='email' id='email' name='email' maxlength=40 value='$email' class='form-control' required autofocus /></p>\n";
					echo "<p><label for='email'>(*) " . _("Repita o e-mail") . "</label>\n";
					echo "<input type='email' name='remail' maxlength=40 value='$remail' class='form-control' oninput='check(this)' required autofocus /></p>\n";
					echo "<p><label for='nome'>(*) " . _("Nome") . "</label>\n";
					echo "<input type='text' name='nome' maxlength=60 value='$nome' class='form-control' required autofocus/></p>\n";
					echo "<p><label for='endereco'>" . _("Endere&ccedil;o") . "</label>\n";
					echo "<input type='text' name='endereco' maxlength=60 value='$endereco' class='form-control' autofocus /></p>\n";
					echo "<p><label for='telefone'>" . _("Telefone") . "</label>\n";
					echo "<input type='tel' name='telefone' maxlength=20 value='$telefone' class='form-control' autofocus /></p>\n";
					echo "<p><label for='cidade'>" . _("Cidade") . "</label>\n";
					echo "<input type='text' name='cidade' maxlength=40 value='$cidade' class='form-control' autofocus /></p>\n";
					echo "<p><label for='cep'>" . _("CEP") . "</label>\n";
					echo "<input type='text' name='cep' maxlength=9 value='$cep' class='form-control' autofocus /></p>\n";
					echo "<p><label for='uf'>" . _("Estado") . "</label>\n";
					echo "<input type='text' name='uf' maxlength=2 value='$uf' class='form-control' autofocus /></p>\n";
					echo "<p><label for='pais'>" . _("Pa&iacute;s") . "</label>\n";
					echo "<input type='text' name='pais' maxlength=20 value='$pais' class='form-control' autofocus /></p>\n";
					echo "<p><label for='profissao'>" . _("Profiss&atilde;o") . "</label>\n";
					echo "<input type='text' name='profissao' maxlength=60 value='$profissao' class='form-control' autofocus /></p>\n";
					echo "<br><span id='textBold'>" . _("Refer&ecirc;ncias (nome do site/link)") . "</span><br>\n";
					echo "<div class='links'><p class='campoLink'>\n";
					
					$sql = "SELECT usuid, endereco, nome FROM usulinks WHERE usuid = '$id'";
					$result = mysql_query( $sql, $dblink ) or die(mysql_error());
					if ( mysql_num_rows($result) == 0 ) {
						echo "<input type='text' maxlength='40' id='nomelink' name='nomelink[]' class='form-control' autofocus />\n
						<input type='url' maxlength=200 id='link' name='link[]' class='form-control' autofocus />\n
						<a href='#' class='removerCampo'>" . _("Remover link") . "</a></p><p>\n
						</div><a href='#' class='adicionarCampo'>" . ("Adicionar Link") . "</a></p>\n";
					} else {
						while ($linha = mysql_fetch_array($result)) {
							echo "<input type='text' maxlength=40 id='nomelink' name='nomelink[]' value='" . $linha["nome"] . 
							"' class='form-control' autofocus />\n
							<input type='url' maxlength=200 name='link[]' value='" . $linha["endereco"] . "' class='form-control' autofocus />
							<a href='#' class='removerCampo'>" . _("Remover link") . "</a></p>\n";
						}
						echo  "</div><p><a href='#' class='adicionarCampo'>" . _("Adicionar link") . "</a></p>\n";
					}
					
					echo "<p><label for='formacao'>" . _("Forma&ccedil;&atilde;o") . "</label><br>\n";
					echo "<textarea name='educacao' cols='70' rows='20' class='form-control' autofocus />$educacao</textarea></p>\n";
					echo "<p><label for='experiencia'>" . _("Experi&ecirc;ncia profissional") . "</label><br>\n";
					echo "<textarea name='experiencia' cols='70' rows='20' class='form-control' autofocus />$experiencia</textarea></p>\n";
					echo "<p><label for='hobby'>" . _("Outros interesses") . "</label><br>\n";
					echo "<textarea name='hobby' cols='70' rows='20' class='form-control' autofocus />$hobby</textarea></p>\n";
					echo "<input type=hidden name=pAction value='UPDATED'>\n";
					echo "<input type=hidden name=perid value='$id'>\n";
					echo "<input type='submit' class='submit' name='enviarusu' value='" . _("Enviar") . "'></fieldset>\n";
					echo "</form><br><br>\n";	
					
				} else {
				
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
				
					$sql = "SELECT usuid, endereco, nome FROM usulinks WHERE usuid = '$id'";
					$result = mysql_query( $sql, $dblink ) or die(mysql_error());
					if ( mysql_num_rows($result) > 0 ) {
						echo "<p class='lead'><strong>" . _("Refer&ecirc;ncias") . ":</strong></p>\n";
						while ($linha = mysql_fetch_array($result)) {
							echo "<p class='lead'><a href = '" . $linha["endereco"] . "' target='_blank'>" . $linha["nome"] . 
							"</a></p>\n";
						}
					}
				
					mysql_close($dblink);
					
				}
				
				include 'rodape.inc';
				
				?>