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

$article_id = null;

if (isset($_GET['id'])) {
    $article_id = clean_input($_GET['id']);

    $select_query = "SELECT * FROM article WHERE id = $article_id AND id_admin = {$_SESSION['id_admin']}";
    $result = $conn->query($select_query);

    if ($result->num_rows != 1) {
        echo "Article not found.";
        exit();
    }

    $article = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title']);
    $content = clean_input($_POST['content']);
    $image = clean_input($_POST['image']);
    $category_id = clean_input($_POST['category_id']);
    $status = isset($_POST['status']) ? 1 : 0;

    $now = date('Y-m-d H:i:s');
    $update_query = "UPDATE article SET title = '$title', content = '$content', image = '$image', status = $status, updated_at = '$now', id_category = $category_id ";

    if ($status) {
        $update_query .= ", publish_date = '$now' ";
    } else {
        $update_query .= ", publish_date = NULL ";
    }

    $update_query .= "WHERE id = $article_id";

    if ($conn->query($update_query) === TRUE) {
        echo "Data Artikel berhasil diupdate!";
    } else {
        echo "Error: " . $update_query . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Article</title>
</head>

<body>
    <h2>Edit Article</h2>

    <form method="post" action="edit-article.php?id=<?php echo $article_id; ?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo isset($article['title']) ? $article['title'] : ''; ?>" required>

        <br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required><?php echo isset($article['content']) ? $article['content'] : ''; ?></textarea>

        <br>

        <label for="image">Image URL:</label>
        <input type="text" id="image" name="image" value="<?php echo isset($article['image']) ? $article['image'] : ''; ?>">

        <br>

        <label for="category_id">Category ID:</label>
        <input type="number" id="category_id" name="category_id" value="<?php echo isset($article['id_category']) ? $article['id_category'] : ''; ?>" required>

        <br>

        <label for="status">Status:</label>
        <input type="checkbox" id="status" name="status" <?php echo isset($article['status']) && $article['status'] ? 'checked' : ''; ?>>

        <br>

        <input type="submit" value="Update">
    </form>
</body>

</html>