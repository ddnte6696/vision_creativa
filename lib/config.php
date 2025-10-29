<?php
	//Direccionamiento a directorios y archivo de BDD
		define( 'A_RAIZ', '' );
		define( 'A_LIB', A_RAIZ.'lib/' );
		define( 'A_JS', A_RAIZ.'js/' );
		define( 'A_MODEL', A_RAIZ.'model/' );
		define( 'A_DOCS', A_RAIZ.'docs/' );
		define( 'A_LOGS', A_RAIZ.'logs/' );
		define( 'A_DOCS_V', 'docs/' );
		define( 'A_VIEW', A_RAIZ.'view/' );
		define( 'A_HEAD_IMPRESION', A_VIEW.'impresion.php' );
		define( 'A_CSS', A_RAIZ.'css/' );
		define( 'A_IMG', A_RAIZ.'img/' );
		define( 'A_RESTORE', A_RAIZ.'restore/' );
		define( 'A_IMG_V', 'img/' );
		define( 'A_CONNECTION', A_LIB.'connection.sql.db.php' );
	//Constantes para conexion a base de datos (SANDBOX)
		define('USER_DB','root');
		define('PASSWRD_DB','');
		define('HOST_DB','localhost');
		define('NAME_DB','diginet_vision_creativa');
		define('PORT_DB','3306');
	/*//Constantes para conexion a base de datos (PRODUCCION)
		define('USER_DB','omnibusg_vision_creativa');
		define('PASSWRD_DB','?^dJQCW5]wX.');
		define('HOST_DB','localhost');
		define('NAME_DB','omnibusg_vision_creativa');
		define('PORT_DB','3306');*/
	//Librerias de encriptado y limpieza
		include_once A_LIB.'self/self_lmpz.php';
		include_once A_LIB.'self/self_form_sender.php';
		include_once A_LIB.'self/self_ncrptcn.php';
	//Archivo de funciones varias
		include_once A_LIB.'funciones.php';
//
?>