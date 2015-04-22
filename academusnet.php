<?php
	session_start();
	include( "buscasessao.php" );
    $ch = curl_init("http://localhost/~cjesus/academusnetv5.0/lms/" . $_POST["arquivo"]);
	curl_setopt($ch, CURLOPT_COOKIE, session_name().'='.session_id() );
	curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST["dados"] );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	session_write_close();
    $output = curl_exec($ch);       
    curl_close($ch);
    echo $output;
?>
