-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.6.21 - Source distribution
-- ОС Сервера:                   Linux
-- HeidiSQL Версия:              9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры базы данных zectratrading_com
CREATE DATABASE IF NOT EXISTS `zectratrading_com` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `zectratrading_com`;


-- Дамп структуры для таблица zectratrading_com.daily_time_sheet
CREATE TABLE IF NOT EXISTS `daily_time_sheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `begin_lunch` time DEFAULT NULL,
  `end_lunch` time DEFAULT NULL,
  `hours` double DEFAULT NULL,
  `main_task` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3FAD8DFDA76ED395` (`user_id`),
  CONSTRAINT `FK_3FAD8DFDA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.daily_time_sheet: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `daily_time_sheet` DISABLE KEYS */;
INSERT INTO `daily_time_sheet` (`id`, `user_id`, `date`, `start_time`, `end_time`, `begin_lunch`, `end_lunch`, `hours`, `main_task`) VALUES
	(1, 3, '2015-03-02', '12:00:00', '20:00:00', '15:00:00', '15:20:00', 8, 'Nothing :)');
/*!40000 ALTER TABLE `daily_time_sheet` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.documents
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uploaded` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A2B07288A76ED395` (`user_id`),
  CONSTRAINT `FK_A2B07288A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.documents: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.forgot_password
CREATE TABLE IF NOT EXISTS `forgot_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `keyForAccess` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.forgot_password: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `forgot_password` DISABLE KEYS */;
/*!40000 ALTER TABLE `forgot_password` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8_unicode_ci NOT NULL,
  `activated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6000B0D3A76ED395` (`user_id`),
  CONSTRAINT `FK_6000B0D3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.notifications: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.offices
CREATE TABLE IF NOT EXISTS `offices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F574FF4C7E3C61F9` (`owner_id`),
  CONSTRAINT `FK_F574FF4C7E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.offices: ~9 rows (приблизительно)
/*!40000 ALTER TABLE `offices` DISABLE KEYS */;
INSERT INTO `offices` (`id`, `owner_id`, `name`, `description`) VALUES
	(1, 3, 'Office1', 'This is Test Office'),
	(2, 4, 'Office2', 'THis is another Test Office'),
	(3, 3, 'Office3', 'dawdawd'),
	(4, 4, 'Office4', 'qweqr'),
	(5, 3, 'Office5', 'asdagfsd'),
	(7, 6, 'awdawd', 'gfrgetrdg'),
	(8, 6, '21652', '1465651'),
	(9, 6, 'mnvbm', 'ljbnkm,'),
	(10, 3, 'qweqweqw', 'eweqwe');
/*!40000 ALTER TABLE `offices` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.office_posts
CREATE TABLE IF NOT EXISTS `office_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `office_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` longtext COLLATE utf8_unicode_ci NOT NULL,
  `posted` datetime NOT NULL,
  `edited` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8106197AFFA0C224` (`office_id`),
  KEY `IDX_8106197AA76ED395` (`user_id`),
  CONSTRAINT `FK_8106197AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_8106197AFFA0C224` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.office_posts: ~25 rows (приблизительно)
/*!40000 ALTER TABLE `office_posts` DISABLE KEYS */;
INSERT INTO `office_posts` (`id`, `office_id`, `user_id`, `message`, `posted`, `edited`) VALUES
	(1, 1, 3, 'test post', '2015-02-28 18:07:51', NULL),
	(2, 1, 3, 'test post #2', '2015-02-28 18:08:18', NULL),
	(3, 1, 3, 'test post #3', '2015-02-28 18:08:56', NULL),
	(4, 1, 3, 'test post #4', '2015-02-28 18:10:10', NULL),
	(5, 1, 3, '1', '2015-02-28 18:35:03', NULL),
	(6, 1, 3, 'qwewqe', '2015-02-28 18:36:03', NULL),
	(7, 1, 3, 'Test New Test', '2015-02-28 22:46:25', NULL),
	(8, 1, 3, 'qwertyu', '2015-02-28 23:02:13', NULL),
	(9, 1, 3, 'Test', '2015-02-28 23:29:10', NULL),
	(10, 1, 3, 'Test', '2015-02-28 23:30:11', NULL),
	(11, 1, 3, 'awdawd', '2015-02-28 23:31:36', NULL),
	(12, 1, 3, 'qweqwrqeweqeq', '2015-02-28 23:31:49', NULL),
	(13, 1, 3, 'dawdkwajd\r\ndkawdja;lwdawd\r\nawdjawldkwa;ldaw\r\ndawkldnam;wfmwe;rgmrwe\r\nwadjwqkd;awmf;maef\r\nawdawkmfefm;lewflwefkejf;le\r\nwdkwq;ldlawmd;lamwd;l\r\nw', '2015-03-01 00:13:20', NULL),
	(14, 1, 3, 'dawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe wadjwqkd;awmf;maef awdawkmfefm;lewflwefkejf;le wdkwq;ldlawmd;lamwd;l w\r\ndawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe wadjwqkd;awmf;maef awdawkmfefm;lewflwefkejf;le wdkwq;ldlawmd;lamwd;l w\r\ndawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe wadjwqkd;awmf;maef awdawkmfefm;lewflwefkejf;le wdkwq;ldlawmd;lamwd;l wdawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe wadjwqkd;awmf;maef awdawkmfefm;lewflwefkejf;le wdkwq;ldlawmd;lamwd;l w', '2015-03-01 00:15:06', NULL),
	(15, 1, 3, 'dkwdajwk\r\nwahdawjkldwa\r\nawdhwajdlwad\r\nwadhawdlawdk\r\nawdiwjaldwa\r\n', '2015-03-01 00:23:18', NULL),
	(16, 1, 3, '<i>qwerty</i>', '2015-03-01 00:31:16', NULL),
	(17, 1, 3, 'dawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe\r\ndawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe\r\ndawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwedawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwedawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe\r\ndawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe\r\ndawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwedawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe\r\ndawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe\r\ndawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe\r\ndawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe\r\ndawdkwajd dkawdja;lwdawd awdjawldkwa;ldaw dawkldnam;wfmwe;rgmrwe\r\n', '2015-03-01 00:34:50', NULL),
	(18, 1, 3, 'qwerty\r\nlolka', '2015-03-01 00:35:12', NULL),
	(19, 1, 3, '@riki34', '2015-03-01 00:38:18', NULL),
	(20, 1, 3, 'New Post', '2015-03-01 02:34:45', NULL),
	(21, 1, 6, 'awdwadw', '2015-03-01 05:36:50', NULL),
	(22, 1, 6, 'gradwadawdawdawdawdadadawdawfafetrgrhjyujeargregsregergergergrgergergregregergergregregregregregr\r\ngradwadawdawdawdawdadadawdawfafetrgrhjyujeargregsregergergergrgergergregregergergregregregregregr\r\ngradwadawdawdawdawdadadawdawfafetrgrhjyujeargregsregergergergrgergergregregergergregregregregregr\r\ngradwadawdawdawdawdadadawdawfafetrgrhjyujeargregsregergergergrgergergregregergergregregregregregr\r\ngradwadawdawdawdawdadadawdawfafetrgrhjyujeargregsregergergergrgergergregregergergregregregregregr\r\ngradwadawdawdawdawdadadawdawfafetrgrhjyujeargregsregergergergrgergergregregergergregregregregregr\r\ngradwadawdawdawdawdadadawdawfafetrgrhjyujeargregsregergergergrgergergregregergergregregregregregr\r\ngradwadawdawdawdawdadadawdawfafetrgrhjyujeargregsregergergergrgergergregregergergregregregregregr\r\ngradwadawdawdawdawdadadawdawfafetrgrhjyujeargregsregergergergrgergergregregergergregregregregregr', '2015-03-01 05:38:15', NULL),
	(23, 1, 6, 'qwew\r\nqwewq', '2015-03-01 05:44:31', NULL),
	(24, 3, 3, 'qwertyu', '2015-03-01 15:16:15', NULL),
	(25, 3, 3, 'dwadawdawd', '2015-03-01 15:16:21', NULL),
	(26, 3, 3, 'afsrgtrhrth', '2015-03-01 15:16:27', NULL),
	(27, 3, 3, 'awdfesfgreghrth', '2015-03-01 15:16:33', NULL);
/*!40000 ALTER TABLE `office_posts` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.office_roles
CREATE TABLE IF NOT EXISTS `office_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `office_id` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BF658D47FFA0C224` (`office_id`),
  CONSTRAINT `FK_BF658D47FFA0C224` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.office_roles: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `office_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `office_roles` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.office_user
CREATE TABLE IF NOT EXISTS `office_user` (
  `office_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`office_id`,`user_id`),
  KEY `IDX_607C7D45FFA0C224` (`office_id`),
  KEY `IDX_607C7D45A76ED395` (`user_id`),
  CONSTRAINT `FK_607C7D45A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_607C7D45FFA0C224` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.office_user: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `office_user` DISABLE KEYS */;
INSERT INTO `office_user` (`office_id`, `user_id`) VALUES
	(1, 6),
	(2, 3);
/*!40000 ALTER TABLE `office_user` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.posts_task
CREATE TABLE IF NOT EXISTS `posts_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` longtext COLLATE utf8_unicode_ci NOT NULL,
  `posted` datetime NOT NULL,
  `edited` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E82EE29C8DB60186` (`task_id`),
  KEY `IDX_E82EE29CA76ED395` (`user_id`),
  CONSTRAINT `FK_E82EE29C8DB60186` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  CONSTRAINT `FK_E82EE29CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.posts_task: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `posts_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts_task` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.projects
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5C93B3A45E237E06` (`name`),
  KEY `IDX_5C93B3A47E3C61F9` (`owner_id`),
  KEY `IDX_5C93B3A4727ACA70` (`parent_id`),
  CONSTRAINT `FK_5C93B3A4727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `FK_5C93B3A47E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.projects: ~6 rows (приблизительно)
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` (`id`, `owner_id`, `parent_id`, `name`, `description`) VALUES
	(1, 3, NULL, 'Project1', 'Test Project'),
	(3, 6, NULL, 'qwerty', 'zxczxv'),
	(4, 6, NULL, 'qweqwe', 'qwewqe'),
	(5, 6, NULL, 'dsffdfs', 'adwadwd'),
	(6, 6, NULL, 'gtgdrg', 'xvdvxdvxdv');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.project_office
CREATE TABLE IF NOT EXISTS `project_office` (
  `project_id` int(11) NOT NULL,
  `office_id` int(11) NOT NULL,
  PRIMARY KEY (`project_id`,`office_id`),
  KEY `IDX_63F58048166D1F9C` (`project_id`),
  KEY `IDX_63F58048FFA0C224` (`office_id`),
  CONSTRAINT `FK_63F58048166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_63F58048FFA0C224` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.project_office: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `project_office` DISABLE KEYS */;
INSERT INTO `project_office` (`project_id`, `office_id`) VALUES
	(1, 1);
/*!40000 ALTER TABLE `project_office` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.project_posts
CREATE TABLE IF NOT EXISTS `project_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` longtext COLLATE utf8_unicode_ci NOT NULL,
  `posted` datetime NOT NULL,
  `edited` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9B08B364166D1F9C` (`project_id`),
  KEY `IDX_9B08B364A76ED395` (`user_id`),
  CONSTRAINT `FK_9B08B364166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `FK_9B08B364A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.project_posts: ~7 rows (приблизительно)
/*!40000 ALTER TABLE `project_posts` DISABLE KEYS */;
INSERT INTO `project_posts` (`id`, `project_id`, `user_id`, `message`, `posted`, `edited`) VALUES
	(1, 1, 3, 'awdwadawd', '2015-02-28 23:32:39', NULL),
	(2, 1, 3, 'wadawdw', '2015-02-28 23:32:44', NULL),
	(3, 1, 4, 'qweqweqwe', '2015-02-28 23:33:26', NULL),
	(4, 1, 4, 'zcasascad', '2015-02-28 23:33:47', NULL),
	(5, 1, 3, 'Test', '2015-03-01 02:40:26', NULL),
	(6, 3, 6, 'awdawd', '2015-03-01 05:55:05', NULL),
	(9, 1, 3, 'dadwdawdawdawdawdwadawd\r\nwadkawjdlkawjdl;ajwlkjwaldjawldkwad\r\nwadwaidjawldjwaldaw', '2015-03-01 16:02:37', NULL),
	(10, 1, 3, 'yejgkmfr4n', '2015-03-02 12:32:28', NULL);
/*!40000 ALTER TABLE `project_posts` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.project_user
CREATE TABLE IF NOT EXISTS `project_user` (
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`project_id`,`user_id`),
  KEY `IDX_B4021E51166D1F9C` (`project_id`),
  KEY `IDX_B4021E51A76ED395` (`user_id`),
  CONSTRAINT `FK_B4021E51166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B4021E51A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.project_user: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `project_user` DISABLE KEYS */;
INSERT INTO `project_user` (`project_id`, `user_id`) VALUES
	(1, 4);
/*!40000 ALTER TABLE `project_user` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.roles: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`) VALUES
	(1, 'ROLE_ADMIN'),
	(2, 'ROLE_USER');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.sprints
CREATE TABLE IF NOT EXISTS `sprints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `office_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4EE469716BF700BD` (`status_id`),
  KEY `IDX_4EE46971FFA0C224` (`office_id`),
  CONSTRAINT `FK_4EE469716BF700BD` FOREIGN KEY (`status_id`) REFERENCES `sprint_statuses` (`id`),
  CONSTRAINT `FK_4EE46971FFA0C224` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.sprints: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `sprints` DISABLE KEYS */;
INSERT INTO `sprints` (`id`, `status_id`, `name`, `description`, `office_id`) VALUES
	(1, 1, 'qwertu', 'zxcbn', 1);
/*!40000 ALTER TABLE `sprints` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.sprint_statuses
CREATE TABLE IF NOT EXISTS `sprint_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.sprint_statuses: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `sprint_statuses` DISABLE KEYS */;
INSERT INTO `sprint_statuses` (`id`, `label`, `color`) VALUES
	(1, 'Open', 'White'),
	(2, 'In-Progress', 'Green'),
	(3, 'Closed', 'Red');
/*!40000 ALTER TABLE `sprint_statuses` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.tasks
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `assigned_id` int(11) DEFAULT NULL,
  `priority_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `sprint_id` int(11) DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `name` longtext COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `progress` int(11) NOT NULL,
  `estimated_hours` int(11) NOT NULL,
  `estimated_minutes` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_50586597C54C8C93` (`type_id`),
  KEY `IDX_50586597727ACA70` (`parent_id`),
  KEY `IDX_50586597166D1F9C` (`project_id`),
  KEY `IDX_50586597E1501A05` (`assigned_id`),
  KEY `IDX_50586597497B19F9` (`priority_id`),
  KEY `IDX_505865976BF700BD` (`status_id`),
  KEY `IDX_505865978C24077B` (`sprint_id`),
  KEY `IDX_505865977E3C61F9` (`owner_id`),
  CONSTRAINT `FK_50586597166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `FK_50586597497B19F9` FOREIGN KEY (`priority_id`) REFERENCES `task_priority` (`id`),
  CONSTRAINT `FK_505865976BF700BD` FOREIGN KEY (`status_id`) REFERENCES `task_statuses` (`id`),
  CONSTRAINT `FK_50586597727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `tasks` (`id`),
  CONSTRAINT `FK_505865977E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_505865978C24077B` FOREIGN KEY (`sprint_id`) REFERENCES `sprints` (`id`),
  CONSTRAINT `FK_50586597C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `task_types` (`id`),
  CONSTRAINT `FK_50586597E1501A05` FOREIGN KEY (`assigned_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.tasks: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` (`id`, `type_id`, `parent_id`, `project_id`, `assigned_id`, `priority_id`, `status_id`, `sprint_id`, `owner_id`, `name`, `description`, `progress`, `estimated_hours`, `estimated_minutes`, `start_date`, `end_date`) VALUES
	(4, 1, NULL, 1, NULL, 1, 1, NULL, 3, 'Task', 'Some test task', 0, 0, 0, '2015-03-02 05:46:30', '2015-03-12 04:03:21'),
	(5, 2, NULL, 1, NULL, 2, 1, NULL, 3, 'Test task', 'Test 12345', 0, 0, 0, '2015-03-12 04:03:21', '2015-05-24 04:03:21'),
	(6, 1, 4, 1, NULL, 1, 1, NULL, 3, 'qw', 'qwq', 0, 0, 0, '2015-02-23 05:13:53', '2015-03-13 05:13:53'),
	(7, 2, 4, 1, NULL, 1, 1, NULL, 3, 'wewew', 'asdsad', 0, 0, 0, '2015-03-14 05:37:22', '2015-03-24 05:37:22');
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.task_priority
CREATE TABLE IF NOT EXISTS `task_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.task_priority: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `task_priority` DISABLE KEYS */;
INSERT INTO `task_priority` (`id`, `label`, `color`) VALUES
	(1, 'low', 'green'),
	(2, 'medium', 'orange'),
	(3, 'high', 'red');
/*!40000 ALTER TABLE `task_priority` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.task_statuses
CREATE TABLE IF NOT EXISTS `task_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.task_statuses: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `task_statuses` DISABLE KEYS */;
INSERT INTO `task_statuses` (`id`, `label`, `color`) VALUES
	(1, 'story', 'lightgray'),
	(2, 'todo', 'blue'),
	(3, 'in-progress', 'violet'),
	(4, 'done', 'green');
/*!40000 ALTER TABLE `task_statuses` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.task_types
CREATE TABLE IF NOT EXISTS `task_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.task_types: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `task_types` DISABLE KEYS */;
INSERT INTO `task_types` (`id`, `label`, `color`) VALUES
	(1, 'task', 'green'),
	(2, 'bug', 'red'),
	(3, 'improvement', 'blue');
/*!40000 ALTER TABLE `task_types` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `registered` datetime NOT NULL,
  `lastactive` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`),
  UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.users: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `password`, `email`, `name`, `surname`, `registered`, `lastactive`, `active`, `avatar`) VALUES
	(3, 'riki34', '2e6401531190d2c9ea5a91d4a16ed8ee026419d3703ee00f70defeae0416d2645b8bddb4d9a87fc8d6c5c812a0157bf59cab49b56b62aaed35a065637f509a6c', 'riki34@spaces.ru', 'Vlad', 'Kosko', '2015-02-28 03:30:27', '2015-02-28 03:30:27', 1, 'riki34/avatar/66324.jpeg'),
	(4, 'Vlad', '2e6401531190d2c9ea5a91d4a16ed8ee026419d3703ee00f70defeae0416d2645b8bddb4d9a87fc8d6c5c812a0157bf59cab49b56b62aaed35a065637f509a6c', 'vladislav.kosko@gmail.com', 'Vlad', 'Kosko', '2015-02-28 12:48:38', '2015-02-28 12:48:38', 1, 'Vlad/avatar/17728.jpeg'),
	(5, 'vasyaL', '2e6401531190d2c9ea5a91d4a16ed8ee026419d3703ee00f70defeae0416d2645b8bddb4d9a87fc8d6c5c812a0157bf59cab49b56b62aaed35a065637f509a6c', 'vasya.lozan@gmail.com', 'Vasya', 'Lozan', '2015-02-28 15:58:07', '2015-02-28 15:58:07', 1, NULL),
	(6, 'Test', '2e6401531190d2c9ea5a91d4a16ed8ee026419d3703ee00f70defeae0416d2645b8bddb4d9a87fc8d6c5c812a0157bf59cab49b56b62aaed35a065637f509a6c', 'test@test', 'Test', 'Test', '2015-02-28 18:41:58', '2015-02-28 18:41:58', 1, 'Test/avatar/57608.jpeg'),
	(7, 'qwerty', '2e6401531190d2c9ea5a91d4a16ed8ee026419d3703ee00f70defeae0416d2645b8bddb4d9a87fc8d6c5c812a0157bf59cab49b56b62aaed35a065637f509a6c', 'qwerty@qwerty', 'QWERTY', 'QWERTY', '2015-03-01 21:18:23', '2015-03-01 21:18:23', 1, 'qwerty/avatar/73031.jpeg');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.user_role
CREATE TABLE IF NOT EXISTS `user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `IDX_2DE8C6A3A76ED395` (`user_id`),
  KEY `IDX_2DE8C6A3D60322AC` (`role_id`),
  CONSTRAINT `FK_2DE8C6A3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_2DE8C6A3D60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.user_role: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
	(3, 1),
	(4, 2),
	(5, 2),
	(6, 2),
	(7, 2);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.user_settings
CREATE TABLE IF NOT EXISTS `user_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `show_hidden_topics` tinyint(1) NOT NULL DEFAULT '1',
  `disable_message_on_email` tinyint(1) NOT NULL DEFAULT '0',
  `disable_message_on_site` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_5C844C5A76ED395` (`user_id`),
  CONSTRAINT `FK_5C844C5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.user_settings: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `user_settings` DISABLE KEYS */;
INSERT INTO `user_settings` (`id`, `user_id`, `show_hidden_topics`, `disable_message_on_email`, `disable_message_on_site`) VALUES
	(3, 3, 1, 0, 0),
	(4, 4, 1, 0, 0),
	(5, 5, 1, 0, 0),
	(6, 6, 1, 0, 0),
	(7, 7, 1, 0, 0);
/*!40000 ALTER TABLE `user_settings` ENABLE KEYS */;


-- Дамп структуры для таблица zectratrading_com.user_settings_notifications
CREATE TABLE IF NOT EXISTS `user_settings_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `msg_email_message_office` tinyint(1) NOT NULL DEFAULT '1',
  `msg_email_message_topic` tinyint(1) NOT NULL DEFAULT '1',
  `msg_email_removed_office` tinyint(1) NOT NULL DEFAULT '1',
  `msg_email_removed_topic` tinyint(1) NOT NULL DEFAULT '1',
  `msg_email_topic_added` tinyint(1) NOT NULL DEFAULT '1',
  `msg_email_task_assigned` tinyint(1) NOT NULL DEFAULT '1',
  `msg_email_task_comment` tinyint(1) NOT NULL DEFAULT '1',
  `msg_site_message_office` tinyint(1) NOT NULL DEFAULT '1',
  `msg_site_removed_office` tinyint(1) NOT NULL DEFAULT '1',
  `msg_site_task_assigned` tinyint(1) NOT NULL DEFAULT '1',
  `msg_site_task_comment` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D3F27F12A76ED395` (`user_id`),
  CONSTRAINT `FK_D3F27F12A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы zectratrading_com.user_settings_notifications: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `user_settings_notifications` DISABLE KEYS */;
INSERT INTO `user_settings_notifications` (`id`, `user_id`, `msg_email_message_office`, `msg_email_message_topic`, `msg_email_removed_office`, `msg_email_removed_topic`, `msg_email_topic_added`, `msg_email_task_assigned`, `msg_email_task_comment`, `msg_site_message_office`, `msg_site_removed_office`, `msg_site_task_assigned`, `msg_site_task_comment`) VALUES
	(3, 3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
	(4, 4, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
	(5, 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
	(6, 6, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
	(7, 7, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
/*!40000 ALTER TABLE `user_settings_notifications` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
