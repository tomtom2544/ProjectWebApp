-- สร้างฐานข้อมูล music_db
CREATE DATABASE IF NOT EXISTS music_db;
USE music_db;

-- สร้างตาราง users สำหรับเก็บข้อมูลผู้ใช้
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- สร้างตาราง songs สำหรับเก็บข้อมูลเพลง
CREATE TABLE IF NOT EXISTS songs (
    song_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    artist_name VARCHAR(100) NOT NULL,
    album_name VARCHAR(100),
    genre VARCHAR(50),
    release_date DATE,
    cover_image VARCHAR(255),  -- เก็บที่อยู่ไฟล์รูปปกเพลง
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    artist_id INT,
    FOREIGN KEY (artist_id) REFERENCES artist_info(artist_id)
);

-- สร้างตาราง reviews สำหรับเก็บรีวิวเพลงของผู้ใช้
CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    song_id INT NOT NULL,
    review TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (song_id) REFERENCES songs(song_id)
);

-- สร้างตาราง playlists สำหรับเก็บเพลย์ลิสต์ของผู้ใช้
CREATE TABLE IF NOT EXISTS playlists (
    playlist_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    playlist_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    song_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- สร้างตาราง playlist_songs เพื่อเชื่อมโยงเพลงกับเพลย์ลิสต์
CREATE TABLE IF NOT EXISTS playlist_songs (
    playlist_song_id INT AUTO_INCREMENT PRIMARY KEY,
    playlist_id INT NOT NULL,
    song_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (playlist_id) REFERENCES playlists(playlist_id),
    FOREIGN KEY (song_id) REFERENCES songs(song_id)
);

-- สร้างตาราง artist_info สำหรับข้อมูลเพิ่มเติมเกี่ยวกับศิลปิน
CREATE TABLE IF NOT EXISTS artist_info (
    artist_id INT AUTO_INCREMENT PRIMARY KEY,
    artist_name VARCHAR(100) NOT NULL UNIQUE,
    bio TEXT,
    birth_date DATE,
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- เกี่ยวกับการรีเซ็ทรหัสผ่าน --
ALTER TABLE users ADD reset_token VARCHAR(255) DEFAULT NULL;

INSERT INTO songs (title, artist_id, artist_name, album_name, genre, release_date, cover_image) VALUES
('Blind Light', '1', 'The Weeknd', 'After Hours', 'Synth-pop', '2019-11-29','songs_covers/Pop.jpg'),
('Bohemian Rhapsody', '2', 'Queen', 'A Night at the Opera', 'Rock', '1975-10-31','songs_covers/Rock.jpg'),
('What a Wonderful World', '3', 'Louis Armstrong', 'What a Wonderful World', 'Jazz', '1967-10-18','songs_covers/Jazz.jpg'),
('Clair de Lune', '4', 'Claude Debussy', 'Suite Bergamasque', 'Classical', '1905-04-15','songs_covers/Classical.jpg'),
('Lose Yourself', '5', 'Eminem', '8 Mile (Soundtrack)', 'Hip-Hop/Rap', '2002-10-28','songs_covers/Hip-hop.jpg');

INSERT INTO artist_info (artist_name, bio, birth_date, country, created_at) VALUES
('Abel Tesfaye', 'The Weeknd (Abel Tesfaye) is a Canadian singer and producer known for his mix of R&B, pop, and synth-pop. He gained fame with his 2011 mixtapes and the hit album After Hours in 2020, featuring the global smash hit "Blinding Lights.', '1990-02-16', 'Canada', '2020-03-20'),
('Queen', 'Queen is a British rock band formed in London in 1970, consisting of Freddie Mercury, Brian May, Roger Taylor, and John Deacon. They are known for their eclectic style and elaborate live performances.', '1970-01-01', 'United Kingdom', '1975-11-21'),
('Louis Armstrong', 'Louis Armstrong was an American trumpeter, composer, and vocalist, recognized as one of the most influential figures in jazz music. He is known for his distinctive raspy voice and virtuosic trumpet playing.', '1901-08-04', 'United States', '1967-10-01'),
('Claude Debussy', 'Claude Debussy was a French composer associated with Impressionist music, known for his innovative harmonies and textures. His work has had a profound influence on 20th-century music.', '1862-08-22', 'France', '1905-01-01'),
('Eminem', 'Eminem is an American rapper, songwriter, and record producer. He is known for his rapid-fire delivery and controversial lyrics, and he is one of the best-selling music artists of all time.', '1972-10-17', 'United States', '2002-10-22');
