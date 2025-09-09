<?php
session_start();
include "includes/db.php";
include "includes/header.php";
include "includes/sidebar.php";

// Activer les erreurs PDO
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier la connexion utilisateur
if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

// --- Date d'aujourd'hui ---
$date_auj = date("Y-m-d H:i:s");

try {
    // --- Suppression balance (dernière ligne uniquement) ---
    if (isset($_GET['delete'])) {
        $id_delete = intval($_GET['delete']);
        $stmt_last = $conn->query("SELECT id FROM balance ORDER BY id DESC LIMIT 1");
        $last_id = $stmt_last->fetchColumn();
        if ($last_id == $id_delete) {
            $stmt_del = $conn->prepare("DELETE FROM balance WHERE id = ?");
            $stmt_del->execute([$id_delete]);
            header("Location: balance.php?deleted=1");
            exit();
        } else {
            echo "<div class='alert alert-danger'>❌ Vous ne pouvez supprimer que la dernière balance.</div>";
        }
    }

    // --- Calcul inventaire produits ---
    $stmt_inventaire = $conn->prepare("SELECT SUM(prix_achat * quantite) as total_inventaire FROM produits");
    $stmt_inventaire->execute();
    $inventaire = $stmt_inventaire->fetch(PDO::FETCH_ASSOC)['total_inventaire'] ?? 0;

    // --- Calcul équipements ---
    $stmt_equipements = $conn->prepare("SELECT SUM(prix_achat * quantite) as total_equipements FROM equipements");
    $stmt_equipements->execute();
    $equipements = $stmt_equipements->fetch(PDO::FETCH_ASSOC)['total_equipements'] ?? 0;

    // --- Crédits clients ---
    $stmt_total_ventes = $conn->prepare("
        SELECT SUM(vd.prix_vente * vd.quantite) AS total_ventes
        FROM ventes_details vd
        INNER JOIN ventes v ON vd.id_vente = v.id
    ");
    $stmt_total_ventes->execute();
    $total_ventes = $stmt_total_ventes->fetch(PDO::FETCH_ASSOC)['total_ventes'] ?? 0;

    $stmt_total_versements = $conn->prepare("SELECT SUM(versement) AS total_versements FROM ventes");
    $stmt_total_versements->execute();
    $total_versements = $stmt_total_versements->fetch(PDO::FETCH_ASSOC)['total_versements'] ?? 0;

    $credit_clients = $total_ventes - $total_versements;

    // --- Crédits fournisseurs ---
    $stmt_total_achats = $conn->prepare("
        SELECT SUM(ad.prix_achat * ad.quantite) AS total_achats
        FROM achats_details ad
        INNER JOIN achats a ON ad.id_achat = a.id
    ");
    $stmt_total_achats->execute();
    $total_achats = $stmt_total_achats->fetch(PDO::FETCH_ASSOC)['total_achats'] ?? 0;

    $stmt_total_versements_achats = $conn->prepare("SELECT SUM(versement) AS total_versements FROM achats");
    $stmt_total_versements_achats->execute();
    $total_versements_achats = $stmt_total_versements_achats->fetch(PDO::FETCH_ASSOC)['total_versements'] ?? 0;

    $credit_fournisseurs = $total_achats - $total_versements_achats;

    // --- Dernière sauvegarde balance ---
    $stmt_last_balance = $conn->prepare("SELECT * FROM balance ORDER BY date DESC LIMIT 1");
    $stmt_last_balance->execute();
    $last_balance = $stmt_last_balance->fetch(PDO::FETCH_ASSOC);

    $last_date_balance = $last_balance['date'] ?? null;
    $last_caisse = $last_balance['caisse'] ?? 0;

    // --- Calcul caisse ---
    if ($last_date_balance) {
        $stmt_ventes_apres = $conn->prepare("SELECT SUM(versement) as total FROM ventes WHERE date > ?");
        $stmt_ventes_apres->execute([$last_date_balance]);
        $ventes_apres = $stmt_ventes_apres->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $stmt_achats_apres = $conn->prepare("SELECT SUM(versement) as total FROM achats WHERE date > ?");
        $stmt_achats_apres->execute([$last_date_balance]);
        $achats_apres = $stmt_achats_apres->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $caisse = $last_caisse + ($ventes_apres - $achats_apres);
        $caisse_message = "Mise à jour caisse depuis le $last_date_balance : " . number_format($caisse, 2, ',', ' ') . " DA";
    } else {
        $caisse = $total_versements - $total_versements_achats;
        $caisse_message = "⚠️ Pas de sauvegarde trouvée. Caisse calculée directement : " . number_format($caisse, 2, ',', ' ') . " DA";
    }

    // --- Sauvegarde nouvelle balance ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sauvegarder'])) {
        $capital = $inventaire + $equipements + $caisse + $credit_clients - $credit_fournisseurs;

        $commentaire = $_POST['commentaire'] ?? null;

        $stmt_insert = $conn->prepare("
            INSERT INTO balance (date, inventaire, equipements, caisse, credit_clients, credit_fournisseurs, capital, commentaire)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt_insert->execute([$date_auj, $inventaire, $equipements, $caisse, $credit_clients, $credit_fournisseurs, $capital, $commentaire]);

        header("Location: balance.php?saved=1");
        exit();
    }

    // --- Récupérer balances ---
    $stmt_balance = $conn->prepare("SELECT * FROM balance ORDER BY id DESC");
    $stmt_balance->execute();
    $balances = $stmt_balance->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur SQL : " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Balance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

    <h3 class="mb-3">📅 Balance du <?= htmlspecialchars($date_auj) ?></h3>

    <?php if (isset($_GET['saved'])): ?>
        <div class="alert alert-success">✅ Nouvelle balance sauvegardée avec succès.</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-warning">🗑️ Dernière balance supprimée avec succès.</div>
    <?php endif; ?>

    <p><strong>Inventaire Produits :</strong> <?= number_format($inventaire, 2, ',', ' ') ?> DA</p>
    <p><strong>Équipements :</strong> <?= number_format($equipements, 2, ',', ' ') ?> DA</p>
    <p><strong>Crédits Clients :</strong> <?= number_format($credit_clients, 2, ',', ' ') ?> DA</p>
    <p><strong>Crédits Fournisseurs :</strong> <?= number_format($credit_fournisseurs, 2, ',', ' ') ?> DA</p>
    <p class="text-info"><strong><?= $caisse_message ?></strong></p>

    <!-- Formulaire Sauvegarde -->
    <form method="post" class="mb-3">
        <div class="mb-3">
            <label for="commentaire" class="form-label">📝 Commentaire</label>
            <textarea name="commentaire" id="commentaire" class="form-control" rows="2" placeholder="Ajouter un commentaire (facultatif)"></textarea>
        </div>
        <button type="submit" name="sauvegarder" class="btn btn-success">💾 Sauvegarder la nouvelle balance</button>
    </form>

    <table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Inventaire</th>
            <th>Équipements</th>
            <th>Caisse</th>
            <th>Crédit Clients</th>
            <th>Crédit Fournisseurs</th>
            <th>Capital</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($balances as $index => $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['date'] ?></td>
            <td><?= number_format($row['inventaire'], 2, ',', ' ') ?></td>
            <td><?= number_format($row['equipements'], 2, ',', ' ') ?></td>
            <td><?= number_format($row['caisse'], 2, ',', ' ') ?></td>
            <td><?= number_format($row['credit_clients'], 2, ',', ' ') ?></td>
            <td><?= number_format($row['credit_fournisseurs'], 2, ',', ' ') ?></td>
            <td><?= number_format($row['capital'], 2, ',', ' ') ?></td>
            <td>
                <?php if ($index === 0): ?>
                    <a href="balance.php?delete=<?= $row['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Supprimer la dernière balance ?')">🗑 Supprimer</a>
                    <a href="balance_modifier.php?id=<?= $row['id'] ?>" 
                       class="btn btn-warning btn-sm">✏ Modifier</a>
                <?php endif; ?>

                <?php if (!empty($row['commentaire'])): ?>
                    <button type="button" 
                            class="btn btn-info btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#commentModal<?= $row['id'] ?>">
                        💬 Commentaire
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="commentModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title">Commentaire (Balance ID <?= $row['id'] ?>)</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <?= nl2br(htmlspecialchars($row['commentaire'])) ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


</body>
</html>
