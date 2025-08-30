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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code        = trim($_POST['code']);
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $quantite    = intval($_POST['quantite']);
    $prix_achat  = !empty($_POST['prix_achat']) ? floatval($_POST['prix_achat']) : null;
    $prix_vente  = floatval($_POST['prix_vente']);
    $etat        = trim($_POST['etat']);
    $photo       = null; // par défaut aucune photo

    if (!empty($code) && !empty($name) && $prix_vente >= 0 && !empty($etat)) {
        try {
            // Gestion de l'upload photo
            if (!empty($_FILES['photo']['name'])) {
                $targetDir = "photos_produits/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                // Nettoyage du nom du produit pour créer le nom du fichier
                $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($name));
                $fileType = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $filename = $safeName . "." . $fileType;
                $targetFile = $targetDir . $filename;

                // Vérification du type de fichier
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                        $photo = $filename; // on enregistre le nom nettoyé
                    } else {
                        $error = "⚠️ Erreur lors du téléchargement de la photo.";
                    }
                } else {
                    $error = "⚠️ Seules les images JPG, JPEG, PNG et GIF sont autorisées.";
                }
            }

            if (!$error) {
                $stmt = $conn->prepare("INSERT INTO produits (code, name, description, quantite, prix_achat, prix_vente, etat, photo) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$code, $name, $description, $quantite, $prix_achat, $prix_vente, $etat, $photo]);

                $success = "✅ Produit ajouté avec succès.";
            }

        } catch (PDOException $e) {
            $error = "⚠️ Erreur lors de l'ajout du produit : " . $e->getMessage();
        }
    } else {
        $error = "⚠️ Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<div class="container mt-4">
    <h2 class="mb-4">Ajouter un produit</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Code</label>
            <input type="text" name="code" class="form-control" required autofocus autocomplete="off">
        </div>

        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="name" class="form-control" required autocomplete="off">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantité</label>
            <input type="number" name="quantite" class="form-control" value="0" required>
        </div>

        <?php if (isset($_SESSION['rank']) && $_SESSION['rank'] === 'admin'): ?>
        <div class="mb-3">
            <label class="form-label">Prix d'achat</label>
            <input type="number" step="0.01" name="prix_achat" class="form-control">
        </div>
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Prix de vente</label>
            <input type="number" step="0.01" name="prix_vente" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">État</label>
            <select name="etat" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                <option value="expose">Exposé</option>
                <option value="garantie">Garantie</option>
                <option value="test">Test</option>
                <option value="autre">Autre</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Photo du produit</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-success">Ajouter</button>
        <a href="produits.php" class="btn btn-secondary">Retour</a>
    </form>
</div>


<?php include 'includes/footer.php'; ?>
