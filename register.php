<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = $_POST['kullanici_adi'];
    $sifre = password_hash($_POST['sifre'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO kullanicilar (kullanici_adi, sifre) VALUES ('$kullanici_adi', '$sifre')";

    if ($conn->query($sql) === TRUE) {
        header("Location: login.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kayıt Ol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Kayıt Ol
                </div>
                <div class="card-body">
                    <form action="register.php" method="post">
                        <div class="mb-3">
                            <label for="kullanici_adi" class="form-label">Kullanıcı Adı</label>
                            <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" required>
                        </div>
                        <div class="mb-3">
                            <label for="sifre" class="form-label">Şifre</label>
                            <input type="password" class="form-control" id="sifre" name="sifre" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Kayıt Ol</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Zaten bir hesabın var mı? <a href="login.php">Giriş Yap</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
