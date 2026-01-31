<?php

$host = "DB_HOST";
$user = "DB_USER";
$pass = "DB_PASSWORD";
$dbname = "DB_NAME";

try {
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed");
}
