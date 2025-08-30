<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

include('includes/db.php');     // doit retourner $conn en PDO
include('includes/header.php');
include('includes/sidebar.php');

// Récupérer les utilisateurs avec PDO
$stmt = $conn->prepare("SELECT id, username, rank FROM users ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-4">
    <h2 class="mb-4">Liste des utilisateurs 👥</h2>

    <!-- Bouton ajouter -->
    <a href="users_ajouter.php" class="btn btn-success mb-3">+ Ajouter un utilisateur</a>

    <!-- Table pour Desktop -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['rank']) ?></td>
                    <td>
                        <a href="users_modifier.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                        <a href="users_supprimer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                           onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                           Supprimer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Cards pour Mobile -->
    <div class="d-block d-md-none">
        <?php foreach ($users as $row): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['username']) ?></h5>
                    <p class="card-text">
                        <br>
                        <strong>Rôle :</strong> <?= htmlspecialchars($row['rank']) ?>
                    </p>
                    <a href="users_modifier.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                    <a href="users_supprimer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                       Supprimer
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
