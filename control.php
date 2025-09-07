<?php
session_start();

include 'includes/sidebar.php';
?>


<div class="container p-4">
    <h1>Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>
    <p>Votre rôle : <?= htmlspecialchars($_SESSION['rank']) ?></p>
</div>


