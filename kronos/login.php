<?php

session_start();
require 'clsUsuario.php';

$db = new PDO('mysql:host=localhost;dbname=kronos',
				'root',
				'');

$usuario = new Usuario($db);

if ($_POST) {
	$errores = [];
	if (trim($_POST["user"]) == "") {
		$errores[] = "Ingresa un usuario";
	}
	if (trim($_POST["user"]) !== "" && !$usuario->existeUsuario($_POST['user'])) {
		$errores[] = "El usuario ingresado no es válido";
	}
	if (trim($_POST["pass"]) == "") {
		$errores[] = "Ingresa una contraseña";
	}
}

// LOGUEO
	if (isset($_POST['user']) && isset($_POST['pass']) && empty($errores)) {

			$usuario->loginUsuario($_POST);

			if (trim($_POST["pass"]) !== "" && $usuario->existeUsuario($_POST['user'])) {
				$errores[] = "Contraseña incorrecta";
			}
	 }


// Si el usuario está logueado, no podrá acceder a login.php, será enviado a su perfil
if (isset($_SESSION["usuario"])) {
	header( "location:perfil.php" );
	exit();
}
 ?>

<!doctype html>
<html lang="en">
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>Kronos</title>

	<!-- Bootstrap Core CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">

	<!-- Google Web Fonts -->
	<link href="http://fonts.googleapis.com/css?family=Roboto+Condensed:300italic,400italic,700italic,400,300,700" rel="stylesheet" type="text/css">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>

	<!-- CSS Files -->
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<link href="css/responsive.css" rel="stylesheet">

	<!--[if lt IE 9]>
		<script src="js/ie8-responsive-file-warning.js"></script>
	<![endif]-->

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

	<!-- Fav and touch icons -->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/fav-144.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/fav-114.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/fav-72.png">
	<link rel="apple-touch-icon-precomposed" href="images/fav-57.png">
	<link rel="shortcut icon" href="images/fav.png">

</head>
<body>


	<?php include 'header.php';?>


	<!-- Main Container Starts -->
		<div id="main-container" class="container">
		<!-- Breadcrumb Starts -->
			<ol class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li class="active">Usuarios</li>
			</ol>
		<!-- Breadcrumb Ends -->

							 <?php if (isset($logueado)): ?>
								 <!-- Main Heading Starts -->
								 	<h2 class="main-heading text-center">
								 		Usuario Logueado
								 	</h2>
								 <!-- Main Heading Ends -->
								 <!-- Login Form Section Starts -->
								 	<section class="login-area">
								 		<div class="row">
								 			<div class="col-sm-6">
								 <!-- Login Panel Starts -->
									<div class="panel panel-smart">
										<div class="panel-heading">
											<h3 class="panel-title">Tu cuenta</h3>
										</div>
										<div class="panel-body">
											<p>
												Bienvenido a tu cuenta.
											</p>
										<!-- Login Form Starts -->
							 <?php endif; ?>

							 <?php if (!isset($logueado)): ?>
								 <!-- Main Heading Starts -->
								 	<h2 class="main-heading text-center">
								 		USUARIOS
								 	</h2>
								 <!-- Main Heading Ends -->
								 <!-- Login Form Section Starts -->
								 	<section class="login-area" style = "display:block; text-align:center;">
								 		<div class="row">
											<div class="col-sm-3">
												</div>
								 			<div class="col-sm-6">
								 <!-- Login Panel Starts -->
			 						<div class="panel panel-smart">
			 							<div class="panel-heading">
			 								<h3 class="panel-title">Login</h3>
			 							</div>
			 							<div class="panel-body">

											<!-- Mostramos los errores -->
										<?php if (isset($errores) && !empty($errores)): ?>

											<?php foreach ($errores as $k => $v): ?>
											<?php echo "<p style='color:red; margin-bottom:-1px'>" . $v . "</p>" ?>
										<?php endforeach; ?>

										<?php endif; ?>
										<br>

										<?php if (!isset($errores)): ?>
			 								<p>
			 									Por favor introduce tu usuario y contraseña.
			 								</p><br>
										<?php endif; ?>
			 							<!-- Login Form Starts -->
								 <form action="login.php" method="POST" class="form-inline" role="form">
 									<div class="form-group">
 										<label class="sr-only" for="exampleInputEmail2">Username</label>
 										<input type="text" class="form-control" id="user" name="user" placeholder="Usuario" if <?=(isset($_POST['user']) && $usuario->existeUsuario($_POST['user'])) ? 'value="' . $_POST['user'] . '"' : ''; ?>>
 									</div>
									<br><br>
 									<div class="form-group">
 										<label class="sr-only" for="exampleInputPassword2">Password</label>
 										<input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña">
 									</div>
									<br><br>
 									<button type="submit" class="btn btn-warning">
 										Entrar
 									</button>
 								</form>
								<br>
								> <a href="user_forget.php">No recuerdo mi usuario.</a><br>
								> <a href="pass_forget.php">No recuerdo mi contraseña.</a>
								<br><br>¿No tienes una cuenta? Puedes registrarte <a href="register.php">aquí.</a>
 							<!-- Login Form Ends -->
						</div>
 						</div>
							 <?php endif; ?>


					<!-- Login Panel Ends -->
					</div>
					<div class="col-sm-6">

					</div>
				</div>
			</section>
		<!-- Login Form Section Ends -->
		</div>
	<!-- Main Container Ends -->

	<?php include 'footer.php';?>

	<!-- JavaScript Files -->
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/jquery-migrate-1.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/bootstrap-hover-dropdown.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
	<script src="js/custom.js"></script>
</body>
</html>
