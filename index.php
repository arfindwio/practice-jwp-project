<?php
// index.php
include './admin/config.php';

// Handle search query
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Handle category filter
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// Query to fetch data articles
$sqlArticle = "SELECT * FROM article";

// Add search filter if a search query is provided
if (!empty($searchQuery)) {
    $sqlArticle .= " WHERE title LIKE '%$searchQuery%'";
}

// Add category filter if a category is selected
if (!empty($categoryFilter)) {
    if (strpos($sqlArticle, 'WHERE') === false) {
        $sqlArticle .= " WHERE";
    } else {
        $sqlArticle .= " AND";
    }
    $sqlArticle .= " id_category IN (SELECT id_category FROM category WHERE category_name = '$categoryFilter')";
}

// Query to fetch categories
$sqlCategories = "SELECT * FROM category";
$resultCategories = $conn->query($sqlCategories);

// Initialize an array to store categories
$categories = array();

// Check if the query is successful and fetch categories
if ($resultCategories->num_rows > 0) {
    while ($row = $resultCategories->fetch_assoc()) {
        $categories[] = $row;
    }
}

$resultArticle = $conn->query($sqlArticle);

// Check for errors in the query execution
if (!$resultArticle) {
    echo "Error in SQL query: " . $conn->error;
    // You might want to handle the error more gracefully, log it, or redirect the user to an error page.
    exit();
}
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

        .truncate-lines-2 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            -webkit-line-clamp: 2;
            /* Number of lines to show */
            text-overflow: ellipsis;
        }

        /* Adjust search input width on small screens */
        @media only screen and (max-width: 576px) {
            #search {
                width: 100%;
            }
        }

        /* Adjust category list style for small screens */
        @media only screen and (max-width: 767px) {
            .list-category {
                display: block;
                margin-bottom: 10px;
            }
        }

        /* Adjust article card style for small screens */
        @media only screen and (max-width: 767px) {
            .article-card {
                height: auto;
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
        <div class="row">
            <!-- List Category Section Start -->
            <div class="col-12 mb-4 d-lg-none mb-lg-0">
                <div class="d-flex flex-column bg-body-secondary shadow-md rounded-2 py-3">
                    <h1 class="fs-4 fw-semi-bold m-0 mb-3 p-0 px-3">Category List</h1>
                    <?php foreach ($categories as $category) : ?>
                        <a href="?category=<?php echo urlencode($category['category_name']); ?>" class="list-category text-decoration-none text-dark fs-5 m-0 px-3 py-1"><?php echo $category['category_name']; ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- List Category Section End -->
            <!-- Article Section Start -->
            <div class="col-12 col-lg-9">
                <?php
                if ($resultArticle->num_rows > 0) {
                    while ($article = $resultArticle->fetch_assoc()) {
                        $sqlAdmin = "SELECT * FROM admin WHERE id_admin = " . $article['id_admin'];
                        $resultAdmin = $conn->query($sqlAdmin);
                        $admin = $resultAdmin->fetch_assoc();
                        $sqlCategory = "SELECT * FROM category WHERE id_category = " . $article['id_category'];
                        $resultCategory = $conn->query($sqlCategory);
                        $category = $resultCategory->fetch_assoc();
                        if ($article["status"]) {
                ?>
                            <div class="d-flex flex-column flex-lg-row border border-2 border-light-subtle rounded-2 shadow-md mb-3 py-3 px-4 p-lg-0 article-card">
                                <div class="col-2 col-lg-3 mx-auto mx-lg-0">
                                    <img src="./src/image/<?php echo $article['image']; ?>" alt="image-news" class="img-fluid">
                                </div>
                                <div class="col-12 p-0 m-0 col-lg-9 ps-lg-4 py-lg-4">
                                    <h1 class="m-0 p-0 mb-lg-3" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $article['title']; ?></h1>
                                    <p class="fs-5  truncate-lines-2 m-0 mb-3 mb-lg-0 pt-1"><?php echo $article['content'] ?></p>
                                    <div class="d-flex align-items-end m-0 p-0 pt-lg-4 pe-lg-3">
                                        <p class="text-body-tertiary fw-semibold m-0 p-0">
                                            <span class="text-dark fw-bold"><?php echo $admin['name']; ?></span>
                                            <a href="#" class="text-primary text-decoration-none mx-3"><?php echo $category['category_name']; ?></a>
                                            <?php
                                            setlocale(LC_TIME, 'id_ID');
                                            $publishDate = new DateTime($article['publish_date']);
                                            echo strftime('%e %B %Y', $publishDate->getTimestamp());
                                            ?>
                                        </p>
                                        <a href="article-detail.php?id=<?php echo $article['id']; ?>" class="text-decoration-none text-white bg-primary rounded-1 py-1 px-2 py-lg-2 px-lg-3 ms-auto">Lihat Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    }
                } else {
                    ?>
                    <div class="border border-2 border-light-subtle text-center">
                        <h1 class="fs-3 m-0 py-3">There are currently no articles to display.</h1>
                    </div>
                <?php
                }
                ?>
            </div>
            <!-- Article Section End -->


            <!-- List Category Section Start -->
            <div class="d-none d-lg-block col-3">
                <div class="d-flex flex-column bg-body-secondary shadow-md py-3">
                    <h1 class="fs-4 fw-semi-bold m-0 mb-3 p-0 px-3">Category List</h1>
                    <?php foreach ($categories as $category) : ?>
                        <a href="?category=<?php echo urlencode($category['category_name']); ?>" class="list-category text-decoration-none text-dark fs-5 m-0 px-3 py-1"><?php echo $category['category_name']; ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- List Category Section End -->
        </div>
    </section>

    <!-- Main Section End -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
<?php
// Tutup koneksi MySQL setelah selesai mengakses data
$conn->close();
?>