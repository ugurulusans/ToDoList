<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = $_POST['kullanici_adi'];
    $sifre = $_POST['sifre'];

    $sql = "SELECT * FROM kullanicilar WHERE kullanici_adi = '$kullanici_adi'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($sifre, $user['sifre'])) {
            $_SESSION['kullanici_id'] = $user['id'];
            header("Location: index.php");
        } else {
            $error = "Geçersiz şifre";
        }
    } else {
        $error = "Kullanıcı bulunamadı";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Giriş Yap
                </div>
                <div class="card-body">
                    <?php if (isset($error)) { ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php } ?>
                    <form action="login.php" method="post">
                        <div class="mb-3">
                            <label for="kullanici_adi" class="form-label">Kullanıcı Adı</label>
                            <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" required>
                        </div>
                        <div class="mb-3">
                            <label for="sifre" class="form-label">Şifre</label>
                            <input type="password" class="form-control" id="sifre" name="sifre" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Giriş Yap</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Hesabın yok mu? <a href="register.php">Kayıt Ol</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
