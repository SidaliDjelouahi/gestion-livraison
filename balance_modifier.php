<?php
// edit_balance.php
session_start();
include "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer l'enregistrement existant
$stmt = $conn->prepare("SELECT * FROM balance WHERE id = ?");
$stmt->execute([$id]);
$balance = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$balance) {
    die("Balance introuvable !");
}

// Mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inventaire = $_POST['inventaire'];
    $caisse = $_POST['caisse'];
    $credit_clients = $_POST['credit_clients'];
    $credit_fournisseurs = $_POST['credit_fournisseurs'];
    $capital = $_POST['capital'];
    $commentaire = $_POST['commentaire']; // <-- NOUVEAU

    $stmt = $conn->prepare("UPDATE balance 
        SET inventaire = ?, caisse = ?, credit_clients = ?, credit_fournisseurs = ?, capital = ?, commentaire = ?
        WHERE id = ?");
    $stmt->execute([$inventaire, $caisse, $credit_clients, $credit_fournisseurs, $capital, $commentaire, $id]);

    header("Location: balance.php?success=1");
    exit();
}

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="container mt-4">
    <h3>Modifier Balance #<?= htmlspecialchars($balance['id']) ?></h3>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Inventaire</label>
            <input type="number" step="0.01" name="inventaire" class="form-control" 
                   value="<?= htmlspecialchars($balance['inventaire']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Caisse</label>
            <input type="number" step="0.01" name="caisse" class="form-control" 
                   value="<?= htmlspecialchars($balance['caisse']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Crédit Clients</label>
            <input type="number" step="0.01" name="credit_clients" class="form-control" 
                   value="<?= htmlspecialchars($balance['credit_clients']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Crédit Fournisseurs</label>
            <input type="number" step="0.01" name="credit_fournisseurs" class="form-control" 
                   value="<?= htmlspecialchars($balance['credit_fournisseurs']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Capital</label>
            <input type="number" step="0.01" name="capital" class="form-control" 
                   value="<?= htmlspecialchars($balance['capital']) ?>" required>
        </div>

        <!-- NOUVEAU CHAMP COMMENTAIRE -->
        <div class="mb-3">
            <label class="form-label">Commentaire</label>
            <textarea name="commentaire" class="form-control" rows="3"><?= htmlspecialchars($balance['commentaire'] ?? '') ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
        <a href="balance.php" class="btn btn-secondary">⬅ Retour</a>
    </form>
</div>

<?php include "includes/footer.php"; ?>
