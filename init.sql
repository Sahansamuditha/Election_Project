

CREATE DATABASE IF NOT EXISTS `voting_app` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `voting_app`;

CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
);


INSERT IGNORE INTO `admins` (`username`, `password`) VALUES
('admin_colombo','admin123'),
('admin_kandy','admin123'),
('admin_galle','admin123'),
('srilanka_admin','admin123'),
('admin_jaffna','admin123');


CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(100) NOT NULL UNIQUE,
  `address` VARCHAR(255) NOT NULL,
  `age` INT(10),
  `nic` VARCHAR(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `candidates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `party_id` INT UNSIGNED NULL,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`party_id`),
  FOREIGN KEY (`party_id`) REFERENCES `parties`(`id`) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS `votes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `candidate_id` INT UNSIGNED NULL,
  `party_id` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_vote` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`candidate_id`) REFERENCES `candidates`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`party_id`) REFERENCES `parties`(`id`) ON DELETE SET NULL
);

INSERT IGNORE INTO `parties` (`id`,`name`) VALUES
(1,'Sri Lanka Podujana Peramuna'),
(2,'National People’s Power'),
(3,'Samagi Jana Balawegaya'),
(4,'Ilankai Tamil Arasu Kadchi');

INSERT IGNORE INTO `candidates` (`id`,`party_id`,`name`) VALUES
(1,1,'Mahinda Rajapaksa'),
(2,1,'Namal Rajapaksa'),
(3,1,'Sagara Kariyawasam'),
(4,1,'Dhammika Perera'),
(5,2,'Anura Kumara Dissanayake'),
(6,2,'Nihal Abeysinghe'),
(7,2,'Bhagya Sri Herath'),
(8,2,'Thilina Tharuka Samarakoon'),
(9,3,'Sajith Premadasa'),
(10,3,'Ranjith Madduma Bandara'),
(11,3,'Harsha de Silva'),
(12,3,'Tissa Attanayake'),
(13,4,'S. Shritharan'),
(14,4,'M. A. Sumanthiran'),
(15,4,'P. Selvarasa'),
(16,4,'S. J. V. Chelvanayakam');


INSERT IGNORE INTO `users` (`id`,`fullname`,`address`,`age`,`nic`) VALUES
(1,'Sahan Samuditha','No; 26, Ruhunu Ridiyagama, Ambalantota',23,'200219601162'),
(2,'Chathupama Thathsarani','"Susitha",Gurupokuna,Hungama',23,'200281301793'),
(2,'Tharaka Jayampathi','No;426,Dalukkanda road, Egodawela, Karandeniya',23,'200278400049'),
(2,'Imal Lakshitha','No; 265/1, Ehala Lelwela,Waduraba,Galle',22,'200304011822'),
(3,'Nisansala Kumari','No; 14, Lake Road, Kandy',22,'200215301234'),
(4,'Dulanjali Perera','No; 45, Galle Road, Colombo',24,'200298401456');
