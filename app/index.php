<?php
require 'db.php';

$tasks = $pdo->query("SELECT * FROM tasks ORDER BY created_at ASC")->fetchAll();
$statuses = ['todo' => 'To Do', 'inprogress' => 'In Progress', 'done' => 'Done'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kanban Board</title>
    <style>
        .board { display: flex; gap: 20px; }
        .column { border: 1px solid #ccc; padding: 10px; width: 300px; }
        .task { background: #f4f4f4; margin: 5px 0; padding: 5px; }
    </style>
</head>
<body>
<h1>Kanban Board 2.0 UPDATE</h1>
<div class="board">
<?php foreach($statuses as $key => $label): ?>
    <div class="column">
        <h2><?= $label ?></h2>
        <?php foreach($tasks as $task): ?>
            <?php if($task['status'] == $key): ?>
                <div class="task">
            <?= htmlspecialchars($task['title']) ?>
            
            <!-- Move task -->
            <form method="post" action="move_task.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= $task['id'] ?>">
                <select name="status">
                    <?php foreach($statuses as $s_key => $s_label): ?>
                        <option value="<?= $s_key ?>" <?= $task['status']==$s_key?'selected':'' ?>><?= $s_label ?></option>
                    <?php endforeach; ?>
                </select>
                <button>Move</button>
            </form>

            <!-- Delete task -->
            <form method="post" action="delete_task.php" style="display:inline;">
                <input type="hidden" name="id" value="<?= $task['id'] ?>">
                <button>Delete</button>
            </form>
        </div>

            <?php endif; ?>
        <?php endforeach; ?>
        <form method="post" action="add_task.php">
            <input type="text" name="title" placeholder="New task" required>
            <input type="hidden" name="status" value="<?= $key ?>">
            <button>Add</button>
        </form>
    </div>
<?php endforeach; ?>
</div>
</body>
</html>
