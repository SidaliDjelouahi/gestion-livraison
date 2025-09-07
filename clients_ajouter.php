<?php
// clients_ajouter.php
session_start();
require_once "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username  = trim($_POST["username"]);
    $telephone = trim($_POST["telephone"]);

    if ($username != "") {
        // Vérifier si le username existe déjà
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $message = "⚠️ Ce client existe déjà.";
        } else {
            $password = password_hash("123", PASSWORD_DEFAULT);
            $rank     = "user";

            $stmt = $conn->prepare("INSERT INTO users (username, password, telephone, rank) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $password, $telephone, $rank]);

            $message = "✅ Client ajouté avec succès !";
            echo "<script>
                    setTimeout(function(){
                        window.location.href = 'clients.php';
                    }, 2000);
                  </script>";
        }
    } else {
        $message = "⚠️ Le champ username est obligatoire.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter Client</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="mb-4">Ajouter un Nouveau Client</h2>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Nom d'utilisateur</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="clients.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
