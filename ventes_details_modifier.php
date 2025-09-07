<?php
// ventes_details_modifier.php
session_start();
require_once "includes/db.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Détail invalide !");
}

$detail_id = intval($_GET['id']);

// Récupérer les informations du détail de vente
$sql = "SELECT vd.*, p.nom AS produit_nom, p.stock AS produit_stock
        FROM ventes_details vd
        JOIN produits p ON vd.id_produit = p.id
        WHERE vd.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$detail_id]);
$detail = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$detail) {
    die("Détail introuvable !");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantite_nouvelle = intval($_POST['quantite']);
    $prix_vente_nouveau = floatval($_POST['prix_vente']);
    $id_produit = $detail['id_produit'];

    // Récupérer ancien stock + ancienne quantité
    $ancien_stock = $detail['produit_stock'];
    $quantite_ancienne = $detail['quantite'];

    // Calcul de la différence
    $difference = $quantite_nouvelle - $quantite_ancienne;

    // Mise à jour du stock (si difference positive => on prend du stock, si négative => on rend du stock)
    $sqlStock = "UPDATE produits SET stock = stock - ? WHERE id = ?";
    $stmtStock = $pdo->prepare($sqlStock);
    $stmtStock->execute([$difference, $id_produit]);

    // Mise à jour du détail de vente
    $sqlUpdate = "UPDATE ventes_details SET quantite = ?, prix_vente = ? WHERE id = ?";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute([$quantite_nouvelle, $prix_vente_nouveau, $detail_id]);

    // Recalculer le total du bon (table ventes)
    $sqlTotal = "SELECT SUM(prix_vente * quantite) AS total FROM ventes_details WHERE id_vente = ?";
    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute([$detail['id_vente']]);
    $total = $stmtTotal->fetchColumn();

    $sqlMajVente = "UPDATE ventes SET total = ? WHERE id = ?";
    $stmtMajVente = $pdo->prepare($sqlMajVente);
    $stmtMajVente->execute([$total, $detail['id_vente']]);

    header("Location: bon_vente_modifier.php?id=" . $detail['id_vente'] . "&msg=detail_modifie");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Détail Vente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h3>Modifier produit dans la vente</h3>
    <form method="post" class="mt-3">
        <div class="mb-3">
            <label class="form-label">Produit</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($detail['produit_nom']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantité</label>
            <input type="number" name="quantite" class="form-control" value="<?= $detail['quantite'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prix de vente</label>
            <input type="number" step="0.01" name="prix_vente" class="form-control" value="<?= $detail['prix_vente'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="bon_vente_modifier.php?id=<?= $detail['id_vente'] ?>" class="btn btn-secondary">Annuler</a>
    </form>

</body>
</html>
