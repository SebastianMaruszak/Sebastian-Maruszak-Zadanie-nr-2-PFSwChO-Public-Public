<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE tasks SET status=? WHERE id=?");
    $stmt->execute([$status, $id]);
}

header('Location: index.php');
?>
