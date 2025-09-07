<?php
session_start();
if (isset($_SESSION['success'])) {
    echo "<div id='successMsg' class='alert alert-success text-center'>"
       . $_SESSION['success'] . "</div>";
    unset($_SESSION['success']);
}

include 'includes/header.php';
include 'includes/db.php';
include 'includes/sidebar.php';

// Générer numéro de bon automatique
$stmt = $conn->query("SELECT MAX(id) AS last_id FROM ventes");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$num_vente = "BV-" . str_pad(($row['last_id'] ?? 0) + 1, 5, "0", STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Nouveau Bon de Vente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
      /* Réserver un espace pour #client_result */
        #client_result {
          max-height: 200px;   /* environ 3 résultats */
          overflow-y: auto;
          display: none;       /* masqué par défaut */
          position: absolute;
          z-index: 1000;
          background: #fff;
          border: 1px solid #ddd;
          border-radius: 6px;
        }

  </style>
</head>
<body class="p-4">

<div class="container">
  <h3 class="mb-4">Nouveau bon de vente</h3>

  <form method="post" action="ventes_details.php" id="venteForm">
    <div class="row mb-3">
      <!-- Numéro de bon -->
      <div class="col-md-4">
        <label class="form-label">Numéro de Bon</label>
        <input type="text" name="num_vente" class="form-control" value="<?= htmlspecialchars($num_vente) ?>" readonly>
      </div>

      <!-- Date -->
      <div class="col-md-4">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
      </div>

      <!-- Recherche client -->
      <div class="col-md-4 position-relative">
        <label class="form-label">Client</label>
        <input type="text" id="client_search" class="form-control" placeholder="Rechercher client...">
        <input type="hidden" name="id_client" id="id_client">
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
        <input type="number" step="0.01" id="prix_vente" class="form-control">
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
      <tbody></tbody>
    </table>

    <div class="row mt-4">
      <div class="col-md-6">
        <label class="form-label">Total du Bon</label>
        <input type="text" id="totalBon" name="total_bon" class="form-control" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Versement (optionnel)</label>
        <input type="number" step="0.01" id="versement" name="versement" class="form-control" placeholder="Laisser vide si payé en totalité">
      </div>
    </div>

    <div class="mt-4 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Valider le Bon</button>
      <a href="bon_vente_historique.php" class="btn btn-secondary">Historique Vente</a>
    </div>
  </form>
</div>

<script>
$(document).ready(function(){

  let produitsArray = [];

  // 🔎 Recherche client
    $("#client_search").keyup(function(){
      let q = $(this).val();
      if(q.length > 1){
        $.get("search_client.php", {q:q}, function(data){
          if($.trim(data) !== "") {
            $("#client_result").html(data).show();
          } else {
            // pas de résultat -> réduire l’espace
            $("#client_result").hide().html("");
          }
        });
      } else { 
        $("#client_result").hide().html("");
      }
    });


  $(document).on("click", ".client-item", function(e){
    e.preventDefault();
    $("#id_client").val($(this).data("id"));
    $("#client_search").val($(this).text());
    $("#client_result").hide();
  });

  // 🔎 Recherche produit
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

  // ➕ Ajouter produit
  $("#btnAdd").click(function(){
    let id = $("#produit_search").data("id_selected");
    let name = $("#produit_search").val();
    let prix = $("#prix_vente").val();
    let qte = $("#quantite").val();

    if(id && name && prix && qte > 0){
      let total = (parseFloat(prix) * parseInt(qte)).toFixed(2);

      // Ajouter au tableau JS
      produitsArray.push({
        id: id,
        name: name,
        prix: parseFloat(prix),
        qte: parseInt(qte),
        total: parseFloat(total)
      });

      // Affichage
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

      // Versement auto si vide
      if(!$("#versement").val() || $("#versement").val() == 0) $("#versement").val($("#totalBon").val());

      // Reset champs
      $("#produit_search").val('').removeData("id_selected");
      $("#prix_vente").val('');
      $("#quantite").val(1);

    } else { alert("Veuillez sélectionner un produit et saisir une quantité valide !"); }
  });

  // ❌ Supprimer produit
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

  // Envoi du tableau JS au serveur
  $("#venteForm").submit(function(e){
    if(produitsArray.length === 0){
      alert("Veuillez ajouter au moins un produit !");
      e.preventDefault();
      return;
    }
    $("<input>").attr({
      type: "hidden",
      name: "produits_json",
      value: JSON.stringify(produitsArray)
    }).appendTo(this);
  });

  // fade out success
  setTimeout(function(){ $("#successMsg").fadeOut("slow"); }, 6000);
});
</script>

</body>
</html>
