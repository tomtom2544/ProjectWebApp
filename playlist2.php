<?php
include 'db.php'; // นำเข้าไฟล์ db.php

// ดึงเพลย์ลิสต์ของผู้ใช้ (สมมติ user_id = 1)
$user_id = 1; 
$sql = "SELECT * FROM playlists WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพลย์ลิสต์ของฉัน</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>เพลย์ลิสต์ของฉัน</h1>
    </header>

    <main>
        <div class="playlist-list">
            <?php foreach ($playlists as $playlist): ?>
                <div class="playlist-item">
                    <h3><?= $playlist['playlist_name'] ?></h3>
                    <a href="playlist_songs.php?playlist_id=<?= $playlist['playlist_id'] ?>">ดูเพลงในเพลย์ลิสต์</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Music Recommendation</p>
    </footer>
</body>
</html>



-------------------------
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
    $song_id = $_GET['delete'];
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM playlist_songs WHERE user_id = :user_id AND song_id = :song_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'song_id' => $song_id]);

    // นำทางกลับไปที่หน้าเพลย์ลิสต์
    header("Location: playlist.php");
    exit;
}

// ดึงข้อมูลเพลงในเพลย์ลิสต์
$user_id = $_SESSION['user_id'];
$sql = "SELECT songs.*, playlist_songs.id AS playlist_id FROM songs 
        JOIN playlist_songs ON songs.song_id = playlist_songs.song_id 
        WHERE playlist_songs.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$songs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพลย์ลิสต์ของฉัน</title>
</head>
<body>
    <h1>เพลย์ลิสต์ของฉัน</h1>

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
                    <td><?= htmlspecialchars($song['artist']) ?></td>
                    <td>
                        <a href="playlist.php?delete=<?= $song['song_id'] ?>" onclick="return confirm('คุณแน่ใจว่าต้องการลบเพลงนี้ออกจากเพลย์ลิสต์หรือไม่?');">ลบ</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php">กลับไปหน้าแรก</a>
</body>
</html>
