<?php
class MPFunctions {
    
    public static function formatCurrency($amount) {
        return number_format($amount, 2, '.', '');
    }
    
    public static function generateOrderId() {
        return 'VC-' . date('Ymd-His') . '-' . rand(1000, 9999);
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)));
    }
    
    public static function logTransaction($type, $data) {
        $logFile = '../logs/transactions.log';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] {$type}: " . json_encode($data) . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
?>
