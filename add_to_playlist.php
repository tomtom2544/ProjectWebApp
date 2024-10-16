<?php
session_start();
include 'db.php'; // นำเข้าไฟล์ db.php

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // เปลี่ยนเส้นทางไปยังหน้าเข้าสู่ระบบหากยังไม่ได้ล็อกอิน
    exit;
}

// รับค่าจากฟอร์ม
$song_id = isset($_POST['song_id']) ? (int)$_POST['song_id'] : 0;
$user_id = $_SESSION['user_id'];
$playlist_id = 1; // กำหนด playlist_id ที่ต้องการ เช่น 1

// เพิ่มเพลงลงในเพลย์ลิสต์
$sql = "INSERT INTO playlist_songs (playlist_id, song_id) VALUES (:playlist_id, :song_id)";
$stmt = $pdo->prepare($sql);
$stmt->execute(['playlist_id' => $playlist_id, 'song_id' => $song_id]);

header("Location: song_detail.php?song_id=$song_id"); // เปลี่ยนเส้นทางกลับไปยังหน้าเพลง
exit;
?>
