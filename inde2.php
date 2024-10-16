<?php
session_start();
include 'db.php'; // เชื่อมต่อกับฐานข้อมูล

// ดึงข้อมูลเพลงแนะนำจากฐานข้อมูล
$sql = "SELECT * FROM songs ORDER BY created_at DESC LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Recommendation</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>แนะนำเพลง</h1>
        <?php if ($isLoggedIn): ?>
            <p>ยินดีต้อนรับ, <?= $_SESSION['username'] ?> <a href="logout.php">ออกจากระบบ</a></p>
        <?php else: ?>
            <a href="login.php">ล็อกอิน</a> | <a href="register.php">ลงทะเบียน</a>
        <?php endif; ?>
    </header>

    <nav>
        <!-- เพิ่มปุ่มสำหรับนำทางไปยังหน้าเพลย์ลิสต์ -->
        <?php if ($isLoggedIn): ?>
            <ul>
                <li><a href="playlist.php">เพลย์ลิสต์ของฉัน</a></li>
            </ul>
        <?php endif; ?>
    </nav>

    <main>
        <h2>เพลงใหม่ล่าสุด</h2>
        <div class="song-list">
            <?php foreach ($songs as $song): ?>
                <div class="song-item">
                    <img src="<?= $song['cover_image'] ?>" alt="<?= $song['title'] ?>" class="cover-image">
                    <h3><?= $song['title'] ?></h3>
                    <p><?= $song['artist'] ?></p>
                    <a href="song_detail.php?song_id=<?= $song['song_id'] ?>">ดูรายละเอียด</a>
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

// ตรวจสอบการค้นหา
$search_query = '';
$songs = [];

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];

    // ดึงข้อมูลเพลงตามการค้นหา
    $sql = "SELECT * FROM songs WHERE title LIKE :search_query OR artist LIKE :search_query";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_query' => '%' . $search_query . '%']);
    $songs = $stmt->fetchAll();
} else {
    // ดึงข้อมูลเพลงทั้งหมดถ้าไม่มีการค้นหา
    $sql = "SELECT * FROM songs LIMIT 10"; // แสดงเพลงล่าสุด 10 เพลง
    $stmt = $pdo->query($sql);
    $songs = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรก - แนะนำเพลง</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .search-button {
            background-color: blue;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>หน้าแรก - แนะนำเพลง</h1>

    <div class="search-container">
        <form action="index.php" method="GET">
            <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="ค้นหาเพลงหรือศิลปิน..." required>
            <button type="submit" class="search-button">ค้นหา</button>
        </form>
    </div>

    <h2>ผลลัพธ์การค้นหา:</h2>
    <?php if (count($songs) > 0): ?>
        <ul>
            <?php foreach ($songs as $song): ?>
                <li><?= htmlspecialchars($song['title']) ?> - <?= htmlspecialchars($song['artist']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>ไม่พบผลลัพธ์สำหรับการค้นหาของคุณ.</p>
    <?php endif; ?>

    <a href="playlist.php">ไปที่เพลย์ลิสต์</a>
</body>
</html>
