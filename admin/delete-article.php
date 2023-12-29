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
    $article_id = clean_input($_GET['id']);

    // Check if the article belongs to the logged-in admin
    $check_query = "SELECT * FROM article WHERE id = $article_id AND id_admin = {$_SESSION['id_admin']}";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows == 1) {
        // Article found, proceed with deletion
        $delete_query = "DELETE FROM article WHERE id = $article_id";

        if ($conn->query($delete_query) === TRUE) {
            echo "Article successfully deleted!";
        } else {
            echo "Error deleting article: " . $conn->error;
        }
    } else {
        echo "Article not found or doesn't belong to the logged-in admin.";
    }
} else {
    echo "Invalid request.";
}
