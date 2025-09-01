<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

// Vérifier si id client est bien passé
if (!isset($_GET['id'])) {
    die("ID fournisseur manquant.");
}
$id_client = intval($_GET['id']);

// --- Infos client ---
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND rank = 'provider'");
$stmt->execute([$id_client]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    die("Fournisseur introuvable.");
}

// --- Calcul total ventes & versements ---
$stmt_total = $conn->prepare("
    SELECT 
        SUM(vd.quantite * vd.prix_achat) AS total_ventes,
        SUM(v.versement) AS total_versements
    FROM achats v
    INNER JOIN achats_details vd ON v.id = vd.id_achat
    WHERE v.id_user = ?
");
$stmt_total->execute([$id_client]);
$result = $stmt_total->fetch(PDO::FETCH_ASSOC);

$total_ventes = $result['total_ventes'] ?? 0;
$total_versements = $result['total_versements'] ?? 0;
$total_credit = $total_ventes - $total_versements;

// --- Historique des achats avec total calculé ---
$stmt_hist = $conn->prepare("
    SELECT 
        v.id, v.num_achat, v.date, v.versement,
        SUM(vd.quantite * vd.prix_achat) AS total
    FROM achats v
    INNER JOIN achats_details vd ON v.id = vd.id_achat
    WHERE v.id_user = ?
    GROUP BY v.id, v.num_achat, v.date, v.versement
    ORDER BY v.date DESC
");
$stmt_hist->execute([$id_client]);
$ventes = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Situation Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h2>Situation du client : <?= htmlspecialchars($client['username']) ?></h2>
    <p>Téléphone : <?= htmlspecialchars($client['telephone']) ?></p>

    <div class="alert alert-info">
        <strong>Total achats :</strong> <?= number_format($total_ventes, 2) ?> DA <br>
        <strong>Total versements :</strong> <?= number_format($total_versements, 2) ?> DA <br>
        <strong>Total crédit :</strong> <?= number_format($total_credit, 2) ?> DA
    </div>

    <h3>Historique des achats</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Num Achat</th>
                <th>Date</th>
                <th>Total</th>
                <th>Versement</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($ventes): ?>
                <?php foreach ($ventes as $vente): ?>
                    <tr>
                        <td><?= $vente['id'] ?></td>
                        <td><?= htmlspecialchars($vente['num_achat']) ?></td>
                        <td><?= htmlspecialchars($vente['date']) ?></td>
                        <td><?= number_format($vente['total'], 2) ?> DA</td>
                        <td><?= number_format($vente['versement'], 2) ?> DA</td>
                        <td>
                            <a href="fournisseur_detail_vente.php?id=<?= $vente['id'] ?>" class="btn btn-primary btn-sm">
                                Détails
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">Aucun achat trouvée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>

<?php include "includes/footer.php"; ?>