<?php
$servername = "localhost";
$username = "kullanici_adiniz";
$password = "sifreniz";
$dbname = "notlar";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
