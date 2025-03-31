<?php
$host = 'DESKTOP-S8ACPHG'; // أو عنوان السيرفر
$dbname = 'online_store';
$username = 'sa'; // غيرها حسب إعداداتك
$password = 'gx@1337'; // كلمة السر إن وجدت

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>