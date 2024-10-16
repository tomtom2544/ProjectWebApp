<?php
include 'db.php'; // เชื่อมต่อกับฐานข้อมูล

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username)) {
        $errors[] = 'กรุณาใส่ชื่อผู้ใช้';
    }

    if (empty($email)) {
        $errors[] = 'กรุณาใส่อีเมล';
    }

    if (empty($password)) {
        $errors[] = 'กรุณาใส่รหัสผ่าน';
    } elseif ($password !== $confirm_password) {
        $errors[] = 'รหัสผ่านไม่ตรงกัน';
    }

    if (empty($errors)) {
        // ตรวจสอบว่าชื่อผู้ใช้หรืออีเมลมีอยู่แล้วหรือไม่
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $errors[] = 'ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้ว';
        } else {
            // เพิ่มผู้ใช้ใหม่
            $hash_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hash_password);

            if ($stmt->execute()) {
                $success_message = 'ลงทะเบียนสำเร็จ! คุณสามารถล็อกอินได้แล้ว';
            } else {
                $errors[] = 'เกิดข้อผิดพลาดในการลงทะเบียน';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียน</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* รูปแบบทั่วไป */
body {
    font-family: Arial, sans-serif;
    background-color: #282828; /* สีพื้นหลัง */
    color: #e0e0e0; /* สีตัวอักษร */
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* รูปแบบ header */
header {
    margin-top: 20px;
}

h1 {
    text-align: center;
    color: #EFA3C7; /* สีเขียวอ่อน */
}

/* รูปแบบ main */
main {
    background-color: #262626; /* สีพื้นหลังของฟอร์ม */
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    width: 300px;
    display: flex;
    flex-direction: column; /* จัดเนื้อหาในแนวตั้ง */
    align-items: center; /* จัดให้อยู่กลาง */
}

/* รูปแบบข้อความแจ้งเตือน */
.error-messages {
    background-color: #ffcccc; /* สีพื้นหลังสำหรับข้อความผิดพลาด */
    color: #d8000c; /* สีตัวอักษรข้อความผิดพลาด */
    border: 1px solid #d8000c;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
    width: 100%;
}

.success-message {
    background-color: #ccffcc; /* สีพื้นหลังสำหรับข้อความสำเร็จ */
    color: #005500; /* สีตัวอักษรข้อความสำเร็จ */
    border: 1px solid #005500;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
    width: 100%;
}

/* รูปแบบฟอร์ม */
form {
    width: 100%;
}

/* รูปแบบ input และ button */
input {
    width: 90%; /* กำหนดให้ input มีความกว้าง 80% */
    padding: 10px; /* เพิ่ม padding ให้กับ input */
    border: 1px solid #ccc; /* สีและขนาดของกรอบ input */
    border-radius: 4px; /* มุมโค้งของ input */
    margin-left: auto; /* จัดให้ input อยู่กลางโดยใช้ margin */
    margin-right: auto; /* จัดให้ input อยู่กลางโดยใช้ margin */
    display: block; /* ทำให้ input เป็น block เพื่อให้จัดกลางได้ */
}
div {
    margin-bottom: 15px; /* เพิ่มระยะห่างระหว่างฟิลด์ */
    display: flex; /* ใช้ flexbox สำหรับจัดเรียง label และ input */
    flex-direction: column; /* จัดให้ label อยู่ด้านบน input */
    align-items: center; /* จัดให้ label และ input อยู่กลาง */
}

label {
    width: 100%; /* กำหนดความกว้างของ label */
    text-align: left; /* จัด text ใน label ให้ชิดซ้าย */
}

.center-button {
    display: flex;
    justify-content: center; /* จัดให้อยู่กลางในแนวนอน */
    background-color: #EFA3C7; /* สีพื้นหลังของปุ่ม */
    color: white; /* สีตัวอักษรของปุ่ม */
    cursor: pointer; /* แสดงว่าเป็นปุ่มกดได้ */
    transition: background-color 0.3s; /* เอฟเฟคเปลี่ยนสีพื้นหลังเมื่อ hover */
    width: 100%; /* กำหนดให้ปุ่มมีความกว้างเต็ม */
    padding: 10px; /* เพิ่ม padding เพื่อให้ปุ่มใหญ่ขึ้น */
    margin-top: 10px; /* ระยะห่างด้านบน */
}

.center-button:hover {
    background-color: darkgrey; /* สีพื้นหลังของปุ่มเมื่อ hover */
}

/* รูปแบบลิงก์ */
p {
    text-align: center; /* จัดลิงก์ให้อยู่กลาง */
}

a {
    color: #80ff80; /* สีลิงก์ */
    text-decoration: none; /* ไม่ให้มีขีดเส้นใต้ */
}

a:hover {
    text-decoration: underline; /* ขีดเส้นใต้เมื่อ hover */
}

</style>
</head>
<body>
    <header>
        <h1>ลงทะเบียน</h1>
    </header>

    <main>
        <form action="register.php" method="POST">
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <?php foreach ($errors as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <p><?= $success_message ?></p>
                </div>
            <?php endif; ?>

            <div>
                <label for="username">ชื่อผู้ใช้:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="email">อีเมล:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div>
                <label for="password">รหัสผ่าน:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="confirm_password">ยืนยันรหัสผ่าน:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit" class="center-button">ลงทะเบียน</button>
        </form>
        <p>มีบัญชีแล้ว? <a href="login.php">ล็อกอินที่นี่</a></p>
    </main>
</body>
</html>
