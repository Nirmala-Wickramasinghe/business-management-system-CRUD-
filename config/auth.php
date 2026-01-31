<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

function requireAdmin() {
    if (($_SESSION["user_role"] ?? "user") !== "admin") {
        die("Access denied. Admins only.");
    }
}
