<?php

require_once __DIR__ . '/../src/bootstrap.php';

$pdo->query('
    CREATE TABLE IF NOT EXISTS `products` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `status` TINYINT NOT NULL DEFAULT 0,
        `description` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;
');

$pdo->query("INSERT INTO `products` SET `title` = 'Product #1', status=1, description='Description #1'");

echo 'Table created' . PHP_EOL;

