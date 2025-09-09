<?php
// equipements_supprimer.php
session_start();
include "includes/db.php";

// Vérifier si admin
if (!isset($_SESSION['user_id']) || $_SESSION['rank'] != 'admin') {
    header("Location: default.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID invalide.");
}

$id = (int)$_GET['id'];

// Supprimer équipement
$stmt = $conn->prepare("DELETE FROM equipements WHERE id=?");
$stmt->execute([$id]);

header("Location: equipements.php?msg=supprime");
exit();
