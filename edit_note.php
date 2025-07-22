<?php
include 'header.php';

$not_id = $_GET['id'];

// Notun bu kullanıcıya ait olup olmadığını kontrol et
$sql_check = "SELECT * FROM notlar WHERE id = $not_id AND kullanici_id = $kullanici_id";
$result_check = $conn->query($sql_check);
if ($result_check->num_rows == 0) {
    echo "<div class='alert alert-danger'>Bu nota erişim yetkiniz yok.</div>";
    include 'footer.php';
    exit();
}
$not = $result_check->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $baslik = $_POST['baslik'];
    $icerik = $_POST['icerik'];
    $durum = $_POST['durum'];
    $kategori_id = $_POST['kategori_id'];

    $sql = "UPDATE notlar SET baslik='$baslik', icerik='$icerik', durum='$durum', kategori_id='$kategori_id' WHERE id=$not_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<div class="card">
    <div class="card-header">
        Notu Düzenle
    </div>
    <div class="card-body">
        <form action="edit_note.php?id=<?php echo $not_id; ?>" method="post">
            <div class="mb-3">
                <label for="baslik" class="form-label">Başlık</label>
                <input type="text" class="form-control" id="baslik" name="baslik" value="<?php echo $not['baslik']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="icerik" class="form-label">İçerik</label>
                <textarea class="form-control" id="icerik" name="icerik" rows="5" required><?php echo $not['icerik']; ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kategori_id" class="form-label">Kategori</label>
                    <select class="form-select" id="kategori_id" name="kategori_id">
                        <option value="">Kategori Seç</option>
                        <?php
                        $sql_kategoriler = "SELECT * FROM kategoriler WHERE kullanici_id = $kullanici_id ORDER BY ad ASC";
                        $result_kategoriler = $conn->query($sql_kategoriler);
                        while ($kategori = $result_kategoriler->fetch_assoc()) {
                            $selected = ($not['kategori_id'] == $kategori['id']) ? 'selected' : '';
                            echo "<option value='{$kategori['id']}' $selected>{$kategori['ad']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="durum" class="form-label">Durum</label>
                    <select class="form-select" id="durum" name="durum">
                        <option value="Sade" <?php echo ($not['durum'] == 'Sade') ? 'selected' : ''; ?>>Sade</option>
                        <option value="Yapılacak" <?php echo ($not['durum'] == 'Yapılacak') ? 'selected' : ''; ?>>Yapılacak</option>
                        <option value="Yapıldı" <?php echo ($not['durum'] == 'Yapıldı') ? 'selected' : ''; ?>>Yapıldı</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Güncelle</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
