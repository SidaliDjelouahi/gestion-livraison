<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

// Vérifier si id client est bien passé
if (!isset($_GET['id'])) {
    die("ID client manquant.");
}
$id_client = intval($_GET['id']);

// --- Infos client ---
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND rank = 'user'");
$stmt->execute([$id_client]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    die("Client introuvable.");
}

// --- Calcul total ventes & versements ---
$stmt_total = $conn->prepare("
    SELECT 
        SUM(vd.quantite * vd.prix_vente) AS total_ventes,
        SUM(v.versement) AS total_versements
    FROM ventes v
    INNER JOIN ventes_details vd ON v.id = vd.id_vente
    WHERE v.id_user = ?
");
$stmt_total->execute([$id_client]);
$result = $stmt_total->fetch(PDO::FETCH_ASSOC);

$total_ventes = $result['total_ventes'] ?? 0;
$total_versements = $result['total_versements'] ?? 0;
$total_credit = $total_ventes - $total_versements;

// --- Historique des ventes avec total calculé ---
$stmt_hist = $conn->prepare("
    SELECT 
        v.id, v.num_vente, v.date, v.versement,
        SUM(vd.quantite * vd.prix_vente) AS total
    FROM ventes v
    INNER JOIN ventes_details vd ON v.id = vd.id_vente
    WHERE v.id_user = ?
    GROUP BY v.id, v.num_vente, v.date, v.versement
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
        <strong>Total ventes :</strong> <?= number_format($total_ventes, 2) ?> DA <br>
        <strong>Total versements :</strong> <?= number_format($total_versements, 2) ?> DA <br>
        <strong>Total crédit :</strong> <?= number_format($total_credit, 2) ?> DA
    </div>

    <h3>Historique des ventes</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Num Vente</th>
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
                        <td><?= htmlspecialchars($vente['num_vente']) ?></td>
                        <td><?= htmlspecialchars($vente['date']) ?></td>
                        <td><?= number_format($vente['total'], 2) ?> DA</td>
                        <td><?= number_format($vente['versement'], 2) ?> DA</td>
                        <td>
                            <a href="vente_details.php?id=<?= $vente['id'] ?>" class="btn btn-primary btn-sm">
                                Détails
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">Aucune vente trouvée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>

<?php include "includes/footer.php"; ?>