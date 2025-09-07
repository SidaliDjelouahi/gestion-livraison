<?php
include 'includes/db.php';

$q = "%" . $_GET['q'] . "%";

// Préparer la requête pour chercher dans "users" uniquement ceux qui ont rank = 'user'
$stmt = $conn->prepare("SELECT id, username, rank 
                        FROM users 
                        WHERE rank = 'user' 
                        AND username LIKE ?");
$stmt->execute([$q]);

while($u = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo "<a href='#' class='list-group-item list-group-item-action client-item' 
             data-id='{$u['id']}'>{$u['username']}</a>";
}
?>
