<?php
include 'includes/db.php';

$q = "%" . $_GET['q'] . "%";

$stmt = $conn->prepare("SELECT id, name FROM clients WHERE name LIKE ?");
$stmt->execute([$q]);

while($c = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo "<a href='#' class='list-group-item list-group-item-action client-item' data-id='{$c['id']}'>{$c['name']}</a>";
}
?>
