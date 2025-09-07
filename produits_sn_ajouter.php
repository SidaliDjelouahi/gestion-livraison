<?php
session_start();
include('includes/db.php'); 
include('includes/header.php');
include('includes/sidebar.php');

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour ajouter un SN.");
}

// Vérifier l'ID du produit
$id_produit = isset($_GET['id_produit']) ? intval($_GET['id_produit']) : 0;

if ($id_produit <= 0) {
    die("Produit invalide.");
}

// Récupérer le produit
$stmt = $conn->prepare("SELECT name FROM produits WHERE id = ?");
$stmt->execute([$id_produit]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sn = trim($_POST['sn']);
    if (!empty($sn)) {
        try {
            $insert = $conn->prepare("INSERT INTO produits_sn (id_produit, sn) VALUES (?, ?)");
            $insert->execute([$id_produit, $sn]);
            $success = "✅ SN ajouté avec succès.";
        } catch (PDOException $e) {
            $error = "⚠️ Erreur : " . $e->getMessage();
        }
    } else {
        $error = "⚠️ Veuillez entrer un numéro de série.";
    }
}
?>

<div class="container mt-4">
    <h2>Ajouter un SN pour : <?= htmlspecialchars($product['name'] ?? 'Produit inconnu') ?></h2>

    <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Numéro de série</label>
            <input type="text" name="sn" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
        <a href="produits_sn.php?id_produit=<?= $id_produit ?>" class="btn btn-secondary">Retour</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
