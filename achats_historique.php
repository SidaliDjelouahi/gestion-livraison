<?php
// achats_historique.php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

// Récupérer les achats avec jointure sur users et total depuis achats_details
$sql = "SELECT a.id, a.num_achat, a.date, a.versement, u.username,
               COALESCE(SUM(ad.prix_achat * ad.quantite), 0) AS total
        FROM achats a
        LEFT JOIN users u ON a.id_user = u.id
        LEFT JOIN achats_details ad ON a.id = ad.id_achat
        GROUP BY a.id, a.num_achat, a.date, a.versement, u.username
        ORDER BY a.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$achats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h3 class="mb-3">Historique des achats</h3>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Num</th>
                <th>Date</th>
                <th>User</th>
                <th>Total</th>
                <th>Versement</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($achats)) : ?>
                <?php foreach ($achats as $achat) : ?>
                    <tr>
                        <td><?= htmlspecialchars($achat['id']) ?></td>
                        <td><?= htmlspecialchars($achat['num_achat']) ?></td>
                        <td><?= htmlspecialchars($achat['date']) ?></td>
                        <td><?= htmlspecialchars($achat['username'] ?? "Inconnu") ?></td>
                        <td><?= number_format($achat['total'], 2, ',', ' ') ?> DA</td>
                        <td><?= number_format($achat['versement'], 2, ',', ' ') ?> DA</td>
                        <td>
                            <!-- Bouton Détails -->
                            <a href="achat_details.php?id=<?= urlencode($achat['id']) ?>" 
                               class="btn btn-info btn-sm">
                                <span class="d-none d-sm-inline">
                                    <i class="glyphicon glyphicon-list"></i> Détails
                                </span>
                                <span class="d-inline d-sm-none">
                                    <i class="glyphicon glyphicon-list"></i>
                                </span>
                            </a>
                        
                            <!-- Bouton Modifier -->
                            <a href="edit_achat.php?id=<?= urlencode($achat['id']) ?>" 
                               class="btn btn-warning btn-sm">
                                <span class="d-none d-sm-inline">
                                    <i class="glyphicon glyphicon-edit"></i> Modifier
                                </span>
                                <span class="d-inline d-sm-none">
                                    <i class="glyphicon glyphicon-edit"></i>
                                </span>
                            </a>
                        
                            <!-- Bouton Supprimer -->
                            <a href="achat_delete.php?id=<?= urlencode($achat['id']) ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Voulez-vous vraiment supprimer cet achat ?');">
                                <span class="d-none d-sm-inline">
                                    <i class="glyphicon glyphicon-trash"></i> Supprimer
                                </span>
                                <span class="d-inline d-sm-none">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7" class="text-center">Aucun achat trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "includes/footer.php"; ?>
