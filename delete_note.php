<?php
include 'session.php';

if (isset($_GET['id'])) {
    $not_id = $_GET['id'];

    // Notun bu kullanıcıya ait olup olmadığını kontrol et
    $sql_check = "SELECT * FROM notlar WHERE id = $not_id AND kullanici_id = $kullanici_id";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        $sql = "DELETE FROM notlar WHERE id=$not_id";

        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        header("Location: index.php"); // Yetkisi yoksa ana sayfaya yönlendir
    }

    $conn->close();
} else {
    header("Location: index.php");
}
?>
