<?php
// equipements.php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['rank'] != 'admin') {
    header("Location: default.php");
    exit();
}

// Récupérer la liste des équipements
$sql = "SELECT * FROM equipements ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$equipements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>⚙️ Gestion des Équipements</h3>
        <a href="equipements_ajouter.php" class="btn btn-success">
            ➕ Ajouter
        </a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Désignation</th>
                <th>Code</th>
                <th>Prix d'achat</th>
                <th>Quantité</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($equipements)): ?>
                <?php foreach ($equipements as $eq): ?>
                    <tr>
                        <td><?= htmlspecialchars($eq['id']) ?></td>
                        <td><?= htmlspecialchars($eq['designation']) ?></td>
                        <td><?= htmlspecialchars($eq['code']) ?></td>
                        <td><?= number_format($eq['prix_achat'], 2, ',', ' ') ?> DA</td>
                        <td><?= htmlspecialchars($eq['quantite']) ?></td>
                        <td>
                            <a href="equipements_modifier.php?id=<?= urlencode($eq['id']) ?>" class="btn btn-warning btn-sm">
                                ✏ Modifier
                            </a>
                            <a href="equipements_supprimer.php?id=<?= urlencode($eq['id']) ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Voulez-vous vraiment supprimer cet équipement ?');">
                                🗑 Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Aucun équipement trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "includes/footer.php"; ?>
