<?php
session_start();
include "includes/db.php";

// Calculer le nombre de commandes avec l'état 'commande'
$commande_count = 0;

if (isset($_SESSION['rank']) && ($_SESSION['rank'] === 'admin' || $_SESSION['rank'] === 'manager')) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM commandes WHERE etat = 'commande'");
    $stmt->execute();
    $commande_count = $stmt->fetchColumn();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIGITEK - Boutique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
    <style>
        body { background: #f8f9fa; }
        .navbar { background: #0d6efd; }
        .navbar-brand { font-weight: bold; font-size: 1.5rem; color: #fff !important; }
        .nav-link { color: #fff !important; }
        .card {
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0px 8px 20px rgba(0,0,0,0.15); }
        .product-title { font-weight: bold; font-size: 1.1rem; }
        .price { color: #0d6efd; font-weight: bold; font-size: 1.2rem; }
        .product-title {
            font-weight: bold;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px; /* espace entre prix et nom */
        }
        .product-title .price {
            color: #d9534f; /* Rouge vif pour différencier */
            font-weight: bold;
            font-size: 1.2rem;
        }

        
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container d-flex justify-content-between align-items-center">
    <a class="navbar-brand" href="#">DIGITEK</a>

    <?php if (isset($_SESSION['user_id'])): ?>
  <!-- Dropdown utilisateur si connecté -->
<div class="dropdown">
  <button class="btn btn-light text-primary px-3 rounded-3 dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
  </button>
  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
    <?php if ($_SESSION['rank'] === 'admin' || $_SESSION['rank'] === 'manager'): ?>
        <li><a class="dropdown-item" href="control.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li>
          <a class="dropdown-item" href="commandes.php">
            <i class="bi bi-bag-check"></i> Commandes 
            <?php if ($commande_count > 0): ?>
                <span class="badge bg-danger"><?= $commande_count ?></span>
            <?php endif; ?>
          </a>
        </li>
    <?php endif; ?>
    <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
  </ul>
</div>

<?php else: ?>
  <!-- Bouton login si pas connecté -->
  <a class="btn btn-light text-primary px-3 rounded-3" href="login.php">
    <i class="bi bi-box-arrow-in-right"></i> Login
  </a>
<?php endif; ?>

      
</nav>
 

<!-- Recherche -->
<div class="container my-4">
    <input type="text" id="search" class="form-control form-control-lg" 
           placeholder="🔎 Rechercher un produit (nom, description ou prix)..." autocomplete="off">
</div>

<!-- Produits -->
<div class="container my-4">
    <h2 class="text-center mb-4">Nos Produits</h2>
    <div class="row g-4" id="product-list">
        <!-- Les produits seront chargés ici via AJAX -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Fonction de recherche instantanée
document.getElementById("search").addEventListener("keyup", function() {
    let query = this.value;
    fetch("search.php?q=" + encodeURIComponent(query))
        .then(response => response.text())
        .then(data => {
            document.getElementById("product-list").innerHTML = data;
        });
});

// Charger tous les produits au démarrage
window.onload = function() {
    fetch("search.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("product-list").innerHTML = data;
        });
};
</script>
</body>
</html>
