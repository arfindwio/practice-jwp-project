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
            header("Location: manage-category.php");
            exit();
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
            <div class="border border-2 rounded-2 p-5 m-0">
                <h2 class="fs-2 mb-4">Edit Category</h2>

                <div>
                    <form method="post" ction="edit-category.php?id=<?php echo $category_id; ?>">
                        <div class="d-flex flex-column mb-3">
                            <label for="category_name" class="fs-4">Category Name:</label>
                            <input type="text" id="category_name" name="category_name" class="form-control" style="width: 50%" placeholder="Input category name" value="<?php echo isset($category['category_name']) ? $category['category_name'] : ''; ?>" required>
                        </div>

                        <a class="text-back text-decoration-none border border-2 border-subtle fw-semibold rounded-3 py-2 px-5" href="manage-category.php">Back</a>
                        <input type="submit" value="Update" class="text-submit text-white rounded-3 border border-1 border-white py-2 px-5">
                    </form>
                </div>
            </div>
        </div>
        <!-- Main Section Start -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>