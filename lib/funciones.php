<?php
  //Defino la zona horaria
    date_default_timezone_set('America/Monterrey');
  //Tipos de extensiones de archivos permitidos
    $extensionesPermitidas = array('jpg', 'jpeg', 'pdf');
  //Función para eliminar los acentos y la virgulilla de la Ñ
    function eliminar_tildes($cadena){
      $cadena = str_replace('Á','A',$cadena);
      $cadena = str_replace('É','E',$cadena);
      $cadena = str_replace('Í','I',$cadena);
      $cadena = str_replace('Ó','O',$cadena);
      $cadena = str_replace('Ú','U',$cadena);
      $cadena = str_replace('Ñ','N',$cadena);
      return $cadena;
    }
  //Funcion para poner o quitar comillas de un texto
    function pone_comillas($texto){ return str_replace('çÇç', '"', $texto); }
    function quita_comillas($texto){ return str_replace('"', 'çÇç', $texto); }
  //Elimina caracteres especiales de la cadena
    function elimina_especiales($cadena){
      //Defino un arreglo con los caracteres especiales a eliminar
      $especiales= array(
        '|',
        '°',
        '¬',
        '!',
        '"',
        '#',
        '$',
        '%',
        '&',
        "/",
        '(',
        ')',
        '=',
        '?',
        '¡',
        '¿',
        ',',
        ".",
        ";",
        "*",
        '<',
        '>',
        "\n"
      );
      //Elimino los caracteres listados del texto
      $resultado=str_replace($especiales,"",$cadena);
      //Devuelvo el resultado corregido
      return $resultado;
    }
  //Funcion para transformar la hora a un formato especifico
    function transforma_hora($hora,$formato="24",$separador=":"){
      //Identifico si se definio un formato de 12 o 24 horas
        if ($formato=="12") {
          //Transformo la hora a un formato de 12 horas
            $texto=date('g:i a', strtotime($hora));
          //
        }else{
          //Obtengo y separo la hora en horas, minutos y segundos
            $dato=explode(":", $hora);
          //Almaceno las partes obtenidas
            $horas=$dato[0];
            $minutos=$dato[1];
            $segundos=$dato[2];
          //Concateno los datos con el separador definido
            $texto=$horas.$separador.$minutos.$separador.$segundos;
          //
        }
      //Retorno el texto formateado
        return $texto;
      //
    }
  //Funcion para transformar la fecha a un formato solicitado
    function transforma_fecha($fecha,$tipo=0,$separador="-"){
      //Obtenemos la fecha y la separamos por su guion medio
        $dato = explode("-",$fecha);
      //Almaceno cada parte en una variable
        $ano=$dato[0];
        $mes=$dato[1];
        $dia=$dato[2];
      //Evaluamos si se sequiere que se convierta a dexto el mes
        if ($tipo==1) {
          //Obtengo el mes y lo paso a texto
            switch ($dato[1]) {
              case '1':
                $mes='Enero';
              break;
              case '2':
                $mes='Febrero';
              break;
              case '3':
                $mes='Marzo';
              break;
              case '4':
                $mes='Abril';
              break;
              case '5':
                $mes='Mayo';
              break;
              case '6':
                $mes='Junio';
              break;
              case '7':
                $mes='Julio';
              break;
              case '8':
                $mes='Agosto';
              break;
              case '9':
                $mes='Septiembre';
              break;
              case '10':
                $mes='Octubre';
              break;
              case '11':
                $mes='Noviembre';
              break;
              case '12':
                $mes='Diciembre';
              break;
            }
          //
        }
      //Concateno los datos con el separador definido
        $texto=$dia.$separador.$mes.$separador.$ano;
      //Retorno el dato obtenido
        return $texto;
      //
    }
  //Devuelve la fecha y hora actuales
    function ahora($tipo){
      //Se obtiene el timepo actual
        $hoy = getdate();
      //Se evalua el tipo de dato que se desea obtener
        switch ($tipo) {
          //Se solicita la fecha
            case '1':
              $actual=date('Y-m-d');
            break;
          //Se solicita la hora
            case '2':
              $actual=date('H:i:s');
            break;
          //Se solicitan el timestamp actual
            case '3':
              $actual=date('Y-m-d H:i:s');
            break;
          //
        }
      //Retorno el dato formateado
        return $actual;
      //
    }
  //Genera una referecia de fecha
    function ref_fecha(){
      //Obtengo la fecha actual en texto separada por guin bajo
        $texto=transforma_fecha(ahora(1),1,"_")."_";
      //Devuelvo el dato formateado
        return $texto;
      //
    }
  //Devuelve la referencia horaria
    function referencia_horaria(){
      //Obtengo la referencia horaria con base en la funcion de transforma hora
        $texto=transforma_hora(ahora(2),"24","");
      //Retorno el texto formateado
        return $texto;
      //
    }
  //Devuelve la referencia tenporal
    function referencia_temporal(){
      //Obtengo la hora actual
      $time=ahora(2);
      //Le elimino los : y la guardo en la variable
      $tiempo=str_replace(':','',$time);
      //Obtengo la fecha actual
      $date=ahora(1);
      //Le elimino los - y la guardo en la variable
      $dia=str_replace('-','',$date);
      //Realizo cun concatenado
      $referencia_temporal=$dia."-".$tiempo;
      //Devuelvo el valor
      return $referencia_temporal;
    }
  //Devuelve el dato limpiado, le da formato al texto y lo encripta según los identificadores 
    function campo_limpiado($dato,$encript = 0,$mayus = 0,$llenado = 0){
      if (($mayus==1)&&($encript==1)) { //Mayusculas y encriptar
        //Limpieza del campo
          $campo = htmlspecialchars(limpiar_campo($dato),ENT_QUOTES);
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($campo)) {
              $campo = $campo;
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //Conversion a mayúsculas
          $campo = mb_strtoupper($campo);
        //Encriptacion
          $campo = encriptar_ligero($campo);
        //
      }elseif (($mayus==2)&&($encript==1)) { //Minusculas y encriptar
        //Limpieza del campo
          $campo = htmlspecialchars(limpiar_campo($dato),ENT_QUOTES);
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($campo)) {
              $campo = $campo;
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //Conversion a minusculas
          $campo = strtolower($campo);
        //Encriptacion
          $campo = encriptar_ligero($campo);
        //
      }elseif (($mayus==0)&&($encript==1)) { //Solo encriptar
        //Limpieza del campo
          $campo = htmlspecialchars(limpiar_campo($dato),ENT_QUOTES);
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($campo)) {
              $campo = $campo;
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //Encriptacion
          $campo = encriptar_ligero($campo);
        //
      }elseif(($mayus==1)&&($encript==2)) { //Mayusculas y desencriptar
        //Revision de cadena llena
          if (!empty($dato)) {
            //No se realiza nada
          }else{
            //Se imprime un mensaje de alerta
            echo "<script>alert('VALOR O DATO INVALIDO');</script>";
            //Se deiene el procedimiento
            die();
          }
        //Desencriptacion
          $campo = desencriptar_ligero($dato);
        //Limpieza del campo
          $campo = htmlspecialchars(limpiar_campo($campo),ENT_QUOTES);
        //Conversion a mayúsculas
          $campo = mb_strtoupper($campo);
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($campo)) {
              $campo = $campo;
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //
      }elseif (($mayus==2)&&($encript==2)) { //Minusculas y desencriptar
        //Revision de cadena llena
          if (!empty($dato)) {
            //No se realiza nada
          }else{
            //Se imprime un mensaje de alerta
            echo "<script>alert('VALOR O DATO INVALIDO');</script>";
            //Se deiene el procedimiento
            die();
          }
        //Desencriptacion
          $campo = desencriptar_ligero($dato);
        //Limpieza del campo
          $campo = htmlspecialchars(limpiar_campo($campo),ENT_QUOTES);
        //Conversion a minusculas
          $campo = strtolower($campo);
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($campo)) {
              $campo = $campo;
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //
      }elseif (($mayus==0)&&($encript==2)) { //Solo desencriptar
        //Revision de cadena llena
          if (!empty($dato)) {
            //No se realiza nada
          }else{
            //Se imprime un mensaje de alerta
            echo "<script>alert('VALOR O DATO INVALIDO');</script>";
            //Se deiene el procedimiento
            die();
          }
        //Desencriptacion
          $campo = desencriptar_ligero($dato);
        //Limpieza del campo
          $campo = htmlspecialchars(limpiar_campo($campo),ENT_QUOTES);
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($campo)) {
              $campo = $campo;
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //
      }elseif(($mayus==1)&&($encript==0)) { //Mayusculas
        //Limpieza del campo
          $campo = htmlspecialchars(limpiar_campo($dato),ENT_QUOTES);
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($campo)) {
              $campo = $campo;
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //Conversion a mayúsculas
          $campo = mb_strtoupper($campo);
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($campo)) {
              $campo = $campo;
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //
      }elseif (($mayus==2)&&($encript==0)) { //Minusculas
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($dato)) {
              //No se realiza nada
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //Limpieza del campo
          $campo = htmlspecialchars(limpiar_campo($dato),ENT_QUOTES);
        //Conversion a minusculas
          $campo = strtolower($campo);
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($campo)) {
              $campo = $campo;
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //
      }elseif (($mayus==0)&&($encript==0)) { //Ningun formato
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($dato)) {
              //No se realiza nada
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //Limpieza del campo
          $campo = htmlspecialchars(limpiar_campo($dato),ENT_QUOTES);
        //Revision de llenado
          if ($llenado==1) {
            if (!empty($campo)) {
              $campo = $campo;
            }else{
              //Se imprime un mensaje de alerta
              echo "<script>alert('¡ATENCION!, UNO DE LOS DATOS ESTA INCOMPLETO');</script>";
              //Se deiene el procedimiento
              die();
            }
          }
        //
      }
      //Se regresa el dato ya formateado
      return $campo;
    }
  //Función para escribir el log de fallos
    function escribir_log($error,$sentencia,$archivo) {
      //Le quito los saltos de linea a la sentencia
      $sentencia = str_replace("\n",'',$sentencia);
      //Creeo la referencia de tiempo
      $referencia=referencia_horaria();
      //Evaluo si existe un logueo
        $linea="$referencia!!NO IDENTIFICADO!!$error!!$sentencia!!$archivo";
      //Encripto el texto
      $texto=campo_limpiado($linea,1,0,0);
      // Ruta y nombre del archivo de log
      $nombre_txt = A_LOGS . ahora(1) . "-ErrorLog.txt";
      // Intentar abrir el archivo
      $archivo = fopen($nombre_txt, 'a+');
      if ($archivo) {
        // Escribir en el archivo
        fwrite($archivo, $texto . "\n");
        // Cerrar el archivo
        fclose($archivo);
        echo "<script>alert('¡ERROR!, CONTACTA CON EL ADMINISTRADOR (CODIGO: $referencia)');</script>";
      } else {
        echo "<script>alert('¡ERROR!');</script>";
      }
    }
  //Función para obtener los datos de una sentencia ejecutada en la BD central
    function retorna_datos($sentencia,$direccion = "") {
      include A_CONNECTION;
      try {
        //Preparo la sentencia a ejecutar
        $sql = $conn->prepare($sentencia);
        //Ejecutar la sentencia
        $sql->execute();
        // Obtener el número de filas afectadas
        $rowCount = $sql->rowCount();
        // Obtener los datos de la tabla
        $datos = array();
        while ($fila = $sql->fetch(PDO::FETCH_ASSOC)) {
            $datos[] = $fila;
        }
        // Cerrar el cursor
        $sql->closeCursor();
        // Retornar el resultado
        return array('data' => $datos, 'rowCount' => $rowCount);;
        //
      } catch (PDOException $e) {
        //Almaceno el error en una variabLe
          $error=$e->getMessage();
        //Verifico si se incluyo una direccion de error
          if (campo_limpiado($direccion,2)!="") {
            $agregado=", Referenciado desde $direccion";
          }else{
            $agregado=Null;
          }
        //Ubico el archivo desde donde se presenta el error
          $archivo=__FILE__."::Funcion retorna_datos$agregado";
        //Mando a escribir el mensaje
        escribir_log($error,$sentencia,$archivo);
        //Detengo el procedimiento
        die();
      }
    }
  //Función para buscar la cantidad de registros existentes
    function busca_existencia($sentencia,$direccion = "") {
      include A_CONNECTION;
      try {
        //Preparo la sentencia a ejecutar
        $sql = $conn->prepare($sentencia);
        //Ejecutar la sentencia
        $sql->execute();
        //Asocio los datos de la tabla obtenidos
        $tabla=$sql->fetch(PDO::FETCH_ASSOC);
        //Retorna el valor obtenido
        return $tabla['exist'];
        //finalizo el cursor
        $sql->CloseCursor();
        //
      } catch (PDOException $e) {
        //Almaceno el error en una variabLe
          $error=$e->getMessage();
        //Verifico si se incluyo una direccion de error
          if (campo_limpiado($direccion,2)!="") {
            $agregado=", Referenciado desde $direccion";
          }else{
            $agregado=Null;
          }
        //Ubico el archivo desde donde se presenta el error
        $archivo=__FILE__."::Funcion busca_existencia$agregado";
        //Mando a escribir el mensaje
        escribir_log($error,$sentencia,$archivo);
        //Detengo el procedimiento
        die();
      }
    }
  //Función para ejecutar sentencias dentro de la base de datos de la plataforma
    function ejecuta_sentencia($sentencia,$mensaje,$direccion = "") {
      include A_CONNECTION;
      try {
        //Preparo la sentencia a ejecutar
        $sql=$conn->prepare($sentencia);
        //ejecuto la sentencia
        $res=$sql->execute();
        //finalizo el cursor
        $sql->CloseCursor();
        //Retorna el valor de mensaje dado
        return $mensaje;
        //
      } catch (PDOException $e) {
        //Almaceno el error en una variabLe
          $error=$e->getMessage();
        //Verifico si se incluyo una direccion de error
          if (campo_limpiado($direccion,2)!="") {
            $agregado=", Referenciado desde $direccion";
          }else{
            $agregado=Null;
          }
        //Ubico el archivo desde donde se presenta el error
        $archivo=__FILE__."::Funcion ejecuta_sentencia$agregado";
        //Mando a escribir el mensaje
        escribir_log($error,$sentencia,$archivo);
        //Detengo el procedimiento
        die();
      }
    }
   // Función para ejecutar sentencias dentro de la base de datos de la plataforma
    function registra_bitacora($campos,$datos,$direccion = "") {
      //Verifico si se incluyo una direccion de error
        if (campo_limpiado($direccion,2)!="") {
          $agregado=", Referenciado desde $direccion";
        }else{
          $agregado=Null;
        }
      //Defino 2 variables vacias
        $texto_campos=Null;
        $texto_datos=Null;
      //Se obtiene datos iniciales
        $fecha=ahora(1);
        $hora=ahora(2);
        $usuario=campo_limpiado($_SESSION[UBI]['clave'],2,0);
      //Procesamiento de arreglo de campos
        //Obtengo la cantidad de elementos en el arreglo
          $cantidad_campos=count($campos);
        //Creeo un ciclo for para recorrer todo el arreglo
          for ($i=0; $i < $cantidad_campos ; $i++) { 
            //Concateno el campo y le agrego una coma y espacio al final
              $texto_campos.=$campos[$i].", ";
            //
          }
        //
      //Procesamiento de arreglo de datos
        //Obtengo la cantidad de elementos en el arreglo
          $cantidad_datos=count($datos);
        //Creeo un ciclo for para recorrer todo el arreglo
          for ($i=0; $i < $cantidad_datos ; $i++) { 
            //Concateno el campo y le agrego una coma y espacio al final
              $texto_datos.="'".$datos[$i]."', ";
            //
          }
        //
      //Se defina la sentencia a ejecutar
        $sentencia="
          INSERT INTO bitacora (
            fecha,
            hora,
            $texto_campos
            usuario
          ) VALUES (
            '$fecha',
            '$hora',
            $texto_datos
            '$usuario'
          );
        ";
      //Se ejecuta la sentencia
        $devuelto=ejecuta_sentencia($sentencia,true,"::Funcion registra_bitacora$agregado");
      //
    }
  //
?>