<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	
	if ($tipo == 0) {
		echo "Sess&atilde;o Expirada. Fa&ccedil;a um novo <a href='login.php'>login</a> ...";
		exit;
	}
	
	include( "cabecalho.php" );
	
	include( "menu.inc" );
	
	include 'dadosdis.inc';
	
	echo "<br><h3 class='blog-post-title'><span class='glyphicon glyphicon-floppy-disk' aria-hidden='true'></span>&nbsp;" . _("Backup") . "</h3></div>";
	
	echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("C&oacute;pia de seguran&ccedil;a") . "</h3></div>";
	echo "<div class='panel-body'>";
	
	$arquivo = "arquivos/$disid.csv";
	
	if ($pAction == "BACKUP") {
		
		if ($delarq == 'on') {
			
			unlink($arquivo);
			
		} else {
		
			include 'connectdb.php';
			
			$fp = fopen($arquivo,"w");
			
			// usuario
			$sql = "SELECT * FROM usuario WHERE id = '$id'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "usuario");
				fputcsv($fp, $linha, ';');
			}
			
			// usulinks
			//$sql = "SELECT * FROM usulinks WHERE usuid = '$id'";
			//$result = mysql_query($sql, $dblink ) or die(mysql_error());
			//while ($linha = mysql_fetch_assoc($result)) {
			//	array_unshift($linha, "usulinks");
			//	fputcsv($fp, $linha, ';');
			//}
			
			// assunto
			$sql = "SELECT * FROM assunto WHERE usuid = '$id'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "assunto");
				fputcsv($fp, $linha, ';');
			}
			
			// bibliografia
			//$sql = "SELECT * FROM bibliografia WHERE usuid = '$id'";
			//$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			//while ($linha = mysql_fetch_assoc($result)) {
			//	array_unshift($linha, "bibliografia");
			//	fputcsv($fp, $linha, ';');
			//}
			
			// EAD
			$sql = "SELECT * FROM ead WHERE usuid = '$id'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "ead");
				fputcsv($fp, $linha, ';');
			}
			
			// material
			$sql = "SELECT * FROM material WHERE usuid = '$id'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "material");
				fputcsv($fp, $linha, ';');
			}
			
			// questoes
			$sql = "SELECT * FROM questoes WHERE usuid = '$id'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "questoes");
				fputcsv($fp, $linha, ';');
				// alternativa
				$sql = "SELECT * FROM alternativa WHERE queid = '" . $linha["id"]. "'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha2 = mysql_fetch_assoc($result2)) {
					array_unshift($linha2, "alternativa");
					fputcsv($fp, $linha2, ';');
				}
			}
			
			// webteca
			//$sql = "SELECT * FROM webteca WHERE usuid = '$id'";
			//$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			//while ($linha = mysql_fetch_assoc($result)) {
			//	array_unshift($linha, "webteca");
			//	fputcsv($fp, $linha, ';');
			//}
			
			// disciplina
			$sql = "SELECT * FROM disciplina WHERE id = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				// enderecos
				$sql = "SELECT * FROM enderecos WHERE id = '" . $linha["endid"] . "'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());
				while ($linha2 = mysql_fetch_assoc($result2)) {
					array_unshift($linha2, "enderecos");
					fputcsv($fp, $linha2, ';');
				}
				// usuend
				$sql = "SELECT * FROM usuend WHERE endid = '" . $linha["endid"] . "'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());
				while ($linha2 = mysql_fetch_assoc($result2)) {
					array_unshift($linha2, "usuend");
					fputcsv($fp, $linha2, ';');					
				}
				// curso
				$sql = "SELECT * FROM curso WHERE id = '" . $linha["curid"] . "'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());
				while ($linha2 = mysql_fetch_assoc($result2)) {
					array_unshift($linha2, "curso");
					$usuid = $linha2["usuid"];
					fputcsv($fp, $linha2, ';');					
				}
				// usucur
				$sql = "SELECT * FROM usucur WHERE curid = '" . $linha["curid"] . "' AND usuid = '$usuid'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());
				while ($linha2 = mysql_fetch_assoc($result2)) {
					array_unshift($linha2, "usucur");
					fputcsv($fp, $linha2, ';');					
				}
				// disciplina
				array_unshift($linha, "disciplina");
				fputcsv($fp, $linha, ';');
				// disalu
				$sql = "SELECT * FROM disalu WHERE disid = '$disid'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());
				while ($linha2 = mysql_fetch_assoc($result2)) {
					//aluno
					$sql = "SELECT * FROM aluno WHERE id = '" . $linha["aluid"] . "'";
					$result3 = mysql_query($sql, $dblink ) or die(mysql_error());
					while ($linha3 = mysql_fetch_assoc($result3)) {
						array_unshift($linha3, "aluno");
						fputcsv($fp, $linha3, ';');
					}
					array_unshift($linha2, "disalu");
					fputcsv($fp, $linha2, ';');
				}
			}
			
			// disusu
			$sql = "SELECT * FROM disusu WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				// usuario
				$sql = "SELECT * FROM usuario WHERE id = '" . $linha["usuid"] . "'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());
				while ($linha2 = mysql_fetch_assoc($result2)) {
					array_unshift($linha2, "usuario");
					fputcsv($fp, $linha2, ';');
				}				
				array_unshift($linha, "disusu");
				fputcsv($fp, $linha, ';');
			}
			
			// agenda
			$sql = "SELECT * FROM agenda WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "agenda");
				fputcsv($fp, $linha, ';');
			}
			
			// aviso
			$sql = "SELECT * FROM aviso WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "aviso");
				fputcsv($fp, $linha, ';');
			}
			
			// forum
			$sql = "SELECT * FROM forum WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "forum");
				fputcsv($fp, $linha, ';');
			}
			
			// mensagens
			$sql = "SELECT * FROM mensagens WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "mensagens");
				fputcsv($fp, $linha, ';');
			}
			
			// menu
			$sql = "SELECT * FROM menu WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "menu");
				fputcsv($fp, $linha, ';');
			}
			
			// planoensino
			$sql = "SELECT * FROM planoensino WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "planoensino");
				fputcsv($fp, $linha, ';');
			}
			
			// aquipes
			$sql = "SELECT * FROM equipes WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				$sql = "SELECT * FROM equialu WHERE equid = '" .$linha["id"] . "'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha2 = mysql_fetch_assoc($result2)) {
					array_unshift($linha2, "equialu");
					fputcsv($fp, $linha2, ';');
				}
				array_unshift($linha, "equipes");
				fputcsv($fp, $linha, ';');
			}
			
			// avaliacao
			$sql = "SELECT * FROM avaliacao WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "avaliacao");
				fputcsv($fp, $linha, ';');
				// avalequi
				$sql = "SELECT * FROM avalequi WHERE avalid = '" . $linha["id"] . "'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha2 = mysql_fetch_assoc($result2)) {
					array_unshift($linha2, "avalequi");
					fputcsv($fp, $linha2, ';');
				}
				// notas
				$sql = "SELECT * FROM notas WHERE avalid = '" . $linha["id"] . "'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha2 = mysql_fetch_assoc($result2)) {
					array_unshift($linha2, "notas");
					fputcsv($fp, $linha2, ';');
				}
			}
			
			// reunioes
			$sql = "SELECT * FROM reunioes WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "reunioes");
				fputcsv($fp, $linha, ';');
			}
			
			// plano
			$sql = "SELECT * FROM plano WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "plano");
				fputcsv($fp, $linha, ';');
				// frequencia
				$sql = "SELECT * FROM frequencia WHERE planid = '" . $linha["id"] . "'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha2 = mysql_fetch_assoc($result2)) {
					array_unshift($linha2, "frequencia");
					fputcsv($fp, $linha2, ';');
				}
				// planead
				$sql = "SELECT * FROM planead WHERE planid = '" . $linha["id"] . "'";
				$result3 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha3 = mysql_fetch_assoc($result3)) {
					array_unshift($linha3, "planead");
					fputcsv($fp, $linha3, ';');
				}
				// planmat
				$sql = "SELECT * FROM planmat WHERE planid = '" . $linha["id"] . "'";
				$result4 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha4 = mysql_fetch_assoc($result4)) {
					array_unshift($linha4, "planmat");
					fputcsv($fp, $linha4, ';');
				}
				// planweb
				$sql = "SELECT * FROM planweb WHERE planid = '" . $linha["id"] . "'";
				$result5 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha5 = mysql_fetch_assoc($result5)) {
					array_unshift($linha5, "planweb");
					fputcsv($fp, $linha5, ';');
				}
			}
			
			// teste
			$sql = "SELECT * FROM teste WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "teste");
				fputcsv($fp, $linha, ';');
				// tesequi
				$sql = "SELECT * FROM tesequi WHERE tesid = '" . $linha["tesid"] . "'";
				$result2 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha2 = mysql_fetch_assoc($result2)) {				
					array_unshift($linha2, "tesequi");
					fputcsv($fp, $linha2, ';');
				}
				// tesque
				$sql = "SELECT * FROM tesque WHERE tesid = '" . $linha["tesid"] . "'";
				$result3 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha3 = mysql_fetch_assoc($result3)) {				
					array_unshift($linha3, "tesque");
					fputcsv($fp, $linha3, ';');
				}		
				// testearq
				$sql = "SELECT * FROM testearq WHERE tesid = '" . $linha["tesid"] . "'";
				$result4 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha4 = mysql_fetch_assoc($result4)) {				
					array_unshift($linha4, "testearq");
					fputcsv($fp, $linha4, ';');
				}
				// correcao
				$sql = "SELECT * FROM correcao WHERE tesid = '" . $linha["tesid"] . "'";
				$result5 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha5 = mysql_fetch_assoc($result5)) {
					array_unshift($linha5, "correcao");
					fputcsv($fp, $linha5, ';');
				}
				// descritiva
				$sql = "SELECT * FROM descritiva WHERE tesid = '" . $linha["tesid"] . "'";
				$result6 = mysql_query($sql, $dblink ) or die(mysql_error());			
				while ($linha6 = mysql_fetch_assoc($result6)) {
					array_unshift($linha6, "descritiva");
					fputcsv($fp, $linha6, ';');
				}
			}
			
			// disbib
			$sql = "SELECT * FROM disbib WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "disbib");
				fputcsv($fp, $linha, ';');
			}
			// disead
			$sql = "SELECT * FROM disead WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "disead");
				fputcsv($fp, $linha, ';');
			}
			// dismat
			$sql = "SELECT * FROM dismat WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "dismat");
				fputcsv($fp, $linha, ';');
			}
			// disweb
			$sql = "SELECT * FROM disweb WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "disweb");
				fputcsv($fp, $linha, ';');
			}
			// distes
			$sql = "SELECT * FROM distes WHERE disid = '$disid'";
			$result = mysql_query($sql, $dblink ) or die(mysql_error());			
			while ($linha = mysql_fetch_assoc($result)) {
				array_unshift($linha, "distes");
				fputcsv($fp, $linha, ';');
			}
			
			fclose($fp);
		
			mysql_close($dblink);
		
			echo  "<div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso ...") . "</strong></div>" ;
			
		}	

	}
	
	echo "<form ENCTYPE='multipart/form-data' action='backup.php' method='POST'>\n";

	echo "<input type='hidden' name='pAction' value='BACKUP'>\n";
	
	if (file_exists($arquivo)) {
		echo "<br>" . _("Arquivo de backup") . ": <a href='$arquivo' target='_blank'>$disid.csv</a>\n";
		echo "<br><br><input type='checkbox' name='delarq'>&nbsp;" . ("Eliminar Arquivo") . " $disid.csv";
		echo "<br><br><input type='submit' class='btn btn-default' name='enviar' value='" . _("Enviar") . "'>";
	} else {
		echo "<br>" . _("Arquivo de backup a ser gerado") . ": $disid.csv\n";
		echo "<br><br><input type='submit' class='btn btn-default' name='enviar' value='" . _("Gerar arquivo") . "'>";
	}
	
	echo "</form>\n";
	
	echo "</div></div>";
		
	include 'rodape.inc';

?>