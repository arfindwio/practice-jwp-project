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

$category_id = null;

if (isset($_GET['id'])) {
    $category_id = clean_input($_GET['id']);

    $select_query = "SELECT * FROM category WHERE id_category = $category_id";
    $result = $conn->query($select_query);

    if ($result->num_rows != 1) {
        echo "Category not found.";
        exit();
    }

    $category = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = clean_input($_POST['category_name']);

    // Check if the updated category name already exists
    $check_query = "SELECT * FROM category WHERE category_name = '$category_name' AND id_category != $category_id";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        echo "Category with the same name already exists.";
    } else {
        // Update the category
        $update_query = "UPDATE category SET category_name = '$category_name' WHERE id_category = $category_id";

        if ($conn->query($update_query) === TRUE) {
            echo "Category successfully updated!";
        } else {
            echo "Error updating category: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
</head>

<body>
    <h2>Edit Category</h2>

    <form method="post" action="edit-category.php?id=<?php echo $category_id; ?>">
        <label for="category_name">Category Name:</label>
        <input type="text" id="category_name" name="category_name" value="<?php echo isset($category['category_name']) ? $category['category_name'] : ''; ?>" required>

        <br>

        <input type="submit" value="Update">
    </form>
</body>

</html>