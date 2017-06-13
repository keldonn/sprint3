<?php

//Esto no se ejecuta porque PHP debería estar en modo inseguro para que funcione.
//Por lo tanto, debe tomarse el contenido de la variable $comando y ejecutarse por linea de comandos.
$comando = 'mysql --user=root --password kronos2 < dump_kronos.sql';

system($comando, $error);

?>
