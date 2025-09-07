<?php
include 'includes/db.php';

$q = "%" . $_GET['q'] . "%";

// Ajout de la condition AND etat = 'expose'
$stmt = $conn->prepare("
    SELECT id, code, name, prix_vente, quantite 
    FROM produits 
    WHERE (name LIKE ? OR code LIKE ?) 
      AND etat = 'expose'
");
$stmt->execute([$q, $q]);

while ($p = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<a href='#' 
            class='list-group-item list-group-item-action produit-item' 
            data-id='{$p['id']}' 
            data-prix='{$p['prix_vente']}'
            data-name='{$p['name']}'>
              {$p['name']} - {$p['prix_vente']} DA - Qté: {$p['quantite']}
          </a>";
}
?>
