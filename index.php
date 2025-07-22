<?php include 'header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <form action="index.php" method="get" class="d-flex">
            <input class="form-control me-2" type="search" name="arama" placeholder="Notlarda Ara" aria-label="Search" value="<?php echo isset($_GET['arama']) ? $_GET['arama'] : ''; ?>">
            <button class="btn btn-outline-success" type="submit">Ara</button>
        </form>
    </div>
    <div class="col-md-6">
        <form action="index.php" method="get" class="d-flex justify-content-end">
            <select name="kategori_id" class="form-select me-2" onchange="this.form.submit()">
                <option value="">Tüm Kategoriler</option>
                <?php
                $sql_kategoriler = "SELECT * FROM kategoriler WHERE kullanici_id = $kullanici_id ORDER BY ad ASC";
                $result_kategoriler = $conn->query($sql_kategoriler);
                while ($kategori = $result_kategoriler->fetch_assoc()) {
                    $selected = (isset($_GET['kategori_id']) && $_GET['kategori_id'] == $kategori['id']) ? 'selected' : '';
                    echo "<option value='{$kategori['id']}' $selected>{$kategori['ad']}</option>";
                }
                ?>
            </select>
        </form>
    </div>
</div>


<div class="row">
    <?php
    $sql = "SELECT n.*, k.ad as kategori_adi FROM notlar n LEFT JOIN kategoriler k ON n.kategori_id = k.id WHERE n.kullanici_id = $kullanici_id";

    if (isset($_GET['arama']) && !empty($_GET['arama'])) {
        $arama = $_GET['arama'];
        $sql .= " AND (n.baslik LIKE '%$arama%' OR n.icerik LIKE '%$arama%')";
    }

    if (isset($_GET['kategori_id']) && !empty($_GET['kategori_id'])) {
        $kategori_id = $_GET['kategori_id'];
        $sql .= " AND n.kategori_id = $kategori_id";
    }

    $sql .= " ORDER BY n.olusturma_tarihi DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            ?>
            <div class="col-md-4 mb-4">
                <div class="card note-card h-100">
                    <div class="card-body">
                        <div class="note-actions">
                            <a href="edit_note.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <a href="delete_note.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu notu silmek istediğinizden emin misiniz?');"><i class="bi bi-trash"></i></a>
                        </div>
                        <h5 class="card-title"><?php echo $row['baslik']; ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            <?php if ($row['kategori_adi']) { ?>
                                <span class="badge bg-secondary"><?php echo $row['kategori_adi']; ?></span>
                            <?php } ?>
                            <span class="badge bg-info"><?php echo $row['durum']; ?></span>
                        </h6>
                        <p class="card-text"><?php echo nl2br($row['icerik']); ?></p>
                    </div>
                    <div class="card-footer text-muted">
                        <small>Oluşturulma: <?php echo $row['olusturma_tarihi']; ?></small>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<div class='col-12'><div class='alert alert-warning'>Hiç not bulunamadı.</div></div>";
    }
    ?>
</div>

<?php include 'footer.php'; ?>
