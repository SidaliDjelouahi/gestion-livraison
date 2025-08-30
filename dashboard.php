<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: default.php");
    exit();
}
include('includes/db.php');
include('includes/header.php');
include('includes/sidebar.php');
?>


<div class="container p-4">
    <h1>Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>
    <p>Votre rôle : <?= htmlspecialchars($_SESSION['rank']) ?></p>
</div>

</body>
</html>
