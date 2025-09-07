<?php
session_start();
include('includes/db.php'); 
include('includes/header.php');
include('includes/sidebar.php');

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour modifier un SN.");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die("SN invalide.");

// Récupérer le SN
$stmt = $conn->prepare("
    SELECT ps.*, p.name AS produit_name 
    FROM produits_sn ps
    JOIN produits p ON ps.id_produit = p.id
    WHERE ps.id = ?
");
$stmt->execute([$id]);
$sn = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$sn) die("SN introuvable.");

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_sn = trim($_POST['sn']);
    if (!empty($new_sn)) {
        try {
            $update = $conn->prepare("UPDATE produits_sn SET sn = ? WHERE id = ?");
            $update->execute([$new_sn, $id]);
            $success = "✅ SN modifié avec succès.";
        } catch (PDOException $e) {
            $error = "⚠️ Erreur : " . $e->getMessage();
        }
    } else {
        $error = "⚠️ Veuillez entrer un numéro de série.";
    }
}
?>

<div class="container mt-4">
    <h2>Modifier SN pour : <?= htmlspecialchars($sn['produit_name']) ?></h2>

    <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Numéro de série</label>
            <input type="text" name="sn" class="form-control" value="<?= htmlspecialchars($sn['sn']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="produits_sn.php?id_produit=<?= $sn['id_produit'] ?>" class="btn btn-secondary">Retour</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
