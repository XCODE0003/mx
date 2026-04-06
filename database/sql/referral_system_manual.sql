-- Ручное создание таблиц реферальной системы (если миграции не запущены)

-- Таблица реферальных ссылок
CREATE TABLE IF NOT EXISTS `referral_links` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Уникальный код реферальной ссылки',
    `name` VARCHAR(255) NOT NULL COMMENT 'Название партнера/источника',
    `description` TEXT NULL COMMENT 'Описание партнера',
    `clicks` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Количество кликов',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Активна ли ссылка',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    INDEX `referral_links_code_index` (`code`),
    INDEX `referral_links_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица посещений по реферальным ссылкам
CREATE TABLE IF NOT EXISTS `referral_visits` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `referral_link_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NULL COMMENT 'Пользователь, если зарегистрирован',
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `visited_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `referral_visits_referral_link_id_visited_at_index` (`referral_link_id`, `visited_at`),
    INDEX `referral_visits_user_id_index` (`user_id`),
    CONSTRAINT `referral_visits_referral_link_id_foreign` FOREIGN KEY (`referral_link_id`) REFERENCES `referral_links` (`id`) ON DELETE CASCADE,
    CONSTRAINT `referral_visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Добавление поля referral_link_id в таблицу users
ALTER TABLE `users` 
ADD COLUMN `referral_link_id` BIGINT UNSIGNED NULL AFTER `id` COMMENT 'Реферальная ссылка, по которой зарегистрировался',
ADD INDEX `users_referral_link_id_index` (`referral_link_id`),
ADD CONSTRAINT `users_referral_link_id_foreign` FOREIGN KEY (`referral_link_id`) REFERENCES `referral_links` (`id`) ON DELETE SET NULL;

-- Добавление поля referral_link_id в таблицу orders
ALTER TABLE `orders` 
ADD COLUMN `referral_link_id` BIGINT UNSIGNED NULL AFTER `user_id` COMMENT 'Реферальная ссылка, по которой совершена покупка',
ADD INDEX `orders_referral_link_id_index` (`referral_link_id`),
ADD CONSTRAINT `orders_referral_link_id_foreign` FOREIGN KEY (`referral_link_id`) REFERENCES `referral_links` (`id`) ON DELETE SET NULL;

-- Пример создания реферальной ссылки
-- INSERT INTO referral_links (code, name, description, is_active, created_at, updated_at) 
-- VALUES ('partner123', 'Партнер Иванов', 'Тестовая реферальная ссылка', 1, NOW(), NOW());

-- Проверка созданных таблиц
SELECT 'referral_links table' as info, COUNT(*) as count FROM referral_links
UNION ALL
SELECT 'referral_visits table', COUNT(*) FROM referral_visits;
