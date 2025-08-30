<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

include('includes/db.php');   // connexion PDO
include('includes/header.php');
include('includes/sidebar.php');

$message = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $rank     = $_POST['rank'];

    if (!empty($username) && !empty($password)) {
        try {
            // Vérifier si l'email existe déjà
            $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $check->execute([$username]);

            if ($check->rowCount() > 0) {
                $message = "<div class='alert alert-danger'>Cet email existe déjà ❌</div>";
            } else {
                // Hash du mot de passe
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $conn->prepare("INSERT INTO users (username, password, rank) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashedPassword, $rank]);

                $message = "<div class='alert alert-success'>✅ Utilisateur ajouté avec succès</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Veuillez remplir tous les champs</div>";
    }
}
?>

<div class="container my-4">
    <h2 class="mb-4">Ajouter un utilisateur ➕</h2>

    <?= $message ?>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="username" class="form-label">Nom d'utilisateur</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="rank" class="form-label">Rôle</label>
            <select name="rank" id="rank" class="form-select" required>
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="user">Utilisateur</option>
            </select>
        </div>


        <!-- Boutons sur la même ligne -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Ajouter</button>
            <a href="users.php" class="btn btn-secondary">⬅ Retour</a>
        </div>
    </form>
</div>

</body>
</html>

