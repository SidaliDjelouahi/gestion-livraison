<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

// --- Calcul du solde ---
$sql_solde = "SELECT 
                IFNULL(SUM(versement),0) AS total_versements,
                IFNULL(SUM(depense),0) AS total_depenses
              FROM fonctionnement";
$stmt_solde = $conn->query($sql_solde);
$res_solde = $stmt_solde->fetch(PDO::FETCH_ASSOC);
$solde = $res_solde['total_versements'] - $res_solde['total_depenses'];

// --- Suppression d'un enregistrement ---
if (isset($_GET['delete'])) {
    $id_delete = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM fonctionnement WHERE id = ?");
    $stmt->execute([$id_delete]);
    $message = "✅ Enregistrement supprimé avec succès.";
}

// --- Récupération de toutes les lignes ---
$sql = "SELECT * FROM fonctionnement ORDER BY date DESC";
$stmt = $conn->query($sql);
$fonctionnements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2 class="mb-4">📊 Tableau des Fonctionnements</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    
    <!-- Affichage du solde -->
    <div class="alert alert-info">
        <strong>💰 Solde actuel :</strong> <?= number_format($solde, 2, ',', ' ') ?> DA
    </div>

    <!-- Bouton Ajouter -->
    <div class="mb-3">
        <a href="fonctionnement_ajouter.php" class="btn btn-primary">➕ Ajouter</a>
    </div>

    <!-- Moteur de recherche -->
    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="🔎 Rechercher dans le tableau...">
    </div>

    <table class="table table-bordered table-striped" id="fonctionnementTable">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Désignation</th>
                <th>Versement</th>
                <th>Dépense</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($fonctionnements): ?>
            <?php foreach ($fonctionnements as $f): ?>
                <tr>
                    <td><?= $f['id'] ?></td>
                    <td><?= htmlspecialchars($f['date']) ?></td>
                    <td><?= htmlspecialchars($f['designation']) ?></td>
                    <td><?= number_format($f['versement'], 2, ',', ' ') ?> DA</td>
                    <td><?= number_format($f['depense'], 2, ',', ' ') ?> DA</td>
                    <td>
                        <a href="fonctionnement_modifier.php?id=<?= $f['id'] ?>" 
                           class="btn btn-sm btn-warning">✏ Modifier</a>

                        <a href="fonctionnement.php?delete=<?= $f['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Supprimer cet enregistrement ?')">🗑 Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">Aucun enregistrement trouvé.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Script de recherche -->
<script>
document.getElementById('searchInput').addEventListener('keyup', function () {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#fonctionnementTable tbody tr");

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>

<?php include "includes/footer.php"; ?>
