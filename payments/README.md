# Integración MercadoPago - Vision Creativa

## Instalación y Configuración

### 1. Configurar Credenciales
- Copiar .env.example a .env
- Editar .env con tus credenciales de MercadoPago

### 2. Configurar Permisos
- Asegurar que la carpeta logs/ sea escribible

## Estructura de Archivos

- config/ - Configuraciones de MercadoPago
- controllers/ - Controladores de pago
- services/ - Servicios de procesamiento
- webhooks/ - Manejo de notificaciones
- views/ - Páginas de resultado
- utils/ - Funciones auxiliares
- logs/ - Archivos de log

## Uso

1. Incluir en tu checkout: payments/views/checkout.php
2. Configurar webhook en MercadoPago: /payments/webhooks/mercadopago_webhook.php
3. Personalizar URLs de retorno en config/mercadopago_config.php
