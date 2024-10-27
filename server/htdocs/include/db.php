<?php

define('DB_HOST', 'db:3306');
define('DB_USER', 'myapp');
define('DB_PASS', 'myapp');
define('DB_NAME', 'mydatabase');

try {
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($connection->connect_errno) {
        throw new Exception("Failed to connect to MySQL: " . $connection->connect_error);
    }
} catch (Exception $e) {
    die("An error has occurred: " . $e->getMessage());
}