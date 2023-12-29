<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');

function clean_input($data)
{
    global $conn;
    return htmlspecialchars(stripslashes(trim($conn->real_escape_string($data))));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = clean_input($_POST['category_name']);

    // Check if the category name already exists
    $check_query = "SELECT * FROM category WHERE category_name = '$category_name'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        echo "Category with the same name already exists.";
    } else {
        // Insert the new category
        $insert_query = "INSERT INTO category (category_name) VALUES ('$category_name')";

        if ($conn->query($insert_query) === TRUE) {
            echo "Category successfully created!";
        } else {
            echo "Error creating category: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Category</title>
</head>

<body>
    <h2>Create Category</h2>

    <form method="post" action="create-category.php">
        <label for="category_name">Category Name:</label>
        <input type="text" id="category_name" name="category_name" required>

        <br>

        <input type="submit" value="Create">
    </form>
</body>

</html>