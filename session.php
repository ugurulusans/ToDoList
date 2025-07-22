<?php
session_start();
include 'db.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

$kullanici_id = $_SESSION['kullanici_id'];
$sql = "SELECT * FROM kullanicilar WHERE id = $kullanici_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>
