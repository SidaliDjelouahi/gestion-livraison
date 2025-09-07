<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_vente = $_POST['num_vente'];
    $date = $_POST['date'];
    $id_client = $_POST['id_client'];
    $total_bon = $_POST['total_bon'];
    $versement = $_POST['versement'] ?? 0;

    try {
        $conn->beginTransaction();

        // Insertion dans ventes
        $stmt = $conn->prepare("INSERT INTO ventes (num_vente, date, id_user, versement) VALUES (?,?,?,?)");
        $stmt->execute([$num_vente, $date, $id_client, $versement]);
        $id_vente = $conn->lastInsertId();

        // Décoder produits
        $produits = json_decode($_POST['produits_json'], true);

        foreach($produits as $p){
            $stmt = $conn->prepare("INSERT INTO ventes_details (id_vente, id_produit, prix_vente, quantite) VALUES (?,?,?,?)");
            $stmt->execute([$id_vente, $p['id'], $p['prix'], $p['qte']]);

            // Mise à jour du stock
            $conn->prepare("UPDATE produits SET quantite = quantite - ? WHERE id=?")
                 ->execute([$p['qte'], $p['id']]);
        }

        $conn->commit();
        $_SESSION['success'] = "Bon de vente enregistré avec succès !";
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['success'] = "Erreur: " . $e->getMessage();
    }

    header("Location: bon_vente.php");
    exit();
}
