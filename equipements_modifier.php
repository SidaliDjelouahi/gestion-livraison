<?php
// equipements_modifier.php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

// Vérifier si admin
if (!isset($_SESSION['user_id']) || $_SESSION['rank'] != 'admin') {
    header("Location: default.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID invalide.");
}

$id = (int)$_GET['id'];

// Charger équipement
$stmt = $conn->prepare("SELECT * FROM equipements WHERE id=?");
$stmt->execute([$id]);
$equipement = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipement) {
    die("Équipement introuvable.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $designation = $_POST['designation'] ?? '';
    $code = $_POST['code'] ?? '';
    $prix_achat = $_POST['prix_achat'] ?? 0;
    $quantite = $_POST['quantite'] ?? 0;

    if (!empty($designation) && !empty($code)) {
        $sql = "UPDATE equipements SET designation=?, code=?, prix_achat=?, quantite=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$designation, $code, $prix_achat, $quantite, $id]);
        $message = "✅ Équipement modifié avec succès.";
        // Recharger
        $stmt = $conn->prepare("SELECT * FROM equipements WHERE id=?");
        $stmt->execute([$id]);
        $equipement = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $message = "⚠️ Veuillez remplir tous les champs.";
    }
}
?>

<div class="container mt-4">
    <h3>✏ Modifier Équipement</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-3">
        <div class="mb-3">
            <label class="form-label">Désignation</label>
            <input type="text" name="designation" value="<?= htmlspecialchars($equipement['designation']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Code</label>
            <input type="text" name="code" value="<?= htmlspecialchars($equipement['code']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prix d'achat</label>
            <input type="number" step="0.01" name="prix_achat" value="<?= htmlspecialchars($equipement['prix_achat']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantité</label>
            <input type="number" name="quantite" value="<?= htmlspecialchars($equipement['quantite']) ?>" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-warning">Mettre à jour</button>
        <a href="equipements.php" class="btn btn-secondary">⬅ Retour</a>
    </form>
</div>

<?php include "includes/footer.php"; ?>
