<?php
// includes/sidebar.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Sidebar caché par défaut */
        #mySidebar {
            height: 100%;
            width: 0;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            overflow-x: hidden;
            transition: 0.4s;
            padding-top: 60px;
            z-index: 1050;
        }

        #mySidebar a {
            padding: 12px 20px;
            text-decoration: none;
            font-size: 18px;
            color: #f8f9fa;
            display: block;
            transition: 0.3s;
        }

        #mySidebar a:hover {
            background-color: #495057;
        }

        /* Bouton flottant en bas à droite */
        .float-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1100;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div id="mySidebar">
        <a href="javascript:void(0)" onclick="toggleSidebar()" class="text-danger fw-bold">✖ Fermer</a>
        <a href="default.php">🏠 Home</a>
        <a href="bon_vente.php">📄 Bon de vente</a>
        <a href="produits.php">📦 Produits</a>
        <a href="commandes.php">🛒 Commandes</a>
        <a href="historique.php">📊 Historique</a>
        <a href="users.php">👥 Utilisateurs</a>
        <a href="clients.php">👤 Clients</a> 

        <!-- Liens visibles uniquement pour les administrateurs -->
        <?php if (isset($_SESSION['rank']) && $_SESSION['rank'] == 'admin'): ?>
            <a href="bon_achat.php">💼 Bon d'Achat</a>
            <a href="fournisseurs.php">🏭 Fournisseurs</a>
            <a href="balance.php">📒 Balance</a> <!-- ✅ Nouveau lien -->
        <?php endif; ?>

        <a href="logout.php" class="text-warning">🚪 Logout</a>
    </div>

    <!-- Bouton flottant -->
    <button class="btn btn-primary rounded-circle p-3 float-btn" onclick="toggleSidebar()">
        ☰
    </button>

    <script>
        function toggleSidebar() {
            let sidebar = document.getElementById("mySidebar");
            if (sidebar.style.width === "250px") {
                sidebar.style.width = "0";
            } else {
                sidebar.style.width = "250px";
            }
        }
    </script>

</body>
</html>
