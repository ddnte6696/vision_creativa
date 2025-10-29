
<!-- SECTION -->
  <div class="section">
    <!-- container -->
    <div class="container">
      <!-- row -->
      <div class="row">
        <!-- Product main img -->
        <div class="col-md-5 col-md-push-2">
          <div id="product-main-img">
            <div class="product-preview">
              <?php echo "<img src='img/productos/$imagen' alt=''>"; ?>
            </div>
          </div>
        </div>
        <!-- /Product main img -->

        <!-- Product thumb imgs -->
        <div class="col-md-2  col-md-pull-5">
          <div id="product-imgs">
            <div class="product-preview">
              <?php echo "<img src='img/productos/$imagen' alt=''>"; ?>
            </div>
          </div>
        </div>
        <!-- /Product thumb imgs -->

        <!-- Product details -->
        <div class="col-md-5">
          <div class="product-details">
            <h2 class="product-name"><?php echo $nombre; ?></h2>
            <div>
              <h3 class="product-price">$<?php echo $precio_base; ?>
              <span class="product-available">In Stock</span>
            </div>
            <p><?php echo $sku; ?></p>
            <div class="add-to-cart">
              <div class="qty-label">
                Cantidad
                <div class="input-number">
                  <input type="number" value="1">
                  <span class="qty-up">+</span>
                  <span class="qty-down">-</span>
                </div>
              </div>
              <button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i>COMPRAR</button>
            </div>
            <ul class="product-links">
              <li>Categoria:</li>
              <li><a href="#"><?php echo $categoria; ?></a></li>
            </ul>
          </div>
        </div>
        <!-- /Product details -->
        <!-- Product tab -->
					<div class="col-md-12">
						<div id="product-tab">
							<!-- product tab nav -->
							<ul class="tab-nav">
								<li class="active"><a data-toggle="tab" href="#tab1">Descripción</a></li>
								<li><a data-toggle="tab" href="#tab2">Detalles</a></li>
							</ul>
							<!-- /product tab nav -->

							<!-- product tab content -->
							<div class="tab-content">
								<!-- tab1  -->
								<div id="tab1" class="tab-pane fade in active">
									<div class="row">
										<div class="col-md-12">
											<p><?php echo $descripcion; ?></p>
										</div>
									</div>
								</div>
								<!-- /tab1  -->

								<!-- tab2  -->
								<div id="tab2" class="tab-pane fade in">
									<div class="row">
										<div class="col-md-12">
                      <table class="table table-striped">
                        <?php
                          //Defino la sentencia para buscar los detalles del producto
                            $sentencia = "SELECT * FROM producto_specs WHERE id_producto = $id";
                          //Ejecuto la sentencia
                            $resultado_sentencia=retorna_datos($sentencia,__FILE__);
                          //Identifico si el reultado no es vacio
                            if ($resultado_sentencia['rowCount'] > 0) {
                              //Almaceno los datos obtenidos
                                $resultado = $resultado_sentencia['data'];
                              // Recorrer los datos y llenar las filas
                                foreach ($resultado as $tabla) {
                                  //Almaceno el en una variables
                                    $dato=explode(':',$tabla['valor']);
                                    echo "<tr><th><strong>$dato[0]:</strong></th><td>$dato[1]</td></tr>";
                                  //
                                }
                              //
                            }
                          //
                        ?>
                      </table>
										</div>
									</div>
								</div>
								<!-- /tab2  -->
							</div>
							<!-- /product tab content  -->
						</div>
					</div>
					<!-- /product tab -->
      </div>
      <!-- /row -->
    </div>
    <!-- /container -->
  </div>
<!-- /SECTION -->