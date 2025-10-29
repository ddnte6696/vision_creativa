<?php
	//Se manda a llamar el archivo de configuración
	include_once 'config.php';
	//Defino la zona horaria
	date_default_timezone_set('America/Monterrey');
	//Obtngo y asigno los datos de la DB
	$user=USER_DB;
	$passwd=PASSWRD_DB;
	$host=HOST_DB;
	$dbname=NAME_DB;
	$port=PORT_DB;
	//Creo la cadena de conexion
		$dsn="mysql:host=$host;dbname=$dbname;port=$port";
	//Trato de realizar la conexion y sino arrojo el error
	try{
		$conn = new PDO($dsn,$user,$passwd);
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
			//Almaceno el error en una variabLe
			$error=$e->getMessage();
			//Defino la sentencia
			$sentencia="Conexion a base de datos";
			//Ubico el archivo desde donde se presenta el error
			$archivo=__FILE__;
			//Mando a escribir el mensaje
			escribir_log($error,$sentencia,$archivo);
			//Detengo el procedimiento
			die();
	}
?>