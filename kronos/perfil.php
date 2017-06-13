<?php

session_start();

if (!isset($_SESSION["usuario"])) {
	header( "location:login.php" );
	exit();
}

require_once("clsUsuario.php");
require_once("clsValidacion.php");

$db = new PDO('mysql:host=localhost;dbname=kronos',
			'root',
			'');

$usuario = new Usuario($db);
$user = $_SESSION["usuario"];

// Utilizando el modo HIBRIDO, se crea una session que indica en qué almacenamiento se encuentra el usuario (JSON o BASEDEDATOS).
if (isset($_SESSION["storage"])) {
	$usuario->storage = $_SESSION["storage"];
}

// Traemnos los datos del perfil del usuario.
if ($_SESSION["usuario"] && empty($_POST)) {
		$usuario->traerDatosUsuario($user);
		$uNombre = $usuario->nombre;
		$uApellido = $usuario->apellido;
		$uMail = $usuario->mail;
		$uTelefono = $usuario->telefono;
		$uFoto = !empty($usuario->foto) ? $usuario->foto : 'sinfoto.png';
		$uDireccion1 = $usuario->direccion1;
		$uDireccion2 = $usuario->direccion2;
		$uCiudad = $usuario->ciudad;
		$uCP = $usuario->cp;
		$uProvincia = $usuario->provincia;
		$uUser = $usuario->usuario;
		$pass = $usuario->password;
}

// Si el usuario actualiza el perfil, se muestran los datos desde la variable global $_POST
if ($_POST)
{
	$usuario->traerDatosUsuario($user);
	$uNombre = $_POST["uNombre"];
	$uApellido = $_POST["uApellido"];
	$uMail = $_POST["uMail"];
	$uFoto = !empty($usuario->foto) ? $usuario->foto : 'sinfoto.png';
	$uTelefono = $_POST["uTelefono"];
	$uDireccion1 = $_POST["uDireccion1"];
	$uDireccion2 = $_POST["uDireccion2"];
	$uCiudad = $_POST["uCiudad"];
	$uCP = $_POST["uCP"];
	$uProvincia = $_POST["uProvincia"];
	$uUser = $_POST["uUser"];
	$pass = $usuario->password;
	$passnuevo = $_POST["passnuevo"];
	$cpass = $_POST["cpass"];

	//Validar
	$validar = new Validacion($db);
	$imagen = isset($_FILES["uFoto"]) ? $_FILES["uFoto"] : '';
	$errores = $validar->validacionEditarUsuario($_POST,$imagen);

	// Si no hay errores... editar.
	if (empty($errores))
	{
		$usuario->editarUsuario($_POST, $uUser, $imagen);
		!empty($usuario->fotoeditada) ? $usuario->recargarPerfil() : '';
		$perfil_editado = 1;
	}
}

  ?>

<!doctype html>
<html lang="en">
<head>

	<meta charset="utf-8">
	<!--[if IE]>
		<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
	<![endif]-->
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
				<li><a href="index.php">Inicio</a></li>
				<li><a href="category-grid.html">Cuenta</a></li>
				<li class="active">Mi perfil</li>
			</ol>
		<!-- Breadcrumb Ends -->
		<!-- Product Info Starts -->
			<div class="row product-info full">
			<!-- Left Starts -->
				<div class="col-sm-4 images-block">
						<img src="img/<?=(isset($uFoto)) ? $uFoto : 'sinfoto.png'; ?>" alt="Foto de perfil" class="img-responsive thumbnail" />
				</div>
			<!-- Left Ends -->
			<!-- Right Starts -->
				<div class="col-sm-8 product-details">

						<!-- Registration Section Starts -->
							<section>
								<div class="row">
									<!-- Registration Block Starts -->
										<div class="panel panel-smart">
											<div class="panel-heading">

												<!-- Mostramos los errores -->
											<?php if (isset($errores) && !empty($errores)): ?>
											<h5>Atención:</h5>
											<ul>
												<?php foreach ($errores as $k => $v): ?>
												<?php echo "<li style='color:red'>" . $v . "</li>" ?>
											<?php endforeach; ?>
											</ul><br>
											<?php endif; ?>

											<?php if (isset($perfil_editado)): ?>
											<ul>
												<li style='color:green'>Se ha actualizado tu perfil.</li>
											</ul><br>
											<?php endif; ?>

												<h3 class="panel-title">Información Personal</h3>
											</div>
											<div class="panel-body">
											<!-- Registration Form Starts -->
												<form action="perfil.php" method="POST" enctype="multipart/form-data" class="form-horizontal" role="form" >
												<!-- Personal Information Starts -->
													<div class="form-group">
														<label for="inputFname" class="col-sm-3 control-label">(*) Nombre: </label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="uNombre" name="uNombre" placeholder="Nombre" <?=(isset($uNombre)) ? 'value="' . $uNombre . '"' : ''; ?>>
														</div>
													</div>
													<div class="form-group">
														<label for="inputLname" class="col-sm-3 control-label">(*) Apellido:</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="uApellido" name="uApellido" placeholder="Apellido" <?=(isset($uApellido)) ? 'value="' . $uApellido . '"' : ''; ?>>
														</div>
													</div>
													<div class="form-group">
														<label for="inputEmail" class="col-sm-3 control-label">(*) Email:</label>
														<div class="col-sm-9">
															<input type="email" class="form-control" id="uMail" name="uMail" placeholder="Email" <?=(isset($uMail)) ? 'value="' . $uMail . '"' : ''; ?> readonly="readonly">
														</div>
													</div>
													<div class="form-group">
														<label for="inputPhone" class="col-sm-3 control-label">Teléfono:</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="uTelefono" name="uTelefono" placeholder="Teléfono" <?=(isset($uTelefono)) ? 'value="' . $uTelefono . '"' : ''; ?>>
														</div>
													</div>
													<div class="form-group">
														<label for="inputFname" class="col-sm-3 control-label">Foto de perfil:</label>
														<div class="col-sm-9">
															<input type="file" class="form-control" id="uFoto" name="uFoto" placeholder="Imagen" <?=(isset($uFoto)) ? 'value="' . $uFoto . '"' : ''; ?>>
														</div>
													</div>
													<div class="form-group">
														<div class="col-sm-offset-3 col-sm-9">
															<button type="submit" class="btn btn-warning">
																Actualizar
															</button>
														</div>
													</div>
												<!-- Personal Information Ends -->
													<h3 class="panel-heading inner">
														Domicilio de Entrega
													</h3>
												<!-- Delivery Information Starts -->

													<div class="form-group">
														<label for="inputAddress1" class="col-sm-3 control-label">Dirección/1:</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="uDireccion1" name="uDireccion1" placeholder="Direccion/1" <?=(isset($uDireccion1)) ? 'value="' . $uDireccion1 . '"' : ''; ?>>
														</div>
													</div>
													<div class="form-group">
														<label for="inputAddress2" class="col-sm-3 control-label">Dirección/2:</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="uDireccion2" name="uDireccion2" placeholder="Direccion/2" <?=(isset($uDireccion2)) ? 'value="' . $uDireccion2 . '"' : ''; ?>>
														</div>
													</div>
													<div class="form-group">
														<label for="inputCity" class="col-sm-3 control-label">Ciudad:</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="uCiudad" name="uCiudad" placeholder="Ciudad" <?=(isset($uCiudad)) ? 'value="' . $uCiudad . '"' : ''; ?>>
														</div>
													</div>
													<div class="form-group">
														<label for="inputPostCode" class="col-sm-3 control-label">Código Postal:</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="uCP" name="uCP" placeholder="CP" <?=(isset($uCP)) ? 'value="' . $uCP . '"' : ''; ?>>
														</div>
													</div>
													<div class="form-group">
														<label for="inputCountry" class="col-sm-3 control-label">Provincia:</label>
														<div class="col-sm-9">
															<input type="text" class="form-control" id="uProvincia" name="uProvincia" placeholder="Provincia" <?=(isset($uProvincia)) ? 'value="' . $uProvincia . '"' : ''; ?>>
														</div>
													</div>
													<div class="form-group">
														<div class="col-sm-offset-3 col-sm-9">
															<button type="submit" class="btn btn-warning">
																Actualizar
															</button>
														</div>
													</div>

												<!-- Delivery Information Ends -->
													<h3 class="panel-heading inner">
														Cambio de Contraseña
													</h3>
														<input type="hidden" class="form-control" id="uUser" name="uUser" placeholder="Usuario" <?=(isset($uUser)) ? 'value="' . $uUser . '"' : ''; ?> readonly="readonly">
														<div class="form-group">
														<label for="inputPassword" class="col-sm-3 control-label">Nueva contraseña:</label>
														<div class="col-sm-9">
															<input type="password" class="form-control" id="passnuevo" name="passnuevo" placeholder="Password">
														</div>
													</div>
													<div class="form-group">
														<label for="inputRePassword" class="col-sm-3 control-label">Confirma la Contraseña:</label>
														<div class="col-sm-9">
															<input type="password" class="form-control" id="cpass" name="cpass" placeholder="Confirmar Password">
															<input type="hidden" id="uCondiciones" name="uCondiciones" value="true">
														</div>
													</div>
													<div class="form-group">

														<div class="col-sm-9">

														</div>
													</div>

													<div class="form-group">
														<div class="col-sm-offset-3 col-sm-9">
															<button type="submit" class="btn btn-warning">
																Actualizar
															</button>
														</div>
													</div>
												<!-- Password Area Ends -->
												</form>
											<!-- Registration Form Starts -->
											</div>
										</div>
									<!-- Registration Block Ends -->
									</div>
								</div>
							</section>
						<!-- Registration Section Ends -->



					<!-- Available Options Ends -->
					</div>
				</div>
			<!-- Right Ends -->
			</div>
		<!-- Product Info Ends -->

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
