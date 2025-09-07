<?php
session_start();
include('includes/db.php'); // connexion PDO

if (!isset($_GET['id'])) {
    header("Location: produits.php?msg=Aucun produit sélectionné");
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = $conn->prepare("DELETE FROM produits WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: produits.php?msg=Produit supprimé avec succès");
        exit;
    } else {
        header("Location: produits.php?msg=Erreur lors de la suppression");
        exit;
    }
} catch (PDOException $e) {
    header("Location: produits.php?msg=Erreur : " . urlencode($e->getMessage()));
    exit;
}
