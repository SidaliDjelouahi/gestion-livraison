<?php
session_start();
include 'includes/db.php';

$data = json_decode($_POST['data'], true);

try {
    $conn->beginTransaction();

    // Enregistrer la vente
    $stmt = $conn->prepare("INSERT INTO ventes (num_vente, date, id_client, versement) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['num_vente'], $data['date'], $data['id_client'], $data['versement'] ?: null]);

    // Enregistrer les détails
    foreach($data['produits'] as $p){
        $stmt = $conn->prepare("INSERT INTO ventes_details (id_vente, id_produit, prix_vente, quantite) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['num_vente'], $p['id'], $p['prix'], $p['qte']]);
    }

    $conn->commit();
    echo "✅ Vente enregistrée avec succès";
} catch(Exception $e){
    $conn->rollBack();
    echo "❌ Erreur: ".$e->getMessage();
}
