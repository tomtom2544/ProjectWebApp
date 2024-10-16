<?php
// db.php - เชื่อมต่อกับฐานข้อมูล
$host = 'localhost';
$dbname = 'music_db';
$username = 'root'; // เปลี่ยนเป็น username ของคุณ
$password = ''; // เปลี่ยนเป็น password ของคุณ

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage());
}


?>
