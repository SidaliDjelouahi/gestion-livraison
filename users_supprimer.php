<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

include('includes/db.php');   // connexion PDO

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id = (int) $_GET['id'];

try {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: users.php?success=deleted");
    exit();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
