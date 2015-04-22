<?php

function BuscaSessao($disid) {
	
	if (empty($disid)) {
		$disid = "null";
	}

	$programas_permitidos = array(0 => "visitante.php", 1 => "matricula.php");
	
	$programa = trim(end(explode("/", $_SERVER['PHP_SELF'])));

	include( "./connectdb.php" );

	$sessao = session_id();
	
	$sql = "SELECT usuid, professor FROM sessao WHERE usuid IS NOT NULL AND sessao = '" . session_id() . "'";
	$result = mysql_query( $sql, $dblink ) or die(mysql_error());

	if (mysql_num_rows($result) > 0) {
		$linha = mysql_fetch_array($result);
		$usuid = $linha["usuid"];
		$_SESSION["visitante"] = false;
	} else {
		if (!empty($disid)) {
			$sql = "SELECT visitante FROM disciplina WHERE id = '$disid'";
			$results = mysql_query( $sql, $dblink ) or die(mysql_error());
			$linha = mysql_fetch_array($results);
			if ($linha["visitante"]) {
				$usuid = "null";
				$linha = array("professor" => "0", "usuid" => "visitante");
				$_SESSION["visitante"] = true;
			}
		}
		if ($programa == "visitante.php" or $programa == "matricula.php") {
			$usuid = "null";
			$linha = array("professor" => "0", "usuid" => "visitante");
			$_SESSION["visitante"] = true;
		}
	}
	
	if (!isset($_SESSION["logok_academusnet"]) or $_SESSION["logok_academusnet"] != 1) {
		if (array_search($programa, $programas_permitidos) === false) {
			if (!$_SESSION["visitante"]) {
				echo "Sess&atilde;o Expirada. Fa&ccedil;a um novo <a href='login.php'>login</a> ...";
				exit;
			}
		}
	}
	
	$sql = "SELECT sessao, (intervalo+(time_to_sec(timediff(now(), timef)))) as intervaloc, (clicks+1) as clicksc, time_to_sec(timediff(now(), timef)) as tempo 
	FROM acesso WHERE usuid = '$usuid' AND sessao = '" . session_id() . "'";
	
	$results = mysql_query( $sql, $dblink ) or die(mysql_error());

	if ( mysql_num_rows($results) > 0 ) {

		$linhas = mysql_fetch_array($results);

		if ($linhas["tempo"] > 6200) {
			echo "Sess&atilde;o Expirada. Fa&ccedil;a um novo <a href='login.php'>login</a> ...";
			exit;
		}
		
	}
		
	$sql = "SELECT * FROM acesso WHERE usuid = '$usuid' AND disid = '$disid' AND sessao = '" . session_id() . "'";
	
	$results = mysql_query( $sql, $dblink ) or die(mysql_error());

	if ( mysql_num_rows($results) > 0 ) {
		$sql = "UPDATE acesso SET intervalo = '" . $linhas["intervaloc"] . "', clicks = '" . $linhas["clicksc"] . "', timef = now() 
		WHERE usuid = '$usuid' AND disid = '$disid' AND sessao = '" . session_id() . "'";
	} else {
		if ($usuid == "null") {
			$sql = "INSERT INTO acesso VALUES (null, $disid, now(), now(), '" . session_id() . "', NULL, NULL, 0, 0)";
		} else {
			$sql = "INSERT INTO acesso VALUES ('$usuid', $disid, now(), now(), '" . session_id() . "', NULL, NULL, 0, 0)";
		}
	}

	$results = mysql_query( $sql, $dblink ) or die(mysql_error());

	mysql_close($dblink);
	
	if (isset($_SESSION["tipov"])) {
		if ($_SESSION["tipo"] == 0) {
			$linha["professor"] = 0;
		}
	}

	return $linha;
}

if (!empty($_POST["disid"])) {
	$_SESSION['disid'] = $_POST['disid'];
}

if (!empty($_SESSION['disid'])) {
	$disid = $_SESSION['disid'];
}

if (!empty($_POST["endid"])) {
	$_SESSION['endid'] = $_POST['endid'];
}

if (!empty($_SESSION['endid'])) {
	$endid = $_SESSION['endid'];
}

foreach ($_POST as $key => $value) {
	$$key = $value;
}

if (!isset($_SESSION["locale"]) or isset($_GET["locale"])) {
	if (isset($_GET["locale"])) {
		$language = $_GET["locale"];
	} else {
		$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5) . ".UTF-8";
	}
	$_SESSION["locale"] = $language;
}

$language = $_SESSION["locale"];
putenv("LC_ALL=$language");
putenv("LC_MESSAGES=$language");
setlocale(LC_ALL, $language);
setlocale(LC_MESSAGES, $language);
$domain = "academusnet";
bindtextdomain($domain, "/academusnetv5.0/lms/locale");
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);

$formato_data = array(
	"en-US"=>"mm/dd/yyyy",
	"en-UK"=>"dd/mm/yyyy",
	"es-ES"=>"dd/mm/yyyy",
	"de-DE"=>"dd.mm.yyyy",
	"fr-FR"=>"dd/mm/yyyy",
	"it-IT"=>"dd-mm-yyyy",
	"ja-JP"=>"yyyy-mm-dd",	
	"pt-BR"=>"dd/mm/yyyy",
	"pt-PT"=>"dd/mm/yyyy"
);

if (array_key_exists(substr($language,0,5), $formato_data)) {
	$_SESSION["data_formato"] = $formato_data[substr($language,0,5)];
} else {
	$_SESSION["data_formato"] = "mm/dd/yyyy";
}

function CriaLink($programa, $parametros) {
	Return "onClick='abrirPag(" . '"' . $programa . '", "' . $parametros . '")' . "'";
}

?>
