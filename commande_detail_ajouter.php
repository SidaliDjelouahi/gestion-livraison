<?php
session_start();
include "includes/db.php";

// Activer affichage erreurs PHP
error_reporting(E_ALL);
ini_set("display_errors", 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produit'], $_POST['telephone'])) {
    try {
        $id_produit = intval($_POST['id_produit']);
        $id_client = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
        $telephone = trim($_POST['telephone']);
        $adresse = !empty($_POST['adresse']) ? trim($_POST['adresse']) : null;
        $commentaire = !empty($_POST['commentaire']) ? trim($_POST['commentaire']) : null;
        $date = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("INSERT INTO commandes (id_produit, id_client, telephone, adresse, commentaire, date) 
                                VALUES (:id_produit, :id_client, :telephone, :adresse, :commentaire, :date)");
        $stmt->execute([
            ':id_produit'   => $id_produit,
            ':id_client'    => $id_client,
            ':telephone'    => $telephone,
            ':adresse'      => $adresse,
            ':commentaire'  => $commentaire,
            ':date'         => $date
        ]);

        echo "<div style='padding:20px; background:#d4edda; color:#155724; border:1px solid #c3e6cb;'>
                ✅ Commande enregistrée avec succès !, on vous contacteras  le plutot possible, merci.
              </div>";
        echo "<a href='" . $_SERVER['HTTP_REFERER'] . "'>⬅ Retour</a>";

    } catch (PDOException $e) {
        echo "<div style='padding:20px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb;'>
                ❌ Erreur SQL : " . htmlspecialchars($e->getMessage()) . "
              </div>";
    }
} else {
    echo "<div style='padding:20px; background:#fff3cd; color:#856404; border:1px solid #ffeeba;'>
            ⚠️ Données invalides : id_produit ou téléphone manquant.
          </div>";
}
