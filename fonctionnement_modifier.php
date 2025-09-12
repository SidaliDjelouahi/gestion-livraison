<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("❌ ID manquant.");
}

$id = intval($_GET['id']);

// 🔹 Récupération de l'enregistrement à modifier
$stmt = $conn->prepare("SELECT * FROM fonctionnement WHERE id = ?");
$stmt->execute([$id]);
$fonctionnement = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fonctionnement) {
    die("❌ Mouvement introuvable.");
}

// 🔹 Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $designation = trim($_POST['designation']);
    $versement = floatval($_POST['versement']);
    $depense = floatval($_POST['depense']);

    if (!empty($designation)) {
        $stmt = $conn->prepare("UPDATE fonctionnement SET designation=?, versement=?, depense=? WHERE id=?");
        $stmt->execute([$designation, $versement, $depense, $id]);
        header("Location: fonctionnement.php?updated=1");
        exit();
    } else {
        $error = "⚠️ La désignation ne peut pas être vide.";
    }
}
?>

<div class="container mt-4">
    <h2 class="mb-4">✏ Modifier un mouvement</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-3">
        <div class="mb-3">
            <label for="date" class="form-label">Date & heure</label>
            <input type="text" id="date" class="form-control" value="<?= htmlspecialchars($fonctionnement['date']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Désignation</label>
            <input type="text" name="designation" class="form-control" value="<?= htmlspecialchars($fonctionnement['designation']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Versement</label>
            <input type="number" step="0.01" name="versement" class="form-control" value="<?= $fonctionnement['versement'] ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Dépense</label>
            <input type="number" step="0.01" name="depense" class="form-control" value="<?= $fonctionnement['depense'] ?>">
        </div>
        <div class="d-flex justify-content-between">
            <a href="fonctionnement.php" class="btn btn-secondary">⬅ Retour</a>
            <button type="submit" class="btn btn-warning">💾 Enregistrer les modifications</button>
        </div>
    </form>
</div>

<?php include "includes/footer.php"; ?>
