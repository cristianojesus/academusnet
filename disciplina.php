<?php
	session_start();
	include "buscasessao.php";
	$linha = BuscaSessao(null);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	if ($tipo == 0) {
		echo _("Sess&atilde;o Expirada. Fa&ccedil;a um novo") . "&nbsp;<a href='login.php'>login</a> ...";
		exit;
	}

	function ListaDados($endid, $curid, $usuid, $selall, $pAction, $tipo) {

		include( "./connectdb.php" );

		if ($usuid != "admin") {
			$sql = "SELECT nome, objetivo, id, cargah, faltas, visitante, matricula, titular FROM disciplina WHERE curid = '$curid' and usuid = '$usuid' ORDER BY 1";
		} else {
			$sql = "SELECT nome, objetivo, id, cargah, faltas, visitante, matricula, titular FROM disciplina WHERE curid = '$curid' ORDER BY 1";
		}

		$result = mysql_query( $sql, $dblink ) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {
			
			if ($tipo == 1) {
				print ("<br><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;Altera dados da disciplina\n" );
			}
			
			echo "<br><br><table class='table'><thread><tr>\n";
			echo "<th></th><th></th><th>" . _("Nome") . "</th>\n";
			echo "<th>" . _("Carga hor&aacute;ria") . "</th>\n";
			echo "<th>" . _("Faltas poss&iacute;veis") . "</th>\n";
			echo "<th>" . _("Visitantes") . "</th>\n";
			echo "<th>" . _("Matr&iacute;culas") . "</th>\n";
			echo "<th>" . _("Titular") . "</th></thread><tbody>\n";
			
			echo "<form action='disciplina.php' id='deleteForm' name='deleteForm' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='DELETE'>\n";
			echo "<input type='hidden' name='curid' value='$curid'>\n";
			echo "<input type='hidden' name='endid' value='$endid'>\n";

			while ($linha = mysql_fetch_array($result)) {

				$disid = $linha["id"];
				
				echo "<tr><td>";

				if ($pAction <> "QUERY") {
					if ( empty( $selall ) ) {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='eliminar[" . $linha["id"] . "]' CHECKED>\n";
					}
				}

				echo "</td>";
				echo "<td><a href='#'" . CriaLink( "disciplina.php", "pAction=UPDATE&disid=" . $linha["id"]. "&curid=$curid&endid=$endid") . ">
				<span class='glyphicon glyphicon-pencil' aria-hidden='true'></a></td>"; 
				echo "<td>" . $linha["nome"] . "</td>";
				echo "<td>" . $linha["cargah"] . "</td>";
				echo "<td>" . $linha["faltas"] . "</td>";
				if ($linha["visitante"]) {
					echo "<td>Aberto</td>";
				} else {
					echo "<td>Fechado</td>";
				}
				if ($linha["matricula"]) {
					echo "<td>Aberto</td>";
				} else {
					echo "<td>Fechado</td>";
				}
				if ($linha["titular"]) {
					echo "<td>Sim</td></tr>";
				} else {
					echo "<td>N&atilde;o</td></tr>";
				}
				
			}
			
			echo "</tbody></table>";
			
		} else {
			
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; disciplinas registradas") . "...</p>\n";
			
			mysql_close($dblink);
			return 0;
		}
		
		mysql_close($dblink);
		return 1;
	}

	function ExcluiDados($eliminar) {

		if (!empty($eliminar)) {
			include( "./connectdb.php" );
			foreach ($eliminar as $disid => $valor) {
				if ($valor == 'on') {
					$sql = "DELETE FROM disciplina WHERE id = '$disid'" ;
					$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				}
			}
		
			mysql_close($dblink);
			
			echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;

		}

	}

	function IncluiDados($endid, $curid, $usuid, $nome, $objetivo, $cargah, $faltas, $sigla, $datai, $dataf, $visitantef, $matricula, $titular) {
		
		if ($endid and $curid) {
			
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $datai, $regsi);
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $dataf, $regsf);
			$datai = $regsi[3] . "-" . $regsi[2] . "-" . $regsi[1];
			$dataf = $regsf[3] . "-" . $regsf[2] . "-" . $regsf[1];
				
			include( "./connectdb.php" );
			
			$sql = "INSERT INTO disciplina VALUES (null, '$usuid', '$endid', '$curid', '$nome', '$cargah', '$objetivo', '$faltas', '$sigla', '$datai', '$dataf', $visitantef, 
			$matricula, now(), $titular)";
			
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			
			$sql = "SELECT LAST_INSERT_ID() as disid";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			$disid = $linha["disid"];
			
			$RecursoDisp[0] = "Agenda";
			$RecursoDisp[1] = "Atividades";
			$RecursoDisp[2] = "Avaliacao";
			$RecursoDisp[3] = "Avisos";
			$RecursoDisp[4] = "Bibliografia";
			$RecursoDisp[5] = "ControleFrequencia";
			$RecursoDisp[6] = "FormacaoEquipes";
			$RecursoDisp[7] = "Forum";
			$RecursoDisp[8] = "Links";
			$RecursoDisp[9] = "MaterialApoio";
			$RecursoDisp[10] = "Mensagens";
			$RecursoDisp[11] = "Orientacao";
			$RecursoDisp[12] = "PlanoEnsino";
			$RecursoDisp[13] = "PlanoAula";
			$RecursoDisp[14] = "TextosResumos";
			
			for ($i=0; $i<=16; $i++) {
				$sql = "INSERT INTO menu VALUES (null, $disid, '" . $RecursoDisp[$i] . "')";
				$result = mysql_query( $sql, $dblink );
			}
			
			echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;

			mysql_close($dblink);

		} else {
			echo  "<br><div class='alert alert-danger' role='alert'><strong>" . _("Opera&ccedil;&atilde;o n&atilde;o realizada ...") . 
			_("Selecione corretamente a institui&ccedil;&atilde;o e o curso") . "</strong></div>" ;
		}
				
		return 1;
		
	}

	function AlteraDados($endid, $curid, $disid, $nome, $objetivo, $cargah, $faltas, $sigla, $datai, $dataf, $visitantef, $matricula, $titular) {
		
		if ($endid and $curid) {
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $datai, $regsi);
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $dataf, $regsf);
			$datai = $regsi[3] . "-" . $regsi[2] . "-" . $regsi[1];
			$dataf = $regsf[3] . "-" . $regsf[2] . "-" . $regsf[1];
		
			include( "./connectdb.php" );
			$sql = "UPDATE disciplina SET nome = '$nome', objetivo = '$objetivo', cargah = '$cargah', faltas = '$faltas', sigla = '$sigla', 
			datai = '$datai', dataf = '$dataf', visitante = $visitantef, matricula = $matricula, datac = now(), titular = $titular WHERE id = '$disid'";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			echo  "<br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			include( "./connectdb.php" );
			mysql_close($dblink);
		} else {
			echo  "<br><div class='alert alert-danger' role='alert'><strong>" . _("Opera&ccedil;&atilde;o n&atilde;o realizada ...") .
			_("Selecione corretamente a institui&ccedil;&atilde;o e o curso") . "</strong></div>" ;
		}
		
		return 0;

	}

	include( "cabecalho.php" );
	
	include( "menup.inc" );
?>

				<script type="text/javascript">

				$(function(){

					$('.datepicker').datepicker({
						format: '<?php echo $_SESSION["data_formato"];?>',                
		                language: 'pt-BR'
					});

					$('#Confirmar').click(function () {
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
								echo "<h4 class='modal-title' id='deleteLabel'>" . _("Notifica&ccedil;&atilde;o de exclus&atilde;o") . "</h4>";
								echo "</div>";
								echo "<div class='modal-body'>";
								echo "<p>" . _("Voc&ecirc; optou por excluir uma disciplina.") . "</p>";
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

			<?php
			
				echo "<div class='jumbotron'>";
			
				include( "connectdb.php" );
				
				$sql = "SELECT nome,email,professor FROM usuario WHERE id = '$id'" ;
			
				$result = mysql_query( $sql, $dblink );
			
				if ( mysql_num_rows($result) > 0) {
					$linha = mysql_fetch_array($result);
					if ($linha["professor"] == 1) {
						echo "<p><h2>" . _("Professor(a)") . "&nbsp;";
					} else {
						echo "<p><h2>";
					}
					echo $linha["nome"] . "</h2></p>";
				}
				
				echo "<br><h2 class='blog-post-title'><span class='glyphicon glyphicon-book' aria-hidden='true'></span>&nbsp;" . _("Disciplinas") . "</h2>";
				
				mysql_close($dblink);
				
				echo "</div>";
				
				if (empty($pAction)) {
					$pAction = "SELECT";
				}

				if ($pAction == "DELETE") {
					ExcluiDados( $eliminar );
				}
    			  				
   				if ($pAction == "INSERT" or $pAction == "INSERTED" or $pAction == "UPDATE" or $pAction == "UPDATED") {
   					
   					if ($pAction == "INSERT") {
	   					$titular = 1;
   					}
   				
   					if ($pAction == "INSERTED") {
						$inclusao = IncluiDados( $endid, $curid, $id, strip_tags($nome), strip_tags($objetivo), strip_tags($cargah), strip_tags($faltas), 
						strip_tags($sigla), strip_tags($datai), strip_tags($dataf), $visitantef, $matricula, $titular);
						if ($inclusao == 1) {
							$nome = "";
							$objetivo = "";
							$cargah = "";
							$faltas = "";
							$sigla = "";
							$dia = "";
							$mes = "";
							$ano = "";
							$diaf = "";
							$mesf = "";
							$anof = "";
							$visitantef = 0;
							$matricula = 0;
							$titular = 1;
						}
					}

					if ($pAction == "UPDATED") {
						$alteracao = AlteraDados( $endid, $curid, $disid, strip_tags($nome), strip_tags($objetivo), strip_tags($cargah), strip_tags($faltas), strip_tags($sigla), 
						strip_tags($datai), strip_tags($dataf), $visitantef, $matricula, $titular);
					}
					
   				}

   				if ($pAction == "INSERT" or $pAction == "INSERTED" or $pAction == "UPDATE") {
   					echo "<a href='#'" . CriaLink( "disciplina.php", "endid=$endid&curid=$curid" ) . ">
   					<button type='button' class='btn btn btn-default'>" . _("Disciplinas") . "</button></A><br><br>\n";
   				} else {
   					echo "<a href='#'" . CriaLink( "disciplina.php", "pAction=INSERT&endid=$endid&curid=$curid" ) . ">
   					<button type='button' class='btn btn btn-default'>" . _("Incluir novas disciplinas") . "</button></a><br><br>\n";
   				}

   				echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
   				echo "<h3 class='panel-title'>" . _("Disciplinas") . "</h3></div>";
   				echo "<div class='panel-body'>";
   				
   				echo "<form action='disciplina.php' id='instituicao' method=post>\n";
   				if ($pAction == "INSERTED") {
   					echo "<input type='hidden' name='pAction' value='INSERT'>\n";
   				} elseif ($pAction == "UPDATED") {
   					echo "<input type='hidden' name='pAction' value='SELECT'>\n";
   				} else {
	   				echo "<input type='hidden' name='pAction' value='$pAction'>\n";
   				}
   				
   				if ($pAction == "INSERT" or $pAction == "INSERTED" or $pAction == "UPDATE") {
   					echo "<p>" . _("Asterisco") . " (*) " . _("indica campo obrigat&oacute;rio.") . "</p>";
   					echo "<p>" . _("Informe 00/00/0000 em data inicial e final para disponibilizar o ambiente por tempo indeterminado.") . "</p>";
   					if ($pAction != "UPDATE") {
	   					echo "<p><label for='endid'>(*) " . _("Institui&ccedil;&atilde;o" ) . "</label>\n";
   					}
   				} else {
   					echo "<p><label for='endid'>" . _("Institui&ccedil;&atilde;o" ) . "</label>\n";
   				}
   				
   				include( "connectdb.php" );
   				
   				if ($pAction != "UPDATE") {
   				
	   				echo "<select onchange='submit()' name='endid' id='endid' class='form-control' required autofocus>\n";
	   				if ($id != "admin") {
	   					$sql = "SELECT e.id, e.nome, e.projeto FROM usuend ue INNER JOIN enderecos e ON ue.endid = e.id WHERE ue.usuid = '$id' ORDER BY 2";
	   				} else {
	   					$sql = "SELECT e.id, e.nome, e.projeto FROM enderecos e ORDER BY 2";
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
   					
   					if (!empty($endid)) {
   						$sql = "SELECT e.projeto FROM enderecos e WHERE id = '$endid'";
   						$result = mysql_query( $sql, $dblink ) or die(mysql_error());
   						$linha = mysql_fetch_array($result);
   						if (!empty($linha["projeto"])) {
	   						echo "<strong><em>" . _("Projeto did&aacute;tico pedag&oacute;gico institucional") . ": </strong>" . $linha["projeto"] . "</em><br><br>";
   						}
   					}
   					
  					echo "<form action='disciplina.php' id='curso' method=post>\n";
   					if ($pAction == "INSERTED") {
   						echo "<input type='hidden' name='pAction' value='INSERT'>\n";
   					} elseif ($pAction == "UPDATED") {
   						echo "<input type='hidden' name='pAction' value='SELECT'>\n";
   					} else {
	   					echo "<input type='hidden' name='pAction' value='$pAction'>\n";
   					}
   					echo "<input type='hidden' name='endid' value='$endid'>\n";
   					if ($pAction == "INSERT" or $pAction == "INSERTED" or $pAction == "UPDATE") {
		   				echo "<p><label for='curid'>(*) " . _("Curso") . "</label>\n";
   					} else {
   						echo "<p><label for='curid'>" . _("Curso") . "</label>\n";
   					}
   					echo "<select onchange='submit()' name='curid' id='curid' class='form-control' required autofocus>\n";
   					if ($id != "admin") {
	   					$sql = "SELECT DISTINCT c.id, c.nome, c.projeto FROM usucur uc INNER JOIN curso c ON uc.curid = c.id WHERE uc.usuid = '$id' AND c.endid = '$endid' ORDER BY 2";
   					} else {
   						$sql = "SELECT DISTINCT c.id, c.nome, c.projeto FROM curso c WHERE c.endid = '$endid' ORDER BY 2";
   					}
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
   					echo "</select></p>";
   					echo "</form>";
   					
   					if (!empty($curid)) {
   						$sql = "SELECT c.projeto FROM curso c WHERE id = '$curid'";
   						$result = mysql_query( $sql, $dblink ) or die(mysql_error());
   						$linha = mysql_fetch_array($result);
   						if (!empty($linha["projeto"])) {
   							echo "<strong><em>" . _("Projeto pedag&oacute;gico de curso") . ": </strong>" . $linha["projeto"] . "</em><br><br>";
   						}
   					}

   				} else {
   					
   					include( "connectdb.php" );
   					$sql = "SELECT e.nome as nomee, e.projeto as projetoe, c.nome as nomec, c.projeto as projetoc FROM 
   					disciplina d INNER JOIN curso c ON c.id = d.curid INNER JOIN enderecos e ON e.id = d.endid WHERE c.id = '$curid'";
   					$result = mysql_query( $sql, $dblink ) or die(mysql_error());
   					$linha = mysql_fetch_array($result);
   					echo "<h2>" . $linha["nomee"] . "</h2><br>";
   					if (!empty($linha["projetoe"])) {
   						echo "<strong><em>" . _("Projeto did&aacute;tico pedag&oacute;gico institucional") . ": </strong>" . $linha["projetoe"] . "</em><br><br>";
   					}
   					echo "<h3>" . $linha["nomec"] . "</h3><br>";
   					if (!empty($linha["projetoc"])) {
   						echo "<strong><em>" . _("Projeto pedag&oacute;gico de curso") . ": </strong>" . $linha["projetoc"] . "</em><br><br>";
   					}
   					
   				}

				if ($pAction == "INSERT" or $pAction == "INSERTED" or $pAction == "UPDATE" or $pAction == "UPDATED") {					

					if ($pAction == "UPDATE" or $alteracao == 1) {
						echo "<br><form action='disciplina.php' method='POST'>\n";
						echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
						echo "<input type='hidden' name='disid' value='$disid'>\n";
						echo "<input type='hidden' name='curid' value='$curid'>\n";
						echo "<input type='hidden' name='endid' value='$endid'>\n";
						$sql = "SELECT nome, faltas, objetivo, id, cargah, sigla, datai, dataf, visitante, matricula, titular FROM disciplina WHERE id = '$disid'";
						$result = mysql_query( $sql, $dblink ) or die(mysql_error());
						if ( mysql_num_rows($result) > 0) {
							$linha = mysql_fetch_array($result);
							$nome = $linha["nome"];
							$objetivo = $linha["objetivo"];
							$cargah = $linha["cargah"];
							$faltas = $linha["faltas"];
							$sigla = $linha["sigla"];
							$visitantef = $linha["visitante"];
							$matricula = $linha["matricula"];
							$titular = $linha["titular"];
							ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $linha["datai"], $regs);
							$datai = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
							ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $linha["dataf"], $regs);
							$dataf = $regs[3] . "/" . $regs[2] . "/" . $regs[1];
						}
					} else {
						echo "<form action='disciplina.php' method='POST'>\n" ;
						echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
						echo "<input type='hidden' name='endid' value='$endid'>\n";
						echo "<input type='hidden' name='curid' value='$curid'>\n";
					}
					if ($pAction == "INSERT" or $pAction == "INSERTED" or $pAction == "UPDATE" or ($pAction == "UPDATED" and $alteracao == 1)) {
						echo "<p><label for='nome'>(*) Nome</label>\n";
						echo "<input type='text' id='nome' name='nome' value='$nome' size=60 maxlength=60 class='form-control' required autofocus></p>\n";
						echo "<p><label for='cargah'>(*) Carga Hor&aacute;ria</label>\n";
						echo "<input type='text' name='cargah' value='$cargah' size=6 maxlength=6 class='form-control' required autofocus></p>\n";
						echo "<p><label for='faltas'>Faltas Poss&iacute;veis</label>\n";
						echo "<input type='text' name='faltas' value='$faltas' size=6 maxlength=6 class='form-control' autofocus></p>";
						echo "<p><label for='datai'>Data Inicial</label>\n";
						echo "<input type='text' name='datai' id='datai' value='$datai' size=10 maxlength=10 class='form-control datepicker' autofocus></p>\n";
						echo "<p><label for='dataf'>Data Final</label>\n";
						echo "<input type='text' name='dataf' id='dataf' value='$dataf' size=10 maxlength=10 class='form-control datepicker' autofocus></p>\n";
						echo "<p><label for='titular'>(*) Voc&ecirc; &eacute; professor da disciplina?</label>\n";
						echo "<select name='titular' class='form-control' required autofocus>";
						echo "<option value='1'";
						if ($titular) {
							echo " selected";
						}
						echo ">Sim</option>\n";
						echo "<option value='0'";
						if (!$titular) {
							echo " selected";
						}
						echo ">N&atilde;o</option>\n";
						echo "</select></p>\n";
						echo "<p><label for='visitante'>(*) Visitantes</label>\n";
						echo "<select name='visitantef' class='form-control' required autofocus>";
						echo "<option value='0'";
						if (!$visitantef) {
							echo " selected";
						}
						echo ">Fechado a visitantes</option>\n";
						echo "<option value='1'";
						if ($visitantef) {
							echo " selected";
						}
						echo ">Aberto a visitantes</option>\n";
						echo "</select></p>\n";
						echo "<p><label for='matricula'>(*) Matr&iacute;culas</label>\n";
						echo "<select name='matricula' class='form-control' required autofocus>";
						echo "<option value='0'";
						if (!$matricula) {
							echo " selected";
						}
						echo ">Fechado a matr&iacute;culas externas</option>\n";
						echo "<option value='1'";
						if ($matricula) {
							echo " selected";
						}
						echo ">Aberto a matr&iacute;culas externas</option>\n";
						echo "</select>\n";
						echo "<p><label for='objetivo'>(*) Objetivos</label></p>\n";
						echo "<textarea name='objetivo' cols='70' rows='20' class='form-control' required autofocus>$objetivo</textarea>\n";
						echo "<p><input class='btn btn-default' type='submit' value'" . _("Enviar") . "'></form></p>\n";
					}
				}
				
				mysql_close($dblink);
				
				if ($pAction == "DELETE" or ($pAction == "UPDATED" and $alteracao == 0) or $pAction == "SELECT") {

					$dados = ListaDados($endid, $curid, $id, $selall, $pAction, $tipo);
					
					if ($dados == 1) {
						echo "<table><tr><td>";
						echo "<input class='btn btn-danger' type='submit' value='" . _("Excluir") . "'></form></td>";
						echo "<td><form action='disciplina.php' id='selall' method='POST'>\n";
						echo "<input type='hidden' name='pAction' value='$pAction'>\n
						<input type='hidden' name='endid' value='$endid'>\n
						<input type='hidden' name='curid' value='$curid'>\n
						<input type='hidden' name='selall' value='1'>\n";
						echo "<input class='btn btn-default' type='submit' value='" . _("Marcar todos") . "'></form></td>";
						echo "<td><form action='disciplina.php' id='selall' method='POST'>\n";
						echo "<input type='hidden' name='pAction' value='$pAction'>\n
						<input type='hidden' name='endid' value='$endid'>\n
						<input type='hidden' name='curid' value='$curid'>\n
						<input type='hidden' name='selall' value='0'>\n";
						echo "<input class='btn btn-default' type='submit' value='" . _("Desmarcar todos") . "'></form></td></tr></table><br>";
					}
				}
				
				echo "</div></div>";
				
				include 'rodape.inc';
				
				?>
