<?php
  //Se incluye el archivo de configuraciones principal
    include_once 'lib/config.php';
  //Se obtiene el dato del producto
    $id=campo_limpiado($_GET['summary'],2);
  //Defino la sentencia para buscar los datos del producto
    $sentencia = "SELECT * FROM productos WHERE id = $id";
  //Ejecuto la sentencia
    $resultado_sentencia=retorna_datos($sentencia,__FILE__);
  //Identifico si el reultado no es vacio
    if ($resultado_sentencia['rowCount'] > 0) {
      //Almaceno los datos obtenidos
        $resultado = $resultado_sentencia['data'];
      // Recorrer los datos y llenar las filas
        foreach ($resultado as $tabla) {
          //Almaceno el en una variables
            $id=$tabla['id'];
            $sku=$tabla['sku'];
            $nombre=$tabla['nombre'];
            $precio_base=$tabla['precio_base'];
            $categoria=$tabla['categoria'];
            $descripcion=$tabla['descripcion'];
            $imagen=$tabla['imagen'];
          //
        }
      //
    }
  //
?>
<!DOCTYPE html>
<html lang="en">
	<?php include 'private/head.php'; ?>
	<body>
		<!-- HEADER -->
      <?php include 'public/header.php'; ?>
		<!-- NAVIGATION -->
      <?php include 'public/navigation.php'; ?>
		<!-- PRODUCT SUMMARY -->
			<?php include 'public/product_summary.php'; ?>
		<!-- FOOTER -->
			<?php include 'public/footer.php'; ?>
		<!-- jQuery Plugins -->
			<?php include 'private/footer_plugins.php'; ?>
		<!-- -->
	</body>
</html>
