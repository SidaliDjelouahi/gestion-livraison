<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Récupérer les bons de vente avec client
$stmt = $conn->query("
    SELECT v.id, v.num_vente, v.date, v.id_client, v.versement, c.username AS client_nom
    FROM ventes v
    JOIN users c ON v.id_client = c.id
    ORDER BY v.id DESC
");
$ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Historique des Bons de Vente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="container">
  <h3 class="mb-4">📑 Historique des Bons de Vente</h3>

  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th>Numéro Bon</th>
        <th>Date</th>
        <th>Client</th>
        <th>Versement</th>
        <th>Total</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($ventes as $v): ?>
        <?php
          // calculer total de ce bon depuis ventes_details
          $stmt2 = $conn->prepare("SELECT SUM(prix_vente * quantite) as total FROM ventes_details WHERE id_vente=?");
          $stmt2->execute([$v['id']]);
          $total = $stmt2->fetchColumn() ?? 0;
        ?>
        <tr>
          <td><?= htmlspecialchars($v['num_vente']) ?></td>
          <td><?= htmlspecialchars($v['date']) ?></td>
          <td><?= htmlspecialchars($v['client_nom']) ?></td>
          <td><?= number_format($v['versement'], 2, '.', ' ') ?></td>
          <td><?= number_format($total, 2, '.', ' ') ?></td>
          <td>
            <a href="bon_vente_modifier.php?id=<?= $v['id'] ?>" class="btn btn-primary btn-sm">
              Détails
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>
