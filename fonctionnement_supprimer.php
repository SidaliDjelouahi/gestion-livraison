<?php
session_start();
include "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("❌ ID manquant.");
}

$id = intval($_GET['id']);

// 🔹 Suppression de l'enregistrement
$stmt = $conn->prepare("DELETE FROM fonctionnement WHERE id = ?");
$stmt->execute([$id]);

header("Location: fonctionnement.php?deleted=1");
exit();
