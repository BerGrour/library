-- --------------------------------------------------------
-- Хост:                         localhost
-- Версия сервера:               10.4.24-MariaDB - mariadb.org binary distribution
-- Операционная система:         Win64
-- HeidiSQL Версия:              11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Дамп структуры для таблица library_new.article
CREATE TABLE IF NOT EXISTS `article` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pages` smallint(6) NOT NULL,
  `annotation` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `issue_id` bigint(20) unsigned DEFAULT NULL,
  `file_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_issue_id_foreign` (`issue_id`),
  KEY `article_file_id_foreign` (`file_id`),
  CONSTRAINT `article_file_id_foreign` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE SET NULL,
  CONSTRAINT `article_issue_id_foreign` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.article_author
CREATE TABLE IF NOT EXISTS `article_author` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint(20) unsigned NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_author_article_id_foreign` (`article_id`),
  KEY `article_author_author_id_foreign` (`author_id`),
  CONSTRAINT `article_author_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE,
  CONSTRAINT `article_author_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.author
CREATE TABLE IF NOT EXISTS `author` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `surname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middlename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.book
CREATE TABLE IF NOT EXISTS `book` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additionalname` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additionalresponse` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bookinfo` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publishplace` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publishhouse` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publishyear` smallint(6) DEFAULT NULL,
  `tom` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pages` smallint(5) unsigned DEFAULT NULL,
  `authorsign` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numbersk` int(11) DEFAULT NULL,
  `recieptdate` date DEFAULT NULL,
  `cost` decimal(8,2) unsigned DEFAULT NULL,
  `ISBN` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `annotation` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `withraw` int(10) unsigned NOT NULL,
  `rubric_id` bigint(20) unsigned DEFAULT NULL,
  `file_id` bigint(20) unsigned DEFAULT NULL,
  `disposition` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `book_rubric_id_foreign` (`rubric_id`),
  KEY `book_file_id_foreign` (`file_id`),
  CONSTRAINT `book_file_id_foreign` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE SET NULL,
  CONSTRAINT `book_rubric_id_foreign` FOREIGN KEY (`rubric_id`) REFERENCES `rubric` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.book_author
CREATE TABLE IF NOT EXISTS `book_author` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `book_id` bigint(20) unsigned NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `book_author_book_id_foreign` (`book_id`),
  KEY `book_author_author_id_foreign` (`author_id`),
  CONSTRAINT `book_author_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_author_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.files
CREATE TABLE IF NOT EXISTS `files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filepath` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.infoarticle
CREATE TABLE IF NOT EXISTS `infoarticle` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inforelease_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edition` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recieptdate` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additionalinfo` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `infoarticle_inforelease_id_foreign` (`inforelease_id`),
  CONSTRAINT `infoarticle_inforelease_id_foreign` FOREIGN KEY (`inforelease_id`) REFERENCES `inforelease` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.infoarticle_author
CREATE TABLE IF NOT EXISTS `infoarticle_author` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `infoarticle_id` bigint(20) unsigned NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `infoarticle_author_infoarticle_id_foreign` (`infoarticle_id`),
  KEY `infoarticle_author_author_id_foreign` (`author_id`),
  CONSTRAINT `infoarticle_author_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`) ON DELETE CASCADE,
  CONSTRAINT `infoarticle_author_infoarticle_id_foreign` FOREIGN KEY (`infoarticle_id`) REFERENCES `infoarticle` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.inforelease
CREATE TABLE IF NOT EXISTS `inforelease` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `seria_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numbersk` smallint(5) unsigned NOT NULL,
  `publishyear` smallint(6) NOT NULL,
  `rubric_id` bigint(20) unsigned DEFAULT NULL,
  `file_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inforelease_rubric_id_foreign` (`rubric_id`),
  KEY `inforelease_seria_id_foreign` (`seria_id`),
  KEY `inforelease_file_id_foreign` (`file_id`),
  CONSTRAINT `inforelease_file_id_foreign` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inforelease_rubric_id_foreign` FOREIGN KEY (`rubric_id`) REFERENCES `rubric` (`id`),
  CONSTRAINT `inforelease_seria_id_foreign` FOREIGN KEY (`seria_id`) REFERENCES `seria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.issue
CREATE TABLE IF NOT EXISTS `issue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `journal_id` bigint(20) unsigned DEFAULT NULL,
  `issuecode` smallint(6),
  `issueyear` smallint(6) NOT NULL,
  `issuenumber` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issuedate` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `issue_journal_id_foreign` (`journal_id`),
  CONSTRAINT `issue_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.journal
CREATE TABLE IF NOT EXISTS `journal` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ISSN` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `disposition` smallint(6) NOT NULL,
  `rubric_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_rubric_id_foreign` (`rubric_id`),
  CONSTRAINT `journal_rubric_id_foreign` FOREIGN KEY (`rubric_id`) REFERENCES `rubric` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.logbook
CREATE TABLE IF NOT EXISTS `logbook` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `book_id` bigint(20) unsigned DEFAULT NULL,
  `issue_id` bigint(20) unsigned DEFAULT NULL,
  `statrelease_id` bigint(20) unsigned DEFAULT NULL,
  `given_date` int(11) NOT NULL,
  `return_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logbook_book_id_foreign` (`book_id`),
  KEY `logbook_user_id_foreign` (`user_id`),
  KEY `logbook_issue_id_foreign` (`issue_id`),
  KEY `logbook_statrelease_id_foreign` (`statrelease_id`),
  CONSTRAINT `logbook_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`),
  CONSTRAINT `logbook_issue_id_foreign` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`),
  CONSTRAINT `logbook_statrelease_id_foreign` FOREIGN KEY (`statrelease_id`) REFERENCES `statrelease` (`id`),
  CONSTRAINT `logbook_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.rubric
CREATE TABLE IF NOT EXISTS `rubric` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shottitle` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.seria
CREATE TABLE IF NOT EXISTS `seria` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.statrelease
CREATE TABLE IF NOT EXISTS `statrelease` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additionalname` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publishplace` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publishyear` smallint(6) DEFAULT NULL,
  `pages` smallint(5) unsigned DEFAULT NULL,
  `recieptdate` date DEFAULT NULL,
  `cost` decimal(8,2) unsigned DEFAULT NULL,
  `code` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authorsign` char(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numbersk` int(10) unsigned DEFAULT NULL,
  `rubric_id` bigint(20) unsigned DEFAULT NULL,
  `disposition` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `statrelease_rubric_id_foreign` (`rubric_id`),
  CONSTRAINT `statrelease_rubric_id_foreign` FOREIGN KEY (`rubric_id`) REFERENCES `statreleaserubric` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.statreleaserubric
CREATE TABLE IF NOT EXISTS `statreleaserubric` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

-- Дамп структуры для таблица library_new.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT 9,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `verification_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fio` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `second_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_username_unique` (`username`),
  UNIQUE KEY `user_email_unique` (`email`),
  UNIQUE KEY `user_second_email_unique` (`second_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
