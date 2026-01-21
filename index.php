<?php
$conn = new mysqli("mysql", "appuser", "apppass", "appdb");

if ($conn->connect_error) {
    die("DB ERROR: " . $conn->connect_error);
}

echo "<h1>Brilliant App 🚀</h1>";
echo "<p>Połączenie z MySQL OK</p>";
