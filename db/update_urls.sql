-- Actualizar URLs en base de datos para omnibus-guadalajara.com
UPDATE mp_config 
SET webhook_url = 'https://omnibus-guadalajara.com/vision_creativa/api/payments/webhook.php'
WHERE environment = 'sandbox';

-- Verificar el cambio
SELECT environment, webhook_url FROM mp_config WHERE is_active = 1;
