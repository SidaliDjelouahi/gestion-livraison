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

// Vérifier si ID est fourni
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id = (int) $_GET['id'];

// Récupérer l'utilisateur
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $message = "<div class='alert alert-danger'>Utilisateur introuvable ❌</div>";
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $rank     = $_POST['rank'];
    $password = $_POST['password'];

    if (!empty($username)) {
        try {
            if (!empty($password)) {
                // Si mot de passe changé
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET username=?, password=?, rank=? WHERE id=?");
                $stmt->execute([$username, $hashedPassword, $rank, $id]);
            } else {
                // Si pas de changement du mot de passe
                $stmt = $conn->prepare("UPDATE users SET username=?, rank=? WHERE id=?");
                $stmt->execute([$username, $rank, $id]);
            }

            $message = "<div class='alert alert-success'>✅ Utilisateur modifié avec succès</div>";

            // Recharger les infos
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Veuillez remplir tous les champs</div>";
    }
}
?>

<div class="container my-4">
    <h2 class="mb-4">Modifier utilisateur ✏️</h2>

    <?= $message ?>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="username" class="form-label">Nom d'utilisateur</label>
            <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe (laisser vide si inchangé)</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>

        <div class="mb-3">
            <label for="rank" class="form-label">Rôle</label>
            <select name="rank" id="rank" class="form-select" required>
                <option value="admin" <?= $user['rank'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= $user['rank'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning">Modifier</button>
            <a href="users.php" class="btn btn-secondary">⬅ Retour</a>
        </div>
    </form>
</div>

</body>
</html>
