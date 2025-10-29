<?php 
  include_once 'lib/config.php';
?>

<div class="section">
  <!-- container -->
    <div class="container">
      <!-- row -->
        <div class="row">
          <!-- section title -->
            <div class="col-md-12">
              <div class="section-title">
                <h3 class="title">Nuevos productos</h3>
              </div>
            </div>
          <!-- /section title -->
          <!-- Products tab & slick -->
            <div class="col-md-12">
              <div class="row">
                <div class="products-tabs">
                  <!-- tab -->
                  <div id="tab1" class="tab-pane active">
                    <div class="products-slick" data-nav="#slick-nav-1">
                      <!-- product -->
                        <?php
                          //Defino la sentencia para buscar los nuevos productos
                            $sentencia = "SELECT * FROM productos ORDER BY id DESC LIMIT 7";
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
                                    $imagen=$tabla['imagen'];
                                  
                                  // Impresion de los datos
                                    echo "
                                      <!-- product -->
                                        <div class='product'>
                                          <div class='product-img'>
                                            <img src='./img/productoS/$imagen' alt=''>
                                            <div class='product-label'>
                                              <span class='new'>NEW</span>
                                            </div>
                                          </div>
                                          <div class='product-body'>
                                            <p class='product-category'>$categoria</p>
                                            <h3 class='product-name'><a href='#'>$nombre</a></h3>
                                            <h4 class='product-price'>$$precio_base</h4>
                                            <div class='product-rating'>
                                              <i class='fa fa-star'></i>
                                              <i class='fa fa-star'></i>
                                              <i class='fa fa-star'></i>
                                              <i class='fa fa-star'></i>
                                              <i class='fa fa-star-o'></i>
                                            </div>
                                          </div>
                                          <div class='add-to-cart'>
                                            <button class='add-to-cart-btn'><i class='fa fa-shopping-cart'></i>DETALLES</button>
                                          </div>
                                        </div>
                                      <!-- /product -->
                                    ";
                                  //
                                }
                              //
                            }
                          //
                        ?>
                      <!-- product -->
                    </div>
                    <div id="slick-nav-1" class="products-slick-nav"></div>
                  </div>
                  <!-- /tab -->
                </div>
              </div>
            </div>
          <!-- Products tab & slick -->
        </div>
      <!-- /row -->
    </div>
  <!-- /container -->
</div>