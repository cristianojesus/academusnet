<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];

	if ($tipo == 0) {
		$pAction = "LIST";
	}
	
	function ListaDados($selall, $usuid, $disid, $pAction, $tipo, $assid) {
		
		if ($pAction == "SELECT_LIST" or $pAction == "GET") {
			echo "<a href='#' onClick='abrirPag(" . '"material.php", "pAction=LIST&disid=' . $disid . '"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Material dispon&iacute;vel") . "</button></A><br><br>\n";
		} elseif ($pAction == "LIST") {
			if ($tipo == 1) {
				echo "<a href='#' onClick='abrirPag(" . '"material.php", "pAction=GET&disid=' . $disid . '"' . ")'>
				<button type='button' class='btn btn btn-default'>" . _("Associar novas refer&ecirc;ncias") . "</button></a>\n";
				echo "<a href='#' onClick='abrirPag(" . '"material.php", "pAction=INSERT"' . ")'>
				<button type='button' class='btn btn btn-default'>" . _("Incluir material") . "</button></A>\n";
			}
		} else {
			echo "<a href='#' onClick='abrirPag(" . '"material.php", "pAction=INSERT"' . ")'>
			<button type='button' class='btn btn btn-default'>" . _("Incluir material") . "</button></A>\n";
		}
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Material de Apoio") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		include("./connectdb.php");

		if ($pAction == "LIST") {
			$sql = "SELECT m.id, m.assid, m.texto, m.link, m.tamanho, m.tipo, a.descricao, dm.disid, m.arquivo FROM material m LEFT JOIN assunto a ON m.assid = a.id 
			INNER JOIN dismat dm ON dm.matid = m.id WHERE dm.disid = '$disid'";
		} elseif ($pAction == "GET") {
			$sql = "SELECT m.id, m.assid, m.texto, m.link, m.tamanho, m.tipo, a.descricao, m.arquivo FROM material m LEFT JOIN assunto a ON m.assid = a.id 
			WHERE m.usuid = '$usuid' AND m.id NOT IN (SELECT matid FROM dismat WHERE disid = '$disid')";
		} else {
			$sql = "SELECT DISTINCT m.id, m.assid, m.texto, m.link, m.tamanho, m.tipo, a.descricao, u.cota, m.arquivo FROM material m LEFT JOIN assunto a ON m.assid = a.id 
			INNER JOIN usuario u ON m.usuid = u.id WHERE m.usuid = '$usuid'";
		}
		
		if (!empty($assid) and $assid != 0) {
			$sql .= " AND m.assid = '$assid'";
		}
		
		$sql .= " ORDER BY 7, 3";
		
		$result = mysql_query($sql, $dblink ) or die(mysql_error());

		if (mysql_num_rows($result) > 0) {
			
			if ($tipo == 1) {
			
				echo "<form action='material.php' method='POST'>\n";
				echo "<p><label for='assunto'>" . _("Assunto") . "</label>\n";
				echo "<select onchange='submit()' name='assid' class='form-control'>";
				echo "<option value='0' selected>" . _("Todos") . "</option>\n";
				
				$sql = "SELECT id, descricao FROM assunto WHERE usuid = '$usuid' ORDER BY 2";
				$resulta = mysql_query($sql, $dblink) or die(mysql_error());
				
				if (mysql_num_rows($resulta) > 0) {
						
					while($linha = mysql_fetch_array($resulta)) {
						echo "<option value='" . $linha["id"];
						if ($linha["id"] == $assid) {
							echo "' selected>";
						} else {
							echo "'>";
						}
						echo $linha["descricao"] . "</option>\n";
					}
				}
				
				echo "</select>\n";
				echo "<input type='hidden' name='pAction' value='$pAction'>\n";
				echo "<input type='hidden' name='disid' value='$disid'></p></form>\n";
	
				echo "<br><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;" . _("Altera dados do material") . "\n" ;
				
			}
				
			if ($pAction != "SELECT_LIST" and $pAction != "GET" and $pAction != "LIST") {
				echo "<br><span class='glyphicon glyphicon-list' aria-hidden='true'></span>&nbsp;" . _("Lista disciplinas que usam o material") . "\n" ;
			}
			
			if ($pAction == "LIST") {
				echo "<form action='material.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='MISS'>\n";
			} elseif ($pAction == "GET") {
				echo "<form action='material.php' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='GOTTEN'>\n";
			} else {
				echo "<form action='material.php' id='deleteForm' name='deleteForm' method='POST'>\n";
				echo "<input type='hidden' name='pAction' value='DELETE'>\n";
			}			
			
			echo "<br><table class='table'><thread><tr>\n" ;
			if ($tipo == 1) echo "<th width='5%'></th><th width='5%'></th>";
			echo "<th>Nome</th>" /*<th align='center'>Tamanho (MB)</th><th>Tipo</th>"*/;
			if ($tipo == 1) echo "<th width='20%'>Assunto</th></tr></thread><tbody>\n";
			
			$tamanho_total = 0;

			while ($linha = mysql_fetch_array($result)) {
				echo "<tr>";
				if ($tipo == 1) {
					echo "<td align='right' nowrap>\n";
					if ( empty( $selall ) ) {
						echo "<input type='checkbox' name='eliassdes[" . $linha["id"] . "]'>\n";
					} else {
						echo "<input type='checkbox' name='eliassdes[" . $linha["id"] . "]' CHECKED\n>";
					}
					echo "</td><td align='left' nowrap><a href='#' onClick='abrirPag(" . '"material.php", "pAction=UPDATE&pActionDest=' . $pAction .
					'&matid=' . $linha["id"] . '")' . "'>\n";
					echo "<span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>\n";
					if (empty($pAction)) {
						$sql = "SELECT * FROM dismat WHERE matid = '" . $linha["id"] . "'";
						$resultdm = mysql_query($sql, $dblink ) or die(mysql_error());
						$sql = "SELECT * FROM planmat WHERE matid = '" . $linha["id"] . "'";
						$resultpm = mysql_query($sql, $dblink ) or die(mysql_error());
						if (mysql_num_rows($resultdm) > 0 or mysql_num_rows($resultpm) > 0) {
							echo "<a href='#' onClick='abrirPag(" . '"material.php", "pAction=SELECT&matid=' . $linha["id"] . '")' . "'>
							<span class='glyphicon glyphicon-list' aria-hidden='true'></span></a>";
						}	
					}
					echo "</td>";
				}
				//if (!empty($linha["link"])) {
					echo "<td><a href='" . $linha["link"] . "' target='_blank'>";
				//} else {
				//	echo "<td><a href='arquivos/" . $linha["arquivo"] . "' target='_blank'>";
				//}
				echo $linha["texto"];
				//if (!empty($linha["link"])) {
					echo "</a></td>"; /*<td align='center'>---</td><td>Link</td>*/
				//} else {
				//	echo "</a><td align='center'>" . number_format($linha["tamanho"] / 1024000, 2,',','.') . "</td>\n";
				//	echo "<td>" . $linha["tipo"] . "</td>\n";
				//}
				if ($tipo == 1) {
					if (!empty($linha["descricao"])) {
						echo "<td>" . $linha["descricao"] . "</td></tr>\n";
					} else {
						echo "<td>---</td></tr>\n";
					}
				} else {
					echo "</tr>\n";
				}
				$tamanho_total += $linha["tamanho"];
				$cota = $linha["cota"];
			}
			
			echo "</tbody></table>\n";

			//if (empty($pAction) and empty($assunto_nome)) {
			//	echo "<br>Cota dispon&iacute;vel: " . number_format($cota / 1024000,2,',','.') . " MB";
			//	echo "<br>Espa&ccedil;o total utilizado: " . number_format($tamanho_total / 1024000,2,',','.') . " MB (" . 
			//	number_format($tamanho_total / $cota * 100,2) . "%)<br><br>";
			//}
			
		} else {
			echo "<p class='lead'>" . _("N&atilde;o h&aacute; material registrado") . "...</p>\n";
			mysql_close($dblink);
			echo "</div></div>";
			return;
		}
		
		mysql_close($dblink);
		
		if ($tipo == 1) {
			echo "<table><tr valign='top'>\n" ;
			if ($pAction == "LIST") {
				echo "<td><button type='submit' class='btn btn-default' name='enviar'>" . _("Desassociar") . "</button></form></td>\n";
			} elseif ($pAction == "GET") {
				echo "<td><button type='submit' class='btn btn-default' name='enviar'>" . _("Associar") . "</button></form></td>\n";
			} else {
				echo "<td><button type='submit' class='btn btn-danger' name='enviar'>" . _("Excluir") . "</button></form></td>\n";
			}

			echo  "<td><form id='selall' action='eaddis.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='$pAction'>\n";
			echo "<input type='hidden' name='selall' value='1'>\n";
			echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Marcar todos") . "</button>\n";
			echo  "</form></td>\n" ;
			echo  "<td><form id='selall' action='eaddis.php' method='POST'>\n" ;
			echo "<input type='hidden' name='pAction' value='$pAction'>\n";
			echo "<input type='hidden' name='selall' value='0'>\n";
			echo "<button type='submit' class='btn btn-default' name='selecionar'>" . _("Desmarcar todos") . "</button>\n";
			echo  "</form></td></tr>\n" ;
			echo "</table>\n";
		}
		
		echo "</div></div>";

		return;
	
	}
	
	function ListaRelacoes($matid) {
		
		include 'connectdb.php';

		$sql = "SELECT texto FROM material WHERE id = '$matid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		echo "<br><h3>" . $linha["texto"] . "</h3><br>";
		echo "<br><a href='material.php'><button type='button' class='btn btn btn-default'>" . _("Material dispon&iacute;vel") . "</button></A><br><br>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Material de Apoio") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo "<table class='table'><thread><tr>\n" ;
		echo "<th>Disciplinas associadas ao material</th></tr></thread><tbody>\n";	

		$sql = "SELECT d.id, d.nome FROM dismat dm INNER JOIN disciplina d ON d.id = dm.disid WHERE dm.matid = '$matid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		$i = 0;

		if (mysql_num_rows($result) > 0) {
			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td><a href='#' onClick='abrirPag(" . '"detplanprof.php", "&disid=' . 
				$linha["id"] . '"' . ")'>" . $linha["nome"] . "</a></td></tr>\n";
			}
			$i = 1;
		}
		
		$sql = "SELECT d.id, d.nome, DATE_FORMAT(p.data, '%d/%m/%Y') as data FROM planmat pm INNER JOIN plano p ON p.id = pm.planid INNER JOIN disciplina d ON d.id = p.disid 
		WHERE pm.matid = '$matid'";
		$result = mysql_query($sql, $dblink ) or die(mysql_error());
		if (mysql_num_rows($result) > 0) {
			while ($linha = mysql_fetch_array($result)) {
				echo "<tr><td><a href='#' onClick='abrirPag(" . '"detplanprof.php", "&disid=' . 
				$linha["id"] . '"' . ")'>" . $linha["nome"] . " (aula de " . $linha["data"] . ")</a></td></tr>\n";
			}
			$i = 1;
		}

		if ($i == 0) {
			echo "</tbody></table><br>N&atilde;o h&aacute; disciplinas associadas ...\n";
		}
		
		echo "</tbody></table>\n";
		
		echo "</div></div>";
		
		mysql_close($dblink);
		
		return;
	
	}
	
	function retira_acentos( $string ) {
		$string = ereg_replace("[^a-zA-Z0-9_.]", "", strtr($string, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ .", "aaaaeeiooouucAAAAEEIOOOUUC_."));
		return $string; // Retorna a String transformada
	}
	
	function ExcluiDados($eliassdes) {
		if (!empty($eliassdes)) {
			include( "./connectdb.php" );
			foreach ($eliassdes as $matid => $valor) {	
				if ($valor == 'on') {
					$sql = "SELECT texto, link FROM material WHERE id = $matid";
					$result = mysql_query($sql, $dblink) or die(mysql_error());
					if ( mysql_num_rows($result) > 0) {
						$linha = mysql_fetch_array($result);
						if (empty($linha["link"])) {
							$arquivo = "arquivos/" . $linha["texto"];
							unlink($arquivo);
						}
						$sql = "DELETE FROM material WHERE id = '$matid'" ;
						mysql_query( $sql, $dblink );
					}
				}
			}
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
			mysql_close($dblink);
		}
		return;
	}
	
	function IncluiDados($usuid, $assid, $texto_name, $texto, $link, $obs, $assunto, $texto_size, $texto_type, $DOCUMENT_ROOT, $texto_temp, $disid) {
		
		include( "./connectdb.php" );
		
		/*$sql = "SELECT cota FROM usuario WHERE id = '$usuid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		$cota = $linha["cota"];
		
		$sql = "SELECT sum(tamanho) as tam FROM material WHERE usuid = '$usuid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		if (($texto_size + $linha["tam"]) > $cota) {
			echo  "<br><br><span id='textErr'><img src='images/error.png' width=16 height=16 border=0> Cota de armazenamento esgotada ...</span><br><br>\n" ;
			mysql_close($dblink);
			return;
		}*/
		
		if ($assid != "0" and $assid == "NA") {
			$sql = "INSERT INTO assunto VALUES (null, '$assunto', '$usuid')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$sql = "SELECT LAST_INSERT_ID() as assid";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			$assid = $linha["assid"];
		}
		if ($assid == "0" or empty($assid)) {
			$assid = 'null';
		}
		
		$data = date("Y") . "-" . date("m") . "-" . date("d");
		
		/*if (empty($link)) {
			$arquivo_new = rand() . "_" . date("YmdHis") . "_" . retira_acentos($texto_name);
			$arquivo_copy = "arquivos/" . $arquivo_new;
			
			if (!copy($texto_temp, $arquivo_copy)) {
				 echo  "<br><br><span id='textErr'><img src='images/error.png' width=16 height=16 border=0>Falha na c&oacute;pia do arquivo ...</span><br><br>\n" ;
				 mysql_close($dblink);
				 return;
			} else {
				$sql = "INSERT INTO material VALUES (null, '$texto_name', '$data', 0, '$obs', '$texto_size', '$texto_type', '$usuid', $assid, null, '$arquivo_new')";
			}
		} else {*/
			$sql = "INSERT INTO material VALUES (null, '$texto', '$data', 0, '$obs', 0, null, '$usuid', $assid, '$link', null)";
		//}
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		
		$sql = "SELECT LAST_INSERT_ID() as matid";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		$matid = $linha["matid"];
		
		$sql = "INSERT INTO dismat VALUES ('$disid', '$matid')";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		mysql_close($dblink);
		return;
	}
	
	function AlteraDados($assid, $texto, $link, $obs, $assunto, $usuid, $matid) {
		
		include( "./connectdb.php" );
		
		if ($assid != "0" and $assid == "NA") {
			$sql = "INSERT INTO assunto VALUES (null, '$assunto', '$usuid')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$sql = "SELECT LAST_INSERT_ID() as assid";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($result);
			$assid = $linha["assid"];
		}
		if ($assid == "0" or empty($assid)) {
			$assid = 'null';
		}
	
		$sql = "UPDATE material SET assid = $assid, texto = '$texto', link = '$link', obs = '$obs' WHERE id = '$matid'";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		mysql_close($dblink);
		return;
	}
	
	function Associar($eliassdes, $disid) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $matid => $valor) {	
			if ($valor == 'on') {
				$sql = "INSERT INTO dismat VALUES ('$disid', '$matid')";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div><br>";
		}
		mysql_close($dblink);
		return;
	}

	function Desassociar($eliassdes, $disid) {
		include( "./connectdb.php" );
		foreach ($eliassdes as $matid => $valor) {	
			if ($valor == 'on') {
				$sql = "DELETE FROM dismat WHERE matid = '$matid' AND disid = '$disid'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
		}
		if (!empty($sql)) {
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div><br>";
		} else {
			echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Selecione ao menos uma refer&ecirc;ncia ...") . "</strong></div><br>";
		}
		mysql_close($dblink);
		return;
	}
	
	function Formulario($matid, $assid, $texto, $link, $obs, $usuid, $tipoa, $assunto, $pActionDest) {
		
		include( "./connectdb.php" );
		
		echo "<a href='#' onClick='abrirPag(" . '"material.php", "pAction=' . $pActionDest . '"' . ")'><button type='button' class='btn btn btn-default'>" . 
		_("Material de Apoio") . "</button></A>\n";
		
		echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
		echo "<h3 class='panel-title'>" . _("Material de Apoio") . "</h3></div>";
		echo "<div class='panel-body'>";
		
		echo _("Asterisco (*) indica campo obrigat&oacute;rio") . "<br><br>";
		
		echo "<p><label for='assunto'>" . _("Assunto") . "</label>\n";
		echo "<select onchange='submit()' name='assid' class='form-control'>";
		echo "<option value='0'";
		if ($assid == "0") {
			echo " selected";
		}
		echo ">---</option>\n";
		echo "<option value='NA'";
		if ($assid == "NA") {
			echo " selected";
		}
		echo ">" . _("Novo assunto") . "</option>\n";
			
		$sql = "SELECT id, descricao FROM assunto WHERE usuid = '$usuid' ORDER BY 2";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		if (mysql_num_rows($result) > 0) {
			while($linha = mysql_fetch_array($result)) {
				echo "<option value='" . $linha["id"];
				if ($linha["id"] == $assid) {	
					echo "' selected>";
				} else {
					echo "'>";
				}
				echo $linha["descricao"] . "</option>\n";
			}
		}
		echo "</select>\n";		
					
		if ($assid == "NA") {
			echo "<br><input type='text' name='assunto' value='$assunto' size='60' maxlength='69' class='form-control'></p>\n";
		}
		
		if (empty($tipoa)) {
			if (!empty($link)) {
				$tipoa = "L";
			} elseif (!empty($matid)) {
				$tipoa = "A";
			} else {
				$tipoa = "L";
			}
		}
		
		/*echo "<p><label for='tipoa'>" . _("Tipo") . "</label>\n";
		
		if (!empty($matid)) {
			echo "<input type='hidden' name='tipoa' value='$tipoa'>\n";
			if ($tipoa == "A") {
				echo "Arquivo";
			} else {
				echo "Link";
			}
		} else {
			echo "<select onchange='submit()' name='tipoa' >";
			echo "<option value='A'";
			if ($tipoa == "A") {
				echo " selected";
			}
			echo ">Arquivo</option>\n";
			echo "<option value='L'";
			if ($tipoa == "L") {
				echo " selected";
			}
			echo ">Link</option>\n";
			echo "</select>\n";
		}
		
		if ($tipoa == "A") {
			echo "<p><label for='texto'>*Nome</label>\n";
			if (empty($matid)) {
				echo "<input type='file' id='texto' name='texto' size='60' maxlength='200' value='$texto' class='ui-widget-content' /></p>\n";
			} else {
				$sql = "SELECT texto FROM material WHERE id = '$matid'";
				$result = mysql_query($sql, $dblink) or die(mysql_error());
				$linha = mysql_fetch_array($result);
				echo $linha["texto"] . "</p>";
				echo "<input type='hidden' name='texto' value='" . $linha["texto"] . "'>\n";
			}
		} elseif ($tipoa == "L") {*/
			echo "<p><label for='texto'>(*) " . _("Nome") . "</label>\n";
			echo "<input type='text' id='texto' name='texto' size='60' maxlength='200' value='$texto' class='form-control' required autofocus/></p>\n";
			echo "<p><label for='link'> (*) Link</label>\n";
			echo "<input type='text' id='link' name='link' size='60' maxlength='200' value='$link' class='form-control' required autofocus/></p>\n";
		//}

		mysql_close($dblink);

		echo "<p><label for='obs'>" . _("Observa&ccedil;&otilde;es") . "</label><br>\n";
		echo "<textarea id='obs' name='obs' cols='70' rows='20' class='form-control' required autofocus/>$obs</textarea></p>\n";
		echo "<input type='submit' name='enviar' class='btn btn btn-default' value='" . _("Enviar") . "'></form><br>\n";
		
		echo "</div></div>";

	}
	
	include( "cabecalho.php" );
	
	if (!empty($disid)) {
		include( "menu.inc" );
	} else {
		include( "menup.inc" );
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
						echo "<p>" . _("Voc&ecirc; optou por excluir material de apoio.") . "</p>";
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
		
	echo "<br><h2 class='blog-post-title'><span class='glyphicon glyphicon-file' aria-hidden='true'></span>&nbsp;" . _("Material de Apoio") . "</h2>";
		
	echo "</div>";
	
	if ($pAction == "DELETE") {
		ExcluiDados($eliassdes);
		ListaDados($selall, $id, $disid, $pActionDest, $tipo, $assid);
	} elseif ($pAction == "INSERT") {
		echo "<form action='material.php' enctype='multipart/form-data' method='POST'>\n" ;
		echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
		Formulario(null, null, null, null, null, $id, null, null, null);
	} elseif ($pAction == "UPDATE") {
		echo "<form action='material.php' method='POST'>\n" ;
		echo "<input type='hidden' name='pActionDest' value='$pActionDest'>\n";
		echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
		echo "<input type='hidden' name='matid' value='$matid'>\n";
		include 'connectdb.php';		
		$sql = "SELECT assid, texto, link, data, download, obs, tamanho FROM material WHERE id='$matid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
		$linha = mysql_fetch_array($result);
		if (empty($linha["link"])) {
			$tipoa = "A";
		} else {
			$tipoa = "L";
		}
		mysql_close($dblink);
		Formulario($matid, $linha["assid"], $linha["texto"], $linha["link"], $linha["obs"], $id, $tipoa, null, $pActionDest);
	} elseif ($pAction == "INSERTED" or $pAction == "UPDATED") {
		if (!empty($enviar)) {
			if ($pAction == "UPDATED") {
				AlteraDados($assid, $texto, $link, $obs, $assunto, $id, $matid);
				ListaDados($selall, $id, $disid, $pActionDest, $tipo, $assid);
			} else {
				//if (empty($link)) {
				//	IncluiDados($id, $assid, $_FILES["texto"]["name"], $texto, $link, $obs, $assunto, $_FILES["texto"]["size"], $_FILES["texto"]["type"], 
				//	$DOCUMENT_ROOT, $_FILES["texto"]["tmp_name"]);
				//} else {				
				IncluiDados($id, $assid, null, $texto, $link, $obs, $assunto, null, null, null, null, $disid);
				//}
				echo "<form action='material.php' class='cmxform' id='material' method='POST'>\n" ;
				echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
				Formulario(null, null, null, null, null, $id, null, null, null);
			}
		} else {
			if ($pAction == "INSERTED") {
				echo "<form action='material.php' class='cmxform' id='material' enctype='multipart/form-data' method='POST'>\n" ;
				echo "<input type='hidden' name='pAction' value='INSERTED'>\n";
				Formulario(null, $assid, $texto, $link, $obs, $id, $tipoa, $assunto, null);
			} else {
				echo "<form action='material.php' class='cmxform' id='material' method='POST'>\n" ;
				echo "<input type='hidden' name='pAction' value='UPDATED'>\n";
				echo "<input type='hidden' name='matid' value='$matid'>\n";
				Formulario($matid, $assid, $texto, $link, $obs, $id, $tipoa, $assunto, null);
			}

		}
	} elseif ($pAction == "MISS") {
		Desassociar($eliassdes, $disid);
		ListaDados($selall, $id, $disid, "LIST", $tipo, $assid);
	} elseif ($pAction == "GOTTEN") {
		Associar($eliassdes, $disid);
		ListaDados($selall, $id, $disid, "GET", $tipo, $assid);
	} elseif ($pAction == "SELECT") {
		ListaRelacoes($matid);
	} elseif (empty($pAction) or $pAction == "GET" or $pAction == "LIST") {
		ListaDados($selall, $id, $disid, $pAction, $tipo, $assid);
	}
	
	include 'rodape.inc';

?>
