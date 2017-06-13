<?php

  $db = new PDO('mysql:host=localhost;dbname=kronos',
			'root',
			'');

  //Contadores
  $registros = 0;
  $agregados = 0;

  //Leemos Json linea por linea y ejecutamos el sql para agregar cada usuario a la BD.
  $usuarios = file_get_contents("usuarios.json");
  $usuariosArray = explode(PHP_EOL, $usuarios);
  array_pop($usuariosArray);
    foreach ($usuariosArray as $key => $usuario) {
         $usuarioArray = json_decode($usuario, true);
         $sql = "INSERT INTO usuario (nombre, apellido, mail, telefono, foto, direccion1, direccion2, ciudad, cp, provincia, usuario, password)
         VALUES ('".$usuarioArray["nombre"]."','".$usuarioArray["apellido"]."','".$usuarioArray["mail"]."','".$usuarioArray['telefono']."','".$usuarioArray['foto']."','".$usuarioArray['direccion1']."','".$usuarioArray['direccion2']."',
         '".$usuarioArray['ciudad']."','".$usuarioArray['cp']."','".$usuarioArray['provincia']."','".$usuarioArray['usuario']."','".$usuarioArray['password']."')";
         $result = $db->query($sql);
         $registros++;
         if (!empty($result)) {
           $agregados++;
         }
     }

  // Informamos al usuario la cantidad de registros migrados.
  echo 'Se migraron ' . $agregados . ' registros a la base de datos de un total de ' . $registros . '.';
  if ($agregados != $registros) {
    echo '<br><br>Algunos registros no pudieron agregarse debido a que ya existía un registro previo en la base de datos con ese usuario o ese mail.';
  }

?>
