<?php
include 'includes/db.php';
$q = "%" . $_GET['q'] . "%";

$stmt = $conn->prepare("SELECT id, code, name, prix_achat, quantite FROM produits WHERE name LIKE ? OR code LIKE ?");
$stmt->execute([$q, $q]);
while ($p = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<a href='#' 
            class='list-group-item list-group-item-action produit-item' 
            data-id='{$p['id']}' 
            data-prix='{$p['prix_achat']}'
            data-name='{$p['name']}'>
              {$p['name']} - {$p['prix_achat']} DA - Qté: {$p['quantite']}
          </a>";
}
?>
