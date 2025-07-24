<?php
// views/watchlist.php

$db = getDbConnection();

// Form gönderimlerini işle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_stock'])) {
        $sql = "INSERT INTO stocks (symbol, name, type, exchange, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $_POST['symbol'],
            $_POST['name'],
            $_POST['type'],
            $_POST['exchange'],
            isset($_POST['is_active']) ? 1 : 0
        ]);
    } elseif (isset($_POST['delete_stock'])) {
        $sql = "DELETE FROM stocks WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$_POST['stock_id']]);
    }
    // Sayfayı yeniden yönlendirerek formun tekrar gönderilmesini önle
    header("Location: index.php?page=watchlist");
    exit;
}

$watchlist_stmt = $db->query("SELECT * FROM stocks ORDER BY type, symbol");
$watchlist = $watchlist_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <!-- Yeni Varlık Ekle Formu -->
    <div class="col-md-4">
        <h2>Yeni Varlık Ekle</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="symbol" class="form-label">Sembol / ID</label>
                <input type="text" class="form-control" id="symbol" name="symbol" placeholder="örn: AAPL veya bitcoin" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">İsim</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="örn: Apple Inc." required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tip</label>
                <select class="form-select" id="type" name="type">
                    <option value="stock">Hisse Senedi</option>
                    <option value="crypto">Kripto Para</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="exchange" class="form-label">Borsa</label>
                <input type="text" class="form-control" id="exchange" name="exchange" placeholder="örn: NASDAQ">
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                <label class="form-check-label" for="is_active">
                    Aktif Olarak Takip Edilsin
                </label>
            </div>
            <button type="submit" name="add_stock" class="btn btn-primary">Ekle</button>
        </form>
    </div>

    <!-- Takip Listesi Tablosu -->
    <div class="col-md-8">
        <h2>Takip Listesi</h2>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Sembol</th>
                        <th>İsim</th>
                        <th>Tip</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($watchlist as $stock): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($stock['symbol']); ?></strong></td>
                            <td><?php echo htmlspecialchars($stock['name']); ?></td>
                            <td><span class="badge text-bg-secondary"><?php echo $stock['type']; ?></span></td>
                            <td>
                                <span class="badge <?php echo $stock['is_active'] ? 'text-bg-success' : 'text-bg-warning'; ?>">
                                    <?php echo $stock['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="stock_id" value="<?php echo $stock['id']; ?>">
                                    <button type="submit" name="delete_stock" class="btn btn-sm btn-danger" onclick="return confirm('Bu varlığı silmek istediğinizden emin misiniz?');">Sil</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
