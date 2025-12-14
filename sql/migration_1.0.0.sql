-- Migration for Reservation plugin tables
CREATE TABLE `glpi_plugin_reservation_categories` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `glpi_plugin_reservation_categories_items` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `categories_id` int UNSIGNED NOT NULL,
    `reservationitems_id` int UNSIGNED NOT NULL,
    `priority` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `reservationitems_id` (`reservationitems_id`),
    KEY `categories_id` (`categories_id`),
    UNIQUE (`categories_id`, `reservationitems_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `glpi_plugin_reservation_reservations` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `reservations_id` int UNSIGNED NOT NULL,
    `baselinedate` timestamp NOT NULL,
    `effectivedate`  timestamp NULL,
    `mailingdate` timestamp NULL,
    `checkindate` timestamp NULL,
    PRIMARY KEY (`id`),
    KEY `reservations_id` (`reservations_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
