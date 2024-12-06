<?php
$host = 'localhost';
$user = 'scheedeti1';
$password = 'scheedeti1';
$dbname = 'scheedeti1';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?>
