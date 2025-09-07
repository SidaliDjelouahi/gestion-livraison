<?php
// vente_delete.php
session_start();
include "includes/db.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

// Vérifier si un ID est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = ["type" => "danger", "text" => "ID de vente invalide."];
    header("Location: ventes_historique.php");
    exit();
}

$id_vente = (int) $_GET['id'];

try {
    $conn->beginTransaction();

    // 1. Récupérer les détails de la vente (produits et quantités)
    $sql = "SELECT id_produit, quantite FROM ventes_details WHERE id_vente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_vente]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($details) {
        // 2. Restituer les quantités dans produits
        foreach ($details as $d) {
            $sql_update = "UPDATE produits SET quantite = quantite + ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([$d['quantite'], $d['id_produit']]);
        }

        // 3. Supprimer les détails
        $sql_delete_details = "DELETE FROM ventes_details WHERE id_vente = ?";
        $stmt = $conn->prepare($sql_delete_details);
        $stmt->execute([$id_vente]);
    }

    // 4. Supprimer la vente
    $sql_delete_vente = "DELETE FROM ventes WHERE id = ?";
    $stmt = $conn->prepare($sql_delete_vente);
    $stmt->execute([$id_vente]);

    $conn->commit();

    // 5. Message succès
    $_SESSION['message'] = ["type" => "success", "text" => "Vente supprimée avec succès."];
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['message'] = ["type" => "danger", "text" => "Erreur lors de la suppression : " . $e->getMessage()];
}

// Redirection
header("Location: ventes_historique.php");
exit();
