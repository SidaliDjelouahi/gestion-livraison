<?php
// equipements_ajouter.php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

// Vérifier si admin
if (!isset($_SESSION['user_id']) || $_SESSION['rank'] != 'admin') {
    header("Location: default.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $designation = trim($_POST['designation'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $prix_achat = $_POST['prix_achat'] ?? 0;
    $quantite = $_POST['quantite'] ?? 0;

    if (!empty($designation) && !empty($code)) {
        // Vérifier si le code existe déjà
        $checkSql = "SELECT COUNT(*) FROM equipements WHERE code = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$code]);
        $exists = $checkStmt->fetchColumn();

        if ($exists > 0) {
            $message = "⚠️ Ce code existe déjà. Veuillez en choisir un autre.";
        } else {
            // Insérer un nouvel équipement
            $sql = "INSERT INTO equipements (designation, code, prix_achat, quantite) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$designation, $code, $prix_achat, $quantite]);
            $message = "✅ Équipement ajouté avec succès.";
        }
    } else {
        $message = "⚠️ Veuillez remplir tous les champs.";
    }
}
?>

<div class="container mt-4">
    <h3>➕ Ajouter un Équipement</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-3">
        <div class="mb-3">
            <label class="form-label">Désignation</label>
            <input type="text" name="designation" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Code</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prix d'achat</label>
            <input type="number" step="0.01" name="prix_achat" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantité</label>
            <input type="number" name="quantite" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="equipements.php" class="btn btn-secondary">⬅ Retour</a>
    </form>
</div>

<?php include "includes/footer.php"; ?>
