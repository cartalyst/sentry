# Drop tables if they exists
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_groups`;
DROP TABLE IF EXISTS `groups`;
DROP TABLE IF EXISTS `throttle`;
DROP TABLE IF EXISTS `users`;



# Dump of table groups
# ------------------------------------------------------------

CREATE TABLE `groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `permissions` text,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `groups_name_unique` (`name`)
) ENGINE=InnoDB;



# Dump of table users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_password_hash` varchar(255) DEFAULT NULL,
  `activation_hash` varchar(255) DEFAULT NULL,
  `persist_hash` varchar(255) DEFAULT NULL,
  `activated` tinyint(4) NOT NULL DEFAULT '0',
  `permissions` text,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB;



# Dump of table throttle
# ------------------------------------------------------------

CREATE TABLE `throttle` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `attempts` int(11) NOT NULL,
  `suspended` tinyint(4) NOT NULL,
  `banned` tinyint(4) NOT NULL,
  `last_attempt_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `suspended_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `throttle_user_id_foreign` (`user_id`),
  CONSTRAINT `throttle_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;



# Dump of table users_groups
# ------------------------------------------------------------

CREATE TABLE `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_groups_user_id_foreign` (`user_id`),
  KEY `users_groups_group_id_foreign` (`group_id`),
  CONSTRAINT `users_groups_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `users_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;
