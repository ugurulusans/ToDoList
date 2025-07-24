<?php
// views/dashboard.php

$db = getDbConnection();
$signals_stmt = $db->query(
    "SELECT s.symbol, s.name, sig.signal_type, sig.price, sig.signal_date, sig.indicators, sig.ai_commentary
     FROM signals sig
     JOIN stocks s ON sig.stock_id = s.id
     WHERE sig.signal_type != 'HOLD'
     ORDER BY sig.signal_date DESC
     LIMIT 50"
);
$signals = $signals_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Sinyal Paneli</h1>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Sembol</th>
                        <th>İsim</th>
                        <th>Sinyal</th>
                        <th>Fiyat</th>
                        <th>RSI</th>
                        <th>AI Yorumu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($signals)): ?>
                        <tr><td colspan="7" class="text-center py-4">Henüz bir AL/SAT sinyali üretilmedi.</td></tr>
                    <?php else: ?>
                        <?php foreach ($signals as $signal):
                            $indicators = json_decode($signal['indicators'], true);
                        ?>
                            <tr>
                                <td><?php echo date('d.m.Y H:i', strtotime($signal['signal_date'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($signal['symbol']); ?></strong></td>
                                <td><?php echo htmlspecialchars($signal['name']); ?></td>
                                <td>
                                    <span class="badge <?php echo $signal['signal_type'] === 'BUY' ? 'text-bg-success' : 'text-bg-danger'; ?>">
                                        <?php echo $signal['signal_type']; ?>
                                    </span>
                                </td>
                                <td>$<?php echo number_format($signal['price'], 2); ?></td>
                                <td><?php echo isset($indicators['rsi']) ? number_format($indicators['rsi'], 2) : 'N/A'; ?></td>
                                <td><i><?php echo htmlspecialchars($signal['ai_commentary'] ?? 'Yorum yok.'); ?></i></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
