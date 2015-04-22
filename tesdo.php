<?php
	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	include( "./connectdb.php" );
	$sql = "SELECT ue.ra FROM usuend ue INNER JOIN disciplina d ON (ue.endid = d.endid) WHERE d.id = '$disid' AND ue.usuid = '$id'";
	$query =  mysql_query ($sql) or die(mysql_error());
	if (mysql_num_rows($query) > 0) {
		$linha = mysql_fetch_array($query);
		$ra = $linha["ra"];
	}
	mysql_close($dblink);
	
	function EnviaEmail($tesid, $aluid, $equid) {

		include( "./connectdb.php" );

		$sql = "SELECT texto, data FROM teste WHERE id = '$tesid'";
		$result = mysql_query( $sql, $dblink );

		if ( mysql_num_rows($result) > 0) {
			$linha = mysql_fetch_array($result);
			$mensagem = $linha["texto"] . " - " . gettext("Data Limite para Desenvolvimento") . " " . $linha["data"] . "\n\n";
		}

		if (empty($equid) or $equid == "null") {
			$sql = "SELECT a.nome, u.email FROM aluno a, usuario u WHERE u.ra = a.id AND a.id = '$aluid'" ;
		} else {
			$sql = "SELECT a.nome, u.email FROM equialu ea, aluno a, usuario u WHERE a.id = ea.aluid AND u.ra = a.id AND a.id = '$aluid'";
		}
		
		$result = mysql_query( $sql, $dblink );

		if  (mysql_num_rows($result) > 0) {

			while ($linhae = mysql_fetch_array($result)) {
			
				$email = $linhae["email"];
				$mensagem = $mensagem . $linhae["nome"] . "\n";

				$sql = "SELECT * FROM tesque WHERE tesid = '$tesid' ORDER BY texto" ;
				$result = mysql_query( $sql, $dblink );

				if ( mysql_num_rows($result) > 0) {

					while ($linha = mysql_fetch_array($result)) {

						$mensagem = $mensagem . "\n*" . $linha["texto"];
						$mensagem = $mensagem . "\n\n" . gettext("Valor da questao") . ": " . $linha["valor"];

						if ( $linha["tipo"] == 'D' ) {

							if ( empty($equid) or $equid == "null" ) {
								$sql = "SELECT * FROM descritiva WHERE tesid = $tesid AND aluid = '$aluid' AND queid = " . $linha["id"];
							} else {
								$sql = "SELECT * FROM descritiva WHERE tesid = $tesid AND equid = '$equid' AND queid = " . $linha["id"];
							}
							
							$resultd = mysql_query( $sql, $dblink );
							$linhad = mysql_fetch_array($resultd);

							if ( mysql_num_rows($resultd) > 0) {
								$mensagem = $mensagem . "\n\n" . gettext("Resposta") . ":\n\n" . $linhad["texto"] . "\n\n";
							} else {
								$mensagem = $mensagem . "\n---\n\n";
							}

						} else {

							$sql = "SELECT * FROM opcoes WHERE queid = " . $linha["id"] ;
							
							$resulta = mysql_query( $sql, $dblink );

							if ( mysql_num_rows($resulta) > 0) {
								$mensagem += "\n";
								while ($linhaa = 	mysql_fetch_array($resulta)) {
									if ($linhaa["resposta"] == 0) {
										$mensagem = $mensagem . "\n" . $linhaa["texto"];
									} else {
										$mensagem = $mensagem . "\n" . $linhaa["texto"] . " (opcoes correta)";
									}
								}

								if (empty($equid) or $equid == "null") {
									$sql = "SELECT correcao.*, opcoes.texto FROM correcao, opcoes WHERE correcao.tesid = $tesid
									 AND correcao.aluid = '$aluid' AND correcao.altid = opcoes.id AND correcao.queid = " . $linha["id"];
								} else {
									$sql = "SELECT correcao.*, opcoes.texto FROM correcao, opcoes WHERE correcao.tesid = $tesid 
									AND  correcao.equid = '$equid' AND correcao.altid = opcoes.id AND correcao.queid = " . $linha["id"];
								}
								
								$resultd = mysql_query( $sql, $dblink );
								$linhad = mysql_fetch_array($resultd);

								if ( mysql_num_rows($resultd) > 0) {
									$mensagem = $mensagem . "\n\n" . gettext("Resposta") . ":\n\n" . $linhad["texto"] . "\n\n";
								} else {
									$mensagem = $mensagem . "\n---\n\n";
								}
							}
						}
					}
				}
				mail($email,gettext("Exercicio Academusnet"),$mensagem,"Content-type:text;\nFrom: suporte@academusnet.pro.br\n");
			}
		}
		mysql_close($dblink);
	}
	
	function GravaArquivo($queid, $tesid, $ra, $delarq, $arquivo, $arquivo_name, $arquivo_size, $tipo, $equid) {

		include( "connectdb.php" );
		
		if (empty($equid) or $equid == "null") {
			$equid = "null";
			$aluid = $ra;
		} else {
			$aluid = "null";
		}

		if (!empty($delarq)) {
			
			$sql = "SELECT arquivo FROM testearq WHERE queid = '$queid' AND tesid = '$tesid' AND (aluid = '$aluid' OR equid = '$equid')";
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());

			if ( mysql_num_rows($result) > 0) {

				$linha = mysql_fetch_array($result);

				$arquivo = "arquivos/" . $linha["arquivo"];
				
				if (!unlink($arquivo)) {
					echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Falha na remo&ccedil;&atilde;o do arquivo ...") . "</strong></div>" ;
					return 0;
				} else {
					if ($equid == 0) {
						$equid = "null";
					}
					if ($aluid == 0) {
						$aluid = "null";
					}
					$sql = "DELETE FROM testearq WHERE aluid = '$aluid' AND tesid = '$tesid' AND queid = '$queid' OR equid = '$equid'";
					$result = mysql_query( $sql, $dblink ) or die(mysql_error());
					echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
					return 1;
				}	
			} else {
				echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Falha na remo&ccedil;&atilde;o do arquivo ...") . "</strong></div>" ;
				return 1;
			}
			
		} else {

			if (!empty($arquivo)) {

				if ($arquivo_size > 600000) {
					echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Arquivo muito grande ...") . "</strong></div>" ;
					return 1;
				}
				
				$sql = "SELECT arquivo FROM testearq WHERE queid = '$queid' AND tesid = '$tesid' AND (aluid = '$aluid' OR equid = '$equid')";
				
				$result = mysql_query($sql, $dblink) or die(mysql_error());
				
				if ( mysql_num_rows($result) > 0) {
					$linha = mysql_fetch_array($result) or mysql_error();
					if (!unlink("arquivos/" . $linha["arquivo"])) {
						echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Falha na substitui&ccedil;&atilde;o do arquivo ...") . "</strong></div>" ;
						return 1;
					}
					if ($equid == 0) {
						$equid = "null";
					}
					if ($aluid == 0) {
						$aluid = "null";
					}
					$sql = "DELETE FROM testearq WHERE (aluid = '$aluid'  OR equid = '$equid') AND tesid = '$tesid' AND queid = '$queid'";
					$result = mysql_query($sql, $dblink) or die(mysql_error());
				}

				$arquivo_copy = trim($ra) . trim($tesid) . trim($queid) . "_" . $arquivo;

				if (!copy($arquivo_name, "arquivos/" . $arquivo_copy )) {
					echo  "<div class='alert alert-danger' role='alert'><strong>" . _("Falha na c&oacute;pia do arquivo ...") . "</strong></div>" ;
					return 1;
				}
				if ($equid == 0) {
					$equid = "null";
				}
				if ($aluid == 0) {
					$aluid = "null";
				}
				
				if ($aluid == "null") {
					$sql = "INSERT INTO testearq VALUES (null, null, $tesid, '$arquivo_copy', $equid, $queid, null, null)";
				} else {
					$sql = "INSERT INTO testearq VALUES (null, '$aluid', $tesid, '$arquivo_copy', $equid, $queid, null, null)";
				}
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());

				echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
				return 1;
			} else {
				return 0;
			}

		}

		mysql_close($dblink);
	}

	function ListaTeste($tesid, $tipo, $disid, $ra, $usuid, $equid) {
		
		if (empty($equid) or $equid == "null") {
			$equid = 'null';
			$aluid = $ra;
		} else {
			$aluid = 'null';
		}

		include( "./connectdb.php" );

		$sql = "SELECT texto, avaliacao FROM teste WHERE id = '$tesid'" ;
		$result = mysql_query($sql, $dblink) or die(mysql_error());

		if ( mysql_num_rows($result) > 0) {

			$linha = mysql_fetch_array($result);
			
			if ($linha["avaliacao"] == 0) {
				$equid = 'null';
			}
			
			if ($linha["avaliacao"] == 1 and $equid == "null" and $tipo == 0) {
				echo  "<div class='alert alert-success' role='alert'><strong>" . 
				_("Esta atividade n&atilde;o est&aacute; programada para ser desenvolvida individualmente ...") . "</strong></div>" ;
				mysql_close($dblink);
				return 0;
			}

			echo "<p class='lead'><strong>" . $linha["texto"] . "</strong></p><br>";
			
			$sql = "SELECT tq.queid, tq.valor, tq.texto as textotq, q.texto as textoq, q.tipo as tipoq 
			FROM tesque tq INNER JOIN questoes q ON (q.id = tq.queid) WHERE tq.tesid = '$tesid' ORDER BY 3";
			
			$result = mysql_query($sql, $dblink) or die(mysql_error());

			if ( mysql_num_rows($result) > 0) {
				
				echo "<form ENCTYPE='multipart/form-data' action='tesdo.php' method='POST'>\n";
					
				echo "<input type='hidden' name='pAction' value='TODO'>\n";
				echo "<input type='hidden' name='tesid' value='$tesid'>\n";
				echo "<input type='hidden' name='disid' value='$disid'>\n";
				echo "<input type='hidden' name='equid' value='$equid'>\n";

				while ($linha = mysql_fetch_array($result)) {
				
					if (!empty($linha["textotq"])) {
						$texto = $linha["textotq"];
					} else {
						$texto = $linha["textoq"];
					}
					
					echo "$texto\n";
					
					echo "<br>" . _("Ponto(s)") . ": " . $linha["valor"] . "<br><br>";

					if ($linha["tipoq"] == "A") {
						
						$sql = "SELECT texto, id FROM alternativa WHERE queid = '" . $linha["queid"] . "' ORDER BY 1" ;

						$resulto = mysql_query( $sql, $dblink );

						if (mysql_num_rows($resulto) > 0) {
							
							echo "<table>";

							while ($linhao = mysql_fetch_array($resulto)) {
								
								echo "</tr>";

								$sql = "SELECT id FROM correcao WHERE tesid = '$tesid' AND queid = " . $linha["queid"] . " AND altid = " . $linhao["id"] . " 
								AND (aluid = '$aluid' OR equid = '$equid')";

								$resultc = mysql_query( $sql, $dblink );

								$linhac = mysql_fetch_array($resultc);

								if (mysql_num_rows($resultc) > 0) {
									echo "<td><input type=radio name='" . $linha["queid"] . "' value='" . $linhao["id"] . "' CHECKED>&nbsp;</td><td>" . $linhao["texto"] . "</td>";
								} else {
									echo "<td><input type=radio name='" . $linha["queid"] . "' value='" . $linhao["id"] . "'>&nbsp;</td><td>" . $linhao["texto"] . "</td>";
								}
								
								echo "</tr>";
							}
							
							echo "</table>";
						}
					
					} elseif ($linha["tipoq"] == "Q") {
						
						//echo _("Obs: Somente &eacute; permitida a carga de arquivos de at&eacute; 500kb") . ".&nbsp;";
						//echo _("Procure sempre enviar arquivos compactados") . ".<br><br>" ;
						
						echo _("Utilize servi&ccedil;os de armazenamento em nuvem como") . " Google Drive, SkyDrive, Dropbox, iCloud, Box, SugarSync " . _("e outros") . ".<br><br>";

						$sql = "SELECT arquivo FROM testearq WHERE tesid = '$tesid' AND (aluid = '$aluid' OR equid = '$equid')";
						$resulta = mysql_query($sql, $dblink) or die(mysql_error());

						if (mysql_num_rows($result) > 0) {
							$linhaa = mysql_fetch_array($resulta);
							$arquivo = $linhaa["arquivo"];
						}

						//if (empty($arquivo)) {
						//	echo _("Arquivo");
						//	echo "<input type='file' name='" . $linha["queid"] . "' size=30 maxlength=60></p>";
						//	echo _("Ou") . "<br><br>" . _("Link do arquivo") . " (http://...) ";
						//	echo "<input type='text' name='" . $linha["queid"] . "' size=75 maxlength=90></p>";
						//} else {
						//	if (strrpos($arquivo, "http://") !== false) {
						//		echo _("Arquivo");
						//		echo "<input type='file' name='" . $linha["queid"] . "' size=30 maxlength=60></p>";
						//		echo _("Ou") . "<br><br>" . _("Link do arquivo") . " (http://...) ";
								echo _("Link do arquivo") . " (http://...) ";
								echo "<input type='text' name='" . $linha["queid"] . "' value='$arquivo' size=75 maxlength=90></p>";
						//	} else {
						//		echo "<input type='checkbox' name='delarq'>&nbsp;" . _("Eliminar Arquivo") . " $arquivo<br>";
						//		echo "<br>&nbsp;" . _("Substituir arquivo") . ":";
						//		echo "<br><input type='file' name='" . $linha["queid"] . "' size=30 maxlength=60></p>";
						//	}
						//}						
						
					} elseif ($linha["tipoq"] == "D") {
						
						$sql = "SELECT texto, id FROM descritiva WHERE tesid = '$tesid' AND queid = " . $linha["queid"] . " AND (aluid = '$aluid' OR equid = '$equid') ORDER BY 1";
						$resultd = mysql_query( $sql, $dblink );
						
						if ( mysql_num_rows($resultd) > 0) {
							$linhad = mysql_fetch_array($resultd);
							echo "<textarea rows=15 name='" . $linha["queid"] . "' class='form-control'>" . $linhad["texto"] . "</textarea><br>";
			 			} else {
							echo "<textarea rows=15 name='" . $linha["queid"] . "' class='form-control'></textarea><br>";
						}
					}
					
					echo "<hr>";
				}

				if ($tipo <> 1) {
					echo "<input type='submit' class='btn btn-default' name='enviarage' value='" . _("Enviar") . "'>";
				} else {
					echo "<input type='submit' class='btn btn-default' name='enviarage' value='" . _("Enviar") . "' disabled>";
				}
				
				echo "</form>";

			}
		}
		
		mysql_close($dblink);
		return;
	}
	
	function ListaBloom($tesid, $tipo, $disid, $ra, $usuid, $equid) {
	
		if (empty($equid) or $equid == "null") {
			$equid = 'null';
			$aluid = $ra;
		} else {
			$aluid = 'null';
		}
	
		include( "./connectdb.php" );
	
		$sql = "SELECT texto, avaliacao FROM teste WHERE id = '$tesid'";
		$result = mysql_query($sql, $dblink) or die(mysql_error());
	
		if ( mysql_num_rows($result) > 0) {
	
			$linha = mysql_fetch_array($result);
				
			if ($linha["avaliacao"] == 0) {
				$equid = 'null';
			}
				
			if ($linha["avaliacao"] == 1 and $equid == "null" and $tipo == 0) {
				echo  "<div class='alert alert-success' role='alert'><strong>" .
				_("Esta atividade n&atilde;o est&aacute; programada para ser desenvolvida individualmente ...") . "</strong></div>" ;
				mysql_close($dblink);
				return 0;
			}
	
			echo "<p class='lead'><strong>" . $linha["texto"] . "</strong></p>";
			
			$bloom = 1;
			while ($bloom <= 6) {
				
				$sql = "SELECT liberado, maximo, desempenho, peso FROM tesbloom WHERE tesid = '$tesid' AND competencia = '$bloom'";
				$result = mysql_query($sql, $dblink) or die(mysql_error());
				$linha = mysql_fetch_array($result);
					
				$liberado = $linha["liberado"];
				$maximo = $linha["maximo"];
				$desempenho = $linha["desempenho"];
				$peso = $linha["peso"];
				
				if ($linha["avaliacao"] == 1) {
					$sql = "SELECT SUM(d.valor) as valord, SUM(tq.valor) as pontosd FROM tesque tq INNER JOIN descritiva d ON (d.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
					WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND (d.aluid = '$aluid' OR d.equid = '$equid') GROUP BY d.aluid, d.tesid";
				} else {
					$sql = "SELECT SUM(d.valor) as valord, SUM(tq.valor) as pontosd FROM tesque tq INNER JOIN descritiva d ON (d.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
					WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND d.aluid = '$aluid' GROUP BY d.aluid, d.tesid";
				}

				$result = mysql_query($sql, $dblink ) or die(mysql_error());
				$linhad = mysql_fetch_array($result);
					
				if ($linha["avaliacao"] == 1) {
					$sql = "SELECT SUM(c.valor) as valorc, SUM(tq.valor) as pontos FROM tesque tq INNER JOIN correcao c ON (c.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
					WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND (c.aluid = '$aluid' OR c.equid = '$equid') GROUP BY c.aluid, c.tesid";
				} else {
					$sql = "SELECT SUM(c.valor) as valorc, SUM(tq.valor) as pontosc FROM tesque tq INNER JOIN correcao c ON (c.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
					WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND c.aluid = '$aluid' GROUP BY c.aluid, c.tesid";
				}
					
				$result = mysql_query($sql, $dblink ) or die(mysql_error());
				$linhac = mysql_fetch_array($result);
					
				if ($linha["avaliacao"] == 1) {
					$sql = "SELECT SUM(ta.valor) as valorta, SUM(tq.valor) as pontosta FROM tesque tq INNER JOIN testearq ta ON (ta.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
					WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND (ta.aluid = '$aluid' OR ta.equid = '$equid') GROUP BY ta.aluid, ta.tesid";
				} else {
					$sql = "SELECT SUM(ta.valor) as valorta, SUM(tq.valor) as pontosta FROM tesque tq INNER JOIN testearq ta ON (ta.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
					WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND ta.aluid = '$aluid' GROUP BY ta.aluid, ta.tesid";
				}
					
				$result = mysql_query($sql, $dblink ) or die(mysql_error());
				$linhata = mysql_fetch_array($result);
					
				$pontos[$bloom] = ($linhad["pontosd"] + $linhac["pontosc"] + $linhata["pontosta"]);
				$notaparcial = ($linhad["valord"] + $linhac["valorc"] + $linhata["valorta"]) * ($peso / 10);
				$nota += $notaparcial;
				
				echo "<em>" . _("Desempenho na compet&ecirc;ncia") . "&nbsp;";
				
				switch ($bloom) {
					case 1:
						echo _("Conhecimento");
						break;
					case 2:
						echo _("Compreens&atilde;o");
						break;
					case 3:
						echo _("Aplica&ccedil;&atilde;o");
						break;
					case 4:
						echo _("An&aacute;lise");
						break;
					case 5:
						echo _("Avalia&ccedil;&atilde;o");
						break;
					case 6:
						echo _("S&iacute;ntese");
						break;
				}
				
				echo ":&nbsp;" . number_format($notaparcial,1) . " (" . _("Peso") . ": " . $peso . "; " . _("Desempenho m&iacute;nimo") . ": $desempenho%)</em><br>";
				
				$bloom++;
				
			}
			
			echo "<br><strong>" . _("Desempenho geral alcan&ccedil;ado at&eacute; o momento:") . "&nbsp;" . number_format($nota,1) . "</strong><br><br>";
			
			$bloom = 1;
			while ($bloom <= 6) {
				
				$sql = "SELECT liberado, maximo, desempenho, peso FROM tesbloom WHERE tesid = '$tesid' AND competencia = '$bloom'";
				$result = mysql_query($sql, $dblink) or die(mysql_error());
				$linha = mysql_fetch_array($result);
					
				$liberado = $linha["liberado"];
				$maximo = $linha["maximo"];
				$desempenho = $linha["desempenho"];
				$peso = $linha["peso"];
				$acumulao = 0;
				
				$sql = "SELECT q.id, q.texto as textoq, tq.texto as textotq, q.tipo, tq.valor, q.bloom, t.avaliacao FROM tesque tq INNER JOIN questoes q ON (tq.queid = q.id)
				INNER JOIN teste t ON tq.tesid = t.id WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' ORDER BY 3";
				
				$result = mysql_query($sql, $dblink ) or die(mysql_error());
				
				if (mysql_num_rows($result) > 0) {
					
					echo "<table class='table'><thread><tr><th></th><th>" . _("Quest&atilde;o") . "</th><th>" . _("Pontos") . "</th><th>" . 
					_("Acumulado") . "</th></tr></thread><tbody>";
					
					while ($linha = mysql_fetch_array($result)) {
						
						if (!empty($linha["textotq"])) {
							$texto = $linha["textotq"];
						} else {
							$texto = $linha["textoq"];
						}
						
						echo "<tr><td></td><td>$texto<br><span class='label label-info'>";
						
						switch ($bloom) {
							case 1:
							echo _("Conhecimento");
							break;
						case 2:
							echo _("Compreens&atilde;o");
							break;
						case 3:
							echo _("Aplica&ccedil;&atilde;o");
							break;
						case 4:
							echo _("An&aacute;lise");
							break;
						case 5:
							echo _("Avalia&ccedil;&atilde;o");
							break;
						case 6:
							echo _("S&iacute;ntese");
							break;
						}
						
						echo "</span></td><td>";
						
						if ($linha["tipo"] == "D") {
							if ($linha["avaliacao"] == 1) {
								$sql = "SELECT d.valor FROM tesque tq INNER JOIN descritiva d ON (d.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
								WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND q.id = '" . $linha["id"] . "' AND (d.aluid = '$aluid' OR d.equid = '$equid')";
							} else {
								$sql = "SELECT d.valor FROM tesque tq INNER JOIN descritiva d ON (d.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
								WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND q.id = '" . $linha["id"] . "' AND d.aluid = '$aluid'";
							}
						} elseif ($linha["tipo"] == "A") {
							if ($linha["avaliacao"] == 1) {
								$sql = "SELECT c.valor FROM tesque tq INNER JOIN correcao c ON (c.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
								WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND q.id = '" . $linha["id"] . "' AND (c.aluid = '$aluid' OR c.equid = '$equid')";
							} else {
								$sql = "SELECT c.valor FROM tesque tq INNER JOIN correcao c ON (c.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
								WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND q.id = '" . $linha["id"] . "' AND c.aluid = '$aluid'";
							}
						} else {
							if ($linha["avaliacao"] == 1) {
								$sql = "SELECT ta.valor FROM tesque tq INNER JOIN testearq ta ON (ta.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
								WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND q.id = '" . $linha["id"] . "' AND (ta.aluid = '$aluid' OR ta.equid = '$equid')";
							} else {
								$sql = "SELECT ta.valor FROM tesque tq INNER JOIN testearq ta ON (ta.queid = tq.queid) INNER JOIN questoes q ON (tq.queid = q.id)
								WHERE tq.tesid = '$tesid' AND q.bloom = '$bloom' AND q.id = '" . $linha["id"] . "' AND ta.aluid = '$aluid'";
							}
						}
							
						$resulta = mysql_query($sql, $dblink ) or die(mysql_error());
						$linhaa = mysql_fetch_array($resulta);
						
						$acumulado += $linhaa["valor"] / $pontos[$bloom] * 100;
						
						echo number_format($linhaa["valor"],1) . "</td><td>";
						echo number_format($acumulado,1) . "%</td></tr>";
					}
					
					echo "</tbody></table>";
					
				}
				
				$bloom++;
				
			}
			
			mysql_close($dblink);
			
			return;
			
		}
		
	}

	include( "cabecalho.php" );
	
	include( "menu.inc" );
	
	include 'dadosdis.inc';
			
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span>&nbsp;" . _("Atividades") . "</h3></div>";
	
	echo "<a href='prova.php'><button type='button' class='btn btn btn-default'>" . _("Atividades") . "</button></A>\n";
	
	if (!empty($planid)) {
		echo "<a href='#' onClick='abrirPag(" . '"plano.php", "pAction=VIEW&planid=' . $planid . '")' . "'><button type='button' class='btn btn btn-default'>" .
		_("Plano de aula") . "</button></a>\n";
	}
	
	echo "<br><br><div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Atividade") . "</h3></div>";
	echo "<div class='panel-body'>";

	if ($pAction == "TODO") {	
					
		if ($enviaemail == 1) {
			EnviaEmail($tesid, $aluid);
		}
		
		$arquivo = 0;
		
		foreach($_FILES AS $aKey => $aValue) {
			$arquivo = GravaArquivo($aKey, $tesid, $ra, $delarq, $_FILES[$aKey]['name'], $_FILES[$aKey]['tmp_name'], $_FILES[$aKey]['size'], $tipo, $equid);
		}

		include( "./connectdb.php" );

		foreach($_POST AS $aKey => $aValue) {
			
			$sql = "SELECT tq.tesid, tq.queid, q.tipo, tq.valor FROM tesque tq INNER JOIN questoes q ON (q.id = tq.queid) WHERE tq.tesid = '$tesid' AND tq.queid = '$aKey'";

			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			
			if (mysql_num_rows($result) > 0) {		

				if (empty($equid) or $equid == "null") {
					$equid = 0;
					$aluid = $ra;
				} else {
					$aluid = 0;
				}

				$linha = mysql_fetch_array($result);

				if ($linha["tipo"] == "D") {
					
					$sqld = "SELECT * FROM descritiva WHERE tesid = '$tesid' AND queid = '$aKey' AND (aluid = '$aluid' OR equid = '$equid')";
					$resultd = mysql_query( $sqld, $dblink ) or die (mysql_error());
									
					if ( mysql_num_rows($resultd) > 0) {
						$sqlq = "UPDATE descritiva SET texto = '$aValue' WHERE tesid = '$tesid' AND queid = '$aKey' AND (aluid = '$aluid' OR equid = '$equid')";
					} else {
						if ($equid == 0) {
							$equid = "null";
						}
						if ($aluid == 0) {
							$aluid = "null";
						}
						if ($aluid == "null") {
							$sqlq = "INSERT INTO descritiva VALUES (NULL, NULL, '$tesid', '$aKey', '$aValue', $equid, null, null)";
						} else {			
							$sqlq = "INSERT INTO descritiva VALUES (NULL, '$aluid', '$tesid', '$aKey', '$aValue', $equid, null, null)";
						}
					}
								
					$resultq = mysql_query( $sqlq, $dblink ) or die (mysql_error());

				} elseif ($linha["tipo"] == "A") {
					
					$sql = "SELECT resposta FROM alternativa WHERE id = '$aValue'";
					$resulta = mysql_query( $sql, $dblink ) or die(mysql_error());
					$linhaa = mysql_fetch_array($resulta);

					$sqlc = "SELECT * FROM correcao WHERE tesid = '$tesid' AND queid = '$aKey' AND (aluid = '$aluid' OR equid = '$equid')";
					$resultc = mysql_query( $sqlc, $dblink );
					
					if ( mysql_num_rows($resultc) > 0) {
						$sqlc = "UPDATE correcao SET altid = '$aValue', valor = '" . ($linha["valor"] * $linhaa["resposta"]) . 
						"' WHERE (aluid = '$aluid' OR equid = '$equid') AND tesid = '$tesid' AND queid = '$aKey'";						
					} else {
						if ($equid == 0) {
							$equid = "null";
						}
						if ($aluid == 0) {
							$aluid = "null";
						}
						if ($aluid == "null") {
							$sqlc = "INSERT INTO correcao VALUES (NULL, NULL, '$tesid', '$aKey', '$aValue', $equid, " . $linha["valor"] * $linhaa["resposta"] . ", null)";
						} else {
							$sqlc = "INSERT INTO correcao VALUES (NULL, '$aluid', '$tesid', '$aKey', '$aValue', $equid, " . $linha["valor"] * $linhaa["resposta"] . ", null)";
						}
					}
					
					$resultc = mysql_query( $sqlc, $dblink ) or die(mysql_error());

				} elseif ($linha["tipo"] == "Q") {
					
					if (!$arquivo) {					

						if (empty($equid) or $equid == "null") {
							$equid = 'null';
							$aluid = $ra;
						} else {
							$aluid = 'null';
						}
					
						if ($aluid != 'null') {
							$sql = "SELECT * FROM testearq WHERE aluid = '$aluid' AND tesid = '$tesid' AND queid = '$aKey'";
						} else {
							$sql = "SELECT * FROM testearq WHERE equid = '$equid' AND tesid = '$tesid' AND queid = '$aKey'";
						}
					
						$result = mysql_query( $sql, $dblink ) or die(mysql_error());
					
						if (mysql_num_rows($result) > 0) {
							if (empty($aValue)) {
								if ($aluid != 'null') {
									$sql = "DELETE FROM testearq WHERE aluid = '$aluid' AND tesid = '$tesid' AND queid = '$aKey'";
								} else {
									$sql = "DELETE FROM testearq WHERE equid = '$equid' AND tesid = '$tesid' AND queid = '$aKey'";
								}
							} else {
								if ($aluid != 'null') {
									$sql = "UPDATE testearq SET arquivo = '$aValue' WHERE aluid = '$aluid' AND tesid = '$tesid' AND queid = '$aKey'";
								} else {
									$sql = "UPDATE testearq SET arquivo = '$aValue' WHERE equid = '$equid' AND tesid = '$tesid' AND queid = '$aKey'";
								}
							}
						} else {
							$sql = "INSERT INTO testearq VALUES (null, $aluid, $tesid, '$aValue', $equid, $aKey, null, null)";
						}
					
						$result = mysql_query( $sql, $dblink ) or die(mysql_error());
						
					}

				}
			}
			
		}
			
		mysql_close($dblink);
		echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
		
	}

	include( "./connectdb.php" );
	
	$sql = "SELECT avaliacao FROM teste WHERE id = '$tesid'";
	$result = mysql_query( $sql, $dblink ) or die(mysql_error());
	$linha = mysql_fetch_array($result);
	if ($linha["avaliacao"]) {
		$sql = "SELECT ea.equid FROM equialu ea, equipes e  WHERE e.id = ea.equid AND ea.aluid = '$ra' AND e.disid = '$disid' ORDER BY 1";
		$result = mysql_query( $sql, $dblink ) or die(mysql_error());
		if ($linha = mysql_fetch_array($result)) {
			$equid = $linha["equid"];
		} else {
			$equid = "";
		}
	} else {
		$equid = "";
	}
	
	//$sql = "SELECT * FROM tesbloom WHERE tesid = '$tesid'";
	//$result = mysql_query( $sql, $dblink ) or die(mysql_error());
	//if (mysql_num_rows($result) > 0) {
	//	ListaBloom($tesid, $tipo, $disid, $ra, $id, $equid);
	//} else {
		ListaTeste($tesid, $tipo, $disid, $ra, $id, $equid);
	//}
	
	mysql_close($dblink);
	
	echo "</div></div>";
	
	include 'rodape.inc';
?>
