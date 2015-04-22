<?php
  session_start();
	include( "buscasessao.php" );
	$linha = BuscaSessao($disid);
	$tipo = $linha["professor"];
	$id = $linha["usuid"];
?>

<!DOCTYPE html>

<BODY>

<?php
	include( "./connectdb.php" );

	if ($pagina < 0) {
		$pagina = 0;
	}

	$SQL = "SELECT * FROM eaddet WHERE eadid = '$eadid' ORDER BY 1";
	echo $SQL;
	$query = mysql_query($SQL, $aDBLink);

	$n = 1;

	while ($linha = mysql_fetch_array($query)) {
		$pag["$n"] = $linha["pagina"];
		$n = $n + 1;
	}

	$numrows = mysql_num_rows($query);
	$ultimo = $n - 1;

	if (!empty($pagina) or $pagina == 0) {
		$SQL = "SELECT texto, comentario FROM ead WHERE id = $eadid";
		$query = mysql_query($SQL, $aDBLink);
		if ($linha = mysql_fetch_array($query)) {
			echo "<br><h1>" . $linha["texto"] . "</h1>";
			if ($pagina > 0) {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . _("P&aacute;gina") . "&nbsp;" . $pagina/$numrows;
				$proximo = $pagina + 1;
				$anterior = $pagina - 1;
				if ($proximo > $ultimo)
					$proximo = 1;
				if ($anterior < 1)
					$anterior = $ultimo;
			} else {
				$proximo = 1;
				$anterior = $ultimo;
			}
			echo "</span><hr>";
			$comentario = $linha["comentario"];
		}
	}

	echo "<center><a href='consmini.php?eadid=$eadid&pagina=" . $pag["1"] . "'>";
	echo "<img src='images/2leftarrow.png' width=16 height=16 border=0></a>&nbsp;";
	echo "<a href='consmini.php?eadid=$eadid&pagina=" . $pag["$anterior"] . "'>";
	echo "<img src='images/1leftarrow.png' width=16 height=16 border=0></a>&nbsp;";
	echo "<a href='consmini.php?eadid=$eadid'>";
	echo "<img src='images/home.png' width=16 height=16 border=0></a>&nbsp;";
	echo "<a href='consmini.php?eadid=$eadid&pagina=" . $pag["$proximo"] . "'>";
	echo "<img src='images/1rightarrow.png' width=16 height=16 border=0></a>&nbsp;";
	echo "<a href='consmini.php?eadid=$eadid&pagina=" . $pag["$ultimo"] . "'>";
	echo "<img src='images/2rightarrow.png' width=16 height=16 border=0></a></center><br>";

	if ($pagina == 0) {
		echo "<span id='textNormal'>$comentario</span>";
	}

	if ($pagina > 0) {
		$SQL = "SELECT id, titulo, pagina, conteudo FROM eaddet WHERE eadid = $eadid AND pagina = $pagina";
		$query = mysql_query($SQL, $aDBLink);
		if ($linha = mysql_fetch_array($query)) {
			echo "<span id='textNormal'>" . $linha["conteudo"] . "</span>";
		}
	}

	mysql_close($aDBLink);

?>

</body>
</html>
