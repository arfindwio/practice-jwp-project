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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $category_id = clean_input($_GET['id']);

    // Check if the category exists
    $check_query = "SELECT * FROM category WHERE id_category = $category_id";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows != 1) {
        echo "Category not found.";
        exit();
    }

    // Perform category deletion
    $delete_query = "DELETE FROM category WHERE id_category = $category_id";

    if ($conn->query($delete_query) === TRUE) {
        // Redirect to manage-category.php after successful deletion
        header("Location: manage-category.php");
        exit();
    } else {
        header("Location: manage-category.php");
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}
