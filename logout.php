<?php
session_start();
session_unset(); // ล้างข้อมูล session ทั้งหมด
session_destroy(); // ทำลาย session

// รีไดเรกไปที่หน้า index.php
header("Location: index.php");
exit;
?>
