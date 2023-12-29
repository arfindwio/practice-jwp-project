<?php
session_start();

require_once('config.php');

function redirectToLogin()
{
    header("Location: login.php");
    exit();
}

function cleanInput($conn, $data)
{
    return htmlspecialchars(stripslashes(trim($conn->real_escape_string($data))));
}

function getCategories($conn)
{
    $categories = [];
    $categoryQuery = "SELECT * FROM category";
    $categoryResult = $conn->query($categoryQuery);

    if ($categoryResult->num_rows > 0) {
        while ($category = $categoryResult->fetch_assoc()) {
            $categories[] = $category;
        }
    }

    return $categories;
}

function insertArticle($conn, $title, $content, $image, $categoryId, $status, $userId)
{
    $insertQuery = "INSERT INTO article (title, content, image, status, created_at, id_admin, id_category) 
                     VALUES (?, ?, ?, ?, NOW(), ?, ?)";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sssiis", $title, $content, $image, $status, $userId, $categoryId);

    if ($stmt->execute()) {
        if ($status) {
            $articleId = $stmt->insert_id;
            $publishDate = date('Y-m-d H:i:s');
            $updateQuery = "UPDATE article SET publish_date = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("si", $publishDate, $articleId);
            $updateStmt->execute();
            $updateStmt->close();
        }
        echo "Data Artikel berhasil ditambahkan!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

function uploadImage()
{
    $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $hash = hash('sha256', uniqid(mt_rand(), true));
    $targetFile = $hash . '.' . $imageFileType;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], "../src/image/" . $targetFile)) {
        return $targetFile;
    }

    return null;
}


if (!isset($_SESSION['id_admin'])) {
    redirectToLogin();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = cleanInput($conn, $_POST['title']);
    $content = cleanInput($conn, $_POST['content']);
    $categoryId = cleanInput($conn, $_POST['category_id']);
    $status = isset($_POST['status']) ? 1 : 0;
    $userId = $_SESSION['id_admin'];

    $image = uploadImage();

    insertArticle($conn, $title, $content, $image, $categoryId, $status, $userId);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Article</title>
</head>

<body>
    <h2>Input Article</h2>

    <form method="post" action="create-article.php" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>

        <br>

        <label for="image">Image File:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <br>

        <label for="category_id">Category</label>
        <select name="category_id" id="category_id">
            <option selected hidden>Choose Category</option>

            <?php
            $categories = getCategories($conn);
            foreach ($categories as $category) {
                echo "<option value={$category['id_category']}>{$category['category_name']}</option>";
            }
            ?>
        </select>

        <br>

        <label for="status">Status:</label>
        <input type="checkbox" id="status" name="status">

        <br>

        <input type="submit" value="Submit">
    </form>
</body>

</html>