Proyecto: vision_creativa
Descripción:
  Plantilla / mini-proyecto front-end con integración PHP parcial para uso como tienda/landing.
  Objetivo: ofrecer estructura de plantilla con secciones, header/footer, templates reutilizables y recursos estáticos.

Estructura principal:
  index.php                         - Punto de entrada.
  private/
    head.php                        - Cabeceras HTML comunes (meta, links CSS).
    footer_plugins.php              - Scripts y plugins incluidos al final.
  public/
    header.php                      - Barra superior / navegación incluida en páginas públicas.
    footer.php                      - Pie de página.
    navigation.php, top_header.php, search_bar.php, cart.php
  template/
    blank.php, hot_deal.php, ...    - Bloques / secciones reutilizables.
  css/                               - Estilos (bootstrap.min.css, style.css, etc.)
  js/                                - Scripts (jquery.min.js, main.js, slick, nouislider).
  img/, fonts/                       - Recursos estáticos.
  README.md                          - Breve descripción.

Requisitos / Dependencias:
  - PHP (>=7.x) para inclusión de fragments PHP.
  - Servidor web (Apache, Nginx) o PHP built-in: php -S localhost:8000 (desde la raíz del proyecto).
  - Librerías incluidas en css/ y js/:
      * Bootstrap
      * jQuery
      * Slick slider
      * NoUISlider
      * Font Awesome

Instalación rápida:
  1) Copiar carpeta `vision_creativa` al directorio público del servidor (ej: htdocs/www).
  2) Levantar servidor PHP o configurar virtual host.
     - PHP built-in: desde la carpeta que contiene `vision_creativa` ejecutar:
       php -S localhost:8000
     - Abrir en el navegador: http://localhost:8000/vision_creativa/

Cómo personalizar:
  - Cambiar contenido global en:
    - private/head.php  -> meta, títulos, links a CSS
    - public/header.php -> navegación principal
    - public/footer.php -> pie y textos legales
  - Templates:
    - Editar /template/*.php para modificar secciones reutilizables.
  - Assets:
    - Colocar imágenes en img/ y fuentes en fonts/.
    - Modificar estilos en css/style.css y scripts en js/main.js.

Notas para desarrolladores:
  - No es un CMS: la lógica es estática y fragmentada mediante includes PHP.
  - Para integrar datos dinámicos, añadir lógica PHP en archivos públicos o crear una API y consumir con AJAX.
  - Revisar rutas relativas en includes si mueve la carpeta.

Archivos clave:
  - vision_creativa/index.php
  - vision_creativa/private/head.php
  - vision_creativa/public/header.php
  - vision_creativa/public/footer.php
  - vision_creativa/template/blank.php
  - vision_creativa/css/style.css
  - vision_creativa/js/main.js

Referencias (archivos mencionados en la documentación)
  - vision_creativa/index.php            - Entrada principal del sitio.
  - vision_creativa/private/head.php    - Head común (meta, links a CSS/JS).
  - vision_creativa/private/footer_plugins.php - Scripts cargados al final.
  - vision_creativa/public/header.php   - Cabecera visible (logo, búsqueda, cuenta).
  - vision_creativa/public/footer.php   - Pie de página.
  - vision_creativa/public/navigation.php - Menú de navegación.
  - vision_creativa/public/top_header.php - Barra superior (contacto, cuenta).
  - vision_creativa/public/search_bar.php - Barra de búsqueda incluible.
  - vision_creativa/public/cart.php     - Fragmento del carrito.
  - vision_creativa/template/blank.php  - Plantilla de página en blanco.
  - vision_creativa/template/hot_deal.php - Sección de ofertas.
  - vision_creativa/template/section_new_products.php - Sección de productos nuevos.
  - vision_creativa/template/section_shop_cards.php - Tarjetas de tienda.
  - vision_creativa/template/section_small_top_selling.php - Productos top (pequeños).
  - vision_creativa/template/section_top_selling.php - Productos top (grandes).
  - vision_creativa/css/bootstrap.min.css
  - vision_creativa/css/style.css
  - vision_creativa/css/slick.css
  - vision_creativa/css/slick-theme.css
  - vision_creativa/css/font-awesome.min.css
  - vision_creativa/css/nouislider.min.css
  - vision_creativa/js/jquery.min.js
  - vision_creativa/js/bootstrap.min.js
  - vision_creativa/js/main.js
  - vision_creativa/js/slick.min.js
  - vision_creativa/js/nouislider.min.js

Contacto / Soporte:
  - Abrir issues o agregar comentarios en el repositorio local.