<?php
	session_unset();
	session_destroy();
	session_start();
	include( "buscasessao.php" );
	include "connectdb.php";
	$qSessao = mysql_query("DELETE FROM sessao WHERE sessao = '" . session_id() . "'") or die(mysql_error());
	mysql_close($dblink);
?>

<!DOCTYPE html>

<?php echo "<html lang=" . substr($language,0,5) . ">"; ?>

<HEAD>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<meta charset="utf-8">
	<title>Academusnet</title>
	<meta name="generator" content="Academusnet" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link href="css/styles.css" rel="stylesheet">
	<link href="stylesheet" type="text/css" href="jquery-ui/css/humanity/jquery-ui.css">
	<link href="stylesheet" type="text/css" href="jquery-ui/jstree/themes/default/style.css">
	<link href="styles.css" rel="stylesheet">
	<link href="shortcut icon" href="favicon.ico" type="image/x-icon">
   
	<script type="text/JavaScript">
		function abrirPag(site, variaveis) {
			var endereco = site;
			var parametro = variaveis;
			var objArquivo = document.getElementById("arquivo");
			var objDados = document.getElementById("dados");
			objArquivo.value = endereco;
			objDados.value = parametro;			
			document.forms['academusnet'].submit()
		}

		function dimensionarColunas(colunas) {
		   maior = 0;
		   for (i = 0; i < colunas.length; i++) {
		      alturaReal = document.getElementById(colunas[i]).offsetHeight;
		      if (alturaReal > maior) {
		         maior = alturaReal;
		      }
		   }
		   for (i = 0; i < colunas.length; i++) {
		      document.getElementById(colunas[i]).style.height = maior + "px";
		   }
		}
		function openPopup(url) {
			window.open(url, "popup_id", "scrollbars,resizable,width=800,height=600");
		}
	
	</script>
	<script src="jquery-ui/js/jquery.js"></script>
	<script src="jquery-ui/js/jquery-ui.min.js"></script>
	<script src="jquery-ui/jquery-validation/jquery.validate.js"></script>
	<script src="jquery-ui/jstree/jquery.jstree.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/scripts.js"></script>
	<script src="jquery-ui/development-bundle/ui/i18n/jquery.ui.datepicker-pt-BR.js"></script>

	<style type="text/css">
		body {
  			padding-top: 40px;
  			padding-bottom: 40px;
  			background-color: #eee;
		}
	</style>

</head>

<body>
    <div class="container">
    	<div class="row">
    		<div class="col-md-4" align=right><br><span style="font-size:120pt;" class="glyphicon glyphicon-education"></span></div>
			<div class="col-md-4">
				<form class="form-signin" name='login' method='post' action='principal.php'>
        			<h2 class="form-signin-heading"><?php echo _("Por favor efetue seu login"); ?></h2>
        			<label for="usuario" class="sr-only"><?php echo _("Usu&aacute;rio"); ?></label>
        			<input type='text' id='id' name='id' class="form-control" placeholder="<?php echo _("Usu&aacute;rio"); ?>" required autofocus>
        			<label for="senha" class="sr-only"><?php echo _("Senha"); ?></label>
        			<input type="password" id="senha" name="senha" class="form-control" placeholder="<?php echo _("Senha"); ?>" required>
        			<br><button class="btn btn-lg btn-primary btn-block" type="submit">Acessar</button>
      			</form>
      			<br><p align="center">
      			<a href="../index.php" class="btn btn-danger btn-huge lato">In&iacute;cio</a>
      			<a href="esqueceu.php" class="btn btn-danger btn-huge lato">Recuperar senha</a>
      			<a href="cadastro.php" class="btn btn-danger btn-huge lato">Cadastre-se</a>
      			</p>
			</div>
			<div class="col-md-4"></div>
    	</div>
    </div>
</body>
</html>