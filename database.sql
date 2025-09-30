-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for magyarositasok
CREATE DATABASE IF NOT EXISTS `magyarositasok` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `magyarositasok`;

-- Dumping structure for table magyarositasok.forum_posts
CREATE TABLE IF NOT EXISTS `forum_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `edited_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- Dumping data for table magyarositasok.forum_posts: ~0 rows (approximately)

-- Dumping structure for table magyarositasok.forum_topics
CREATE TABLE IF NOT EXISTS `forum_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `edited_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- Dumping data for table magyarositasok.forum_topics: ~0 rows (approximately)

-- Dumping structure for table magyarositasok.games
CREATE TABLE IF NOT EXISTS `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- Dumping data for table magyarositasok.games: ~6 rows (approximately)
INSERT INTO `games` (`id`, `title`, `description`, `cover_image`) VALUES
	(1, 'The Witcher 3: Wild Hunt', 'Légy Ríviai Geralt, a witcher, a szörnyvadász. Vár a háború dúlta, szörnyek prédálta kontinens. Aktuális megbízásod? Megkeresi Cirit, a Prófécia Gyermekét, az élő fegyvert, aki megváltoztathatja a világ képét.', 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Witcher_3_cover_art.jpg/250px-Witcher_3_cover_art.jpg'),
	(2, 'Cyberpunk 2077', 'A Cyberpunk 2077 nyílt világú akciókaland-RPG, amely a hatalom, a fényűzés és a testmódosítások lázában élő jövőbeli megapoliszban, Night Cityben játszódik.', 'https://upload.wikimedia.org/wikipedia/en/9/9f/Cyberpunk_2077_box_art.jpg'),
	(3, 'Stardew Valley', 'Örökölted nagyapád régi farmját a Stardew Valley-ban. Egy marék átörökölt szerszámmal és néhány érmével indulsz el, hogy új életet kezdj. Megtanulsz-e megélni a föld terményeiből, és sikerül-e ezeket a benőtt mezőket virágzó otthonná alakítanod?', 'https://upload.wikimedia.org/wikipedia/en/f/fd/Logo_of_Stardew_Valley.png'),
	(4, 'Phasmophobia', 'A Phasmophobia egy 4 játékos online kooperatív pszichológiai horrorjáték. A természetfeletti tevékenységek száma növekszik, és rajtad és csapatodon múlik, hogy minden rendelkezésre álló szellemvadász felszerelésedet felhasználva minél több bizonyítékot gyűjtsetek.', 'https://data.xxlgamer.com/products/5142/DGslpxgzt6Q7HO-big.jpg'),
	(5, 'STAR WARS Jedi: Survivor™', 'Cal Kestis történetét folytatja a STAR WARS Jedi: Survivor™, egy galaxisméretű, külső nézetes akció-kalandjáték', 'https://s.pacn.ws/1/p/15y/star-wars-jedi-survivor-755125.10.jpg'),
	(6, 'Brick Rigs', 'Építsd meg saját járműveidet, vagy tölts le egyet a Workshopba feltöltött 200 000-nél több alkotás közül, és élvezd a Brick Rigs dinamikus vezetési és rombolási fizikai motorját!', 'https://static.driffle.com/fit-in/720x512/media-gallery/prod/166525242016434400_Brick_Rigs.webp');

-- Dumping structure for table magyarositasok.translations
CREATE TABLE IF NOT EXISTS `translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `status` enum('Kész','Béta','Folyamatban') NOT NULL,
  `description` text DEFAULT NULL,
  `translators` varchar(255) DEFAULT NULL,
  `translation_method` varchar(100) DEFAULT 'Nem megadott',
  `file_path` varchar(255) DEFAULT NULL,
  `download_url` varchar(255) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `game_id` (`game_id`),
  CONSTRAINT `translations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `translations_ibfk_2` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- Dumping data for table magyarositasok.translations: ~1 rows (approximately)
INSERT INTO `translations` (`id`, `user_id`, `game_id`, `version`, `status`, `description`, `translators`, `translation_method`, `file_path`, `download_url`, `upload_date`) VALUES
	(16, 1, 6, '1.5.3', 'Kész', 'Olvasd El.txt ben benne van hogyan kell telepíteni.', 'Taks', 'Nem megadott', NULL, 'https://mega.nz/file/eMcDwaTI#cSjE-GX4qvvl_Rh4pkDpP_rdNZYdvlMmpFLjUHc1EcA', '2025-09-30 11:58:31');

-- Dumping structure for table magyarositasok.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `bio` text DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT 'https://i.pravatar.cc/150',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- Dumping data for table magyarositasok.users: ~1 rows (approximately)
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `bio`, `avatar_url`, `created_at`) VALUES
	(1, 'admin', 'admin@admin.com', 'password', 'admin', 'bio', 'uploads/avatars/avatar_68db081eae9b83.18294511.jpg', '2025-09-29 22:04:18');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
