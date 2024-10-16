<?php
session_start();
include 'db.php'; // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // ตรวจสอบว่าอีเมลมีอยู่ในฐานข้อมูลหรือไม่
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // สร้าง reset token และบันทึกลงในฐานข้อมูล
        $reset_token = bin2hex(random_bytes(16)); // สร้าง token ที่ไม่ซ้ำ
        $sql = "UPDATE users SET reset_token = :reset_token WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['reset_token' => $reset_token, 'email' => $email]);

        // ส่งลิงค์รีเซ็ตรหัสผ่านทางอีเมล (ตัวอย่าง)
        $reset_link = "http://yourwebsite.com/reset_password.php?token=$reset_token";
        // ที่นี่คุณสามารถใช้ฟังก์ชัน mail() เพื่อส่งลิงค์ให้ผู้ใช้
        echo "ลิงค์รีเซ็ทรหัสผ่านได้ถูกส่งไปยังอีเมลของคุณ.";

        // นำผู้ใช้กลับไปที่หน้าล็อกอินหลังจากส่งอีเมล
        header("refresh: 1; url=login.php");
        exit;
    } else {
        $error = "อีเมลนี้ไม่ถูกต้อง.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน</title>
    <style>
        body {
            background-color: #282828;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color:#262626;
            padding: 40px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h1 {
            font-size: 24px;
            color: white;
            margin-bottom: 20px;
        }

        input[type="email"] {
            width: 94.5%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #EFA3C7;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: darkgrey;
        }

        p {
            color: red;
            font-size: 14px;
        }

        .success-message {
            color: green;
        }


        .back-to-index-button {
            display: block; /* แสดงเป็นบล็อก */
            background-color: #ff6f61; /* สีพื้นหลัง */
            color: white; /* สีตัวอักษร */
            padding: 10px 20px; /* ขนาดปุ่ม */
            border-radius: 5px; /* มุมมน */
            text-decoration: none; /* ไม่มีขีดเส้นใต้ */
            margin-top: 20px; /* ระยะห่างด้านบน */
            transition: background-color 0.3s; /* เอฟเฟกต์การเปลี่ยนสี */
        }

        .back-to-index-button:hover {
            background-color: #ff8c7a; /* สีเมื่อ hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ลืมรหัสผ่าน</h1>
        <?php if (isset($error)): ?>
            <p><?= $error ?></p>
        <?php endif; ?>
        <form action="forgot_password.php" method="POST">
            <input type="email" name="email" placeholder="อีเมล" required>
            <button type="submit">ส่งลิงค์รีเซ็ตรหัสผ่าน</button>
        </form>
        <a href="login.php" class="back-to-index-button">กลับไปยังเข้าสู่ระบบ</a>
    </div>
</body>
</html>
