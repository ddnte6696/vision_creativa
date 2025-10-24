<header>
  <!-- CONTACTO Y CUENTA -->
    <?php include 'public/top_header.php'; ?>
  <!-- ENCABEZADO PRINCIPAL -->
    <div id="header">
      <div class="container">
        <div class="row">
          <!-- LOGO -->
            <div class="col-md-3">
              <div class="header-logo">
                <a href="#" class="logo">
                  <img src="./img/logo.png" alt="">
                </a>
              </div>
            </div>
          <!-- BARRA DE BUSQUEDA -->
            <div class="col-md-6">
              <?php include 'public/search_bar.php'; ?>
            </div>
          <!-- CUENTA -->
            <div class="col-md-3 clearfix">
              <div class="header-ctn">
                <!-- WISHLIST -->
                  <div>
                    <a href="#">
                      <i class="fa fa-heart-o"></i>
                      <span>Your Wishlist</span>
                      <div class="qty">2</div>
                    </a>
                  </div>
                <!-- CARRITO -->
                  <?php include 'public/cart.php'; ?>
                <!-- Menu Toogle -->
                  <div class="menu-toggle">
                    <a href="#">
                      <i class="fa fa-bars"></i>
                      <span>Menu</span>
                    </a>
                  </div>
                <!-- -->
              </div>
            </div>
          <!-- -->
        </div>
      </div>
    </div>
  <!-- -->
</header>