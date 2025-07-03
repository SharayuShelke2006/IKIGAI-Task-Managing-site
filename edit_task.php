<?php
include("includes/db.php");

// Fetch task by ID
if (!isset($_GET['id'])) {
    die("Task ID is missing.");
}
$task_id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM tasks WHERE id = $task_id");
$task = mysqli_fetch_assoc($result);

if (!$task) {
    die("Task not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
      form {
        color: black;}
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Edit Task</h2>
    <form method="POST" action="task_handler.php" class="p-4 bg-white rounded shadow">
        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">

        <div class="mb-3">
            <label class="form-label">Task</label>
            <input type="text" name="task" class="form-control" value="<?php echo htmlspecialchars($task['task']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Assigned To</label>
            <input type="text" name="assigned_to" class="form-control" value="<?php echo htmlspecialchars($task['assigned_to']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo $task['due_date']; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-control" required>
                <?php
                $categories = mysqli_query($conn, "SELECT * FROM categories");
                while ($cat = mysqli_fetch_assoc($categories)) {
                    $selected = $task['category_id'] == $cat['id'] ? "selected" : "";
                    echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Remark</label>
            <textarea name="remark" class="form-control" rows="3"><?php echo htmlspecialchars($task['remark']); ?></textarea>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="completed" id="completed" <?php echo $task['completed'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="completed">Mark as Completed</label>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="pinned" id="pinned" <?php echo $task['pinned'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="pinned">Pin this task</label>
        </div>

        <button type="submit" name="update_task" class="btn btn-primary">Update Task</button>
        <a href="index.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
</body>
</html>
