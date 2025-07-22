<?php
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $baslik = $_POST['baslik'];
    $icerik = $_POST['icerik'];
    $durum = $_POST['durum'];
    $kategori_id = $_POST['kategori_id'];
    $yeni_kategori = $_POST['yeni_kategori'];

    if (!empty($yeni_kategori)) {
        $sql_yeni_kategori = "INSERT INTO kategoriler (ad, kullanici_id) VALUES ('$yeni_kategori', $kullanici_id)";
        if ($conn->query($sql_yeni_kategori) === TRUE) {
            $kategori_id = $conn->insert_id;
        } else {
            echo "Error: " . $sql_yeni_kategori . "<br>" . $conn->error;
        }
    }

    $sql = "INSERT INTO notlar (kullanici_id, kategori_id, baslik, icerik, durum) VALUES ($kullanici_id, '$kategori_id', '$baslik', '$icerik', '$durum')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<div class="card">
    <div class="card-header">
        Yeni Not Ekle
    </div>
    <div class="card-body">
        <form action="add_note.php" method="post">
            <div class="mb-3">
                <label for="baslik" class="form-label">Başlık</label>
                <input type="text" class="form-control" id="baslik" name="baslik" required>
            </div>
            <div class="mb-3">
                <label for="icerik" class="form-label">İçerik</label>
                <textarea class="form-control" id="icerik" name="icerik" rows="5" required></textarea>
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
                            echo "<option value='{$kategori['id']}'>{$kategori['ad']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="yeni_kategori" class="form-label">Veya Yeni Kategori Ekle</label>
                    <input type="text" class="form-control" id="yeni_kategori" name="yeni_kategori">
                </div>
            </div>
            <div class="mb-3">
                <label for="durum" class="form-label">Durum</label>
                <select class="form-select" id="durum" name="durum">
                    <option value="Sade">Sade</option>
                    <option value="Yapılacak">Yapılacak</option>
                    <option value="Yapıldı">Yapıldı</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Ekle</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
