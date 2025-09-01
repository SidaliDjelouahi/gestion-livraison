<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    exit("Accès refusé");
}

$q = isset($_GET['q']) ? trim($_GET['q']) : "";

// Si manager → ne peut voir que les "user"
if ($_SESSION['rank'] === 'manager') {
    $sql = "SELECT id, username, rank FROM users WHERE rank = 'user' AND username LIKE ? ORDER BY id DESC";
    $params = ["%$q%"];
} elseif ($_SESSION['rank'] === 'admin') {
    $sql = "SELECT id, username, rank FROM users WHERE username LIKE ? OR rank LIKE ? ORDER BY id DESC";
    $params = ["%$q%", "%$q%"];
} else {
    exit("Accès refusé");
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Génération HTML des résultats
foreach ($users as $row) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>" . htmlspecialchars($row['username']) . "</td>
            <td>" . htmlspecialchars($row['rank']) . "</td>
            <td>
                <a href='users_modifier.php?id={$row['id']}' class='btn btn-sm btn-warning'>Modifier</a>
                <a href='users_supprimer.php?id={$row['id']}' class='btn btn-sm btn-danger'
                   onclick=\"return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');\">
                   Supprimer
                </a>
            </td>
          </tr>";
}
?>
