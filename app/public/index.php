<?php

require_once __DIR__ . '/../src/bootstrap.php';

$stmt = $pdo->query('SHOW DATABASES');

if (!empty($stmt->fetchAll())) {
    echo 'Database works !<br />';
}
