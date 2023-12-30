<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch categories
$categoriesQuery = "SELECT * FROM category";
$categoriesResult = $conn->query($categoriesQuery);

$categories = [];
if ($categoriesResult && $categoriesResult->num_rows > 0) {
    while ($row = $categoriesResult->fetch_assoc()) {
        $categories[] = $row;
    }
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

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
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
        <a href="dashboard.php" class="fs-5 d-block sidebar-hover text-white text-decoration-none py-3 px-5 mt-5" style="width: 100%;">
            Dashboard
        </a>
        <a href="manage-article.php" class="fs-5 d-block sidebar-hover text-white text-decoration-none py-3 px-5" style="width: 100%;">
            Manage Article
        </a>
        <a href="manage-category.php" class="fs-5 d-block sidebar-selected text-white text-decoration-none py-3 px-5" style="width: 100%;">
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
            <a href="./create-category.php" class="d-inline-block fs-5 text-decoration-none bg-success bg-opacity-75 text-white rounded-4 px-4 py-2 mb-3">
                <div class="d-flex align-items-center justify-content-center">
                    <img src="../src/image/icon-create.svg" alt="create data" class="p-1" style="width: 25px; filter: invert(100%) sepia(100%) saturate(0%) hue-rotate(227deg) brightness(111%) contrast(101%);">
                    <p class="p-0 m-0 ms-2">Input Category</p>
                </div>
            </a>

            <table style="width: 100%;">
                <tr>
                    <th class="fw-bold fs-5">No</th>
                    <th class="fw-bold fs-5">Category Name</th>
                    <th class="fw-bold fs-5">Action</th>
                </tr>
                <?php foreach ($categories as $index => $category) : ?>
                    <tr>
                        <td style="col-2"><?php echo $index + 1; ?></td>
                        <td class="col-8"><?php echo $category['category_name']; ?></td>
                        <td class="d-flex flex-nowrap">
                            <a href="./edit-category.php?id=<?php echo $category['id_category'] ?>" class="fs-5 d-inline-block text-decoration-none text-white bg-warning bg-opacity-75 rounded-4 px-4 py-2 me-2">
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="../src/image/icon-edit.svg" alt="icon edit" class="p-1" style="width: 30px; filter: invert(100%) sepia(100%) saturate(0%) brightness(111%) contrast(101%);">
                                    <p class="p-0 m-0 ms-1">Edit</p>
                                </div>
                            </a>
                            <div class="fs-5 d-flex align-items-center justify-content-center text-decoration-none text-white bg-danger bg-opacity-75 rounded-4 px-4 py-2" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $category['id_category']; ?>" style="cursor: pointer;">
                                <img src="../src/image/icon-delete.svg" alt="icon delete" class="p-1" style="width: 30px; filter: invert(100%) sepia(100%) saturate(0%) hue-rotate(227deg) brightness(111%) contrast(101%);">
                                <p class="p-0 m-0 ms-1">Delete</p>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <!-- Main Section End -->

        <!-- Delete Modal Section Start-->
        <div class="modal fade" id="deleteModal<?php echo $category['id_category']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete the category "<?php echo $category['category_name']; ?>"?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="./delete-category.php?id=<?php echo $category['id_category'] ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Modal Section Start -->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>