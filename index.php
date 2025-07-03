<?php include("includes/db.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

<div class="d-flex" id="wrapper">



  <!-- Sidebar -->
  <div class="border-end bg-gradient" id="sidebar-wrapper">
      <button class="btn btn-outline-light mb-3" id="menu-toggle" style="background: black;">☰</button>
    <div class="sidebar-heading text-white fw-bold">Task Categories</div>
    <div class="list-group list-group-flush" id="categoryList">
      <?php
      $result = mysqli_query($conn, "SELECT * FROM categories");
      while($row = mysqli_fetch_assoc($result)) {
        echo "<a href='?category_id={$row['id']}' class='list-group-item list-group-item-action'>{$row['name']}</a>";
      }
      ?>
    </div>
    <div class="p-3">
      <button class="btn btn-outline-light w-100" data-bs-toggle="modal" data-bs-target="#addCategoryModal">+ Add Category</button>
    </div>
  </div>

  <!-- Page Content -->
  <div id="page-content-wrapper" class="w-100 p-4">
    <h2> <img src="TASKMANAGERLOGO.png" alt="" width="40px" height="40px" style="font-family:fantasy">  IKIGAI</h2>
  <form class="row g-2 align-items-end mb-3" method="GET" action="index.php">
  <div class="col-auto">
    <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="<?php echo $_GET['search'] ?? ''; ?>" style="width: 450px;">
  </div>
  <div class="col-auto">
    <input type="month" name="month" class="form-control" value="<?php echo $_GET['month'] ?? ''; ?>" style="width: 280px;">
  </div>
  <div class="col-auto">
    <button class="btn btn-primary" style="color: white;">Search</button>
  </div>
</form>




    <div class="d-flex justify-content-end mb-3">
      <a href="add_task.php" class="btn btn-primary">+ Add Task</a>
    </div>

    <div class="row">
      <?php
      $category_id = $_GET['category_id'] ?? null;
        $search = $_GET['search'] ?? '';
       $filters = [];

      if ($category_id) $filters[] = "category_id = $category_id";
      if (!empty($search)) $filters[] = "(task LIKE '%$search%' OR assigned_to LIKE '%$search%')";

      // New: Month filter
      if (!empty($_GET['month'])) {
        $month = $_GET['month']; // format: YYYY-MM
        $filters[] = "DATE_FORMAT(due_date, '%Y-%m') = '$month'";
      }

        $where = $filters ? 'WHERE ' . implode(' AND ', $filters) : '';
        $tasks = mysqli_query($conn, "SELECT * FROM tasks $where ORDER BY pinned DESC, created_at DESC");

      while($task = mysqli_fetch_assoc($tasks)) {
        $today = date('Y-m-d');
        $isOverdue = !$task['completed'] && $task['due_date'] < $today;
        $overdueClass = $isOverdue ? "overdue-task" : "";

       echo "
<div class='col-md-4'>
  <div class='card mb-3 shadow-sm position-relative'>
    <div class='card-body " . ($task['completed'] ? "completed-task" : "")  .$overdueClass. "'>
      <h5 class='card-title d-flex justify-content-between align-items-center'>
        <span>" . htmlspecialchars($task['task']) . "</span>
        <form method='POST' action='task_handler.php' class='ms-2'>
          <input type='hidden' name='task_id' value='" . $task['id'] . "'>
          <button name='toggle_pin' class='btn btn-sm " . ($task['pinned'] ? "btn-warning" : "btn-outline-secondary") . "' title='Pin'>
            📌
          </button>
        </form>
      </h5>
      <p class='card-text'>Assigned to: " . htmlspecialchars($task['assigned_to']) . "</p>
      <p class='card-text'>Due: " . htmlspecialchars($task['due_date']) . "</p>
      <p class='card-text text-muted'>" . htmlspecialchars($task['remark']) . "</p>

      <form method='POST' action='task_handler.php' class='d-flex justify-content-between align-items-center'>
  <input type='hidden' name='task_id' value='" . $task['id'] . "'>
  <input type='hidden' name='toggle_complete' value='1'> <!-- Always tells backend this is a complete toggle -->

  <div>
    <input type='checkbox' name='completed' onchange='this.form.submit()' " . ($task['completed'] ? "checked" : "") . ">
    <label class='ms-1'>Completed</label>
  </div>


        <div class='d-flex gap-2'>
          <a href='edit_task.php?id=" . $task['id'] . "' class='btn btn-sm btn-outline-primary'>✏️</a>
          <button name='delete_task' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Delete this task?\")'>❌</button>
        </div>
      </form>
    </div>
  </div>
</div>
";


      }
      ?>
    </div>
  </div>
</div>


<!-- Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="category_handler.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="category_name" class="form-control" placeholder="Category Name" required>
      </div>
      <div class="modal-footer">
        <button type="submit" name="add_category" class="btn btn-primary">Add</button>
      </div>
    </form>
  </div>
</div>
<!-- Floating Today's Tasks Panel -->
<div id="todays-tasks-panel">
  <h5 class="text-white px-3 pt-3 mb-2">📅 Today's Tasks</h5>
  <div class="scroll-area px-3 pb-3">
    <?php
    $today = date('Y-m-d');
    $todayTasks = mysqli_query($conn, "SELECT * FROM tasks WHERE due_date = '$today' ORDER BY pinned DESC, created_at DESC");

    if (mysqli_num_rows($todayTasks) === 0) {
      echo "<p class='text-white-50'>No tasks due today.</p>";
    }

    while ($task = mysqli_fetch_assoc($todayTasks)) {
      echo "
      <div class='card mb-3'>
        <div class='card-body small " . ($task['completed'] ? "completed-task" : "") . "'>
          <h6 class='card-title d-flex justify-content-between'>
            <span>" . htmlspecialchars($task['task']) . "</span>
            " . ($task['pinned'] ? "📌" : "") . "
          </h6>
          <p class='mb-1'>To: " . htmlspecialchars($task['assigned_to']) . "</p>
          <p class='text-muted small mb-0'>" . htmlspecialchars($task['remark']) . "</p>
        </div>
      </div>
      ";
    }
    ?>
  </div>
</div>

<script>
 
  document.getElementById("menu-toggle").addEventListener("click", function () {
    document.getElementById("wrapper").classList.toggle("toggled");
  });
</script>

</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Ask permission for notifications
    if (Notification.permission !== "granted") {
      Notification.requestPermission();
    }

    // Get last notification time from localStorage
    const lastNotified = localStorage.getItem("lastTaskNotify");
    const now = Date.now();

    // Only show notifications if 1 hour has passed
    const ONE_HOUR = 3600000;

    if (!lastNotified || (now - parseInt(lastNotified)) > ONE_HOUR) {
      const todayTasks = <?php
        $today = date('Y-m-d');
        $todayTasks = mysqli_query($conn, "SELECT task, assigned_to FROM tasks WHERE due_date = '$today' AND completed = 0");
        $jsArray = [];

        while ($task = mysqli_fetch_assoc($todayTasks)) {
          $jsArray[] = [
            'task' => $task['task'],
            'assigned_to' => $task['assigned_to']
          ];
        }

        echo json_encode($jsArray);
      ?>;

      if (Notification.permission === "granted") {
        todayTasks.forEach(task => {
          new Notification("🕒 Task Due Today", {
            body: `${task.task} (Assigned to: ${task.assigned_to})`,
            icon: "https://cdn-icons-png.flaticon.com/512/1827/1827370.png"
          });
        });

        // Save the current timestamp after sending notifications
        localStorage.setItem("lastTaskNotify", now.toString());
      }
    }
  });
</script>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
