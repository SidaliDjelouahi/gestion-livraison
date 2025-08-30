<?php
// bon_vente_modifier.php
session_start();
include "includes/db.php";

error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!isset($_GET['id'])) {
    die("ID vente manquant.");
}
$id_vente = intval($_GET['id']);

// Charger la vente
$stmt = $conn->prepare("SELECT * FROM ventes WHERE id=?");
$stmt->execute([$id_vente]);
$vente = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$vente) die("Vente introuvable.");

// Charger client
$stmt = $conn->prepare("SELECT * FROM clients WHERE id=?");
$stmt->execute([$vente['id_client']]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

// Charger les détails de la vente
$stmt = $conn->prepare("SELECT vd.*, p.name 
                        FROM ventes_details vd 
                        JOIN produits p ON vd.id_produit=p.id 
                        WHERE vd.id_vente=?");
$stmt->execute([$id_vente]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier Bon de Vente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">

<div class="container">
  <h3 class="mb-4">Modifier Bon de Vente #<?= htmlspecialchars($vente['num_vente']) ?></h3>

  <form method="post" action="ventes_details_modifier.php" id="venteForm">
    <input type="hidden" name="id_vente" value="<?= $id_vente ?>">

    <div class="row mb-3">
      <div class="col-md-4">
        <label class="form-label">Numéro de Bon</label>
        <input type="text" name="num_vente" class="form-control" value="<?= htmlspecialchars($vente['num_vente']) ?>" readonly>
      </div>

      <div class="col-md-4">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($vente['date']) ?>" required>
      </div>

      <div class="col-md-4 position-relative">
        <label class="form-label">Client</label>
        <input type="text" id="client_search" class="form-control" value="<?= htmlspecialchars($client['name']) ?>">
        <input type="hidden" name="id_client" id="id_client" value="<?= $client['id'] ?>">
        <div id="client_result" class="list-group position-absolute w-100"></div>
      </div>
    </div>

    <!-- Recherche produit -->
    <div class="row mb-3">
      <div class="col-md-4 position-relative">
        <label class="form-label">Produit</label>
        <input type="text" id="produit_search" class="form-control" placeholder="Rechercher produit...">
        <input type="hidden" id="id_produit">
        <div id="produit_result" class="list-group position-absolute w-100"></div>
      </div>
      <div class="col-md-4">
        <label class="form-label">Prix de vente</label>
        <input type="number" step="0.01" id="prix_vente" class="form-control" readonly>
      </div>
      <div class="col-md-4">
        <label class="form-label">Quantité</label>
        <input type="number" id="quantite" class="form-control" min="1" value="1">
      </div>
    </div>

    <button type="button" id="btnAdd" class="btn btn-success mb-3">+ Ajouter produit</button>

    <table class="table table-bordered" id="tableProduits">
      <thead class="table-light">
        <tr>
          <th>Produit</th>
          <th>Prix de vente</th>
          <th>Quantité</th>
          <th>Total</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($details as $d): ?>
          <tr>
            <td><?= htmlspecialchars($d['name']) ?></td>
            <td><?= $d['prix_vente'] ?></td>
            <td><?= $d['quantite'] ?></td>
            <td class="ligne-total"><?= number_format($d['prix_vente'] * $d['quantite'], 2) ?></td>
            <td><button type="button" class="btn btn-danger btn-sm btnDelete" data-id="<?= $d['id_produit'] ?>">Supprimer</button></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="row mt-4">
      <div class="col-md-6">
        <label class="form-label">Total du Bon</label>
        <input type="text" id="totalBon" name="total_bon" class="form-control" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Versement</label>
        <input type="number" step="0.01" id="versement" name="versement" class="form-control" value="<?= $vente['versement'] ?>">
      </div>
    </div>

    <div class="mt-4 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Mettre à jour le Bon</button>
      <a href="bon_vente_historique.php" class="btn btn-secondary">Annuler</a>
    </div>
  </form>
</div>

<script>
$(document).ready(function(){
  let produitsArray = [];

  // Pré-remplir tableau JS avec les produits existants
  $("#tableProduits tbody tr").each(function(){
    let name = $(this).find("td:eq(0)").text();
    let prix = parseFloat($(this).find("td:eq(1)").text());
    let qte = parseInt($(this).find("td:eq(2)").text());
    let total = parseFloat($(this).find("td:eq(3)").text());
    let id = $(this).find(".btnDelete").data("id");

    produitsArray.push({id:id, name:name, prix:prix, qte:qte, total:total});
  });
  calculerTotal();

  // Recherche client
  $("#client_search").keyup(function(){
    let q = $(this).val();
    if(q.length > 1){
      $.get("search_client.php", {q:q}, function(data){
        $("#client_result").html(data).show();
      });
    } else { $("#client_result").hide(); }
  });
  $(document).on("click", ".client-item", function(e){
    e.preventDefault();
    $("#id_client").val($(this).data("id"));
    $("#client_search").val($(this).text());
    $("#client_result").hide();
  });

  // Recherche produit
  $("#produit_search").keyup(function(){
    let q = $(this).val();
    if(q.length > 1){
      $.get("search_produit.php", {q:q}, function(data){
        $("#produit_result").html(data).show();
      });
    } else { $("#produit_result").hide(); }
  });
  $(document).on("click", ".produit-item", function(e){
    e.preventDefault();
    $("#produit_search").val($(this).data("name")).data("id_selected", $(this).data("id"));
    $("#prix_vente").val($(this).data("prix"));
    $("#produit_result").hide();
  });

  // Ajouter produit
  $("#btnAdd").click(function(){
    let id = $("#produit_search").data("id_selected");
    let name = $("#produit_search").val();
    let prix = $("#prix_vente").val();
    let qte = $("#quantite").val();

    if(id && name && prix && qte > 0){
      let total = (parseFloat(prix) * parseInt(qte)).toFixed(2);
      produitsArray.push({id:id, name:name, prix:parseFloat(prix), qte:parseInt(qte), total:parseFloat(total)});

      let row = `
        <tr>
          <td>${name}</td>
          <td>${prix}</td>
          <td>${qte}</td>
          <td class="ligne-total">${total}</td>
          <td><button type="button" class="btn btn-danger btn-sm btnDelete" data-id="${id}">Supprimer</button></td>
        </tr>`;
      $("#tableProduits tbody").append(row);
      calculerTotal();

      $("#produit_search").val('').removeData("id_selected");
      $("#prix_vente").val('');
      $("#quantite").val(1);
    } else { alert("Veuillez sélectionner un produit valide."); }
  });

  // Supprimer produit
  $(document).on("click", ".btnDelete", function(){
    let id = $(this).data("id");
    produitsArray = produitsArray.filter(p => p.id != id);
    $(this).closest("tr").remove();
    calculerTotal();
  });

  function calculerTotal(){
    let total = produitsArray.reduce((sum, p) => sum + p.total, 0);
    $("#totalBon").val(total.toFixed(2));
  }

  // Envoi des produits JSON
  $("#venteForm").submit(function(e){
    if(produitsArray.length === 0){
      alert("Veuillez ajouter au moins un produit !");
      e.preventDefault();
      return;
    }
    $("<input>").attr({
      type:"hidden",
      name:"produits_json",
      value:JSON.stringify(produitsArray)
    }).appendTo(this);
  });
});
</script>

</body>
</html>
