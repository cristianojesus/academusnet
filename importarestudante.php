<?php

session_start();
include( "buscasessao.php" );
$linha = BuscaSessao();
$tipo = $linha["professor"];
$id = $linha["usuid"];
$extensoesPermitidas = array("csv");

include( "cabecalho.php" );

print("<div id='content'>");
			
include 'dadosdis.inc';

print("<br><span id='textBold'>Aprendizes</span><br><br>");
			
echo "<A HREF='#' id='textLink' onClick='abrirPag(", '"estudante.php", "endid=',$endid,'"',")'>Voltar</A>\n";

if ($pAction=="INSERT") {
	$abrearquivo = fopen("arquivos/$arquivo", "r");
	if (!$abrearquivo){
		print ("<span id=textNormal>Arquivo n&atilde;o encontrado</span>\n");
	} else {
		include( "./connectdb.php" );
		print ("<br><br>");
		while ($valores = fgetcsv($abrearquivo, 2048, ";")) {
			if( mb_detect_encoding($valores[0],"auto") != "ISO-8859-1" ) {
				$registro = mb_convert_encoding($valores[0], "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
			}
			if( mb_detect_encoding($valores[0],"auto") != "ISO-8859-1" ) {
				$nome = mb_convert_encoding($valores[1], "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
			}	
			$sql = 'SELECT * FROM aluno WHERE id="' . $registro . '"';
			$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			if ( mysql_num_rows($result) > 0) {
				if (empty($disid)) {
					print("<span id=textNormal>" . $registro . "-" . $nome . " - Registro j&aacute; existe...</span><br>\n");
					$erro = 1;
				}
			} else {
				$sql = "INSERT INTO aluno VALUES (" . '"' . $registro . '", "' . $id . '", "' . $nome . '", null, "' . $endid . '"' . ")";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
			}
			if (!empty($disid)) {
				$sql = "SELECT * FROM disalu WHERE disid = '$disid' AND aluid = '$registro'";
				$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				if ( mysql_num_rows($result) > 0) {
					print("<span id=textNormal>" . $registro . "-" . $nome . " - Registro j&aacute; existe...</span><br>\n");
					$erro = 1;
				} else {
					$sql = "INSERT INTO disalu VALUES ('$disid', '$registro')";
					$result = mysql_query( $sql, $dblink ) or die(mysql_error());
				}
			}
		}
		if (empty($erro)) {
			print( "<br><br><span id='textSuc'><img src='images/important.png' width=16 height=16 border=0>
			&nbsp;Inclus&atilde;o realizada com sucesso ...</span><br><br>\n" );
		} else {
			print( "<br><br><span id='textErr'><img src='images/error.png' width=16 height=16 border=0> 
			Ocorreram erros durante a importa&ccedil;&atilde;o ...</span><br><br>\n");
		}
		mysql_close($dblink);
		unlink("arquivos/$arquivo");
	}
} elseif ($pAction=="CONFIRM") {
	if (empty($_FILES["arquivo"]["name"])) {
		print("<br><br><span id='textErr'><img src='images/error.png' width=16 height=16 border=0> Selecione um arquivo ...</span>\n");
	} else {
		$partes = explode(".", $_FILES["arquivo"]["name"]);
		$extensao = array_pop($partes);
		if (in_array($extensao, $extensoesPermitidas) == false) {
			print("<br><br><span id='textErr'><img src='images/error.png' width=16 height=16 border=0> Tipo de arquivo (" . $extensao . ") 
			n&atilde;o permitido</span>\n");
		} else {
			$abrearquivo = fopen($_FILES["arquivo"]["tmp_name"], "r");
			if (!$abrearquivo){
				print ("<br><br><span id='textErr'><img src='images/error.png' width=16 height=16 border=0>Arquivo n&atilde;o encontrado</span>\n");
			} else {
				print ("<br><br>");
				while ($valores = fgetcsv($abrearquivo, 2048, ";")) {
					if( mb_detect_encoding($valores[0],"auto") != "ISO-8859-1" ) {
						$registro = mb_convert_encoding($valores[0], "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
					}
					if( mb_detect_encoding($valores[0],"auto") != "ISO-8859-1" ) {
						$nome = mb_convert_encoding($valores[1], "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
					}
					print("<span id=textNormal>" . $registro . "-" . $nome . "</span><br>\n");
				}

				$arquivo_new = $id . "_" . trim($_FILES["arquivo"]["name"]);
				$arquivo_copy = "arquivos/" . $arquivo_new;
				if (!copy($_FILES["arquivo"]["tmp_name"], $arquivo_copy )) {
		 			print( "<br><br><span id='textErr'><img src='images/error.png' width=16 height=16 border=0> 
		 			Falha na c&oacute;pia do arquivo ...</span><br><br> $arquivo_copy\n" );
				} else {
					print("<form action='importarestudante.php' method='POST'>\n");
					echo "<input type='hidden' name='pAction' value='INSERT'>\n
					<input type='hidden' name='endid' value='$endid'>\n
					<input type='hidden' name='arquivo' value='$arquivo_new'>\n";
					print("<br><input type='submit' name='enviar' value='Confirmar Importa&ccedil;&atilde;o'></td></form>\n");
				}
				fclose($abrearquivo);
			}
		}
	}
} else {
	print ("<form ENCTYPE='multipart/form-data' action='importarestudante.php' method='POST'>\n");
	echo "<input type='hidden' name='pAction' value='CONFIRM'>\n
	<input type='hidden' name='endid' value='$endid'>\n";
	print("<br><br><span id=textBold>Informe um aquivo csv que contenha registro e nome dos estudantes separados por ponto-e-v&iacute;rgula (;).
	<br><br>Exemplo:\n");
	print("<br><br><span id=textSmall>150150150;Arist&oacute;teles de Estagira<br>160160160;Di&oacute;genes de S&iacute;nope</span>\n");
	print("<br><br>*Arquivo\n");
	print("<br><input type='file' name='arquivo' value='$arquivo' size=60 maxlength=90>\n");
	print("<br><br><input type='submit' name='enviar' value='Importar'></form>");
}

include 'rodape.inc';

?>