<?php
session_start();

// Connexion PDO sécurisée
$dsn = "mysql:host=localhost;dbname=u174726466_Gl;charset=utf8";
$user = "u174726466_Gl";
$pass = "Unisoft**11"; // change par ton vrai mot de passe

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username   = trim($_POST['username']);
    $telephone  = trim($_POST['telephone']);
    $password   = trim($_POST['password']);
    $confirm    = trim($_POST['confirm_password']);
    $rank       = "user"; // par défaut

    // Vérification des champs
    if (empty($username) || empty($telephone) || empty($password) || empty($confirm)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérification si l’utilisateur existe déjà
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR telephone = ?");
        $check->execute([$username, $telephone]);

        if ($check->rowCount() > 0) {
            $error = "❌ Ce nom d’utilisateur ou numéro de téléphone existe déjà.";
        } else {
            // Hash du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insertion avec téléphone
            $stmt = $pdo->prepare("INSERT INTO users (username, telephone, password, rank) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $telephone, $hashedPassword, $rank])) {
                $success = "✅ Compte créé avec succès ! Redirection...";
                $_SESSION['user'] = $username;
                $_SESSION['rank'] = $rank;
            } else {
                $error = "❌ Erreur lors de l'inscription.";
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inscription</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card shadow-lg rounded-4">
          <div class="card-body p-4">
            <h3 class="card-title text-center mb-3">Créer un compte</h3>
            
            <?php if ($error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
              <div class="alert alert-success text-center">
                <?= htmlspecialchars($success) ?>
              </div>
              <script>
                setTimeout(() => {
                  window.location.href = "login.php";
                }, 2000); // ⏳ redirection après 2 secondes
              </script>
            <?php else: ?>
              <form method="post" action="">
                <div class="mb-3">
                  <label class="form-label">Nom d'utilisateur</label>
                  <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Mot de passe</label>
                  <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Confirmer le mot de passe</label>
                  <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Telephone</label>
                  <input type="text" name="telephone" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">S'inscrire</button>
              </form>
              <p class="text-center mt-3">
                Déjà inscrit ? <a href="login.php">Se connecter</a>
              </p>
            <?php endif; ?>
            
          </div>
        </div>
        <p class="text-center mt-3 text-muted">© 2025 - MonApp</p>
      </div>
    </div>
  </div>
</body>
</html>
