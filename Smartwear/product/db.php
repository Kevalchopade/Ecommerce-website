<?php
$host = "localhost";
$user = "root"; // Change if needed
$pass = ""; // Change if needed
$dbname = "smartwear";

$conn = new mysqli($host='localhost', $user='root', $pass='', $dbname='smartwear');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
