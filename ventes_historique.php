<?php
// ventes_historique.php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

// Récupérer les ventes avec jointure sur users et total depuis ventes_details
$sql = "SELECT v.id, v.num_vente, v.date, v.versement, u.username,
               COALESCE(SUM(vd.prix_vente * vd.quantite), 0) AS total
        FROM ventes v
        LEFT JOIN users u ON v.id_user = u.id
        LEFT JOIN ventes_details vd ON v.id = vd.id_vente
        GROUP BY v.id, v.num_vente, v.date, v.versement, u.username
        ORDER BY v.id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h3 class="mb-3">Historique des ventes</h3>

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
            <?php if (!empty($ventes)) : ?>
                <?php foreach ($ventes as $vente) : ?>
                    <tr>
                        <td><?= htmlspecialchars($vente['id']) ?></td>
                        <td><?= htmlspecialchars($vente['num_vente']) ?></td>
                        <td><?= htmlspecialchars($vente['date']) ?></td>
                        <td><?= htmlspecialchars($vente['username'] ?? "Inconnu") ?></td>
                        <td><?= number_format($vente['total'], 2, ',', ' ') ?> DA</td>
                        <td><?= number_format($vente['versement'], 2, ',', ' ') ?> DA</td>
                        <td>
                            <!-- Bouton Détails -->
                            <a href="vente_details.php?id=<?= urlencode($vente['id']) ?>" 
                               class="btn btn-info btn-sm">
                                <span class="d-none d-sm-inline">
                                    <i class="glyphicon glyphicon-list"></i> Détails
                                </span>
                                <span class="d-inline d-sm-none">
                                    <i class="glyphicon glyphicon-list"></i>
                                </span>
                            </a>
                        
                            <!-- Bouton Modifier -->
                            <a href="edit_vente.php?id=<?= urlencode($vente['id']) ?>" 
                               class="btn btn-warning btn-sm">
                                <span class="d-none d-sm-inline">
                                    <i class="glyphicon glyphicon-edit"></i> Modifier
                                </span>
                                <span class="d-inline d-sm-none">
                                    <i class="glyphicon glyphicon-edit"></i>
                                </span>
                            </a>
                        
                            <!-- Bouton Supprimer -->
                            <a href="vente_delete.php?id=<?= urlencode($vente['id']) ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Voulez-vous vraiment supprimer cette vente ?');">
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
                    <td colspan="7" class="text-center">Aucune vente trouvée.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "includes/footer.php"; ?>