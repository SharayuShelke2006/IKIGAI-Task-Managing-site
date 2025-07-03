
<?php include("includes/db.php"); ?>
<!DOCTYPE html>
<html>
 
<head>
  <title>Add Task</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
   <style>
    body {
      color: black;
    }
  </style>
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2 style="color:white">Add Task</h2>
  <form action="task_handler.php" method="POST" class="p-4 rounded shadow bg-white">
    <div class="mb-3">
      <label class="form-label">Task</label>
      <input type="text" name="task" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Assigned To</label>
      <input type="text" name="assigned_to" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Due Date</label>
      <input type="date" name="due_date" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Category</label>
      <select name="category_id" class="form-control" required>
        <?php
        $res = mysqli_query($conn, "SELECT * FROM categories");
        while($row = mysqli_fetch_assoc($res)) {
          echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Remark</label>
      <textarea name="remark" class="form-control" rows="3"></textarea>
    </div>
    <button type="submit" name="add_task" class="btn btn-primary">Add Task</button>
  </form>
</div>
</body>
</html>
