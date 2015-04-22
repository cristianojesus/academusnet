<?php

	session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao();
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
	$extensoesPermitidas = array("text/csv");
	
	if ($tipo == 0) {
		echo _("Sess&atilde;o Expirada. Fa&ccedil;a um novo") . "&nbsp;<a href='login.php'>login</a> ...";
		exit;
	}
	
	include( "cabecalho.php" );
		
	include( "menup.inc" );
	
	echo "<div class='jumbotron'>";
			
	include( "connectdb.php" );
			
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
			
	echo "<br><h2 class='blog-post-title'><span class='glyphicon glyphicon-floppy-open' aria-hidden='true'></span>&nbsp;" . _("Recupera&ccedil;&atilde;o") . "</h2>";
			
	echo "</div>";
	
	echo "<div class='panel panel-default'>\n<div class='panel-heading'>";
	echo "<h3 class='panel-title'>" . _("Recupera&ccedil;&atilde;o") . "</h3></div>";
	echo "<div class='panel-body'>";

	if ($pAction=="INSERT") {
		$abrearquivo = fopen("arquivos/$arquivo", "r");
		if (!$abrearquivo){
			echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" . 
			_("Arquivo n&atilde;o encontrado ...") . "</strong></div><br>";
		} else {
			include( "./connectdb.php" );
			echo "<br><br>";
			while ($linha = fgetcsv($abrearquivo, 2048, ";")) {
				$sql = "INSERT INTO " . $linha[0] . " VALUES (";
				for ($i=1; $i<count($linha); $i++) {
					$sql .= "'" . $linha[$i] . "'";
					if ($i != (count($linha)-1)) {
						$sql .= ",";
					} else {
						$sql .= ")";
					}
				}
				$result = mysql_query( $sql, $dblink );
				if (!$result) {
					echo "<br>" . $linha[0] . " - " . mysql_error();
				}
			}
			if (empty($erro)) {
				echo  "<br><br><div class='alert alert-success' role='alert'><strong>" . _("Opera&ccedil;&atilde;o realizada com sucesso...") . "</strong></div><br>";
			} else {
				echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" . _("Ocorreram erros durante a importa&ccedil;&atilde;o...") . "</strong></div><br>";
			}
			mysql_close($dblink);
			unlink("arquivos/$arquivo");
		}
	} elseif ($pAction=="CONFIRM") {
		if (!$_FILES) {
			echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" . _("Selecione um arquivo...") . "</strong></div><br>";
		} else {
			//$partes = explode(".", $arquivo_name);
			//$extensao = array_pop($partes);
			$extensao = $_FILES["arquivo"]["type"];
			$arquivo = $_FILES["arquivo"]["tmp_name"];
			if (in_array($extensao, $extensoesPermitidas) == false) {
				echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" . _("Tipo de arquivo") . "&nbsp;$extensao&nbsp;" . _("n&atilde;o permitido") . "</strong></div><br>";
			} else {
				$abrearquivo = fopen("$arquivo", "r");
				if (!$abrearquivo){
					echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" . 
					_("Arquivo n&atilde;o encontrado ...") . "</strong></div><br>";
				} else {
					echo "<br><br>";
					while ($valores = fgetcsv($abrearquivo, 2048, ";")) {
						echo "<span id=textNormal>" . $valores[0] . " - " . $valores[1] . "</span><br>\n";
					}
					$arquivo_new = $id . "_" . trim($arquivo_name);
					$arquivo_copy = "arquivos/" . $arquivo_new;
					if (!copy($arquivo, $arquivo_copy )) {
						echo  "<br><br><div class='alert alert-danger' role='alert'><strong>" .
						_("Falha na c&oacute;pia do arquivo...") . "</strong></div><br>";
					} else {
						echo "<form action='restore.php' method='POST'>\n";
						echo "<input type='hidden' name='pAction' value='INSERT'>\n
						<input type='hidden' name='endid' value='$endid'>\n
						<input type='hidden' name='arquivo' value='$arquivo_new'>\n";
						echo "<br><input type='submit' name='enviar' class='btn btn btn-default' value='" . _("Confirmar importa&ccedil;&atilde;o") . "'></td></form>\n";
					}
					fclose($abrearquivo);
				}
			}
		}
	} else {
		echo "<form ENCTYPE='multipart/form-data' action='restore.php' method='POST'>\n";
		echo "<input type='hidden' name='pAction' value='CONFIRM'>\n";
		echo "<p><label for='arquivo'>(*) " . _("Arquivo") . "</label>\n";
		echo "<br><input type='file' name='arquivo' value='$arquivo' size=60 maxlen	gth=90 class='form-control'>\n";
		echo "<br><br><input type='submit' class='btn btn btn-default' name='enviar' value='" . _("Restaurar") . "'></form>";
	}
	
	echo "</div></div>";

	include 'rodape.inc';

?>

