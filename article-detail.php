<?php
// article-detail.php
include './admin/config.php';

// Get the article ID from the URL
$articleId = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : '';

// Validate and sanitize the article ID
if (!$articleId) {
    // Redirect the user to the homepage or handle it in another way
    header("Location: index.php");
    exit();
}

// Define $searchQuery
$searchQuery = isset($_GET['search']) ? filter_var($_GET['search'], FILTER_SANITIZE_STRING) : '';

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
    $admin = fetchDetailsById($conn, 'admin', 'id_admin', $adminId);

    $categoryId = $articleDetail['id_category'];

    // Fetch category details
    $category = fetchDetailsById($conn, 'category', 'id_category', $categoryId);

    // Format and display publish date
    setlocale(LC_TIME, 'id_ID');
    $publishDate = new DateTime($articleDetail['publish_date']);

    // Include the HTML header
    includeHtmlHeader();

    // Include the navigation bar
    includeNavbar($searchQuery);

    // Display the article details
    includeArticleDetails($articleDetail, $admin, $category, $publishDate);

    // Include the HTML footer
    includeHtmlFooter();
} else {
    // Include the HTML header
    includeHtmlHeader();

    // Include the navigation bar
    includeNavbar($searchQuery);

    // Display the article not found message
    includeArticleNotFound();

    // Include the HTML footer
    includeHtmlFooter();
}

// Close the MySQL connection
$conn->close();

// Function to fetch details by ID from the database
function fetchDetailsById($conn, $tableName, $idColumn, $idValue)
{
    $sql = "SELECT * FROM $tableName WHERE $idColumn = $idValue";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Function to include HTML header
function includeHtmlHeader()
{
?>
    <!DOCTYPE html>
    <html lang="en">
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
    <?php
}

// Function to include navigation bar
function includeNavbar($searchQuery)
{
    ?>
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
    <?php
}

// Function to display article details
function includeArticleDetails($articleDetail, $admin, $category, $publishDate)
{
    ?>
        <!-- Main Section Start -->
        <section class="container mt-4">
            <!-- Breadcrumb Section Start -->
            <div class="d-flex fs-5 mb-4">
                <a href="index.php" class="d-flex text-decoration-none text-secondary fw-bold p-0 m-0">
                    <img src="./src/image/icon-home.svg" alt="icon home" style="width: 25px;">
                    <p class="m-0 p-0 ms-2">Home </p>
                </a>
                <p class="text-decoration-none text-secondary p-0 m-0 ms-2"><span class="fw-bold">></span> Detail Article</p>
            </div>
            <!-- Breadcrumb Section End -->

            <div class="row">
                <!-- Article Section Start -->
                <div class="col-12">
                    <div class="border border-2 border-light-subtle rounded-2 shadow-md mb-3 py-3 px-4">
                        <div class="d-flex flex-column align-items-center text">
                            <h1 class="text-center fw-bold fs-1 m-0 mb-4 p-0 "><?php echo strtoupper($articleDetail['title']); ?></h1>
                            <p class="text-dark fw-bold m-0 mb-2 p-0"><?php echo $admin['name'] ?> - <span class="fw-semibold text-warning">ArfinMagz</span></p>
                            <div class="d-block d-md-flex text-center text-body-tertiary fw-semibold m-0 mb-3 p-0">
                                <a href="index.php?category=<?php echo urlencode($category['category_name']); ?>" class="text-primary text-decoration-none me-md-2 m-0 p-0"><?php echo $category['category_name'] ?></a>
                                <p class="m-0 p-0"><?php echo strftime('%e %B %Y', $publishDate->getTimestamp()); ?></p>
                            </div>
                            <div class="d-flex col-10 col-md-5 mb-4 mx-md-0">
                                <img src="./src/image/<?php echo $articleDetail['image']; ?>" alt="image-news" class="w-100">
                            </div>
                        </div>
                        <p class="fs-5 m-0 pt-1"><?php echo $articleDetail['content']; ?></p>
                    </div>
                </div>
                <!-- Article Section End -->
            </div>
        </section>

        <!-- Main Section End -->
    <?php
}

// Function to display article not found message
function includeArticleNotFound()
{
    ?>
        <section class="container mt-4">

            <!-- Breadcrumb Section Start -->
            <div class="d-flex fs-5 mb-4">
                <a href="index.php" class="d-flex text-decoration-none text-secondary fw-bold p-0 m-0">
                    <img src="./src/image/icon-home.svg" alt="icon home" style="width: 25px;">
                    <p class="m-0 p-0 ms-2">Home </p>
                </a>
                <p class="text-decoration-none text-secondary p-0 m-0 ms-2"><span class="fw-bold">></span> Detail Article</p>
            </div>
            <!-- Breadcrumb Section End -->

            <div class="row">
                <!-- Article Section Start -->
                <div class="col-12">
                    <div class="border border-2 border-light-subtle rounded-2 shadow-md mb-3 py-3 px-4">
                        <h1 class="text-center fw-bold fs-3 m-0 p-0">Article Not Found</h1>
                    </div>
                </div>
                <!-- Article Not Found Section End -->
            </div>
        </section>
    <?php
}

// Function to include HTML footer
function includeHtmlFooter()
{
    ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>

    </html>
<?php
}
?>