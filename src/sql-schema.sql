-- Create database
CREATE DATABASE IF NOT EXISTS skolidrottedu CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE skolidrottedu;

-- Create courses table
CREATE TABLE `courses` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`hash` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`title` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`byline` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`description` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`image` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`background` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`content` MEDIUMTEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	`is_copy` TINYINT(1) NULL DEFAULT '0',
	`uid` BIGINT(20) NULL DEFAULT NULL,
	`is_active` TINYINT(1) NULL DEFAULT '1',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;

-- Create invites table
CREATE TABLE `invites` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`code` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	`is_active` TINYINT(1) NULL DEFAULT '1',
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `invites_code` (`code`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;

-- Create password_reset_tokens table
CREATE TABLE `password_reset_tokens` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`token` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `password_reset_tokens_email` (`email`) USING BTREE,
	INDEX `password_reset_tokens_token` (`token`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;

-- Create users table
CREATE TABLE `users` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`first_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`last_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`phone_number` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`email` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`password` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`signed_in_at` TIMESTAMP NULL DEFAULT NULL,
	`signed_out_at` TIMESTAMP NULL DEFAULT NULL,
	`last_ip_address` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	`admin` TINYINT(1) NULL DEFAULT '0',
	`is_active` TINYINT(1) NULL DEFAULT '0',
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `users_email_unique` (`email`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;

-- Create user_details table
CREATE TABLE `user_details` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
	`image` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`birthday_year` INT(10) NULL DEFAULT NULL,
	`birthday_month` INT(10) NULL DEFAULT NULL,
	`birthday_day` INT(10) NULL DEFAULT NULL,
	`gender` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`about` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`statement_1` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`statement_1_answer` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`statement_2` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`statement_2_answer` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`statement_3` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`statement_3_answer` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `user_details_user_id` (`user_id`) USING BTREE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;