<?php


class Usuario {
	private $db;
	public $nombre;
	public $apellido;
	public $mail;
	public $telefono;
	public $foto;
	public $direccion1;
	public $direccion2;
	public $ciudad;
	public $cp;
	public $provincia;
	public $usuario;
	public $password;
	public $fotoeditada;
	public $storage = 'HIBRIDO'; // 'BASEDEDATOS', 'JSON', 'HIBRIDO';
	// Utilizando 'HIBRIDO' podran loguearse usuarios almacenados tanto en BD como en JSON. Al loguearse, se almacena en SESSION el tipo de storage de ese usuario (json o basededatos), para que luego la web utilice los metodos de la clase que se corresponden con ese storage.


	public function __construct($db) {
		$this->db = $db;
	}

	public function existeUsuario($u) {
	  if ($this->storage == 'JSON') {
				$usuarios = file_get_contents("usuarios.json");
				$usuariosArray = explode(PHP_EOL, $usuarios);
				array_pop($usuariosArray);
				foreach ($usuariosArray as $key => $usuario) {
			  $usuarioArray = json_decode($usuario, true);
					if ($u == $usuarioArray["usuario"])	{
						return true;
					}
				}
				return false;
			 }
		if ($this->storage == 'BASEDEDATOS') {
				$sql = "SELECT usuario FROM usuario WHERE usuario = '".$u."'";
				$result = $this->db->query($sql);
				$usuario = $result->fetch(PDO::FETCH_ASSOC);
				return $usuario;
			 }
	  if ($this->storage == 'HIBRIDO') {
	 			$usuarios = file_get_contents("usuarios.json");
	 			$usuariosArray = explode(PHP_EOL, $usuarios);
	 			array_pop($usuariosArray);
	 			foreach ($usuariosArray as $key => $usuario) {
	 		  $usuarioArray = json_decode($usuario, true);
	 				if ($u == $usuarioArray["usuario"])	{
	 					return true;
	 				}
	 			}
				$sql = "SELECT usuario FROM usuario WHERE usuario = '".$u."'";
				$result = $this->db->query($sql);
				$usuario = $result->fetch(PDO::FETCH_ASSOC);
				return $usuario;
			 }
 }


	public function loginUsuario($arr){
		if ($this->storage == 'JSON') {
			if (isset($arr['user']) && isset($arr['pass'])) {
				$usuarios = file_get_contents("usuarios.json");
				$usuariosArray = explode(PHP_EOL, $usuarios);
				array_pop($usuariosArray);
				foreach ($usuariosArray as $key => $usuario) {
						$usuarioArray = json_decode($usuario, true);
						if ($arr['user'] == $usuarioArray["usuario"]) {
							$userok = $arr['user'];
							$passjson = $usuarioArray["password"];
							if (md5($arr['pass']) == $passjson) {
								$logueado = 'true';
								$_SESSION["usuario"] = $usuarioArray["usuario"];
								$_SESSION["nombre"] = $usuarioArray["nombre"];
								return $logueado;
							}
					 }
				}
			}
		}
		if ($this->storage == 'BASEDEDATOS') {
			$sql = "SELECT id, usuario, mail FROM usuario WHERE usuario = '".$arr['user']."' and password = '".md5($arr['pass'])."'";
			$result = $this->db->query($sql);
			$usuario = $result->fetch(PDO::FETCH_ASSOC);
			if($usuario){
				session_start();
				$logueado = 'true';
				$_SESSION['usuario']=$usuario['usuario'];
				$_SESSION['mail']=$usuario['mail'];
				$_SESSION['id']=$usuario['id'];
				return $logueado;
			}
		 }
		 if ($this->storage == 'HIBRIDO') {
 			if (isset($arr['user']) && isset($arr['pass'])) {
 				$usuarios = file_get_contents("usuarios.json");
 				$usuariosArray = explode(PHP_EOL, $usuarios);
 				array_pop($usuariosArray);
 				foreach ($usuariosArray as $key => $usuario) {
 						$usuarioArray = json_decode($usuario, true);
 						if ($arr['user'] == $usuarioArray["usuario"]) {
 							$userok = $arr['user'];
 							$passjson = $usuarioArray["password"];
 							if (md5($arr['pass']) == $passjson) {
 								$logueado = 'true';
 								$_SESSION["usuario"] = $usuarioArray["usuario"];
 								$_SESSION["nombre"] = $usuarioArray["nombre"];
								$_SESSION["storage"] = 'JSON';
 								return $logueado;
 							}
 					 } else {
						$sql = "SELECT id, usuario, mail FROM usuario WHERE usuario = '".$arr['user']."' and password = '".md5($arr['pass'])."'";
 						$result = $this->db->query($sql);
 						$usuario = $result->fetch(PDO::FETCH_ASSOC);
 						if($usuario){
 							session_start();
 							$logueado = 'true';
 							$_SESSION['usuario']=$usuario['usuario'];
 							$_SESSION['mail']=$usuario['mail'];
 							$_SESSION['id']=$usuario['id'];
							$_SESSION['storage'] = 'BASEDEDATOS';
 							return $logueado;
 						}
 					 }
 				}
 			}
 		}
	}


	public function crearUsuario($miUsuario, $imagen)	{
		if ($imagen == $_FILES["uFoto"]) {
			if ($imagen["error"] == UPLOAD_ERR_OK)
			{
				$uniqid = uniqid();
				$rand_start = rand(1,5);
				$rand_8_char = substr($uniqid,$rand_start,8);
				$nombre=$imagen["name"];
				$archivo=$imagen["tmp_name"];
				$ext = pathinfo($nombre, PATHINFO_EXTENSION);
				$miArchivo = dirname(__FILE__);
				$miArchivo = $miArchivo . "/img/";
				$miNombreArchivo = "FotoPerfil_" . $rand_8_char . "." . $ext;
				$miArchivo = $miArchivo . $miNombreArchivo;
				move_uploaded_file($archivo, $miArchivo);
			}
		}
		$usuario = [
			"nombre" => $miUsuario["uNombre"],
			"apellido" => $miUsuario["uApellido"],
			"mail" => $miUsuario["uMail"],
			"telefono" => $miUsuario["uTelefono"],
			"foto" => isset($miNombreArchivo) ? $miNombreArchivo : NULL,
			"direccion1" => $miUsuario["uDireccion1"],
			"direccion2" => $miUsuario["uDireccion2"],
			"ciudad" => $miUsuario["uCiudad"],
			"cp" => $miUsuario["uCP"],
			"provincia" => $miUsuario["uProvincia"],
			"usuario" => $miUsuario["uUser"],
			"password" => md5($miUsuario["pass"]),
			"id" => $this->storage == 'JSON' ? $this->traerNuevoId() : NULL,
		];
		$_SESSION["usuario"] = $miUsuario["uUser"];
		$_SESSION["nombre"] = $miUsuario["uNombre"];
		return $usuario;
		}


  public function traerNuevoId() {
		$usuarios = file_get_contents("usuarios.json");
		$usuariosArray = explode(PHP_EOL, $usuarios);
		//Para quitar el último ENTER
		array_pop($usuariosArray);
		if (count($usuarios) == 0) {
			return 1;
		}
		$ultimoUsuario = $usuariosArray[count($usuariosArray) - 1];
		$ultimoUsuarioArray = json_decode($ultimoUsuario, true);
		return $ultimoUsuarioArray["id"] + 1;
	}


	public function guardarUsuario($arr) {
		 if ($this->storage == 'JSON') {
				$usuarioJSON = json_encode($arr);
				file_put_contents("usuarios.json", $usuarioJSON . PHP_EOL, FILE_APPEND);
			}
		// La opción 'HIBRIDO' permite el logueo y actualización de usuarios json (que vienen de la version anterior de la web), pero los nuevos usuarios los almacenamos en un solo lugar, la BD.
		 if ($this->storage == 'BASEDEDATOS' || $this->storage == 'HIBRIDO') {
				$sql = "INSERT INTO usuario (nombre, apellido, mail, telefono, foto, direccion1, direccion2, ciudad, cp, provincia, usuario, password)
		 		VALUES ('".$arr['nombre']."','".$arr['apellido']."','".$arr['mail']."','".$arr['telefono']."','".$arr['foto']."','".$arr['direccion1']."','".$arr['direccion2']."',
		 		'".$arr['ciudad']."','".$arr['cp']."','".$arr['provincia']."','".$arr['usuario']."','".$arr['password']."')";
		 		$this->db->query($sql);
		 		return $this->db->lastInsertId();
		  }
	}


		public function editarUsuario($POSTusuario, $uUser, $imagen) {
			if ($imagen == $_FILES["uFoto"]) {
				if ($imagen["error"] == UPLOAD_ERR_OK)
				{
					$uniqid = uniqid();
					$rand_start = rand(1,5);
					$rand_8_char = substr($uniqid,$rand_start,8);
					$nombre=$imagen["name"];
					$archivo=$imagen["tmp_name"];
					$ext = pathinfo($nombre, PATHINFO_EXTENSION);
					$miArchivo = dirname(__FILE__);
					$miArchivo = $miArchivo . "/img/";
					$miNombreArchivo = "FotoPerfil_" . $rand_8_char . "." . $ext;
					$miArchivo = $miArchivo . $miNombreArchivo;
					move_uploaded_file($archivo, $miArchivo);
				}
			}
	    if ($this->storage == 'JSON') {
					$usuarios = file_get_contents("usuarios.json");
					$usuariosArray = explode(PHP_EOL, $usuarios);
					file_put_contents("usuarios.json", '');
					array_pop($usuariosArray);
					foreach ($usuariosArray as $key => $usuario) {
						$usuarioArray = json_decode($usuario, true);
							if ($uUser == $usuarioArray["usuario"]) {
								$usuarioArray["nombre"] = $_POST["uNombre"];
								$usuarioArray["apellido"] = $_POST["uApellido"];
								$usuarioArray["telefono"] = $_POST["uTelefono"];
								if ($imagen["error"] == UPLOAD_ERR_OK) {
									$usuarioArray["foto"] = $miNombreArchivo;
									$this->fotoeditada = $miNombreArchivo;
								};
								$usuarioArray["direccion1"] = $_POST["uDireccion1"];
								$usuarioArray["direccion2"] = $_POST["uDireccion2"];
								$usuarioArray["ciudad"] = $_POST["uCiudad"];
								$usuarioArray["cp"] = $_POST["uCP"];
								$usuarioArray["provincia"] = $_POST["uProvincia"];
								if ((!empty($_POST["passnuevo"]) || !empty($_POST["cpass"])) && ($_POST["passnuevo"] == $_POST["cpass"])) {
									$usuarioArray["password"] = md5($_POST["passnuevo"]);
								}
								$todosUsuarios = json_encode($usuarioArray);
							} else {
								$todosUsuarios = json_encode($usuarioArray);
							}
						file_put_contents("usuarios.json", $todosUsuarios . PHP_EOL, FILE_APPEND);
					}
				}
			  if ($this->storage == 'BASEDEDATOS' || $this->storage == 'HIBRIDO') {
					if ($imagen["error"] == UPLOAD_ERR_OK) {
						$POSTusuario["uFoto"] = $miNombreArchivo;
					};
						$sql = "UPDATE usuario SET nombre = :nombre, apellido = :apellido, telefono = :telefono, direccion1 = :direccion1, direccion2 = :direccion2, ciudad = :ciudad, cp = :cp, provincia = :provincia
            WHERE usuario = :usuario";
						$stmt = $this->db->prepare($sql);
						$stmt->bindParam(':nombre', $POSTusuario['uNombre'], PDO::PARAM_STR);
						$stmt->bindParam(':apellido', $POSTusuario['uApellido'], PDO::PARAM_STR);
						$stmt->bindParam(':telefono', $POSTusuario['uTelefono'], PDO::PARAM_STR);
						$stmt->bindParam(':direccion1', $POSTusuario['uDireccion1'], PDO::PARAM_STR);
						$stmt->bindParam(':direccion2', $POSTusuario['uDireccion2'], PDO::PARAM_STR);
						$stmt->bindParam(':ciudad', $POSTusuario['uCiudad'], PDO::PARAM_STR);
						$stmt->bindParam(':cp', $POSTusuario['uCP'], PDO::PARAM_STR);
						$stmt->bindParam(':provincia', $POSTusuario['uProvincia'], PDO::PARAM_STR);
						$stmt->bindParam(':usuario', $uUser, PDO::PARAM_INT);
						$stmt->execute();
					if ((!empty($POSTusuario["passnuevo"]) || !empty($POSTusuario["cpass"])) && ($POSTusuario["passnuevo"] == $POSTusuario["cpass"])) {
						$sql2 = "UPDATE usuario SET password = '".md5($POSTusuario['passnuevo'])."'
						WHERE usuario = '".$uUser."'";
						$result = $this->db->query($sql2);
					}
					if (!empty($POSTusuario["uFoto"])) {
						$sql3 = "UPDATE usuario SET foto = '".$POSTusuario['uFoto']."'
						WHERE usuario = '".$uUser."'";
						$result = $this->db->query($sql3);
						$this->fotoeditada = $miNombreArchivo;
					}
				}
			}


	public function traerDatosUsuario($user) {
		 if ($this->storage == 'JSON') {
			 $usuarios = file_get_contents("usuarios.json");
			 $usuariosArray = explode(PHP_EOL, $usuarios);
			 array_pop($usuariosArray);
				 foreach ($usuariosArray as $key => $usuario) {
						 $usuarioArray = json_decode($usuario, true);
						 if ($user == $usuarioArray["usuario"]) {
							 $this->nombre = $usuarioArray["nombre"];
							 $this->apellido = $usuarioArray["apellido"];
							 $this->mail = $usuarioArray["mail"];
							 $this->telefono = $usuarioArray["telefono"];
							 $this->foto = $usuarioArray["foto"];
							 $this->direccion1 = $usuarioArray["direccion1"];
							 $this->direccion2 = $usuarioArray["direccion2"];
							 $this->ciudad = $usuarioArray["ciudad"];
							 $this->cp = $usuarioArray["cp"];
							 $this->provincia = $usuarioArray["provincia"];
							 $this->usuario = $usuarioArray["usuario"];
							 $this->password = $usuarioArray["password"];
						 }
			 }
	 	 }
	   if ($this->storage == 'BASEDEDATOS' || $this->storage == 'HIBRIDO') {
				 $sql = "SELECT * FROM usuario WHERE usuario = '".$user."'";
				 $result = $this->db->query($sql);
				 $usuario = $result->fetch(PDO::FETCH_ASSOC);
							 $this->nombre = $usuario["nombre"];
							 $this->apellido = $usuario["apellido"];
							 $this->mail = $usuario["mail"];
							 $this->telefono = $usuario["telefono"];
							 $this->foto = $usuario["foto"];
							 $this->direccion1 = $usuario["direccion1"];
							 $this->direccion2 = $usuario["direccion2"];
							 $this->ciudad = $usuario["ciudad"];
							 $this->cp = $usuario["cp"];
							 $this->provincia = $usuario["provincia"];
							 $this->usuario = $usuario["usuario"];
							 $this->password = $usuario["password"];
		 }
	}


	public function resetearPass($mail) {
				$uniqid = uniqid();
				$rand_start = rand(1,5);
				$rand_10_char = substr($uniqid,$rand_start,10);
				$nuevopass = $rand_10_char;
		 if ($this->storage == 'JSON') {
				$usuarios = file_get_contents("usuarios.json");
				$usuariosArray = explode(PHP_EOL, $usuarios);
				file_put_contents("usuarios.json", '');
				array_pop($usuariosArray);
				foreach ($usuariosArray as $key => $usuario) {
					$usuarioArray = json_decode($usuario, true);
						if ($mail == $usuarioArray["mail"]) {
							$usuarioArray["password"] = md5($nuevopass);
							$todosUsuarios = json_encode($usuarioArray);
						} else {
							$todosUsuarios = json_encode($usuarioArray);
						}
					file_put_contents("usuarios.json", $todosUsuarios . PHP_EOL, FILE_APPEND);
				}
			 }
		  if ($this->storage == 'BASEDEDATOS') {
					$sql = "UPDATE usuario SET password = '".md5($nuevopass)."'
					WHERE mail = '".$mail."'";
					$result = $this->db->query($sql);
			}
			 if ($this->storage == 'HIBRIDO') {
					 $sql = "UPDATE usuario SET password = '".md5($nuevopass)."'
					 WHERE mail = '".$mail."'";
					 $result = $this->db->query($sql);
					 $existe = $result->fetch(PDO::FETCH_ASSOC);
					 if (empty($existe)) {
						 $usuarios = file_get_contents("usuarios.json");
						 $usuariosArray = explode(PHP_EOL, $usuarios);
						 file_put_contents("usuarios.json", '');
						 array_pop($usuariosArray);
						 foreach ($usuariosArray as $key => $usuario) {
							 $usuarioArray = json_decode($usuario, true);
								 if ($mail == $usuarioArray["mail"]) {
									 $usuarioArray["password"] = md5($nuevopass);
									 $todosUsuarios = json_encode($usuarioArray);
								 } else {
									 $todosUsuarios = json_encode($usuarioArray);
								 }
							 file_put_contents("usuarios.json", $todosUsuarios . PHP_EOL, FILE_APPEND);
						 }
					 }
			}
		$this->mailNuevoPass($mail, $nuevopass);
	 }


 	public function userForget($mail) {
		 		if ($this->storage == 'JSON') {
					$usuarios = file_get_contents("usuarios.json");
					$usuariosArray = explode(PHP_EOL, $usuarios);
					array_pop($usuariosArray);
					foreach ($usuariosArray as $key => $usuario) {
							$usuarioArray = json_decode($usuario, true);
							if ($mail == $usuarioArray["mail"]) {
								$forget_user = $usuarioArray["usuario"];
							}
					  }
	 			 }
	 		  if ($this->storage == 'BASEDEDATOS') {
	 					$sql = "SELECT usuario FROM usuario
	 					WHERE mail = '".$mail."'";
	 					$result = $this->db->query($sql);
						$forget_user = $result->fetch(PDO::FETCH_ASSOC);
	 			}
				if ($this->storage == 'HIBRIDO') {
	 					$sql = "SELECT usuario FROM usuario
	 					WHERE mail = '".$mail."'";
	 					$result = $this->db->query($sql);
						$forget_user = $result->fetch(PDO::FETCH_ASSOC);
						if (empty($forget_user)) {
							$usuarios = file_get_contents("usuarios.json");
							$usuariosArray = explode(PHP_EOL, $usuarios);
							array_pop($usuariosArray);
							foreach ($usuariosArray as $key => $usuario) {
									$usuarioArray = json_decode($usuario, true);
									if ($mail == $usuarioArray["mail"]) {
										$forget_user = $usuarioArray["usuario"];
									}
							  }
						}
	 			}
 		return $forget_user;
 	 }


	public function mailNuevoPass($mail, $pass) {
			$destinatario = $mail;
			$asunto = "Nueva Contraseña";
			$cuerpo = '
			<html>
			<head>
				 <title>Nueva Contraseña</title>
			</head>
			<body>
			<h1>Kronos</h1>
			<br>
			<p>Hemos recibido una solicitud para resetear tu contraseña.</p><br>
			<p>Tu nueva contraseña es: <b>' . $pass . '</b></p>
			<br>
			Te recomendamos cambiarla por una que puedas recordar :)<br>
			Ahora si, <a href="http://localhost/sprint3/login.php">¡quiero loguearme!</a>
			</body>
			</html>
			';
			//para el envío en formato HTML
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "From: Kronos <info@kronos.com.ar>\r\n";
			$headers .= "Reply-To: info@kronos.com.ar\r\n";
			$headers .= "Return-path: info@kronos.com.ar\r\n";
			mail($destinatario,$asunto,$cuerpo,$headers);
		}

	public function usuarioCreado() {
		header("location:exito.php");exit;
	}

	public function recargarPerfil() {
		header("location:perfil.php");exit;
	}
}
