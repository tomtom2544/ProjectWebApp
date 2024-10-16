<?php
session_start();
include 'db.php'; // นำเข้าไฟล์ db.php
$isLoggedIn = isset($_SESSION['user_id']);

if (!$isLoggedIn) {
    echo "กรุณาล็อกอินก่อนเพื่อทำการรีวิว";
    exit;
}

$user_id = $_SESSION['user_id']; // เก็บค่า user_id จาก session

// ดึงข้อมูลเพลงที่ผู้ใช้เลือก
$song_id = isset($_GET['song_id']) ? (int) $_GET['song_id'] : 0;
$sql = "SELECT * FROM songs WHERE song_id = :song_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':song_id', $song_id, PDO::PARAM_INT);
$stmt->execute();
$song = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่ามีเพลงหรือไม่
if (!$song) {
    echo "เพลงไม่พบ!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['review_id'])) {
        // แก้ไขรีวิวที่มีอยู่
        $review_id = (int)$_POST['review_id'];
        $new_review = $_POST['review'];
        
        // ตรวจสอบว่ารีวิวนี้เป็นของผู้ใช้ที่ล็อกอินอยู่หรือไม่
        $sql = "UPDATE reviews SET review = :review, updated_at = NOW() WHERE review_id = :review_id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['review' => $new_review, 'review_id' => $review_id, 'user_id' => $user_id]);
    } elseif (isset($_POST['delete_review_id'])) {
        // ลบรีวิว
        $review_id = (int)$_POST['delete_review_id'];
        $sql = "DELETE FROM reviews WHERE review_id = :review_id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['review_id' => $review_id, 'user_id' => $user_id]);
    } else {
        // เพิ่มรีวิวใหม่
        $review = $_POST['review'];
        $sql = "INSERT INTO reviews (user_id, song_id, review) VALUES (:user_id, :song_id, :review)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id, 'song_id' => $song_id, 'review' => $review]);
    }
    header("Location: song_detail.php?song_id=$song_id");
    exit;
}

// ดึงข้อมูลรีวิว พร้อมทั้งวันที่สร้างและแก้ไข
$sql = "SELECT reviews.*, users.username FROM reviews JOIN users ON reviews.user_id = users.user_id WHERE song_id = :song_id ORDER BY reviews.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['song_id' => $song_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($song['title']) ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styles remain unchanged */
        body {
            background-color: #282828;
            color: #e0e0e0;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header, footer {
            background-color: #EFA3C7;
            color: white;
            padding: 20px;
            text-align: center;
        }

        main {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 30px;
        }

        .song-cover {
            width: 300px;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
        }

        h1, h2 {
            margin: 20px 0 10px 0;
            color: white;
        }

        p {
            color: #d3d3d3;
            font-size: 18px;
        }

        .details, .review-section {
            background-color: #404040;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 800px;
            margin-top: 20px;
        }

        .details p, .review-section p {
            margin: 10px 0;
        }

        .review-section {
            background-color: #404040;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
        }

        .review-section ul {
            list-style-type: none;
            padding: 0;
        }

        .review-section li {
            margin: 10px 0;
            background-color: #303030;
            padding: 15px;
            border-radius: 5px;
        }

        .review-section form textarea {
            width: 97%;
            height: 75px;
            border-radius: 5px;
            border: none;
            padding: 10px;
            background-color: #1a1a1a;
            color: #e0e0e0;
        }

        .review-section button {
            background-color: #EFA3C7;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .review-section button:hover {
            background-color: #ffb6c1;
        }
        footer {
            margin-top: 30px;
        }

        a {
            color: #EFA3C7;
            text-decoration: none;
        }

        a:hover {
            color: #ffb6c1;
        }
        .add-to-playlist-button {
        background-color: #EFA3C7; /* สีพื้นหลัง */
        color: white; /* สีตัวอักษร */
        border: none; /* ไม่มีขอบ */
        border-radius: 5px; /* มุมมน */
        padding: 10px 20px; /* ขนาดปุ่ม */
        cursor: pointer; /* เปลี่ยน cursor เมื่อ hover */
        transition: background-color 0.3s; /* เอฟเฟกต์การเปลี่ยนสี */
        }

        .add-to-playlist-button:hover {
        background-color: #ffb6c1; /* สีเมื่อ hover */
        }
        .back-to-index-button {
        display: inline-block; /* แสดงเป็นบล็อก */
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
    <header>
        <h1><?= htmlspecialchars($song['title']) ?></h1>
    </header>

    <main>
        <img class="song-cover" src="<?= htmlspecialchars($song['cover_image']) ?>" alt="<?= htmlspecialchars($song['title']) ?>">
        <div class="details">
            <h2><strong>ศิลปิน: </strong><?= htmlspecialchars($song['artist_name']) ?></h2>
            <p><strong>อัลบั้ม:</strong> <?= htmlspecialchars($song['album_name']) ?></p>
            <p><strong>แนวเพลง:</strong> <?= htmlspecialchars($song['genre']) ?></p>
            <p><strong>วันที่ปล่อย:</strong> <?= htmlspecialchars($song['release_date']) ?></p>
        </div>

        <?php if ($isLoggedIn): ?>
            <form action="add_to_playlist.php" method="POST">
                <input type="hidden" name="song_id" value="<?= $song['song_id'] ?>">
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                <button type="submit" class="add-to-playlist-button">เพิ่มลงในเพลย์ลิสต์</button>
            </form>
        <?php else: ?>
            <p>กรุณาล็อกอินเพื่อเพิ่มเพลงลงในเพลย์ลิสต์</p>
        <?php endif; ?>

         <!-- ส่วนที่แสดงรีวิว -->
<div class="review-section">
    <h3>รีวิว</h3>

    <!-- ฟอร์มสำหรับเพิ่มหรือแก้ไขรีวิว -->
    <form method="POST" action="song_detail.php?song_id=<?= $song_id ?>">
        <textarea name="review" placeholder="เขียนรีวิวของคุณที่นี่..." required></textarea>
        <button type="submit">ส่งรีวิว</button>
    </form>

    <h4>รีวิวที่มีอยู่:</h4>
    <ul>
        <?php foreach ($reviews as $review): ?>
            <li>
                <strong><?= htmlspecialchars($review['username']) ?>:</strong>
                <p class="review-time">
                    <?php if ($review['updated_at']): ?>
                        แก้ไขเมื่อ: <?= htmlspecialchars($review['updated_at']) ?>
                    <?php else: ?>
                        รีวิวเมื่อ: <?= htmlspecialchars($review['created_at']) ?>
                    <?php endif; ?>
                </p>
                <hr></hr>
                <p><?= htmlspecialchars($review['review']) ?></p>
                <?php if ($isLoggedIn && $review['user_id'] == $user_id): ?>
                    <!-- ฟอร์มแก้ไขรีวิวของผู้ใช้ -->
                    <form method="POST" action="song_detail.php?song_id=<?= $song_id ?>" style="display: inline;">
                        <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
                        <textarea name="review" required><?= htmlspecialchars($review['review']) ?></textarea>
                        <button type="submit" class="edit-button">แก้ไขรีวิว</button>
                    </form>
                    <!-- ฟอร์มสำหรับลบรีวิว -->
                    <form method="POST" action="song_detail.php?song_id=<?= $song_id ?>" style="display: inline;">
                        <input type="hidden" name="delete_review_id" value="<?= $review['review_id'] ?>">
                        <button type="submit" class="delete-button" onclick="return confirm('คุณแน่ใจว่าจะลบรีวิวนี้?');" style="background-color: #ff7f7f; color: white;">ลบรีวิว</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

        <a href="index.php" class="back-to-index-button">กลับไปยังหน้าแรก</a>
    </main>
    <footer>
        <p>&copy; 2024 Music Recommendation</p>
    </footer>
</body>
</html>
