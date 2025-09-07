<?php
session_start();
include 'includes/db.php'; // ⚠️ Assure-toi que ce fichier crée bien $pdo (et pas $conn)

// Activer l'affichage des erreurs (mode debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Vérifier si l'utilisateur existe
        $stmt = $conn->prepare("SELECT id, username, password, rank FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // ✅ Création de la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['rank'] = $user['rank'];
        
            // ✅ Redirection selon rôle
            if ($user['rank'] === 'admin' || $user['rank'] === 'manager') {
                header("Location: control.php");
            } elseif ($user['rank'] === 'user') {
                header("Location: default.php");
            } else {
                // Sécurité : si rôle inconnu → on bloque
                session_destroy();
                header("Location: login.php?error=role_invalide");
            }
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }

    } catch (PDOException $e) {
        $error = "Erreur serveur : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="manifest" href="manifest.json">
</head>
<body class="bg-light d-flex align-items-center vh-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow-lg rounded-4">
          <div class="card-body p-4">
            <h3 class="card-title text-center mb-3">Se connecter</h3>
            <?php if ($error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="">
              <div class="mb-3">
                <label class="form-label">Nom d'utilisateur</label>
                <input type="text" name="username" class="form-control" required autofocus autocomplete="off">
              </div>
              <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Connexion</button>
            </form>
            <p class="text-center mt-3">
              <a href="signup.php">Créer un compte</a>
            </p>
            <p class="text-center mt-3">
              <a href="default.php">Retoure</a>
            </p>

          </div>
        </div>
        <p class="text-center mt-3 text-muted">© 2025 - MonApp</p>
      </div>
    </div>
  </div>
</body>
</html>
