<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- PWA manifest -->
  <link rel="manifest" href="manifest.json">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Optionnel: icône -->
  <link rel="icon" type="image/png" href="icon.png">
  
  

  <script>
    // Enregistrer le Service Worker
    if ("serviceWorker" in navigator) {
      navigator.serviceWorker.register("sw.js")
        .then(reg => console.log("Service Worker enregistré:", reg.scope))
        .catch(err => console.error("Erreur SW:", err));
    }
  </script>
</head>
<body>
