<?php
// views/portfolio.php

$db = getDbConnection();
$apiClient = new ApiClient(); // Anlık fiyatlar için

// Varsayılan portföyü al
$portfolio_stmt = $db->query("SELECT * FROM portfolios WHERE id = 1 LIMIT 1");
$portfolio = $portfolio_stmt->fetch(PDO::FETCH_ASSOC);

if (!$portfolio) {
    echo "<div class='alert alert-warning'>Henüz bir portföy oluşturulmamış.</div>";
    return; // Portföy yoksa devam etme
}

// Pozisyonları ve geçmiş işlemleri çek
$positions_stmt = $db->prepare("SELECT p.*, s.symbol FROM positions p JOIN stocks s ON p.stock_id = s.id WHERE p.portfolio_id = ?");
$positions_stmt->execute([$portfolio['id']]);
$positions = $positions_stmt->fetchAll(PDO::FETCH_ASSOC);

$transactions_stmt = $db->prepare("SELECT t.*, s.symbol FROM transactions t JOIN stocks s ON t.stock_id = s.id WHERE t.portfolio_id = ? ORDER BY t.transaction_date DESC");
$transactions_stmt->execute([$portfolio['id']]);
$transactions = $transactions_stmt->fetchAll(PDO::FETCH_ASSOC);

// Toplam değeri hesapla
$total_assets_value = 0;
foreach ($positions as &$pos) {
    $current_price_data = $apiClient->getFinnhubCandles($pos['symbol'], 1); // Son 1 günlük veri yeterli
    $pos['current_price'] = end($current_price_data['c']) ?? $pos['average_cost'];
    $pos['current_value'] = $pos['current_price'] * $pos['quantity'];
    $pos['pnl'] = $pos['current_value'] - ($pos['average_cost'] * $pos['quantity']);
    $total_assets_value += $pos['current_value'];
}
unset($pos); // Referansı temizle

$total_portfolio_value = $total_assets_value + $portfolio['current_balance'];
$total_pnl = $total_portfolio_value - $portfolio['initial_balance'];
?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <h5 class="card-title text-muted">Toplam Portföy Değeri</h5>
            <p class="card-text fs-4">$<?php echo number_format($total_portfolio_value, 2); ?></p>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <h5 class="card-title text-muted">Nakit Bakiye</h5>
            <p class="card-text fs-4">$<?php echo number_format($portfolio['current_balance'], 2); ?></p>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <h5 class="card-title text-muted">Toplam Kar/Zarar</h5>
            <p class="card-text fs-4 <?php echo $total_pnl >= 0 ? 'positive' : 'negative'; ?>">
                $<?php echo number_format($total_pnl, 2); ?>
            </p>
        </div></div>
    </div>
</div>

<h2 class="mb-3">Mevcut Pozisyonlar</h2>
<div class="table-responsive">
    <table class="table table-sm">
        <thead>
            <tr><th>Hisse</th><th>Adet</th><th>Ort. Maliyet</th><th>Anlık Fiyat</th><th>Toplam Değer</th><th>K/Z</th></tr>
        </thead>
        <tbody>
            <?php foreach ($positions as $pos): ?>
                <tr>
                    <td><strong><?php echo $pos['symbol']; ?></strong></td>
                    <td><?php echo number_format($pos['quantity'], 4); ?></td>
                    <td>$<?php echo number_format($pos['average_cost'], 2); ?></td>
                    <td>$<?php echo number_format($pos['current_price'], 2); ?></td>
                    <td>$<?php echo number_format($pos['current_value'], 2); ?></td>
                    <td class="<?php echo $pos['pnl'] >= 0 ? 'positive' : 'negative'; ?>">
                        $<?php echo number_format($pos['pnl'], 2); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<h2 class="mt-5 mb-3">Geçmiş İşlemler</h2>
<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
    <table class="table table-striped table-sm">
        <thead>
            <tr><th>Tarih</th><th>Hisse</th><th>İşlem</th><th>Adet</th><th>Fiyat</th><th>Tutar</th></tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $tx): ?>
                <tr>
                    <td><?php echo date('d.m.Y H:i', strtotime($tx['transaction_date'])); ?></td>
                    <td><?php echo $tx['symbol']; ?></td>
                    <td><span class="badge text-bg-<?php echo $tx['transaction_type'] === 'BUY' ? 'info' : 'warning'; ?>"><?php echo $tx['transaction_type']; ?></span></td>
                    <td><?php echo number_format($tx['quantity'], 4); ?></td>
                    <td>$<?php echo number_format($tx['price'], 2); ?></td>
                    <td>$<?php echo number_format($tx['quantity'] * $tx['price'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
