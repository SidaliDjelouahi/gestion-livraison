<?php
session_start();
include "includes/db.php";

// Activer affichage erreurs
error_reporting(E_ALL);
ini_set("display_errors", 1);

$q = isset($_GET['q']) ? trim($_GET['q']) : "";

// Filtrage des produits exposés
$sql = "SELECT * FROM produits WHERE quantite > 0 AND etat = 'expose'";
$params = [];

if ($q !== "") {
    $sql .= " AND (name LIKE :q OR description LIKE :q OR prix_vente LIKE :q)";
    $params[':q'] = "%$q%";
}

$sql .= " ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($produits) {
    foreach ($produits as $row) {
        $id_modal = "commandeModal" . $row['id']; // ID unique pour chaque modal
        ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm">
                <?php if (!empty($row['photo'])): ?>
                    <img src="photos_produits/<?= htmlspecialchars($row['photo']); ?>" 
                         class="card-img-top" alt="<?= htmlspecialchars($row['name']); ?>">
                <?php endif; ?>
                <div class="card-body d-flex flex-column">
                    <h5 class="product-title">
                        <span class="price"><?= number_format($row['prix_vente'], 2) ?> DA</span>
                        <?= htmlspecialchars($row['name']) ?>
                    </h5>
                    <p class="text-muted small"><?= nl2br(htmlspecialchars($row['description'])); ?></p>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">Stock: <?= $row['quantite']; ?></span>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#<?= $id_modal ?>">
                            Commander
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Commander -->
        <div class="modal fade" id="<?= $id_modal ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <form method="post" action="save_commande.php" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Commander <?= htmlspecialchars($row['name']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
              </div>
              <div class="modal-body">
                  <input type="hidden" name="id_produit" value="<?= $row['id']; ?>">
                  
                  <?php if (isset($_SESSION['user_id'])): ?>
                      <input type="hidden" name="id_client" value="<?= $_SESSION['user_id']; ?>">
                  <?php endif; ?>

                  <div class="mb-3">
                      <label class="form-label">Produit</label>
                      <input type="text" class="form-control" value="<?= htmlspecialchars($row['name']); ?>" disabled>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Prix</label>
                      <input type="text" class="form-control" value="<?= number_format($row['prix_vente'], 2, ',', ' '); ?> DA" disabled>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Téléphone *</label>
                      <input type="text" name="telephone" class="form-control" required>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Adresse</label>
                      <textarea name="adresse" class="form-control"></textarea>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Commentaire</label>
                      <textarea name="commentaire" class="form-control"></textarea>
                  </div>
                  
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Commander</button>
              </div>
              <?php if (!isset($_SESSION['user_id'])): ?>
                  <div class="alert alert-success" role="alert">
                      💡 Tu peux voir toutes tes commandes si tu crées un compte 
                      <a href="signup.php" class="alert-link">ici</a>.
                  </div>
              <?php endif; ?>          
            </form>
          </div>
        </div>
        <?php
    }
} else {
    echo "<p class='text-center text-muted'>Aucun produit trouvé.</p>";
}



