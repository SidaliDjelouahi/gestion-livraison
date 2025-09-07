<?php
// achat_details.php
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

// Vérifier l'ID de l'achat
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $erreur = "ID d'achat invalide.";
} else {
    $id_achat = (int) $_GET['id'];

    // Récupérer les infos de l'achat
    $sql = "SELECT a.id, a.num_achat, a.date, a.versement, u.username
            FROM achats a
            LEFT JOIN users u ON a.id_user = u.id
            WHERE a.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_achat]);
    $achat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$achat) {
        $erreur = "Achat introuvable.";
    } else {
        // Récupérer les détails de l'achat :
        // - produit.nom depuis produits
        // - prix_achat et quantite depuis achats_details
        $sql_details = "SELECT ad.id, ad.quantite, ad.prix_achat, p.name AS produit_nom
                        FROM achats_details ad
                        LEFT JOIN produits p ON ad.id_produit = p.id
                        WHERE ad.id_achat = ?";
        $stmt = $conn->prepare($sql_details);
        $stmt->execute([$id_achat]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<div class="container mt-4">
    <?php if ($erreur): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($erreur) ?>
        </div>
        <a href="achats_historique.php" class="btn btn-secondary">⬅ Retour</a>
    <?php else: ?>
        <h3>Détails de l'achat n° <?= htmlspecialchars($achat['num_achat']) ?></h3>
        <p><strong>Date :</strong> <?= htmlspecialchars($achat['date']) ?></p>
        <p><strong>Utilisateur :</strong> <?= htmlspecialchars($achat['username'] ?? "Inconnu") ?></p>
        <p><strong>Versement :</strong> <?= number_format($achat['versement'], 2, ',', ' ') ?> DA</p>

        <h4 class="mt-4">Produits achetés</h4>
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
                        $total = $d['prix_achat'] * $d['quantite'];
                        $total_general += $total;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($d['id']) ?></td>
                            <td><?= htmlspecialchars($d['produit_nom'] ?? "Produit supprimé") ?></td>
                            <td><?= number_format($d['prix_achat'], 2, ',', ' ') ?> DA</td>
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
                        <td colspan="5" class="text-center">Aucun produit trouvé pour cet achat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="achats_historique.php" class="btn btn-secondary">⬅ Retour</a>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
