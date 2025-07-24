<?php
// borsa-botu/cron.php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/app/ApiClient.php';
require_once __DIR__ . '/app/TechnicalAnalysis.php';
require_once __DIR__ . '/app/PortfolioManager.php';

echo "Cron Job Başladı: " . date('Y-m-d H:i:s') . "\n";

$db = getDbConnection();
$apiClient = new ApiClient();

// Aktif portföy var mı kontrol et
$portfolio_stmt = $db->query("SELECT * FROM portfolios WHERE is_active = 1 LIMIT 1");
$active_portfolio = $portfolio_stmt->fetch(PDO::FETCH_ASSOC);
$portfolioManager = $active_portfolio ? new PortfolioManager($db, $active_portfolio['id']) : null;

// Takip edilen varlıkları çek
$stock_stmt = $db->query("SELECT * FROM stocks WHERE is_active = 1");
$stocks_to_track = $stock_stmt->fetchAll(PDO::FETCH_ASSOC);

echo count($stocks_to_track) . " adet varlık işlenecek...\n";

foreach ($stocks_to_track as $stock) {
    echo "--- İşleniyor: {$stock['symbol']} ---\n";
    $close_prices = [];

    // 1. Veri Çekme
    if ($stock['type'] === 'stock') {
        $data = $apiClient->getFinnhubCandles($stock['symbol']);
        if (!empty($data) && $data['s'] === 'ok') {
            $close_prices = $data['c'];
        }
    } elseif ($stock['type'] === 'crypto') {
        $data = $apiClient->getCoinGeckoOhlc($stock['symbol']); // CoinGecko ID'si sembolle aynı varsayılıyor
        if (!empty($data)) {
            // CoinGecko [timestamp, open, high, low, close] formatında döner
            $close_prices = array_column($data, 4);
        }
    }

    if (empty($close_prices)) {
        echo "Fiyat verisi alınamadı.\n";
        continue;
    }
    echo count($close_prices) . " adet günlük veri alındı.\n";

    // 2. Teknik Analiz
    $rsi_values = TechnicalAnalysis::rsi($close_prices, 14);
    if (empty($rsi_values)) {
        echo "RSI hesaplanamadı.\n";
        continue;
    }
    $latest_rsi = end($rsi_values);
    $latest_price = end($close_prices);
    echo "Son Fiyat: {$latest_price}, Son RSI: " . round($latest_rsi, 2) . "\n";

    // 3. Sinyal Üretme
    $signal_type = 'HOLD';
    if ($latest_rsi <= 30) $signal_type = 'BUY';
    if ($latest_rsi >= 70) $signal_type = 'SELL';

    // 4. Sinyali Kaydet
    $sql = "INSERT INTO signals (stock_id, signal_type, price, signal_date, indicators) VALUES (?, ?, ?, NOW(), ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$stock['id'], $signal_type, $latest_price, json_encode(['rsi' => $latest_rsi])]);
    echo "Sinyal ({$signal_type}) veritabanına kaydedildi.\n";

    // 5. Portföy İşlemi (Eğer portföy aktifse)
    if ($portfolioManager && $signal_type !== 'HOLD') {
        if ($signal_type === 'BUY') {
            $buy_amount_usd = 1000; // Her alımda 1000$ değerinde alım yap
            $quantity_to_buy = $buy_amount_usd / $latest_price;
            if ($active_portfolio['current_balance'] >= $buy_amount_usd) {
                $portfolioManager->buy($stock['id'], $quantity_to_buy, $latest_price);
                echo "Portföye alım emri işlendi.\n";
            } else {
                echo "Alım için yeterli bakiye yok.\n";
            }
        } elseif ($signal_type === 'SELL') {
            $position = $portfolioManager->getPosition($stock['id']);
            if ($position && $position['quantity'] > 0) {
                $portfolioManager->sell($stock['id'], $position['quantity'], $latest_price);
                echo "Portföydeki pozisyon için satış emri işlendi.\n";
            }
        }
    }
}

echo "Cron Job Bitti: " . date('Y-m-d H:i:s') . "\n";
?>
