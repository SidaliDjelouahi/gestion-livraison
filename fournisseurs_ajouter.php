<?php
// fournisseurs_ajouter.php
session_start();
include "includes/db.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $telephone = trim($_POST['telephone']);

    if (!empty($username) && !empty($telephone)) {
        try {
            // Insertion dans la table users
            $stmt = $conn->prepare("INSERT INTO users (username, password, telephone, rank) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, password_hash("123", PASSWORD_DEFAULT), $telephone, "provider"]);

            $message = "Fournisseur ajouté avec succès !";
            echo "<script>
                setTimeout(function(){
                    window.location.href = 'fournisseurs.php';
                }, 2000);
            </script>";
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<?php include "includes/header.php"; ?>
<?php include "includes/sidebar.php"; ?>

<div class="container mt-4">
    <h2>Ajouter un Fournisseur</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nom d'utilisateur</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Ajouter</button>
        <a href="fournisseurs.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include "includes/footer.php"; ?>
