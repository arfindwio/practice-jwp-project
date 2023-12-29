<?php
// index.php
include './admin/config.php';

// Handle search query
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Handle category filter
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// Query untuk mengambil data artikel
$sqlArticle = "SELECT * FROM article";

// Add search filter if a search query is provided
if (!empty($searchQuery)) {
    $sqlArticle .= " WHERE title LIKE '%$searchQuery%'";
}

// Add category filter if a category is selected
if (!empty($categoryFilter)) {
    // Assuming the category name is stored in the 'category_name' column
    $sqlArticle .= " WHERE id_category IN (SELECT id_category FROM category WHERE category_name = '$categoryFilter')";
}


$resultArticle = $conn->query($sqlArticle);

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
            background-color: rgb(206, 212, 218);
        }
    </style>
</head>

<body>
    <!-- Section Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-dark border-bottom border-body" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="./src/image/logo-magz.svg" alt="logo" style="width: 50px;">
                <span class="fs-4 fw-bold">ArfinMagz</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <form class="d-flex ms-auto" role="search" method="GET">
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
            <div class="col-8">

                <?php
                // Periksa apakah query berhasil dijalankan
                if ($resultArticle->num_rows > 0) {
                    // Output data dari setiap baris
                    while ($article = $resultArticle->fetch_assoc()) {
                        // Fetch admin data for the article
                        $sqlAdmin = "SELECT * FROM admin WHERE id_admin = " . $article['id_admin'];
                        $resultAdmin = $conn->query($sqlAdmin);
                        $admin = $resultAdmin->fetch_assoc();

                        // Fetch category data for the article
                        $sqlCategory = "SELECT * FROM category WHERE id_category = " . $article['id_category'];
                        $resultCategory = $conn->query($sqlCategory);
                        $category = $resultCategory->fetch_assoc();

                        if ($article["status"]) {
                ?>
                            <!-- Article Section Start -->
                            <div class="d-flex border border-2 border-light-subtle" style="height: 12rem;">
                                <img src="./src/image/<?php echo $article['image']; ?>" alt="image-news" class="col-4 object-fit-cover">
                                <div class="col-8 ps-3 py-3">
                                    <h1 class="m-0 p-0" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $article['title']; ?></h1>
                                    <!-- Truncate content to 150 characters -->
                                    <?php
                                    $truncatedContent = (strlen($article['content']) > 150) ? substr($article['content'], 0, 150) . '...' : $article['content'];
                                    ?>
                                    <p class="m-0 pt-1"><?php echo $truncatedContent; ?></p>
                                    <div class="d-flex align-items-center m-0 pt-4 pe-3">
                                        <p class="text-body-tertiary fw-semibold m-0 p-0">
                                            <span class="text-dark fw-bold"><?php echo $admin['name']; ?></span> <a href="#" class="text-primary text-decoration-none mx-3"><?php echo $category['category_name']; ?></a> <?php echo $article['publish_date']; ?>
                                        </p>
                                        <a href="#" class="text-decoration-none text-white bg-primary rounded-1 py-1 px-3 ms-auto">Lihat Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                            <!-- Article Section end -->
                        <?php
                        } else {
                        ?>
                            <div class="border border-2 border-light-subtle text-center">
                                <h1 class="fs-3 m-0 py-3">There are currently no articles to display.</h1>
                            </div>
                    <?php
                        }
                    }
                } else {
                    ?>
                    <div class="border border-2 border-light-subtle text-center">
                        <h1 class="fs-3 m-0 py-3">No articles found.</h1>
                    </div>
                <?php
                }
                ?>

            </div>

            <!-- List Category Section Start -->
            <div class="col-3">
                <div class="d-flex flex-column bg-body-secondary py-3">
                    <h1 class="fs-4 fw-semi-bold m-0 mb-3 p-0 px-3">Category List</h1>
                    <?php foreach ($categories as $category) : ?>
                        <a href="?category=<?php echo urlencode($category['category_name']); ?>" class="list-category text-decoration-none text-dark m-0 px-3 py-1"><?php echo $category['category_name']; ?></a>
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