<?php
session_start();
include('includes/db.php'); 

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour supprimer un SN.");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die("SN invalide.");

// Récupérer l'id_produit pour redirection
$stmt = $conn->prepare("SELECT id_produit FROM produits_sn WHERE id = ?");
$stmt->execute([$id]);
$sn = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$sn) die("SN introuvable.");

$id_produit = $sn['id_produit'];

try {
    $delete = $conn->prepare("DELETE FROM produits_sn WHERE id = ?");
    $delete->execute([$id_produit]);
    header("Location: produits_sn.php?id_produit=$id_produit&msg=" . urlencode("SN supprimé avec succès"));
    exit;
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
