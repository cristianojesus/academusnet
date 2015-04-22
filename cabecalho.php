<!doctype html>

<?php echo "<html lang=" . substr($language,0,5) . ">"; ?>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Academusnet</title>
	<meta name="generator" content="Academusnet" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="jquery-ui/css/humanity/jquery-ui.css" rel="stylesheet">
	<link href="jquery-ui/jstree/themes/classic/style.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">
	<link href="styles.css" rel="stylesheet">
	<link href="jquery-ui/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet">
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
	<script src="jquery-ui/jstree/jquery.jstree.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/scripts.js"></script>
	<script src="jquery-ui/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
	<?php
	if (substr($_SESSION["locale"],0,5) != "en_US") {
		echo "<script src='jquery-ui/bootstrap-datepicker/dist/locales/bootstrap-datepicker." . 
		strtr(substr($_SESSION["locale"], 0, 5), "_", "-") . ".min.js' charset='UTF-8'></script>";
	}
	?>

</head>

<body>

	<header class="navbar navbar-default navbar-static-top" role="banner">
		<div class="container">
			<div class="navbar-header">
				<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
        				<span class="sr-only">Toggle navigation</span>
        				<span class="icon-bar"></span>
        				<span class="icon-bar"></span>
        				<span class="icon-bar"></span>
      				</button>
      				<?php
						if ($_SESSION["logok_academusnet"]) { ?>
							<a href="principal.php" class="navbar-brand"><img src="../images/logo.png" style="margin: -12px 0px"></a>
						<?php } else {?>
							<a href="login.php" class="navbar-brand"><img src="../images/logo.png" style="margin: -12px 0px"></a>
						<?php }?>
    			</div>
    			<nav class="collapse navbar-collapse" role="navigation">
      				<ul class="nav navbar-nav">
        				<li>
        				<?php
        					if (!$_SESSION["visitante"]) {
        						echo "<a href='principal.php' id=textBold>" . _("Principal") . "</a></li>";
        						echo "<li><a href='mailto:suporte@academusnet.pro.br'>" . _("Suporte") . "</a></li>";
        					}
        				?>
      				</ul>
					<div class="navbar-collapse collapse">
      					<ul class="nav navbar-nav navbar-right">
      					<?php
      						if ($_SESSION["professor"] == 1 or $_SESSION["professor"] == 2) {
      							echo "<li class='dropdown'>\n";
      							echo "<a class='dropdown-toggle' role='button' data-toggle='dropdown' href='#'>\n";
      							echo "<i class='glyphicon glyphicon-eye-open'></i>\n";
      							echo _("Interface");
      							echo "<span class='caret'></span></a>\n";
      							echo "<ul id='g-account-menu' class='dropdown-menu' role='menu'>\n";
      							echo "<li><a href='principal.php?tipo=1'>" . _("Professor") . "</a></li>";
								echo "<li><a href='principal.php?tipo=0'>" . _("Aluno") . "</a></li></ul></li>\n";
      						} 
      					?>
        					<li class="dropdown">
          						<a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#">
								<i class="glyphicon glyphicon-globe"></i>
								<?php echo _("Idioma");?>
								<span class='caret'></span></a>
								<ul id='g-account-menu' class='dropdown-menu' role='menu'>
									<li><a href='principal.php?locale=pt_BR.UTF-8'>Portugu&ecirc;s Brasileiro</a></li>
									<li><a href='principal.php?locale=en_US.UTF-8'>English</a></li>
								</ul>
        					</li>
        					<li class="dropdown">
          						<a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#">
							<i class="glyphicon glyphicon-user"></i>

							<?php

							if ($_SESSION["logok_academusnet"]) {
								echo $_SESSION["nome_usuario"];
								echo "<span class='caret'></span></a>\n";
          							echo "<ul id='g-account-menu' class='dropdown-menu' role='menu'>\n";
            							echo "<li><a href='perfil.php'>" . _("Perfil") . "</a></li></ul>\n";
							}
							else {
								echo "Visitante";
								echo "<span class='caret'></span></a>\n";
							} 

							mysql_close($dblink);
							
							?>
							
        					</li>
        					<li><a href="login.php"><i class="glyphicon glyphicon-lock"></i> <?php echo _("Sair"); ?></a></li>
      					</ul>
    				</div>

    			</nav>
  		</div>
	</header>

	<form name='academusnet' action='academusnet.php' method='post'>
		<input type="hidden" id="arquivo" name="arquivo" value="">
		<input type="hidden" id="dados" name="dados" value="">
	</form>
	
	<div class="container">
		<div class="row">

