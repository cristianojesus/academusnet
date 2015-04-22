<?php

// define a conexão com o banco de dados

$dblink = mysql_connect("localhost", "root", "senha") or die(mysql_error());
mysql_select_db( "academusnet", $dblink ) or die(mysql_error());

//mysql_query("SET NAMES 'utf8'");
//mysql_query('SET character_set_connection=utf8');
//mysql_query('SET character_set_client=utf8');
//mysql_query('SET character_set_results=utf8');

$aDBLink = $dblink;

?>
