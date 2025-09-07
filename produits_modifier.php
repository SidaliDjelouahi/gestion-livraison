<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

include 'includes/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$success = '';
$error = '';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: produits.php");
    exit();
}

$id = intval($_GET['id']);

// Récupération des infos du produit
$stmt = $conn->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    die("⚠️ Produit introuvable !");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code        = trim($_POST['code']);
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $quantite    = intval($_POST['quantite']);
    $prix_achat  = !empty($_POST['prix_achat']) ? floatval($_POST['prix_achat']) : null;
    $prix_vente  = floatval($_POST['prix_vente']);
    $etat        = trim($_POST['etat']);
    $photo       = $produit['photo']; // garder l’ancienne si pas modifiée

    if (!empty($code) && !empty($name) && $prix_vente >= 0 && !empty($etat)) {
        try {
            // Gestion de l’upload photo
            if (!empty($_FILES['photo']['name'])) {
                $targetDir = "photos_produits/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                // Nettoyage du nom et génération d’un nom unique
                $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($name));
                $fileType = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $filename = $safeName . "_" . time() . "." . $fileType;
                $targetFile = $targetDir . $filename;

                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                        $photo = $filename;
                    } else {
                        $error = "⚠️ Erreur lors du téléchargement de la photo.";
                    }
                } else {
                    $error = "⚠️ Seules les images JPG, JPEG, PNG et GIF sont autorisées.";
                }
            }

            if (!$error) {
                $stmt = $conn->prepare("UPDATE produits 
                    SET code = ?, name = ?, description = ?, quantite = ?, prix_achat = ?, prix_vente = ?, etat = ?, photo = ? 
                    WHERE id = ?");
                $stmt->execute([$code, $name, $description, $quantite, $prix_achat, $prix_vente, $etat, $photo, $id]);

                $success = "✅ Produit modifié avec succès.";
                // mise à jour des données pour réafficher le formulaire
                $produit = [
                    'code' => $code,
                    'name' => $name,
                    'description' => $description,
                    'quantite' => $quantite,
                    'prix_achat' => $prix_achat,
                    'prix_vente' => $prix_vente,
                    'etat' => $etat,
                    'photo' => $photo
                ];
            }
        } catch (PDOException $e) {
            $error = "⚠️ Erreur lors de la modification du produit : " . $e->getMessage();
        }
    } else {
        $error = "⚠️ Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<div class="container mt-4">
    <h2 class="mb-4">Modifier le produit</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Code</label>
            <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($produit['code']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($produit['name']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($produit['description']); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantité</label>
            <input type="number" name="quantite" class="form-control" value="<?= htmlspecialchars($produit['quantite']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Prix d'achat</label>
            <input type="number" step="0.01" name="prix_achat" class="form-control" value="<?= htmlspecialchars($produit['prix_achat']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Prix de vente</label>
            <input type="number" step="0.01" name="prix_vente" class="form-control" value="<?= htmlspecialchars($produit['prix_vente']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">État</label>
            <select name="etat" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                <option value="expose"   <?= ($produit['etat'] === 'expose') ? 'selected' : ''; ?>>Exposé</option>
                <option value="garantie" <?= ($produit['etat'] === 'garantie') ? 'selected' : ''; ?>>Garantie</option>
                <option value="test"     <?= ($produit['etat'] === 'test') ? 'selected' : ''; ?>>Test</option>
                <option value="autre"    <?= ($produit['etat'] === 'autre') ? 'selected' : ''; ?>>Autre</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Photo actuelle</label><br>
            <?php if (!empty($produit['photo'])): ?>
                <img src="photos_produits/<?= htmlspecialchars($produit['photo']); ?>?t=<?= time(); ?>" 
                     alt="photo" width="120" class="mb-2"><br>
            <?php else: ?>
                <span>Aucune photo</span><br>
            <?php endif; ?>
            <input type="file" name="photo" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="produits.php" class="btn btn-secondary">Retour</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
