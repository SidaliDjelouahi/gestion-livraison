<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

// Suppression commande
if (isset($_GET['delete'])) {
    $id_delete = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM commandes WHERE id = ?");
    $stmt->execute([$id_delete]);
    $message = "✅ Commande supprimée avec succès.";
}

// Récupérer commandes avec clients + produits
$sql = "SELECT c.id, c.date, c.telephone, 
               cl.name AS client_name, 
               p.name AS produit_name
        FROM commandes c
        LEFT JOIN clients cl ON c.id_client = cl.id
        LEFT JOIN produits p ON c.id_produit = p.id
        ORDER BY c.date DESC";
$stmt = $conn->query($sql);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2 class="mb-4">📋 Liste des commandes</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Client</th>
                <th>Téléphone</th>
                <th>Produit</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($commandes): ?>
            <?php foreach ($commandes as $cmd): ?>
                <tr>
                    <td><?= $cmd['id'] ?></td>
                    <td><?= htmlspecialchars($cmd['client_name']) ?></td>
                    <td><?= htmlspecialchars($cmd['telephone']) ?></td>
                    <td><?= htmlspecialchars($cmd['produit_name']) ?></td>
                    <td><?= htmlspecialchars($cmd['date']) ?></td>
                    <td>
                        <a href="commandes.php?delete=<?= $cmd['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Supprimer cette commande ?')">🗑 Supprimer</a>

                        <a href="commande_detail.php?id=<?= $cmd['id'] ?>" 
                           class="btn btn-sm btn-info">📄 Détail</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">Aucune commande trouvée.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "includes/footer.php"; ?>
