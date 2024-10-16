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

session_start(); // เริ่มต้น session

// ตรวจสอบการค้นหา
$search_query = '';
$songs = [];

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];

    // ดึงข้อมูลเพลงตามการค้นหา
    $sql = "SELECT * FROM songs WHERE title LIKE :search_query OR artist LIKE :search_query";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search_query' => '%' . $search_query . '%']);
    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // ดึงข้อมูลเพลงใหม่ล่าสุดถ้าไม่มีการค้นหา
    $sql = "SELECT * FROM songs ORDER BY created_at DESC LIMIT 10"; // แสดงเพลงล่าสุด 10 เพลง
    $stmt = $pdo->query($sql);
    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
$isLoggedIn = isset($_SESSION['user_id']);
$isSearch = isset($_GET['search']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Recommendation</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #282828; /* พื้นหลังสีมืด */
            color: #e0e0e0; /* สีตัวหนังสือขาว */
            font-family: Arial, sans-serif;
        }
        header{
            background-color: #EFA3C7; /* สีเขียวเข้ม */
            color: white;
            padding: 20px;
            text-align: center;
        }
        footer {
            background-color: #EFA3C7; /* สีเขียวเข้ม */
            color: white;
            padding: 20px;
            text-align: center;
            margin-top:11%;
        }
        h1, h2 {
            color: white;
        }
        nav {

            margin-bottom: 20px;
        }
        nav ul {
            list-style: none;
            padding: 0;
        }
        nav ul li {
            display: inline;
            margin-right: 10px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
        }
        nav ul li a:hover {
            color:white ;
        }
        .search-container input {
            padding: 5px;
            width: 250px;
            border: 1px solid white;
            background-color: #1a1a1a;
            color: #e0e0e0;
        }
        .search-button {
            background-color: #EFA3C7;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .search-button:hover {
            background-color: lightpink;
        }
        .song-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .song-item {
            background-color: #262626; /* สีมืดสำหรับรายการเพลง */
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            width: 200px;
        }
        .cover-image {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .logout-link {
            position: absolute;
            top: 20px; /* ระยะห่างจากด้านบน */
            right: 20px; /* ระยะห่างจากด้านขวา */
            background-color: #282828; /* สีพื้นหลัง */
            color: white; /* สีข้อความ */
            padding: 10px 15px; /* เพิ่ม padding */
            text-decoration: none; /* ลบเส้นขีดใต้ลิงก์ */
            border-radius: 10px; /* ทำให้มุมมน */
        }
        .logout-link:hover {
            background-color: darkgrey; /* เปลี่ยนสีเมื่อ hover */
        }
        .back-button {
            background-color: #EFA3C7;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #ffb6c1;
        }
        .auth-buttons {
        margin-top: 10px; /* ระยะห่างจากหัวข้อ */
        }

        .auth-button {
        background-color: #EFA3C7; /* สีพื้นหลัง */
        color: white; /* สีข้อความ */
        padding: 10px 15px; /* เพิ่ม padding */
        text-decoration: none; /* ลบเส้นขีดใต้ลิงก์ */
        border-radius: 5px; /* ทำให้มุมมน */
        margin-right: 10px; /* ระยะห่างระหว่างปุ่ม */
        }

        .auth-button:hover {
        background-color: #ffb6c1; /* เปลี่ยนสีเมื่อ hover */
        }
        .playlist-button {
            background-color: #EFA3C7;
            color: white;
            padding: 15px 30px;
            font-size: 20px;
            border: none;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            display: block; /* แสดงเป็นบล็อก */
            margin: 0 auto; /* จัดตรงกลางหน้าจอ */
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .playlist-button:hover {
            background-color: #d88ba6;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
<header>
    <h1>Sound Zone</h1>
        <?php if ($isLoggedIn): ?>
            <p>ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?> <a class="logout-link" href="logout.php">ออกจากระบบ</a></p>
        <?php else: ?>
        <div class="auth-buttons">
            <a href="login.php" class="auth-button">เข้าสู่ระบบ</a>
            <a href="register.php" class="auth-button">ลงทะเบียน</a>
        </div>
        <?php endif; ?>
    </header>

    <nav>
        <?php if ($isLoggedIn): ?>
            <ul>
                <li><a href="playlist.php" class="playlist-button">เพลย์ลิสต์ของฉัน</a></li>
            </ul>
        <?php endif; ?>
    </nav>

    <div class="search-container">
        <form action="index.php" method="GET">
            <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="ค้นหาเพลงหรือศิลปิน..." required>
            <button type="submit" class="search-button">ค้นหา</button>
        </form>
    </div>
    
    <main>
        <h2>เพลงใหม่ล่าสุด</h2>
        <div class="song-list">
            <?php foreach ($songs as $song): ?>
                <div class="song-item">
                    <!-- เพิ่มลิงก์รอบรูปภาพที่เปลี่ยนไปยังหน้า song_detail.php โดยส่ง song_id -->
                    <a href="song_detail.php?song_id=<?= $song['song_id'] ?>">
                        <img src="<?= htmlspecialchars($song['cover_image']) ?>" alt="<?= htmlspecialchars($song['title']) ?>" class="cover-image">
                    </a>
                    <h3><?= htmlspecialchars($song['title']) ?></h3>
                    <p><?= htmlspecialchars($song['artist_name']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($isSearch): ?>
            <div>
                <br><br><a href="index.php" class="back-button">ย้อนกลับ</a>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 Music Recommendation</p>
    </footer>
</body>
</html>
