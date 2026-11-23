CREATE DATABASE IF NOT EXISTS bike_rental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bike_rental;

CREATE TABLE IF NOT EXISTS `users`(
   `user_id` INT AUTO_INCREMENT,
   `firstname` VARCHAR(255) NOT NULL,
   `lastname` VARCHAR(255) NOT NULL,
   `login` INT NOT NULL,
   `password` VARCHAR(255) NOT NULL,
   `role` ENUM('citizen','employee','admin') NOT NULL DEFAULT 'citizen',
   PRIMARY KEY(`user_id`),
   UNIQUE(`login`)
);

CREATE TABLE IF NOT EXISTS `bikes`(
   `bike_id` INT AUTO_INCREMENT,
   `registration_number` VARCHAR(50) NOT NULL,
   `availability` BOOLEAN NOT NULL DEFAULT TRUE,
   `photo` VARCHAR(255),
   PRIMARY KEY(`bike_id`),
   UNIQUE(`registration_number`)
);

CREATE TABLE IF NOT EXISTS reservations (
   `reservation_id` INT AUTO_INCREMENT,
   `user_id` INT NOT NULL,
   `bike_id` INT NOT NULL,
   `res_num` VARCHAR(20) NOT NULL,
   `res_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   `start_date` DATETIME NOT NULL,
   `end_date` DATETIME NOT NULL,
   status ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
   PRIMARY KEY(`reservation_id`),
   UNIQUE(`res_num`),
   FOREIGN KEY(`user_id`) REFERENCES `users`(`user_id`),
   FOREIGN KEY(`bike_id`) REFERENCES `bikes`(`bike_id`)
);
