<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword === $confirmPassword) {
        // ตรวจสอบว่าโทเค็นยังใช้ได้อยู่หรือไม่
        $sql = "SELECT * FROM users WHERE reset_token = :token AND reset_token_expires > NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();

        if ($user) {
            // อัปเดตรหัสผ่านใหม่
            $sql = "UPDATE users SET password = :password, reset_token = NULL, reset_token_expires = NULL WHERE reset_token = :token";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['password' => md5($newPassword), 'token' => $token]);

            $success = "รีเซ็ตรหัสผ่านเรียบร้อยแล้ว";
        } else {
            $error = "โทเค็นไม่ถูกต้องหรือหมดอายุ";
        }
    } else {
        $error = "รหัสผ่านไม่ตรงกัน";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>รีเซ็ตรหัสผ่าน</h1>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php elseif (isset($success)): ?>
        <p style="color:green;"><?= $success ?></p>
    <?php endif; ?>
    <form action="reset_password.php" method="POST">
        <input type="hidden" name="token" value="<?= $_GET['token'] ?>" required>
        <input type="password" name="new_password" placeholder="รหัสผ่านใหม่" required>
        <input type="password" name="confirm_password" placeholder="ยืนยันรหัสผ่าน" required>
        <button type="submit">รีเซ็ตรหัสผ่าน</button>
    </form>
</body>
</html>
