<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Vision Creativa</title>
    <link href="../../css/estilos.css" rel="stylesheet">
    <link href="../../css/checkout.css" rel="stylesheet">
</head>
<body>
    <div class="container checkout-container">
        <div class="row">
            <div class="col-md-8">
                <div class="checkout-form">
                    <h2>Finalizar Compra</h2>
                    
                    <form id="checkout-form" method="POST">
                        <div class="section">
                            <h3>Información del Cliente</h3>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nombre">Nombre *</label>
                                    <input type="text" id="nombre" name="nombre" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="apellido">Apellido *</label>
                                    <input type="text" id="apellido" name="apellido" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="email">Email *</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="telefono">Teléfono</label>
                                    <input type="tel" id="telefono" name="telefono">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg">
                            Proceder al Pago
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="order-summary">
                    <h3>Resumen del Pedido</h3>
                    <div id="cart-summary">
                        <!-- Se llena dinámicamente con JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../js/jquery-3.7.1.min.js"></script>
</body>
</html>
