<?php
// clients.php
session_start();
require_once "includes/db.php"; // fichier de connexion PDO ($conn)
include 'includes/header.php';
include 'includes/sidebar.php';

// Vérifier connexion
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}

// ------------------ Totaux globaux ------------------
$sql_totaux = "
    SELECT 
        SUM(vd.quantite * vd.prix_vente) AS total_ventes,
        SUM(v.versement) AS total_versements
    FROM ventes v
    INNER JOIN ventes_details vd ON v.id = vd.id_vente
";
$stmt_totaux = $conn->query($sql_totaux);
$res_totaux = $stmt_totaux->fetch(PDO::FETCH_ASSOC);

$total_ventes    = $res_totaux['total_ventes'] ?? 0;
$total_versements = $res_totaux['total_versements'] ?? 0;
$total_credit    = $total_ventes - $total_versements;

// ------------------ Liste des clients ------------------
$sql = "SELECT id, username, telephone FROM users WHERE rank = 'user' ORDER BY username ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ------------------ Si recherche par date ------------------
$totaux_date = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['date_filter'])) {
    $date_filter = $_POST['date_filter'];

    $sql_date = "
        SELECT 
            SUM(vd.quantite * vd.prix_vente) AS total_ventes,
            SUM(v.versement) AS total_versements
        FROM ventes v
        INNER JOIN ventes_details vd ON v.id = vd.id_vente
        WHERE DATE(v.date) = ?
    ";
    $stmt_date = $conn->prepare($sql_date);
    $stmt_date->execute([$date_filter]);
    $totaux_date = $stmt_date->fetch(PDO::FETCH_ASSOC);

    $totaux_date['total_ventes']    = $totaux_date['total_ventes'] ?? 0;
    $totaux_date['total_versements'] = $totaux_date['total_versements'] ?? 0;
    $totaux_date['total_credit']    = $totaux_date['total_ventes'] - $totaux_date['total_versements'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des Clients</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<style>
.btn-xs {
    padding: 0.2rem 0.4rem;
    font-size: 0.5rem;
    border-radius: 0.2rem;
}
</style>
<body class="bg-light">

<div class="container py-4">
    <h2 class="mb-4">Liste des Clients</h2>

  <!-- Totaux globaux -->
  <div class="alert alert-info">
    <h4>Totaux globaux</h4>
    <p><strong>Total ventes :</strong> <?= number_format($total_ventes, 2) ?> DA</p>
    <p><strong>Total versements :</strong> <?= number_format($total_versements, 2) ?> DA</p>
    <p><strong>Total crédit :</strong> <?= number_format($total_credit, 2) ?> DA</p>
  </div>

  <!-- Filtre par date -->
  <form method="post" class="row g-2 mb-4">
    <div class="col-auto">
      <input type="date" name="date_filter" class="form-control" required>
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary">Total d'une date</button>
    </div>
  </form>

  <?php if ($totaux_date): ?>
    <!-- Modal affiché automatiquement si recherche par date -->
    <script>
      window.onload = function() {
        var myModal = new bootstrap.Modal(document.getElementById('modalTotauxDate'));
        myModal.show();
      }
    </script>

    <div class="modal fade" id="modalTotauxDate" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Totaux du <?= htmlspecialchars($date_filter) ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p><strong>Total ventes :</strong> <?= number_format($totaux_date['total_ventes'], 2) ?> DA</p>
            <p><strong>Total versements :</strong> <?= number_format($totaux_date['total_versements'], 2) ?> DA</p>
            <p><strong>Total crédit :</strong> <?= number_format($totaux_date['total_credit'], 2) ?> DA</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
  
    <!-- Bouton ajouter client -->
      <div class="mb-3 text-end">
        <a href="clients_ajouter.php" class="btn btn-success">
          <i class="bi bi-person-plus"></i> Ajouter nouveau client
        </a>
      </div>


  <!-- Liste des clients -->
  
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>User</th>
        <th>Téléphone</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($clients)): ?>
        <?php foreach ($clients as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['id']) ?></td>
            <td><?= htmlspecialchars($c['username']) ?></td>
            <td><?= htmlspecialchars($c['telephone']) ?></td>
            <td>
              <!-- Desktop : boutons complets -->
              <div class="d-none d-md-block">
                <a href="clients_situation.php?id=<?= urlencode($c['id']) ?>" class="btn btn-primary btn-sm">
                  Situation
                </a>
                <a href="client_edit.php?id=<?= urlencode($c['id']) ?>" class="btn btn-warning btn-sm">
                  Modifier
                </a>
                <a href="client_delete.php?id=<?= urlencode($c['id']) ?>" 
                   onclick="return confirm('Supprimer ce client ?')" 
                   class="btn btn-danger btn-sm">
                  Supprimer
                </a>
              </div>

              <!-- Mobile : icônes seules -->
              <div class="d-block d-md-none">
                <a href="clients_situation.php?id=<?= urlencode($c['id']) ?>" class="btn btn-outline-primary btn-xs">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="client_edit.php?id=<?= urlencode($c['id']) ?>" class="btn btn-outline-warning btn-xs">
                  <i class="bi bi-pencil"></i>
                </a>
                <a href="client_delete.php?id=<?= urlencode($c['id']) ?>" 
                   onclick="return confirm('Supprimer ce client ?')" 
                   class="btn btn-outline-danger btn-xs">
                  <i class="bi bi-trash"></i>
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="4" class="text-center">Aucun client trouvé.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
