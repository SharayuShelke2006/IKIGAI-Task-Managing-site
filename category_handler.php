<?php
include("includes/db.php");

if (isset($_POST['add_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $query = "INSERT INTO categories (name) VALUES ('$name')";
    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
    } else {
        echo "Error adding category.";
    }
}
?>
