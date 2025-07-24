<?php
// borsa-botu/index.php

require_once __DIR__ . '/config.php';

// Basit bir yönlendirici (router)
$page = $_GET['page'] ?? 'dashboard';

// Gerekli uygulama mantığı dosyalarını dahil et
// (Bu kısım ilgili sayfa yüklendiğinde daha spesifik hale getirilebilir)
require_once __DIR__ . '/app/SignalManager.php';
require_once __DIR__ . '/app/PortfolioManager.php';


// Sayfa başlığını ve navigasyonu yükle
include __DIR__ . '/views/header.php';

// İstenen sayfayı yükle
switch ($page) {
    case 'dashboard':
        include __DIR__ . '/views/dashboard.php';
        break;
    case 'portfolio':
        include __DIR__ . '/views/portfolio.php';
        break;
    case 'watchlist':
        include __DIR__ . '/views/watchlist.php';
        break;
    case 'settings':
        include __DIR__ . '/views/settings.php';
        break;
    default:
        include __DIR__ . '/views/404.php';
        break;
}

// Sayfa altlığını yükle
include __DIR__ . '/views/footer.php';

?>
