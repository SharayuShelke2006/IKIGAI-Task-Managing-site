<?php
include("includes/db.php");

if (isset($_POST['add_task'])) {
    $task = mysqli_real_escape_string($conn, $_POST['task']);
    $assigned_to = mysqli_real_escape_string($conn, $_POST['assigned_to']);
    $due_date = $_POST['due_date'];
    $category_id = $_POST['category_id'];
    $remark = mysqli_real_escape_string($conn, $_POST['remark']);
    $created_at = date("Y-m-d H:i:s");

    $query = "INSERT INTO tasks (task, assigned_to, due_date, category_id, remark, created_at) 
              VALUES ('$task', '$assigned_to', '$due_date', '$category_id', '$remark', '$created_at')";
              
    if (mysqli_query($conn, $query)) {
        header("Location: index.php?category_id=$category_id");
    } else {
        echo "Error adding task.";
    }
}
if (isset($_POST['delete_task'])) {
  $task_id = $_POST['task_id'];
  mysqli_query($conn, "DELETE FROM tasks WHERE id = $task_id");
  header("Location: index.php");
}

if (isset($_POST['toggle_complete'])) {
    $task_id = $_POST['task_id'];
    $completed = isset($_POST['completed']) ? 1 : 0;

    $query = "UPDATE tasks SET completed = $completed WHERE id = $task_id";
    mysqli_query($conn, $query);
    header("Location: index.php");
}


if (isset($_POST['toggle_pin'])) {
  $task_id = $_POST['task_id'];
  mysqli_query($conn, "UPDATE tasks SET pinned = NOT pinned WHERE id = $task_id");
  header("Location: index.php");
}

if (isset($_POST['update_task'])) {
    $id = $_POST['task_id'];
    $task = mysqli_real_escape_string($conn, $_POST['task']);
    $assigned_to = mysqli_real_escape_string($conn, $_POST['assigned_to']);
    $due_date = $_POST['due_date'];
    $category_id = $_POST['category_id'];
    $remark = mysqli_real_escape_string($conn, $_POST['remark']);
    $completed = isset($_POST['completed']) ? 1 : 0;
    $pinned = isset($_POST['pinned']) ? 1 : 0;

    $query = "UPDATE tasks SET 
                task = '$task',
                assigned_to = '$assigned_to',
                due_date = '$due_date',
                category_id = '$category_id',
                remark = '$remark',
                completed = $completed,
                pinned = $pinned
              WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: index.php?category_id=$category_id");
    } else {
        echo "Error updating task.";
    }
}

?>
