<?php
// vente_details.php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

// Initialiser variable erreur
$erreur = null;

// Vérifier l'ID de la vente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $erreur = "ID de vente invalide.";
} else {
    $id_vente = (int) $_GET['id'];

    // Récupérer les infos de la vente
    $sql = "SELECT v.id, v.num_vente, v.date, v.versement, u.username
            FROM ventes v
            LEFT JOIN users u ON v.id_user = u.id
            WHERE v.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_vente]);
    $vente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vente) {
        $erreur = "Vente introuvable.";
    } else {
        // ⚡ Récupérer les détails de la vente :
        // - produit.nom depuis produits
        // - prix_vente depuis ventes_details
        $sql_details = "SELECT vd.id, vd.quantite, vd.prix_vente, p.name AS produit_nom
                        FROM ventes_details vd
                        LEFT JOIN produits p ON vd.id_produit = p.id
                        WHERE vd.id_vente = ?";
        $stmt = $conn->prepare($sql_details);
        $stmt->execute([$id_vente]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<div class="container mt-4">
    <?php if ($erreur): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($erreur) ?>
        </div>
        <a href="ventes_historique.php" class="btn btn-secondary">⬅ Retour</a>
    <?php else: ?>
        <h3>Détails de la vente n° <?= htmlspecialchars($vente['num_vente']) ?></h3>
        <p><strong>Date :</strong> <?= htmlspecialchars($vente['date']) ?></p>
        <p><strong>Utilisateur :</strong> <?= htmlspecialchars($vente['username'] ?? "Inconnu") ?></p>
        <p><strong>Versement :</strong> <?= number_format($vente['versement'], 2, ',', ' ') ?> DA</p>

        <h4 class="mt-4">Produits vendus</h4>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Produit</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($details)) : ?>
                    <?php 
                    $total_general = 0;
                    foreach ($details as $d) :
                        $total = $d['prix_vente'] * $d['quantite'];
                        $total_general += $total;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($d['id']) ?></td>
                            <td><?= htmlspecialchars($d['produit_nom'] ?? "Produit supprimé") ?></td>
                            <td><?= number_format($d['prix_vente'], 2, ',', ' ') ?> DA</td>
                            <td><?= htmlspecialchars($d['quantite']) ?></td>
                            <td><?= number_format($total, 2, ',', ' ') ?> DA</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-secondary">
                        <td colspan="4" class="text-end"><strong>Total Général</strong></td>
                        <td><strong><?= number_format($total_general, 2, ',', ' ') ?> DA</strong></td>
                    </tr>
                <?php else : ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucun produit trouvé pour cette vente.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="ventes_historique.php" class="btn btn-secondary">⬅ Retour</a>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
