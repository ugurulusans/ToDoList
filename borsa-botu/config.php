<?php
// borsa-botu/config.php

// Hata Raporlama (Geliştirme sırasında E_ALL, üretimde 0 yapın)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Zaman Dilimi
date_default_timezone_set('Europe/Istanbul');

// Veritabanı Ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'borsa_db');
define('DB_USER', 'borsa_user');
define('DB_PASS', 'YourStrongPassword');

// API Anahtarları
define('API_KEYS', [
    'FINNHUB'       => 'd1httd1r01qhsrhc45ggd1httd1r01qhsrhc45h0',
    'ALPHA_VANTAGE' => 'Z1KZIVL6C8CYBAZ1',
    'COINGECKO'     => 'CG-djQv9e1rnrEYMZAjhDetSMeK',
    'AI_API'        => 'sk-fdaf2d20ccf84bdb9f4c1d06d7829364',
]);

// Telegram Ayarları (Opsiyonel)
define('TELEGRAM_BOT_TOKEN', 'YOUR_TELEGRAM_BOT_TOKEN');
define('TELEGRAM_CHAT_ID', 'YOUR_TELEGRAM_CHAT_ID');

// Veritabanı Bağlantı Fonksiyonu
function getDbConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->exec("set names utf8");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $exception) {
        // Gerçek bir uygulamada burada hata loglanmalı ve kullanıcıya genel bir mesaj gösterilmelidir.
        die("Veritabanı bağlantı hatası: " . $exception->getMessage());
    }
}
?>
