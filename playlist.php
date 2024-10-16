<?php
session_start();
include 'db.php'; // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าเข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ลบเพลงออกจากเพลย์ลิสต์
if (isset($_GET['delete'])) {
    $playlist_song_id = $_GET['delete'];

    $sql = "DELETE FROM playlist_songs WHERE playlist_song_id = :playlist_song_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['playlist_song_id' => $playlist_song_id]);

    // นำทางกลับไปที่หน้าเพลย์ลิสต์
    header("Location: playlist.php");
    exit;
}

// ดึงข้อมูลเพลงในเพลย์ลิสต์
$user_id = $_SESSION['user_id'];
$sql = "SELECT playlist_songs.playlist_song_id, songs.title, songs.artist_name 
        FROM playlist_songs 
        JOIN songs ON playlist_songs.song_id = songs.song_id 
        WHERE playlist_songs.playlist_id = :playlist_id"; // กำหนด playlist_id ที่ต้องการ
$stmt = $pdo->prepare($sql);
$stmt->execute(['playlist_id' => 1]); // เปลี่ยนให้ตรงกับ ID ของเพลย์ลิสต์ที่ต้องการ
$songs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพลย์ลิสต์ของฉัน</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #1a1a1a;
            color: #f5f5f5;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #EFA3C7;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #2c2c2c;
        }
        tr:nth-child(odd) {
            background-color: #1e1e1e;
        }
        .delete-button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
        }
        .delete-button:hover {
            background-color: #c0392b;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            background-color: #EFA3C7;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .back-link:hover {
            background-color: #d88ba6;
        }
    </style>
</head>
<body>
    <h1>เพลย์ลิสต์ของฉัน</h1>

    <?php if (count($songs) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ชื่อเพลง</th>
                    <th>ศิลปิน</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($songs as $song): ?>
                    <tr>
                        <td><?= htmlspecialchars($song['title']) ?></td>
                        <td><?= htmlspecialchars($song['artist_name']) ?></td>
                        <td>
                            <form action="playlist.php" method="GET" style="display:inline;">
                                <input type="hidden" name="delete" value="<?= $song['playlist_song_id'] ?>">
                                <button type="submit" class="delete-button" onclick="return confirm('คุณแน่ใจว่าต้องการลบเพลงนี้ออกจากเพลย์ลิสต์หรือไม่?');">ลบ</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>ไม่มีเพลงในเพลย์ลิสต์ของคุณ.</p>
    <?php endif; ?>

    <a href="index.php" class="back-link">กลับไปหน้าแรก</a>
</body>
</html>
