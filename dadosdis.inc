<?php

    include( "./connectdb.php" );

    echo "<div class='jumbotron'>"; 
    
	if ($id != "visitante") {
		
		$sql = "SELECT nome FROM usuario WHERE id = '$id'";
		
		$resultu = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linhau = mysql_fetch_array($resultu);
		
		if ($tipo != 1) {
			echo "<h2>" . strtoupper($linhau["nome"]) . "</h2>";
		}

		$sql = "SELECT ra FROM usuend WHERE usuid = '$id' AND endid = '$endid'";		
		$resultu = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linhau = mysql_fetch_array($resultu);

	} else {
		echo "<h2>Visitante</h2>";
	}
	
	if (!empty($disid)) {
	    $sql = "SELECT d.faltas, d.cargah, d.objetivo, d.nome as nomedis, u.nome as nomeprof, u.email, d.endid, d.usuid, c.nome as nomecur, d.titular FROM disciplina d 
		INNER JOIN curso c ON (c.id = d.curid) INNER JOIN usuario u ON u.id = d.usuid WHERE d.id = '$disid'";
		$resultd = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linhad = mysql_fetch_array($resultd);
		$endid = $linhad["endid"];
	}
	
	if (!empty($endid)) {
		$sql = "SELECT nome FROM enderecos WHERE id = '$endid'";
		$resulte = mysql_query( $sql, $dblink ) or die(mysql_error());
		$linhae = mysql_fetch_array($resulte);
		echo "<p><h2>" . $linhae["nome"] . "</h2></p><br>";
	}

	if (!empty($disid)) {
		
		$profmail = $linhad["email"];

		echo "<p><h3><strong><span class='glyphicon glyphicon-road' aria-hidden='true'></span> " . $linhad["nomecur"] .
		"</h3></p><p><h3><span class='glyphicon glyphicon-blackboard' aria-hidden='true'></span> " . $linhad["nomedis"] . "</strong></h3><br>";
		
		//if (file_exists("rss/$disid.xml")) {
		//	print(" <a href='rss/$disid.xml' target='_blank'><img src='images/rss.png' width='16' height='16'></a>");
		//}
		
		if ($linhad["titular"]) {
			echo "<p><h3>" . _("Professor(a)") . "&nbsp;" . $linhad["nomeprof"] . "&nbsp;&nbsp;<a href='mailto:$profmail'>
			<span class='glyphicon glyphicon-envelope' aria-hidden='true'></span></a>";
			echo "<a href='#' id='textLink' onClick='abrirPag(" . '"consper.php", "menu=' . $menu . '&endid=' . $endid . '&perfil=' . $linhad["usuid"] . '"' . ")'>
			<span class='glyphicon glyphicon-user' aria-hidden='true'></span></a></h3></p>";
		}
	
		$sql = "SELECT u.id, u.nome, u.email FROM disusu du INNER JOIN usuario u ON du.usuid = u.id WHERE du.disid = '$disid'";
		$resultp = mysql_query( $sql, $dblink );
		
		if ( mysql_num_rows($resultp) > 0) {
	
			while ($linhap = mysql_fetch_array($resultp)) {
				echo "<p><h3>" . _("Professor(a)") . "&nbsp;";
				echo $linhap["nome"] . "<a href='mailto:" . $linhap["email"] . "'>&nbsp;&nbsp;&nbsp;<span class='glyphicon glyphicon-envelope' aria-hidden='true'></span></a>";
				echo "<a href='#' id='textLink' onClick='abrirPag(" . '"consper.php", "perfil=' . $linhap["id"] . '"' . ")'>
				<span class='glyphicon glyphicon-user' aria-hidden='true'></span></a></h3></p>";
			}
			
		} else {
			if (!$linhad["titular"]) {
				echo "<p><h3>" . _("Professor(a)") . "&nbsp;" . _("Sem professor definido") . "</h3></p>";
			}
		}
		
	}
	
	mysql_close($dblink);
?>
