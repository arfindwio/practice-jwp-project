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
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');

    $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

    if (!in_array($imageFileType, $allowedExtensions)) {
        header("Location: create-article.php");
        exit();
    }

    $hash = hash('sha256', uniqid(mt_rand(), true));
    $targetFile = $hash . '.' . $imageFileType;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], "../src/image/" . $targetFile)) {
        return $targetFile;
    }

    header("Location: create-article.php");
    exit();
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

    if ($image === null) {
        if (isset($_GET['error']) && $_GET['error'] === 'InvalidFileExtension') {
            header("Location: create-article.php");
            exit();
        } elseif (isset($_GET['error']) && $_GET['error'] === 'FailedToUploadImage') {
            header("Location: create-article.php");
            exit();
        } else {
            header("Location: create-article.php");
            exit();
        }
    }

    insertArticle($conn, $title, $content, $image, $categoryId, $status, $userId);

    header("Location: manage-article.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .sidebar-hover:hover {
            background-color: rgba(206, 212, 218, .3);

        }

        .sidebar-selected {
            background-color: rgba(206, 212, 218, .7);

        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        .text-back {
            background-color: rgba(248, 249, 250, 1);
            color: black !important;
        }

        .text-back:hover {
            background-color: rgba(211, 212, 213, 1);
            color: black !important;
        }

        .text-submit {
            background-color: rgba(13, 109, 253, 1);
        }

        .text-submit:hover {
            background-color: rgba(13, 109, 253, .7);
        }
    </style>
</head>

<body>
    <!-- Sidebar Section Start -->
    <div class="col-2 bg-dark" style="position: fixed;height: 100vh;">
        <a href="dashboard.php" class="d-flex justify-content-center align-items-center text-decoration-none text-white text-center py-4 p-0 m-0">
            <img src="../src/image/logo-magz.svg" alt="logo" style="width: 50px;">
            <span class="fs-2 fw-bold ms-2">ArfinMagz</span>
        </a>
        <a href="dashboard.php" class="fs-5 d-block sidebar-hover text-white text-decoration-none py-3 px-5 mt-5" style="width: 100%;">
            Dashboard
        </a>
        <a href="manage-article.php" class="fs-5 d-block sidebar-selected text-white text-decoration-none py-3 px-5" style="width: 100%;">
            Manage Article
        </a>
        <a href="manage-category.php" class="fs-5 d-block sidebar-hover text-white text-decoration-none py-3 px-5" style="width: 100%;">
            Manage Category
        </a>
        <a href="logout.php" class="fs-5 d-block sidebar-hover text-white text-decoration-none py-3 px-5" style="width: 100%;">
            Logout
        </a>
    </div>
    <!-- Sidebar Section End -->

    <div class="col-10 float-end">
        <!-- Navbar Section Start -->
        <nav class="bg-dark-subtle shadow-md">
            <div class="container-fluid px-5 py-4">
                <p class="text-secondary fs-3 fw-bold p-0 py-1 m-0">Hi, <?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin'; ?>!</p>
            </div>
        </nav>
        <!-- Navbar Section Start -->

        <!-- Main Section Start -->
        <div class="container-fluid px-5 pt-5">

            <!-- Breadcrumb Section Start -->
            <div class="d-flex fs-5 mb-4">
                <a href="manage-article.php" class="d-flex text-decoration-none text-secondary fw-bold p-0 m-0">
                    <img src="../src/image/icon-home.svg" alt="icon home" style="width: 25px;">
                    <p class="m-0 p-0 ms-2">Manage Article </p>
                </a>
                <p class="text-decoration-none text-secondary p-0 m-0 ms-2"><span class="fw-bold">></span> Input Article</p>
            </div>
            <!-- Breadcrumb Section End -->

            <!-- Create Article Section Start -->
            <div class="border border-2 rounded-2 p-5 m-0">
                <h2 class="fs-2 mb-4">Input Article</h2>

                <div>
                    <form method="post" action="create-article.php" enctype="multipart/form-data">
                        <div class="d-flex flex-column mb-3">
                            <label for="title" class="fs-4">Title:</label>
                            <input type="text" id="title" name="title" class="form-control" style="width: 50%" placeholder="Input article title" required>
                        </div>
                        <div class="d-flex flex-column mb-3">
                            <label for="content" class="fs-4">Content:</label>
                            <textarea id="content" name="content" class="form-control" style="width: 50%" placeholder="Input article content" required></textarea>
                        </div>
                        <div class="d-flex flex-column mb-3">
                            <label for="image" class="fs-4">Image</label>
                            <input type="file" id="image" name="image" accept="image/*" class="form-control" style="width: 50%" required>
                        </div>
                        <div class="d-flex flex-column mb-3">
                            <label for="category_id" class="fs-4">Category</label>
                            <select name="category_id" id="category_id" class="form-select" style="width: 50%" aria-label="Default select example">
                                <option selected hidden>Choose Category</option>

                                <?php
                                $categories = getCategories($conn);
                                foreach ($categories as $category) {
                                    echo "<option value={$category['id_category']}>{$category['category_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="d-flex flex-column mb-3">
                            <label for="status" class="fs-4">Status:</label>
                            <div class="d-flex align-items-center">
                                <p class="p-0 m-0 me-3 fs-5">Draft</p>
                                <div class="form-check form-switch d-flex">
                                    <input type="checkbox" id="status" name="status" class="form-check-input" role="switch" id="flexSwitchCheckDefault" style="height: 30px; width: 60px">
                                </div>
                                <p class="p-0 m-0 ms-3 fs-5">Publish</p>
                            </div>
                        </div>

                        <a class="text-back text-decoration-none border border-2 border-subtle fw-semibold rounded-3 py-2 px-5" href="manage-article.php">Back</a>
                        <input type="submit" value="Submit" class="text-submit text-white rounded-3 border border-1 border-white py-2 px-5">
                    </form>
                </div>
            </div>
            <!-- Create Article Section Start -->

        </div>
        <!-- Main Section End -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>