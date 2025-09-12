<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

// --- Ajout d'un enregistrement ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $designation = trim($_POST['designation']);
    $versement = floatval($_POST['versement']);
    $depense = floatval($_POST['depense']);
    $date = date("Y-m-d H:i:s");

    if (!empty($designation)) {
        $stmt = $conn->prepare("INSERT INTO fonctionnement (date, designation, versement, depense) VALUES (?, ?, ?, ?)");
        $stmt->execute([$date, $designation, $versement, $depense]);
        header("Location: fonctionnement_ajouter.php?success=1");
        exit();
    } else {
        $error = "⚠️ Veuillez entrer une désignation.";
    }
}

// --- Récupération des enregistrements ---
$sql = "SELECT * FROM fonctionnement ORDER BY date DESC";
$stmt = $conn->query($sql);
$fonctionnements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2 class="mb-4">➕ Ajouter un mouvement</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">✅ Mouvement ajouté avec succès.</div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- Formulaire -->
    <form method="POST" class="card p-3 mb-4">
        <div class="mb-3">
            <label for="date" class="form-label">Date & heure</label>
            <input type="text" id="date" class="form-control" value="<?= date("Y-m-d H:i:s") ?>" disabled>
        </div>
        <div class="mb-3">
            <label for="designation" class="form-label">Désignation</label>
            <input type="text" name="designation" id="designation" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="versement" class="form-label">Versement</label>
            <input type="number" step="0.01" name="versement" id="versement" class="form-control" value="0">
        </div>
        <div class="mb-3">
            <label for="depense" class="form-label">Dépense</label>
            <input type="number" step="0.01" name="depense" id="depense" class="form-control" value="0">
        </div>
        <div class="d-flex justify-content-between">
            <a href="fonctionnement.php" class="btn btn-secondary">⬅ Retour</a>
            <button type="submit" class="btn btn-success">💾 Sauvegarder</button>
        </div>
    </form>

<?php include "includes/footer.php"; ?>
