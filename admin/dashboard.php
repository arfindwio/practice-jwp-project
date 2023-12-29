<?php
include 'config.php';  // Include the database connection file
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch Article Count
$articleCountQuery = "SELECT COUNT(*) AS article_count FROM article";
$articleCountResult = $conn->query($articleCountQuery);

if ($articleCountResult) {
    $articleCountRow = $articleCountResult->fetch_assoc();
    $articleCount = $articleCountRow['article_count'];
} else {
    $articleCount = 0; // Default value if the query fails
}

$categoryCountQuery = "SELECT COUNT(*) AS category_count FROM category";
$categoryCountResult = $conn->query($categoryCountQuery);

if ($categoryCountResult) {
    $categoryCountRow = $categoryCountResult->fetch_assoc();
    $categoryCount = $categoryCountRow['category_count'];
} else {
    $categoryCount = 0;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .sidebar-hover:hover {
            background-color: rgba(206, 212, 218, .3);

        }

        .sidebar-selected {
            background-color: rgba(206, 212, 218, .7);

        }
    </style>
</head>

<body>
    <!-- Sidebar Section Start -->
    <div class="col-2 bg-dark" style="position: fixed;height: 100vh;">
        <a href="index.php" class="d-flex justify-content-center align-items-center text-decoration-none text-white text-center py-4 p-0 m-0">
            <img src="../src/image/logo-magz.svg" alt="logo" style="width: 50px;">
            <span class="fs-2 fw-bold ms-2">ArfinMagz</span>
        </a>
        <a href="dashboard.php" class="fs-5 d-block sidebar-hover sidebar-selected text-white text-decoration-none py-3 px-5 mt-5" style="width: 100%;">
            Dashboard
        </a>
        <a href="manage-article.php" class="fs-5 d-block sidebar-hover text-white text-decoration-none py-3 px-5" style="width: 100%;">
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
        <div class="container-fluid d-flex px-5 pt-5">
            <div class="col-4 px-2">
                <div class="bg-success-subtle d-flex align-items-center rounded-3 py-4 px-4">
                    <img src="../src/image/icon-category.svg" alt="logo article" class="object-fit-cover" style="width: 40%;">
                    <div class="ps-4 p-0 m-0">
                        <p class="fs-3 fw-bold p-0 m-0"><?php echo $categoryCount; ?></p>
                        <p class="fs-3 fw-bold p-0 m-0">Category</p>
                    </div>
                </div>
            </div>
            <div class="col-4 px-2">
                <div class="bg-success-subtle d-flex align-items-center rounded-3 py-4 px-4">
                    <img src="../src/image/icon-article.svg" alt="logo article" class="object-fit-cover" style="width: 40%;">
                    <div class="ps-2 p-0 m-0">
                        <p class="fs-3 fw-bold p-0 m-0"><?php echo $articleCount; ?></p>
                        <p class="fs-3 fw-bold p-0 m-0">Articles</p>
                    </div>
                </div>
            </div>

        </div>
        <!-- Main Section End -->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>