<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobieramy ID zadania z formularza
    $id = $_POST['id'];

    // Przygotowane zapytanie DELETE
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
}

// Po usunięciu przekierowujemy z powrotem na kanban
header('Location: index.php');
exit;
?>
