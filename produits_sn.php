<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

include('includes/db.php'); 
include('includes/header.php');
include('includes/sidebar.php');

$id_produit = isset($_GET['id_produit']) ? intval($_GET['id_produit']) : 0;

// Récupérer les numéros de série avec le nom du produit
$stmt = $conn->prepare("
    SELECT ps.id, ps.sn, p.name 
    FROM produits_sn ps
    JOIN produits p ON ps.id_produit = p.id
    WHERE ps.id_produit = ?
    ORDER BY ps.id DESC
");
$stmt->execute([$id_produit]);
$sn_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le nom du produit pour afficher en titre
$product_stmt = $conn->prepare("SELECT name FROM produits WHERE id = ?");
$product_stmt->execute([$id_produit]);
$product = $product_stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container my-4">
    <h2 class="mb-4">Numéros de série pour : <?= htmlspecialchars($product['name'] ?? 'Produit inconnu') ?></h2>

    <!-- Barre recherche -->
    <input type="text" id="searchSN" class="form-control mb-3" placeholder="🔎 Rechercher un SN...">

    <!-- Bouton ajouter -->
    <a href="produits_sn_ajouter.php?id_produit=<?= $id_produit ?>" class="btn btn-success mb-3">+ Ajouter un SN</a>

    <!-- Table SN -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="snTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Produit</th>
                    <th>Numéro de série</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sn_list as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['sn']) ?></td>
                        <td>
                            <a href="produits_sn_modifier.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <a href="produits_sn_supprimer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Voulez-vous vraiment supprimer ce SN ?');">
                               Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// 🔎 Recherche instantanée SN
document.getElementById("searchSN").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#snTable tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
    });
});
</script>
