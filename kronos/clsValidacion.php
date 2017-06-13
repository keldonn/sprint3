<?php

class Validacion {
	private $db;
  public $minPass = 8;
	public $storage = 'HIBRIDO'; // 'BASEDEDATOS', 'JSON', 'HIBRIDO';
	// Usando 'HIBRIDO' podran loguearse usuarios almacenados tanto en la BD como en JSON. Al loguearse, se almacena en SESSION el tipo de storage de ese usuario (json o basededatos), para que luego la web utilice los métodos de la clase que se corresponden con ese storage.


	public function __construct($db) {
		$this->db = $db;
	}


	public function existeUsuarioConEsteEmail($e) {
		if ($this->storage == 'JSON') {
				$usuarios = file_get_contents("usuarios.json");
				$usuariosArray = explode(PHP_EOL, $usuarios);
				array_pop($usuariosArray);
				foreach ($usuariosArray as $key => $usuario) {
					$usuarioArray = json_decode($usuario, true);
					if ($e == $usuarioArray["mail"])					{
						return true;
					}
				}
				return false;
			}
		if ($this->storage == 'BASEDEDATOS') {
	 			$sql = "SELECT mail FROM usuario WHERE mail = '".$e."'";
	 			$sth = $this->db->query($sql);
	 			return $sth->fetch(PDO::FETCH_ASSOC);
	 		}
		if ($this->storage == 'HIBRIDO') {
				$sql = "SELECT mail FROM usuario WHERE mail = '".$e."'";
				$sth = $this->db->query($sql);
				$result = $sth->fetch(PDO::FETCH_ASSOC);
					if (empty($result)) {
						$usuarios = file_get_contents("usuarios.json");
						$usuariosArray = explode(PHP_EOL, $usuarios);
						array_pop($usuariosArray);
						foreach ($usuariosArray as $key => $usuario) {
							$usuarioArray = json_decode($usuario, true);
							if ($e == $usuarioArray["mail"]) {
								return true;
							}
						 }
						return false;
					} else {
						return $result;
					}
			}
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

	public function esUnaImagen($ext) {
		$ext = strtolower($ext);
		if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif') {
			return TRUE;
		}
		return FALSE;
	}


	public function tienePesoValido($size) {
		$pesoMaximo = 300000;
		// 300 KB
		if ($size > $pesoMaximo) {
			return FALSE;
		}
		return TRUE;
	}


	public function validarPassword($p) {
		if(strlen($p) < $this->minPass) {
			return FALSE;
		}
		return TRUE;
	}


	public function validacionUsuario($miUsuario, $imagen) {
		$errores = [];
		if (trim($miUsuario["uNombre"]) == "") {
			$errores[] = "Ingresa tu nombre";
		}
		if (trim($miUsuario["uApellido"]) == "") {
			$errores[] = "Ingresa tu apellido";
		}
		if ($miUsuario["uMail"] == "") {
			$errores[] = "Ingresa tu mail";
		} else {
			if (!filter_var($miUsuario["uMail"], FILTER_VALIDATE_EMAIL)) {
				$errores[] = "El mail no es válido";
			}
			if ($this->existeUsuarioConEsteEmail($miUsuario["uMail"])) {
				$errores[] = "Email ya registrado";
			}
		}
			if (!empty($miUsuario["uTelefono"]) && !is_numeric($miUsuario["uTelefono"])) {
				$errores[] = "El telefono ingresado no es válido";
			}
			if (!empty($miUsuario["uCP"]) && !is_numeric($miUsuario["uCP"])) {
				$errores[] = "El código postal ingresado no es válido";
			}
	 	if ($imagen == $_FILES["uFoto"]) {
			if ($imagen["error"] == UPLOAD_ERR_OK) {
				$nombre=$imagen["name"];
				$archivo=$imagen["tmp_name"];
				$ext = pathinfo($nombre, PATHINFO_EXTENSION);
					if (!$this->esUnaImagen($ext) || !$this->tienePesoValido($imagen['size'])) {
						$errores[] = 'La imagen es muy pesada o no tiene un formato valido';
					}
				}
		}
		if ($miUsuario["uUser"] == "") {
			$errores[] = "Ingresa un usuario";
		} else {
			if ($this->existeUsuario($miUsuario["uUser"]) && !isset($_SESSION["usuario"])) {
				$errores[] = "El Usuario ingresado ya existe";
				}
			}
		if (trim($miUsuario["pass"]) == "") {
			$errores[] = "Ingresa tu contraseña";
		} else {
			if (!$this->validarPassword($miUsuario["pass"])) {
					$errores[] = 'Tu contraseña debe tener al menos ' . $this->minPass . ' caracteres';
			}
		}
		if (trim($miUsuario["cpass"]) == "") {
			$errores[] = "Ingresa tu contraseña nuevamente";
		}
		if ($miUsuario["pass"] != $miUsuario["cpass"]) {
			$errores[] = "Las contraseñas ingresadas deben ser iguales";
		}
		if (!isset($miUsuario["uCondiciones"])) {
			$errores[] = "Debes aceptar los terminos y condiciones";
		}
		return $errores;
	}


  public function validacionEditarUsuario($miUsuario, $imagen) {
				$errores = [];
				if (trim($miUsuario["uNombre"]) == "") {
					$errores[] = "Ingresa tu nombre";
				}
				if (trim($miUsuario["uApellido"]) == "") {
					$errores[] = "Ingresa tu apellido";
				}
				if (!empty($miUsuario["uTelefono"]) && !is_numeric($miUsuario["uTelefono"])) {
				  $errores[] = "El telefono ingresado no es válido";
			  }
				if (!empty($miUsuario["uCP"]) && !is_numeric($miUsuario["uCP"])) {
				  $errores[] = "El código postal ingresado no es válido";
			  }
				if ($imagen == $_FILES["uFoto"]) {
					if ($imagen["error"] == UPLOAD_ERR_OK) {
						$nombre=$imagen["name"];
						$archivo=$imagen["tmp_name"];
						$ext = pathinfo($nombre, PATHINFO_EXTENSION);
							if (!$this->esUnaImagen($ext) || !$this->tienePesoValido($imagen['size'])) {
								$errores[] = 'La imagen es muy pesada o no tiene un formato valido';
							}
						}
				if ((!empty($miUsuario["passnuevo"]) || !empty($miUsuario["cpass"])) && ($miUsuario["passnuevo"] !== $miUsuario["cpass"])) {
					$errores[] = "Las contraseñas ingresadas deben ser iguales";
				} else {
					if ((!empty($miUsuario["passnuevo"]) || !empty($miUsuario["cpass"])) && (!$this->validarPassword($miUsuario["passnuevo"]))) {
						$errores[] = 'Tu contraseña debe tener al menos ' . $this->minPass . ' caracteres';
					}
				}
		    if (!isset($miUsuario["uCondiciones"])) {
					$errores[] = "Debes aceptar los terminos y condiciones";
				}
				return $errores;
			}
		}
}
