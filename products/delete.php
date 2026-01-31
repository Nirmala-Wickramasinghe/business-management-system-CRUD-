<?php
require_once "../config/db.local.php";

$id = $_GET["id"];

$stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
$stmt->bindParam(":id", $id);
$stmt->execute();

header("Location: index.php");
exit;
