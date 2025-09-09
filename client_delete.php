<?php
// clients_supprimer.php
session_start();
require_once "includes/db.php"; // Connexion PDO ($conn)

// Vérifier connexion utilisateur
if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

// Vérifier si un ID est passé
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: clients.php?error=id_invalide");
    exit();
}

$client_id = intval($_GET['id']);

try {
    // Vérifier si le client existe et est de type "user"
    $sql_check = "SELECT * FROM users WHERE id = ? AND rank = 'user'";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$client_id]);
    $client = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        header("Location: clients.php?error=not_found");
        exit();
    }

    // Vérifier la dette du client à partir des ventes
    $sql_check_dette = "
        SELECT 
            COALESCE(SUM(vd.quantite * vd.prix_vente), 0) - 
            COALESCE(SUM(v.versement), 0) AS dette
        FROM ventes v
        INNER JOIN ventes_details vd ON v.id = vd.id_vente
        WHERE v.id_user = ?
    ";
    $stmt_check_dette = $conn->prepare($sql_check_dette);
    $stmt_check_dette->execute([$client_id]);
    $dette = $stmt_check_dette->fetchColumn();

    if ($dette > 0) {
        // Le client a une dette → afficher un message explicite
        include "includes/header.php";
        include "includes/sidebar.php";
        echo '<div class="container mt-4">';
        echo '<div class="alert alert-danger">';
        echo '<h4>Suppression impossible</h4>';
        echo '<p>Le client <strong>' . htmlspecialchars($client['username']) . '</strong> ne peut pas être supprimé car il a une <strong> situation (dette ou historique de ventes)</strong>.</p>';
        echo '<p>Pour le supprimer définitivement, vous devez d\'abord <strong>supprimer son historique de ventes</strong>.</p>';
        echo '<a href="clients.php" class="btn btn-secondary mt-2">Retour à la liste des clients</a>';
        echo '</div>';
        echo '</div>';
        include "includes/footer.php";
        exit();
    }

    // Supprimer le client si pas de dette
    $sql_delete = "DELETE FROM users WHERE id = ? AND rank = 'user'";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->execute([$client_id]);

    header("Location: clients.php?success=deleted");
    exit();

} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}