<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

include('includes/db.php');     // connexion PDO
include('includes/header.php');
include('includes/sidebar.php');

// Récupérer les produits
$stmt = $conn->prepare("SELECT * FROM produits ORDER BY id DESC");
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Préparer les totaux des quantités par état
$totaux = [];
foreach ($produits as $p) {
    $totaux[$p['etat']] = ($totaux[$p['etat']] ?? 0) + $p['quantite'];
}

// Récupérer le rôle utilisateur
$userRank = $_SESSION['rank'] ?? 'user';
?>

<div class="container my-4">
    <h2 class="mb-4">Liste des produits 📦</h2>

    <!-- Barre recherche -->
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="🔎 Rechercher un produit...">

    <!-- Bouton dropdown états -->
    <div class="dropdown mb-3">
        <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            📊 Quantités par état
        </button>
        <ul class="dropdown-menu">
            <?php foreach ($totaux as $etat => $somme): ?>
                <li>
                    <a class="dropdown-item" href="#" onclick="showTotal('<?= $etat ?>', <?= $somme ?>)">
                        <?= ucfirst($etat) ?> : <?= $somme ?> unités
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Zone d'affichage du total -->
    <div id="etatResult" class="alert alert-secondary d-none"></div>

    <!-- Bouton ajouter -->
    <a href="produits_ajouter.php" class="btn btn-success mb-3">+ Ajouter un produit</a>

    <!-- Table pour Desktop -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-bordered table-hover" id="produitsTable">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Quantité</th>
                    <?php if ($userRank === 'admin'): ?>
                        <th>Prix Achat</th>
                    <?php endif; ?>
                    <th>Prix Vente</th>
                    <th>État</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['code']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= $row['quantite'] ?></td>
                    <?php if ($userRank === 'admin'): ?>
                        <td><?= number_format($row['prix_achat'], 2, ',', ' ') ?> DA</td>
                    <?php endif; ?>
                    <td><?= number_format($row['prix_vente'], 2, ',', ' ') ?> DA</td>
                    <td>
                        <?php
                            $etatBadge = [
                                'expose'   => 'success',
                                'vendue'   => 'danger',
                                'garantie' => 'primary',
                                'test'     => 'warning'
                            ];
                        ?>
                        <span class="badge bg-<?= $etatBadge[$row['etat']] ?? 'secondary' ?>">
                            <?= htmlspecialchars($row['etat']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="produits_modifier.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                        <a href="produits_sn.php?id_produit=<?= $row['id'] ?>" class="btn btn-sm btn-info">SN</a>
                        <a href="produits_supprimer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                           onclick="return confirm('Voulez-vous vraiment supprimer ce produit ?');">
                           Supprimer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Cards pour Mobile -->
    <div class="d-block d-md-none" id="produitsCards">
        <?php foreach ($produits as $row): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['name']) ?> (<?= htmlspecialchars($row['code']) ?>)</h5>
                    <p class="card-text">
                        <strong>Description :</strong> <?= htmlspecialchars($row['description']) ?><br>
                        <strong>Quantité :</strong> <?= $row['quantite'] ?><br>
                        <?php if ($userRank === 'admin'): ?>
                            <strong>Prix Achat :</strong> <?= number_format($row['prix_achat'], 2, ',', ' ') ?> DA<br>
                        <?php endif; ?>
                        <strong>Prix Vente :</strong> <?= number_format($row['prix_vente'], 2, ',', ' ') ?> DA<br>
                        <strong>État :</strong> 
                        <span class="badge bg-<?= $etatBadge[$row['etat']] ?? 'secondary' ?>">
                            <?= htmlspecialchars($row['etat']) ?>
                        </span>
                    </p>
                    <a href="produits_modifier.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                    <a href="produits_sn.php?id_produit=<?= $row['id'] ?>" class="btn btn-sm btn-info">SN</a>
                    <a href="produits_supprimer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Voulez-vous vraiment supprimer ce produit ?');">
                       Supprimer
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
<script>
// 🔎 Recherche instantanée
document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#produitsTable tbody tr");
    let cards = document.querySelectorAll("#produitsCards .card");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
    });

    cards.forEach(card => {
        card.style.display = card.innerText.toLowerCase().includes(filter) ? "" : "none";
    });
});

// 📊 Afficher somme des quantités par état
function showTotal(etat, somme) {
    let box = document.getElementById("etatResult");
    box.classList.remove("d-none");
    box.innerHTML = `<strong>${etat.toUpperCase()}</strong> : ${somme} unités`;
}
</script>

</body>
</html>
