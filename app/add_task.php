<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("INSERT INTO tasks (title, status) VALUES (?, ?)");
    $stmt->execute([$title, $status]);
}

header('Location: index.php');
?>
