<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	$extensoesPermitidas = array("csv");
	
	if ($tipo == 0 and $pAction != "SELECT_LIST") {
		$pAction = "LIST";
	}
	
	if ($tipo == 1 and empty($pAction) and !empty($disid)) {
		$pAction = "LIST";
	}
	
	function ListaDados($selall, $endid, $nome, $pag, $disid, $pAction, $tipo) {
		
		include("./connectdb.php");
		
		if ($pAction == "LIST") {
			$sql = "SELECT al.id, UPPER(al.nome) as nome, u.nome as professor, ue.usuid as perfil, da.ativo FROM disalu da INNER JOIN aluno al ON (al.id=da.aluid) 
			INNER JOIN usuario u ON (u.id = al.usuid) LEFT JOIN usuend ue ON (ue.ra = al.id AND ue.endid = '$endid') WHERE da.disid = '$disid'";
		} elseif ($pAction == "GET") {
			$sql = "SELECT endid FROM disciplina WHERE id = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_array($result)) {
				$endid = $linha["endid"];
			}
			$sql = "SELECT al.id, UPPER(al.nome) as nome, ue.usuid as perfil, u.nome as professor, al.ativo FROM aluno al LEFT JOIN usuario u ON u.id = al.usuid 
			LEFT JOIN usuend ue ON ue.ra = al.id WHERE al.endid = '$endid' AND al.id NOT IN (SELECT aluid FROM disalu WHERE disid = '$disid')";
		} else {
			$sql = "SELECT al.id, UPPER(al.nome) as nome, u.nome as professor, ue.usuid as perfil, al.ativo FROM aluno al LEFT JOIN usuario u ON u.id = al.usuid 
			LEFT JOIN usuend ue ON ue.ra = al.id WHERE al.endid = '$endid'";
		}
		
		if (!empty($nome)) {
			$sql .= " AND UPPER(al.nome) LIKE UPPER('%$nome%')";
		}
		
		$sql .= " GROUP BY al.id ORDER BY 2";
		
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		
		$paginas = (mysql_num_rows($result) - (mysql_num_rows($result) % 50)) / 50;
		
		if ((mysql_num_rows($result) % 50) > 0) {
			$paginas += 1;
		}
			
		if (empty($pag)) {
			$pag = 1;
		}
		
		if ($paginas > 1) {
			echo "<form action='estudante.php' method='POST'>\n";
			echo "<div class='row'><div class='col-lg-9'>";
			echo "<br><label for='nome'>" . _("Nome") . "</label>\n";
			echo "<input type='text' name='nome' class='form-control'>\n";
			echo "<input type='hidden' name='pAction' value='$pAction'>\n";
			echo "<input type='hidden' name='endid' value='$endid'>\n";
			echo "<input type='hidden' name='disid' value='$disid'>\n";
			echo "</div><div class='col-lg-3'><br><br><button type='submit' class='btn btn-default' name='enviar'>" . _("Pesquisar") . "</button></div></div></form>\n";
		
			echo "<form action='estudante.php' method='POST'>\n";
			echo "<input type='hidden' name='pAction' value='$pAction'>\n";
			echo "<input type='hidden' name='endid' value='$endid'>\n";
			echo "<input type='hidden' name='disid' value='$disid'>\n";
		
			echo "<br><label for='pagina'>" . _("Ir para p&aacute;gina") . "... " . _("(digite 999 para exibir todos)") . "</label><div class='row'><div class='col-lg-2'>" . 
			"<INPUT type='text' class='form-control' name='pag' value='$pag' size='4' maxlength='4'></div><div class='col-lg-10'>" . 
			"<button type='submit' class='btn btn btn-default' name='enviar'>" . _("Enviar") . "</button></p></form>\n</div></div>\n";
			
			if ($pag == 1 or $pag == 999) {
				echo "<br>[" . _("Retroceder") . "]&nbsp;";
			} else {
				echo "<br><A HREF='#' onClick='abrirPag(",'"estudante.php", "endid=', $endid, '&disid=', $disid, '&pAction=', $pAction, "&pag=",$pag-1,'"',")'>[retroceder]</a>&nbsp;\n";
			}
			
			echo _("P&aacute;g.") . "$pag/$paginas";
			
			if ($pag == $paginas or $pag == 999) {
				echo "&nbsp;[" . _("Avan&ccedil;ar") . "]&nbsp;&nbsp;\n";
			} else {
				echo "&nbsp;<A HREF='#' onClick='abrirPag(",'"estudante.php", "endid=', $endid, '&disid=', $disid, '&pAction=', $pAction, '&pag=',$pag+1,'"',")'>
				[avan&ccedil;ar]</a>&nbsp;&nbsp;\n";
			}

		}

		if (mysql_num_rows($result) > 0) {
			
			if ($tipo == 1) {
				echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" .
				_("Altera dados do estudante") . "<br><br>\n";
			}
			
			echo "<p>" . _("N&uacute;mero de estudantes") . ": " . mysql_num_rows($result) . "</p>";
			
			echo "<br><table class='table'>\n" ;
			if ($tipo == 1) {
				echo "<thread><th></th><th></th>";
			}
			echo "<th>Registro</th><th>Nome</th>\n";
			if ($tipo == 1) {
				if ($pAction == "LIST") {
					echo "<th>&Uacute;ltimo Acesso na Disciplina</th>";
				} else {
					echo "<th>&Uacute;ltimo Acesso no Sistema</th>";
				}
				//echo "<th>Professor Respons&aacute;vel</th>";
				echo "<th></th></thread><tbody>\n";
			}	

			if ($pAction == "LIST") {
				echo "<form action='estudante.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='MISS'>\n";
				echo "<input type='hidden' name='endid' value='$endid'>\n";
				echo "<input type='hidden' name='disid' value='$disid'>\n";
			} elseif ($pAction == "GET") {
				echo "<form action='estudante.php' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTEN'>\n";
				echo "<input type='hidden' name='endid' value='$endid'>\n";
				echo "<input type='hidden' name='disid' value='$disid'>\n";
			} else {
				echo "<form action='estudante.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='DELETE'>\n";
				echo "<input type='hidden' name='endid' value='$endid'>\n";
				echo "<input type='hidden' name='disid' value='$disid'>\n";
			}
			
			$registro = 1;
			$pagreg = 1;

			while ($linha = mysql_fetch_array($result)) {
				
				if ($pagreg == $pag or $pag == 999) {

					echo "<tr>\n";
					
					if ($tipo == 1) {
						echo "<td width='10px' align='right'>\n";
						if ( empty( $selall ) ) {
							echo "<input type='checkbox' name='eliassdes[" . $linha["id"] . "]'>\n";
						} else {
							echo "<input type='checkbox' name='eliassdes[" . $linha["id"] . "]' CHECKED></td>\n";
						}
						echo "<td><a href='#' onClick='abrirPag(" . '"estudante.php", 
						"pAction=UPDATE&pActionDest=' . $pAction . '&estid=' . $linha["id"] . '")' . "'>\n";
						echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>\n";
					}
					
					echo "<td>" . $linha["id"] . "</td><td>";
					
					if (!empty($linha["perfil"])) {
						if ($pAction == "LIST" or $pAction == "GET") {
							echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=SELECT_LIST&disid=' . $disid .
							'&estid=' . $linha["perfil"] . '")' . "'>\n";
						} else {
							echo "<a href='#' onClick='abrirPag(" . '"estudante.php", 
							"pAction=SELECT&endid=' . $endid . '&estid=' . $linha["perfil"] . '")' . "'>\n";
						}
					}
										
					echo $linha["nome"];
					
					if (!empty($linha["perfil"])) {
						echo "</a>\n";
						if ($tipo == 1) {
							if ($pAction == "LIST") {
								$sql = "SELECT MAX(timef) as timef FROM acesso WHERE usuid = '" . $linha["perfil"] . "' AND disid='$disid' GROUP BY usuid";
							} else {
								$sql = "SELECT MAX(timef) as timef FROM acesso WHERE usuid = '" . $linha["perfil"] . "' GROUP BY usuid";
							}
							$result_a = mysql_query($sql, $dblink ) or die(mysql_error());
							if ($acesso = mysql_fetch_array($result_a)) {
								$regs = array();
								ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})",$acesso["timef"], $regs);
								$timef = $regs[3] . "-" . $regs[2] . "-" . $regs[1] . " " . $regs[4] . ":" . $regs[5];
							} else {
								$timef = "";
							}
							echo "</td><td>$timef</td>";
						} else {
							echo "</td></tr>\n";
						}
					} else {
						if ($tipo == 1) {
							echo "</td><td>\n";
						}
					}
					if ($tipo == 1) {
						echo "</td>";
						//echo "</td><td>" . $linha["professor"] . "</td>";
						if ($linha["ativo"]) {
							echo "<td align='center'><a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=OFF&endid=' . $endid . '&disid=' . $disid . '&perfil=' 
							. $linha["id"] . '"' . ")'>" . _("Ativo") . "</a></td></tr>";					
						} else {
							echo "<td align='center'><a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=ON&endid=' . $endid . '&disid=' . $disid . '&perfil='
							. $linha["id"] . '"' . ")'>" . _("Inativo") . "</a></td></tr>";
						}
					} else {
						echo "</td></tr>\n";
					}
				}
				
				$registro++;
				if ($registro > 50) {
					$pagreg++;
					$registro = 1;
				}
			}
			
			echo "</tbody></table>";
			
		} else {
			echo "<p class='lead'>N&atilde;o h&aacute; estudantes registrados ...</p>\n";
			mysql_close($dblink);
			return;
		}
		
		echo "</table>\n";
		
		mysql_close($dblink);
		
		if ($tipo == 1) {
			echo "<table><tr valign='top'>\n" ;
			if ($pAction == "LIST") {
				echo "<td><button type='submit' class='btn btn-danger' name='enviar'>" . _("Desassociar") . "</button></form></td>\n" ;
			} elseif ($pAction == "GET") {
				echo "<td><button type='submit' class='btn btn-default' name='enviar'>" . _("Associar") . "</button></form></td>\n" ;
			} else {
				echo "<td><button type='submit' class='btn btn-danger' name='enviar'>" . _("Excluir") . "</button></form></td>\n" ;
			}
			echo "<td><form action='estudante.php' id='selall' method='POST'>\n" ;
			echo "<input type='hidden' name='endid' value='$endid'>\n";
			echo "<input type='hidden' name='disid' value='$disid'>\n";
			echo "<input type='hidden' name='pAction' value='$pAction'>\n";
			echo "<input type='hidden' name='selall' value='1'>\n";
			echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Marcar todos") . "</button>\n" ;
			echo "</form></td>\n" ;
			echo "<td><form action='estudante.php' id='selall' method='POST'>\n";
			echo "<input type='hidden' name='endid' value='$endid'>\n";
			echo "<input type='hidden' name='disid' value='$disid'>\n";
			echo "<input type='hidden' name='pAction' value='$pAction'>\n";
			echo "<input type='hidden' name='selall' value='0'>\n";
			echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Desmarcar todos") . "</button>\n" ;
			echo "</form></td></tr></table>\n" ;
		}

		return;
	
	}
	
	function ExcluiDados($eliminar, $usuid) {
		if (!empty($eliminar)) {
			include( "./connectdb.php" );
			foreach ($eliminar as $estid => $valor) {	
				if ($valor == 'on') {
					$sql = "SELECT usuid FROM aluno WHERE id = '$estid'";
					$result = mysql_query( $sql, $dblink ) or die(mysql_query());
					$linha = mysql_fetch_array($result);
					if ($linha["usuid"] == $usuid) {
						$sql = "DELETE FROM aluno WHERE id = '$estid'" ;
						if (!mysql_query( $sql, $dblink )) {
							echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" . _("Estudante est&aacute; associado a outros dados.") .
							_("Exclus&atilde;o n&atilde;o realizada ...") . "</strong></div>" ;
							mysql_close($dblink);
							return;
						}
					} else {
						echo "<br><br><div class='alert alert-danger' role='alert'><strong>" . 
						_("N&atilde;o &eacute; permitido excluir estudantes que foram cadastrados por terceiros.") . 
						"&nbsp;" . _("Exclus&atilde;o interrompida ...") . "</strong></div>" ;
						mysql_close($dblink);
						return;
					}
				}
			}
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" 
			. _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
		}
		return;
	}
	
	function IncluiDados($nome, $estid, $endid, $usuid) {
		if (!empty($endid)) {
			include( "./connectdb.php" );
			$sql = "INSERT INTO aluno (id, usuid, nome, endid) VALUES ('$estid', '$usuid', '$nome', '$endid', '1')";
			if (mysql_query( $sql, $dblink )) {
				echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			} else {
				echo "<br><br><div class='alert alert-danger' role='alert'><strong>" . 
				_("Estudante j&aacute; cadastrado. Os dados n&atilde;o foram inclu&iacute;dos") . "...</strong></div>";
			}
			mysql_close($dblink);
		} else {
			echo "<br><br><div class='alert alert-danger' role='alert'><strong>" .
			_("Informe a institui&ccedil;&atilde;o.") . " " . _("Opera&ccedil;&atilde;o n&atilde;o realizada") . "...</strong></div>";
		}
		return;
	}
	
	function AlteraDados($estid, $estidn, $usuid, $nome) {

		include( "connectdb.php" );

		if (!empty($estidn)) {
			$sql = "UPDATE aluno SET id = '$estidn', nome = '$nome' WHERE id = '$estid'";
			if (mysql_query( $sql, $dblink ) or die(mysql_error())) {
				echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			} else {
				echo "<br><br><div class='alert alert-danger' role='alert'><strong>" . 
				_("Estudante j&aacute; cadastrado. Os dados n&atilde;o foram alterados") . "...</strong></div>" ;
			}
		} else {
			$sql = "UPDATE aluno SET nome = '$nome' WHERE id = '$estid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			mysql_close($dblink);
			return;
		}

		mysql_close($dblink);

	}
	
	function AlteraAtivo($endid, $disid, $perfil, $pAction) {

		include( "connectdb.php" );
		
		if ($pAction == "ON") {
			$ativo = '1';
		} else {
			$ativo = '0';
		}
		
		if (!empty($disid)) {
			$sql = "UPDATE disalu SET ativo = $ativo WHERE aluid = '$perfil' AND disid = '$disid'";
		} else {
			$sql = "UPDATE aluno SET ativo = $ativo WHERE id = '$perfil' AND endid = '$endid'";
		}
		
		if (mysql_query( $sql, $dblink ) or die(mysql_error())) {
			if (empty($disid)) {
				$sql = "SELECT id FROM disciplina WHERE endid = '$endid'";
				$result = mysql_query($sql, $dblink ) or die(mysql_error());
				while ($linha = mysql_fetch_array($result)) {
					$sql = "UPDATE disalu SET ativo = $ativo WHERE disid = '" . $linha["id"] . "' AND aluid = '$perfil'";
					$result2 = mysql_query($sql, $dblink ) or die(mysql_error());
				}
			}
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . 
			_("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		}
		
		mysql_close($dblink);
		return;

	}
	
	function Associar($eliassdes, $disid) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $estid => $valor) {	
			if ($valor == 'on') {
				$sql = "INSERT INTO disalu VALUES ('$disid', '$estid', '1')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo "<br><br><div class='alert alert-danger' role='alert'><strong>" . 
			_("Selecione ao menos um estudante") . "...</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}

	function Desassociar($eliassdes, $disid) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $estid => $valor) {	
			if ($valor == 'on') {
				$sql = "DELETE FROM disalu WHERE aluid = '$estid' AND disid = '$disid'" ;
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$sql = "DELETE FROM correcao WHERE aluid = '$estid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$sql = "DELETE FROM descritiva WHERE aluid = '$estid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$sql = "DELETE FROM testearq WHERE aluid = '$estid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$sql = "DELETE FROM frequencia WHERE aluid = '$estid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$sql = "DELETE FROM reunioes WHERE aluid = '$estid' AND disid = '$disid'" ;
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				$sql = "SELECT id FROM avaliacao WHERE disid = '$disid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				while ($linha = mysql_fetch_array($result)) {
					$sql = "DELETE FROM notas WHERE aluid = '$estid' AND avalid = '" . $linha["id"] . "'"; 
					$resultn = mysql_query( $sql, $dblink ) or die(mysql_error());
				}				
			}
		}
		if (!empty($sql)) {
			echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		} else {
			echo "<br><br><div class='alert alert-danger' role='alert'><strong>" . 
			_("Selecione ao menos um estudante") . "...</strong></div>" ;
		}
		mysql_close($dblink);
		return;
	}
	
	function Formulario($estid, $nome, $endid, $id, $pAction, $pActionDest) {
		echo "<p><label for='registro'>(*) Registro</label>\n";
		if ($pAction == "UPDATE") {
			echo "<input type='text' id='estid' name='estid' disabled value='$estid' size=30 maxlength=30 class='form-control'></p>\n";
			echo "<p><label for='registro'>Novo Registro</label>\n";
			echo "<input type='text' id='estidn' name='estidn' value='' size=30 maxlength=30 class='form-control'></p>\n";
		} else {
			echo "<input type='text' id='estid' name='estid' value='$estid' size=30 maxlength=30 class='form-control'>\n";
		}
		echo "<p><label for='registro'>(*) Nome</label>\n";
		echo "<input type='text' id='nome' name='nome' value='$nome' size=60 maxlength=60 class='form-control'></p>\n";
		echo "<button class='btn btn-default' type='submit' name='enviar' value='Enviar'>" . _("Enviar") . "</button></form>\n";
	}
	
	include( "cabecalho.php" );
	
	if (empty($disid)) {
		include "menup.inc";
	} else {
		include "menu.inc";
	}
	
?>

	<script type="text/javascript">

		$(function(){

			$('#ConfirmarExc').click(function () {
				document.deleteForm.submit();
				modal.modal('hide');
			});

			$('form#deleteForm').submit(function(e){
				e.preventDefault();
				$('#deleteConfirmModal').modal('show');
			});
			
		});

	</script>
				
	<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
				
				<?php
					echo "<h4 class='modal-title' id='deleteLabel'>" . _("Notifica&ccedil;&atilde;o") . "</h4>";
					echo "</div>";
					echo "<div class='modal-body'>";
					echo "<p>" . _("Voc&ecirc; optou por excluir ou desassociar um estudante.") . "</p>";
					echo _("Se esta &eacute; a a&ccedil;&atilde;o que deseja executar, ");
					echo _("por favor confirme sua escolha ou cancele a opera&ccedil;&atilde;o para retornar.") . "</p>";
					echo "</div>";
					echo "<div class='modal-footer'>";
					echo "<button type='button' class='btn btn-success' data-dismiss='modal' id='Cancelar'>" . _("Cancelar") . "</button>";
					echo "<button type='button' class='btn btn-danger' id='ConfirmarExc'>" . _("Confirmar") . "</button>";
				?>
				                	
		       </div>
			</div>
		</div>
	</div>

<?php

	if (empty($disid)) {

		include( "connectdb.php" );
		
		echo "<div class='jumbotron'>";

		$sql = "SELECT nome,email,professor FROM usuario WHERE id = '$id'" ;

		$result = mysql_query( $sql, $dblink );

		if ( mysql_num_rows($result) > 0) {
			$linhau = mysql_fetch_array($result);
			if ($linhau["professor"] == 1) {
				echo "<p><h2>" . _("Professor(a)") . "&nbsp;";
			} else {
				echo "<p><h2>";
			}
			echo $linhau["nome"] . "</h2></p>";
		}
		
		mysql_close($dblink);
		
	} else {
		
		include 'dadosdis.inc';
		
	}

	echo "<br><h2 class='blog-post-title'><span class='glyphicon glyphicon-education' aria-hidden='true'></span>&nbsp;" . _("Estudantes") . "</h2>";

	echo "</div>";
	
	if ($pAction == "SELECT_LIST") {
		echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=LIST&disid=' . $disid . '&endid=' . $endid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Estudantes associados") . "</button></A>\n";
	} elseif ($pAction == "SELECT" or $pAction == "MISS" or $pAction == "GOTTEN") {
		echo "<br><a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=GET&disid=' . $disid . '&endid=' . $endid . '"' . ")'>
		<button type='button' class='btn btn btn-default'>" . _("Estudantes cadastrados") . "</button></A><br><br>\n";
	} elseif ($pAction == "OFF" or $pAction == "ON") {
		if (empty($disid)) {
			echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=IMPORT&disid=' . $disid . '&endid=' . $endid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Importar novos estudantes") . "</button></A>\n";
			echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "disid=' . $disid . '&endid=' . $endid . '&pAction=INSERT"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Incluir novo estudante") . "</button></A>\n";
		} else {
			echo "<br><a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=GET&disid=' . $disid . '&endid=' . $endid . '"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Estudantes cadastrados") . "</button></A><br><br>\n";
		}	
	} elseif ($pAction == "GET") {
		echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=LIST&disid=' . $disid . '&endid=' . $endid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Estudantes associados") . "</button></A>\n";
		echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=IMPORT&disid=' . $disid . '&endid=' . $endid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
		_("Associar estudantes por meio de importa&ccedil;&atilde;o") . "</button></A>\n";
	} elseif ($pAction == "LIST") {
		if ($tipo == 1) {
			echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=GET&disid=' . $disid . '&endid=' . $endid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Estudantes cadastrados") . "</button></a>\n";
		}
	} elseif ($pAction == "IMPORT" or $pAction == "IMPORT_CONFIRM" or $pAction == "IMPORT_INSERT") {
		if (empty($disid)) {
			echo "<br><a href='#' onClick='abrirPag(" . '"estudante.php", "disid=' . $disid . '&endid=' . $endid . '"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Estudantes cadastrados") . "</button></A>\n";
			echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "disid=' . $disid . '&endid=' . $endid . '&pAction=INSERT"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Incluir novo estudante") . "</button></A>\n";
		} else {
			echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=LIST&disid=' . $disid . '&endid=' . $endid . '"' . ")'><button type='button' class='btn btn btn-default'>" .
			_("Estudantes associados") . "</button></A>\n";
			echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=GET&disid=' . $disid . '&endid=' . $endid . '"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Estudantes cadastrados") . "</button></A><br><br>\n";
		}
	} else {
		echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "pAction=IMPORT&disid=' . $disid . '&endid=' . $endid . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Importar novos estudantes") . "</button></A>\n";
		echo "<a href='#' onClick='abrirPag(" . '"estudante.php", "disid=' . $disid . '&endid=' . $endid . '&pAction=INSERT"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Incluir novo estudante") . "</button></A>\n";
	}
	
	echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Estudantes") . "</h3></div>";
	echo "<div class='panel-body'>";
	
	if ($pAction != "SELECT" and $pAction != "SELECT_LIST" and empty($disid)) {
		
		echo "<form action='estudante.php' id='instituicao' method=post>\n";
		echo "<input type='hidden' name='pAction' value='$pAction'>\n";
		echo "<input type='hidden' name='disid' value='$disid'>\n";
		
		if ($pAction == "INSERT" or $pAction == "INSERTED") {
			echo "<br>Asterisco (*) indica campo obrigat&oacute;rio<br><br>";
			echo "<p><label for='endid'>(*) " . _("Institui&ccedil;&atilde;o" ) . "</label>\n";
		} else {
			echo "<p><label for='endid'>" . _("Institui&ccedil;&atilde;o" ) . "</label>\n";
		}

		include( "connectdb.php" );
	
		echo "<select onchange='submit()' name='endid' id='endid' class='form-control'>\n";
	
		if ($id == "admin") {
			$sql = "SELECT e.id, e.nome FROM enderecos e ORDER BY 2";
		} else {
			$sql = "SELECT e.id, e.nome FROM usuend ue INNER JOIN enderecos e ON ue.endid = e.id WHERE ue.usuid = '$id' ORDER BY 2";
		}
	
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
	
		echo "</select></p></form>";
	
		mysql_close($dblink);

	}
	
	if ($pAction=="IMPORT_INSERT") {
		$abrearquivo = fopen("arquivos/$arquivo", "r");
		if (!$abrearquivo){
			echo "<div class='alert alert-danger' role='alert'><strong>" .
			_("Arquivo n&atilde;o encontrado") . "...</strong></div>" ;
		} else {
			if (!empty($endid)) {
				include( "./connectdb.php" );
				print ("<br><br>");
				//$sql = "SELECT curid FROM disciplina WHERE id = '$disid'";
				//$result = mysql_query( $sql, $aDBLink ) or die(mysql_error());
				//$linha = mysql_fetch_array($result);
				//$curid = $linha["curid"];				
				$sql = "SELECT * FROM endadmin WHERE usuid = '$id' AND endid = '$endid'";
				$result = mysql_query( $sql, $aDBLink ) or die(mysql_error());
				if (mysql_num_rows($result) == 0) {
					//$sql = "SELECT * FROM curadmin WHERE usuid = '$id' AND curid = '$curid'";
					//$result = mysql_query( $sql, $aDBLink ) or die(mysql_error());
					//if (mysql_num_rows($result) == 0) {
					$inclusao = 0;
				} else {
					$inclusao = 1;
				}
				//} else {
				//	$inclusao = 1;
				//}
				while ($valores = fgetcsv($abrearquivo, 2048, ";")) {
					if( mb_detect_encoding($valores[0],"auto") != "ISO-8859-1" ) {
						//$registro = mb_convert_encoding($valores[0], "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
						$registro = mb_convert_encoding($valores[0], "ISO-8859-1", "auto");
					}
					if( mb_detect_encoding($valores[0],"auto") != "ISO-8859-1" ) {
						//$nome = mb_convert_encoding($valores[1], "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
						$nome = mb_convert_encoding($valores[1], "ISO-8859-1", "auto");
					}
					$sql = 'SELECT * FROM aluno WHERE id="' . $registro . '"';
					$result = mysql_query( $sql, $dblink ) or die(mysql_error());
					if ( mysql_num_rows($result) > 0) {
						if (empty($disid)) {
							echo "<span>" . $registro . "-" . $nome . " - " . _("Registro j&aacute; existe") . "...</span><br>\n";
							$erro = 1;
						}
					} else {
						if ($inclusao) {
							$sql = "INSERT INTO aluno (id, usuid, nome, email, endid) 
							VALUES (" . '"' . $registro . '", "' . $id . '", "' . $nome . '", null, "' . $endid . '"' . ")";
							$result = mysql_query( $sql, $dblink ) or die(mysql_error());
						} else {
							echo "<span>" . $registro . "-" . $nome . " - " . 
							_("Estudante n&atilde;o cadastrado. Inclus&atilde;o permitida ao administrador da institui&ccedil;&atilde;o") . "...</span><br>\n";
							$erro = 1;
						}
					}
					if (!empty($disid)) {
						$sql = "SELECT * FROM disalu WHERE disid = '$disid' AND aluid = '$registro'";
						$result = mysql_query( $sql, $dblink ) or die(mysql_error());
						if ( mysql_num_rows($result) > 0) {
							echo "<span>" . $registro . "-" . $nome . " - " . _("Registro j&aacute; existe") . "...</span><br>\n";
							$erro = 1;
						} else {
							if (!$erro) {
								$sql = "INSERT INTO disalu (disid, aluid) VALUES ('$disid', '$registro')";
								$result = mysql_query( $sql, $dblink ) or die(mysql_error());
							}
						}
					}
				}
				if (empty($erro)) {
					echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
				} else {
					echo "<br><br><div class='alert alert-danger' role='alert'><strong>" .
					_("Ocorreram erros durante a importa&ccedil;&atilde;o") . "...</strong></div>\n";
				}
				mysql_close($dblink);
				unlink("arquivos/$arquivo");
			} else {
				echo "<br><br><div class='alert alert-danger' role='alert'><strong>" .
				_("Selecione uma institui&ccedil;&atilde;o") . "...</strong></div>" ;
			}
		}
	} elseif ($pAction=="IMPORT_CONFIRM") {
		if (empty($_FILES["arquivo"]["name"])) {
			echo "<div class='alert alert-danger' role='alert'><strong>" .
			_("Selecione um arquivo") . "...</strong></div>" ;
		} else {
			if (!empty($endid)) {
				$partes = explode(".", $_FILES["arquivo"]["name"]);
				$extensao = array_pop($partes);
				if (in_array($extensao, $extensoesPermitidas) == false) {
					echo "<div class='alert alert-danger' role='alert'><strong>" .
					_("Tipo de arquivo") . "($extensao)" . _("n&atilde;o permitido") . "...</strong></div>" ;
				} else {
					$abrearquivo = fopen($_FILES["arquivo"]["tmp_name"], "r");
					if (!$abrearquivo){
						echo "<div class='alert alert-danger' role='alert'><strong>" .
							_("Arquivo n&atilde;o encontrado") . "...</strong></div>" ;
					} else {
						while ($valores = fgetcsv($abrearquivo, 2048, ";")) {
							if( mb_detect_encoding($valores[0],"auto") != "ISO-8859-1" ) {
								//$registro = mb_convert_encoding($valores[0], "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
								$registro = mb_convert_encoding($valores[0], "ISO-8859-1", "auto");
							}
							if( mb_detect_encoding($valores[0],"auto") != "ISO-8859-1" ) {
								//$nome = mb_convert_encoding($valores[1], "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
								$nome = mb_convert_encoding($valores[1], "ISO-8859-1", "auto");
							}
							print("<span id=lead>" . $registro . "-" . $nome . "</span><br>\n");
						}
	
						$arquivo_new = $id . "_" . trim($_FILES["arquivo"]["name"]);
						$arquivo_copy = "arquivos/" . $arquivo_new;
						if (!copy($_FILES["arquivo"]["tmp_name"], $arquivo_copy )) {
							echo "<div class='alert alert-danger' role='alert'><strong>" .
							_("Falha na c&oacute;pia do arquivo") . "...</strong><br><br> $arquivo_copy</div>" ;
						} else {
							echo "<form action='estudante.php' method='POST'>\n";
							echo "<input type='hidden' name='pAction' value='IMPORT_INSERT'>\n
							<input type='hidden' name='endid' value='$endid'>\n
							<input type='hidden' name='arquivo' value='$arquivo_new'>\n";
							echo "<input type='hidden' name='disid' value='$disid'>\n";
							echo "<br><button class='btn btn-default' type='submit' name='enviar'>" . _("Confirmar importa&ccedil;&atilde;o") . "</button></td></form><br>\n";
						}
						fclose($abrearquivo);
					}
				}
			} else {
				echo "<div class='alert alert-danger' role='alert'><strong>" .
				_("Selecione uma institui&ccedil;&atilde;o") . "...</strong></div>" ;
			}
		}
	} elseif ($pAction == "IMPORT") {
		echo "<form ENCTYPE='multipart/form-data' action='estudante.php' method='POST'>\n";
		echo "<input type='hidden' name='pAction' value='IMPORT_CONFIRM'>\n
		<input type='hidden' name='endid' value='$endid'>\n";
		echo "<input type='hidden' name='disid' value='$disid'>\n";
		echo "<br>" . _("Informe um aquivo .csv que contenha registro e nome dos estudantes separados por ponto e v&iacute;rgula (;).") . "<br><br>" . _("Exemplo") . ":\n";
		echo "<br><br>150150150;" . _("Arist&oacute;teles de Estagira") . "<br>160160160;" . _("Di&oacute;genes de S&iacute;nope") . "\n";
		echo "<br><br><label for='arquivo'>" . _("Arquivo") . "</label>\n";
		echo "<br><input type='file' name='arquivo' value='$arquivo' size=60 maxlength=90 class='form-control'>\n";
		echo "<br><br><button class='btn btn-default' type='submit' name='enviar'>" . _("Importar") . "</button></form><br>";
	}

	if ($pAction == "DELETE") {
		ExcluiDados($eliassdes, $id);
		ListaDados($selall, $endid, null, null, null, null, $tipo);
	} elseif ($pAction == "ON" or $pAction == "OFF") {
		AlteraAtivo($endid, $disid, $perfil, $pAction);
		if (!empty($disid)) {
			ListaDados($selall, $endid, $nome, $pag, $disid, "LIST", $tipo);
		} else {
			ListaDados($selall, $endid, $nome, $pag, $disid, $pAction, $tipo);
		}
	} elseif ($pAction == "INSERT") {
		echo "<form action='estudante.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario(null, null, null, $id, null, null);
	} elseif ($pAction == "UPDATE") {
		echo "<form action='estudante.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
		echo "<input type='hidden' name='pActionDest' value='$pActionDest'>\n";
		echo "<input type='hidden' name='endid' value='$endid'>\n";
		echo "<input type='hidden' name='disid' value='$disid'>\n";
		echo "<input type='hidden' name='estid' value='$estid'>\n";
		include 'connectdb.php';
		$sql = "SELECT id, nome FROM aluno WHERE id='$estid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		Formulario($linha["id"], $linha["nome"], $endid, $id, $pAction, $pActionDest);
	} elseif ($pAction == "INSERTED" or $pAction == "UPDATED") {
		if ($pAction == "UPDATED") {
			AlteraDados($estid, $estidn, $id, $nome);
			ListaDados($selall, $endid, null, null, $disid, $pActionDest, $tipo);
		} else {
			IncluiDados($nome, $estid, $endid, $id);
			echo "<form action='estudante.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
			echo "<input type='hidden' name='disid' value='$disid'>\n";
			Formulario(null, null, null, $id, null, null);
		}
	} elseif ($pAction == "MISS") {
		Desassociar($eliassdes, $disid);
		ListaDados($selall, $endid, $nome, $pag, $disid, "LIST", $tipo);
	} elseif ($pAction == "GOTTEN") {
		Associar($eliassdes, $disid);
		ListaDados($selall, $endid, $nome, $pag, $disid, "LIST", $tipo);		
	} elseif ($pAction == "SELECT" or $pAction == "SELECT_LIST") {
		$perfil = $estid;
		include 'perfil.inc';
	} elseif (empty($pAction) or $pAction == "GET" or $pAction == "LIST") {
		ListaDados($selall, $endid, $nome, $pag, $disid, $pAction, $tipo);
	}
	
	mysql_close($dblink);
	
	echo "</div></div>";
	
	include 'rodape.inc';

?>
