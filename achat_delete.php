<?php
// achat_delete.php
session_start();
include "includes/db.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

// Vérifier si un ID est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = ["type" => "danger", "text" => "ID d'achat invalide."];
    header("Location: achats_historique.php");
    exit();
}

$id_achat = (int) $_GET['id'];

try {
    $conn->beginTransaction();

    // 1. Récupérer les détails de l'achat (produits et quantités)
    $sql = "SELECT id_produit, quantite FROM achats_details WHERE id_achat = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_achat]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($details) {
        // 2. Retirer les quantités du stock
        foreach ($details as $d) {
            $sql_update = "UPDATE produits SET quantite = quantite - ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([$d['quantite'], $d['id_produit']]);
        }

        // 3. Supprimer les détails
        $sql_delete_details = "DELETE FROM achats_details WHERE id_achat = ?";
        $stmt = $conn->prepare($sql_delete_details);
        $stmt->execute([$id_achat]);
    }

    // 4. Supprimer l'achat
    $sql_delete_achat = "DELETE FROM achats WHERE id = ?";
    $stmt = $conn->prepare($sql_delete_achat);
    $stmt->execute([$id_achat]);

    $conn->commit();

    // 5. Message succès
    $_SESSION['message'] = ["type" => "success", "text" => "Achat supprimé avec succès."];
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['message'] = ["type" => "danger", "text" => "Erreur lors de la suppression : " . $e->getMessage()];
}

// Redirection
header("Location: achats_historique.php");
exit();
