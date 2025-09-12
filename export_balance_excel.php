<?php
// export_balance_excel.php
session_start();
require "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}

// Définir en-têtes pour téléchargement CSV
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=balances_" . date("Y-m-d") . ".csv");

// Ouvrir la sortie standard
$output = fopen("php://output", "w");

// Écrire BOM UTF-8 pour compatibilité Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Entêtes colonnes
$headers = ["ID", "Date", "Inventaire", "Équipements", "Caisse", "Crédit Clients", "Crédit Fournisseurs", "Capital", "Commentaire"];
fputcsv($output, $headers, ";");

// Récupération données
$stmt = $conn->prepare("SELECT * FROM balance ORDER BY id DESC");
$stmt->execute();
$balances = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Écrire chaque ligne dans le CSV
foreach ($balances as $row) {
    fputcsv($output, [
        $row['id'],
        "'".$row['date']."",  // 👈 Apostrophe pour forcer texte
        $row['inventaire'],
        $row['equipements'],
        $row['caisse'],
        $row['credit_clients'],
        $row['credit_fournisseurs'],
        $row['capital'],
        $row['commentaire']
    ], ";");
}


fclose($output);
exit;
