<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id_admin'])) {
    // Jika belum login, arahkan ke halaman login.php
    header("Location: login.php");
    exit();
}

// Sertakan file konfigurasi database
require_once('config.php');

// Fungsi untuk membersihkan input
function clean_input($data)
{
    global $conn; // Add this line to access the database connection
    return htmlspecialchars(stripslashes(trim($conn->real_escape_string($data))));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari formulir input
    $title = clean_input($_POST['title']);
    $content = clean_input($_POST['content']);
    $image = clean_input($_POST['image']); // Anda mungkin ingin menghandle upload file, sesuaikan sesuai kebutuhan
    $category_id = clean_input($_POST['category_id']);
    $status = isset($_POST['status']) ? 1 : 0; // Jika checkbox 'status' dicentang, beri nilai 1, jika tidak, beri nilai 0
    $created_at = date('Y-m-d H:i:s'); // Waktu saat ini

    // Lakukan operasi penambahan data ke tabel article
    $insert_query = "INSERT INTO article (title, content, image, status, created_at, id_admin, id_category) 
                     VALUES ('$title', '$content', '$image', $status, '$created_at', {$_SESSION['id_admin']}, $category_id)";

    if ($conn->query($insert_query) === TRUE) {
        if ($status) {
            // Jika status true, tambahkan data pada publish_date
            $article_id = $conn->insert_id; // Ambil ID artikel yang baru ditambahkan
            $publish_date = date('Y-m-d H:i:s'); // Waktu saat ini
            $update_query = "UPDATE article SET publish_date = '$publish_date' WHERE id = $article_id";
            $conn->query($update_query);
        }
        echo "Data Artikel berhasil ditambahkan!";
    } else {
        echo "Error: " . $insert_query . "<br>" . $conn->error;
    }
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

    <form method="post" action="create-article.php">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>

        <br>

        <label for="image">Image URL:</label>
        <input type="text" id="image" name="image">

        <br>

        <label for="category_id">Category ID:</label>
        <input type="number" id="category_id" name="category_id" required>

        <br>

        <label for="status">Status:</label>
        <input type="checkbox" id="status" name="status">

        <br>

        <input type="submit" value="Submit">
    </form>
</body>

</html>