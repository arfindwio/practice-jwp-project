<?php
// article-detail.php
include './admin/config.php';

// Get the article ID from the URL
$articleId = isset($_GET['id']) ? $_GET['id'] : '';

// Validate and sanitize the article ID
$articleId = filter_var($articleId, FILTER_VALIDATE_INT);

// Check if the article ID is valid
if (!$articleId) {
    // Redirect the user to the homepage or handle it in another way
    header("Location: index.php");
    exit();
}

// Define $searchQuery
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Validate and sanitize $searchQuery if needed
$searchQuery = filter_var($searchQuery, FILTER_SANITIZE_STRING);

// Query to fetch the specific article
$sqlArticleDetail = "SELECT * FROM article WHERE id = $articleId";
$resultArticleDetail = $conn->query($sqlArticleDetail);

// Check for errors in the query execution
if (!$resultArticleDetail) {
    echo "Error in SQL query: " . $conn->error;
    // You might want to handle the error more gracefully, log it, or redirect the user to an error page.
    exit();
}

// Check if the article exists
if ($resultArticleDetail->num_rows > 0) {
    // Fetch article details
    $articleDetail = $resultArticleDetail->fetch_assoc();

    $adminId = $articleDetail['id_admin'];

    // Fetch admin details
    $sqlAdmin = "SELECT * FROM admin WHERE id_admin = $adminId";
    $resultAdmin = $conn->query($sqlAdmin);
    $admin = $resultAdmin->fetch_assoc();

    $categoryId = $articleDetail['id_category'];

    // Fetch category details
    $sqlCategory = "SELECT * FROM category WHERE id_category = $categoryId";
    $resultCategory = $conn->query($sqlCategory);
    $category = $resultCategory->fetch_assoc();

    // Format and display publish date
    setlocale(LC_TIME, 'id_ID');
    $publishDate = new DateTime($articleDetail['publish_date']);
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <style>
            #search {
                border: none;
                outline: none;
                background-color: transparent;
            }

            #search:focus {
                border: none;
                outline: none;
            }

            .list-category:hover {
                background-color: rgba(206, 212, 218, .4);
            }

            .list-category-selected {
                background-color: rgb(206, 212, 218);
            }

            @media only screen and (max-width: 576px) {
                #search {
                    width: 100%;
                }
            }

            @media only screen and (max-width: 767px) {
                .list-category {
                    display: block;
                    margin-bottom: 10px;
                }
            }
        </style>
    </head>

    <body>
        <!-- Section Navbar Start -->
        <nav class="navbar navbar-expand-xxl bg-dark border-bottom border-body" data-bs-theme="dark">
            <div class="container">
                <a href="index.php" class="navbar-brand d-flex align-items-center" href="#">
                    <img src="./src/image/logo-magz.svg" alt="logo" style="width: 50px;">
                    <span class="fs-4 fw-bold ms-2">ArfinMagz</span>
                </a>
                <div class="d-none d-sm-block">
                    <form class="d-flex ms-auto" action="index.php" role="search" method="GET">
                        <div class="d-flex border border-2 py-1 ps-2 pe-0">
                            <img src="./src//image/icon-search.svg" alt="" style="width: 20px; filter: invert(85%) sepia(100%) saturate(19%) hue-rotate(303deg) brightness(105%) contrast(104%);">
                            <input class="ms-2" id="search" type="search" placeholder="Search" aria-label="Search" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <button type="submit" hidden>Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </nav>
        <!-- Section Navbar End -->

        <!-- Main Section Start -->
        <section class="container mt-4">
            <!-- Breadcrumb Section Start -->
            <div class="d-flex align-items-center fs-5 mb-4">
                <img src="./src/image/icon-home.svg" alt="icon home" style="width: 25px;">
                <a href="index.php" class="text-decoration-none text-secondary fw-bold p-0 m-0 ms-2">Home </a>
                <p href="index.php" class="text-decoration-none text-secondary p-0 m-0 ms-2"><span class="fw-bold">></span> Detail Article</p>
            </div>
            <!-- Breadcrumb Section End -->

            <div class="row">
                <!-- Article Section Start -->
                <div class="col-12">
                    <div class="border border-2 border-light-subtle rounded-2 shadow-md mb-3 py-3 px-4">
                        <div class="d-flex flex-column align-items-center text">
                            <h1 class="text-center fw-bold fs-1 m-0 mb-4 p-0 "><?php echo strtoupper($articleDetail['title']); ?></h1>
                            <p class="text-dark fw-bold m-0 mb-2 p-0"><?php echo $admin['name'] ?> - <span class="fw-semibold text-warning">ArfinMagz</span></p>
                            <p class="text-body-tertiary fw-semibold m-0 mb-3 p-0">
                                <a href="#" class="text-primary text-decoration-none me-2"><?php echo $category['category_name'] ?></a>
                                <?php
                                echo strftime('%e %B %Y', $publishDate->getTimestamp());
                                ?>
                            </p>
                            <div class="col-2 col-md-3 mb-4 mx-md-0">
                                <img src="./src/image/<?php echo $articleDetail['image']; ?>" alt="image-news" class="img-fluid">
                            </div>
                        </div>
                        <p class="fs-5 m-0 pt-1"><?php echo $articleDetail['content']; ?></p>
                    </div>
                </div>
                <!-- Article Section End -->
            </div>
        </section>

        <!-- Main Section End -->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>

    </html>

<?php
} else {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <style>
            #search {
                border: none;
                outline: none;
                background-color: transparent;
            }

            #search:focus {
                border: none;
                outline: none;
            }

            .list-category:hover {
                background-color: rgba(206, 212, 218, .4);
            }

            .list-category-selected {
                background-color: rgb(206, 212, 218);
            }

            @media only screen and (max-width: 576px) {
                #search {
                    width: 100%;
                }
            }

            @media only screen and (max-width: 767px) {
                .list-category {
                    display: block;
                    margin-bottom: 10px;
                }
            }
        </style>
    </head>

    <body>
        <!-- Section Navbar Start -->
        <nav class="navbar navbar-expand-xxl bg-dark border-bottom border-body" data-bs-theme="dark">
            <div class="container">
                <a href="index.php" class="navbar-brand d-flex align-items-center" href="#">
                    <img src="./src/image/logo-magz.svg" alt="logo" style="width: 50px;">
                    <span class="fs-4 fw-bold ms-2">ArfinMagz</span>
                </a>
                <div class="d-none d-sm-block">
                    <form class="d-flex ms-auto" action="index.php" role="search" method="GET">
                        <div class="d-flex border border-2 py-1 ps-2 pe-0">
                            <img src="./src//image/icon-search.svg" alt="" style="width: 20px; filter: invert(85%) sepia(100%) saturate(19%) hue-rotate(303deg) brightness(105%) contrast(104%);">
                            <input class="ms-2" id="search" type="search" placeholder="Search" aria-label="Search" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <button type="submit" hidden>Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </nav>
        <!-- Section Navbar End -->

        <!-- Main Section Start -->
        <section class="container mt-4">
            <!-- Breadcrumb Section Start -->
            <div class="d-flex align-items-center fs-5 mb-4">
                <img src="./src/image/icon-home.svg" alt="icon home" style="width: 25px;">
                <a href="index.php" class="text-decoration-none text-secondary fw-bold p-0 m-0 ms-2">Home </a>
                <p href="index.php" class="text-decoration-none text-secondary p-0 m-0 ms-2"><span class="fw-bold">></span> Detail Article</p>
            </div>
            <!-- Breadcrumb Section End -->

            <div class="row">
                <!-- Article Section Start -->
                <div class="col-12">
                    <div class="border border-2 border-light-subtle rounded-2 shadow-md mb-3 py-3 px-4">
                        <h1 class="text-center fw-bold fs-3 m-0 p-0">Article Not Found</h1>
                    </div>
                </div>
            </div>
            <!-- Article Section End -->
            </div>
        </section>

        <!-- Main Section End -->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>

    </html>
<?php
}

// Close the MySQL connection
$conn->close();
?>