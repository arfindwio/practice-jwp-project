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

function getArticleById($conn, $articleId)
{
    $articleQuery = "SELECT * FROM article WHERE id = ?";
    $stmt = $conn->prepare($articleQuery);
    $stmt->bind_param("i", $articleId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}

function updateArticle($conn, $articleId, $title, $content, $image, $categoryId, $status)
{
    // Check if status is false and set publish_date to null
    $publishDate = ($status == 1) ? 'NOW()' : 'NULL';

    $updateQuery = "UPDATE article SET title = ?, content = ?, image = ?, id_category = ?, status = ?, publish_date = $publishDate, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssiii", $title, $content, $image, $categoryId, $status, $articleId);

    if ($stmt->execute()) {
        echo "Data Artikel berhasil diupdate!";
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

$articleData = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $articleId = cleanInput($conn, $_GET['id']);
        $articleData = getArticleById($conn, $articleId);
    }

    if ($articleData === null) {
        echo "Article not found.";
        exit();
    }

    // Display the HTML form only when $articleData is available
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

        <form method="post" action="edit-article.php?id=<?php echo $articleData['id']; ?>" enctype="multipart/form-data">
            <!-- Hidden input to store the article ID -->
            <input type="hidden" name="article_id" value="<?php echo $articleData['id']; ?>">
            <!-- Hidden input to store the existing image URL -->
            <input type="hidden" name="existing_image" value="<?php echo $articleData['image']; ?>">

            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo $articleData['title']; ?>" required>

            <br>

            <label for="content">Content:</label>
            <textarea id="content" name="content" required><?php echo $articleData['content']; ?></textarea>

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
                    $selected = ($category['id_category'] == $articleData['id_category']) ? "selected" : "";
                    echo "<option value={$category['id_category']} $selected>{$category['category_name']}</option>";
                }
                ?>
            </select>

            <br>

            <label for="status">Status:</label>
            <input type="checkbox" id="status" name="status" <?php echo ($articleData['status'] == 1) ? "checked" : ""; ?>>

            <br>

            <input type="submit" value="Update">
        </form>
    </body>

    </html>
<?php
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleId = cleanInput($conn, $_POST['article_id']);
    $title = cleanInput($conn, $_POST['title']);
    $content = cleanInput($conn, $_POST['content']);
    $categoryId = cleanInput($conn, $_POST['category_id']);
    $status = isset($_POST['status']) ? 1 : 0;

    // Check if a new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        // If a new image is uploaded, use the uploaded image
        $image = uploadImage();
    } else {
        // If no new image is uploaded, use the existing image URL
        $image = cleanInput($conn, $_POST['existing_image']);
    }

    updateArticle($conn, $articleId, $title, $content, $image, $categoryId, $status);
}
?>