<?php
session_start();

// Cek apakah pengguna sudah login
if (isset($_SESSION['id_admin'])) {
    header("Location: admin.php");
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
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);

    $sql = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Assign user details to session variables
        $_SESSION['id_admin'] = $user['id_admin'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];

        header("Location: admin.php");
        exit();
    } else {
        $error_message = "Username or password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <h2>Login</h2>

    <?php
    // Tampilkan pesan kesalahan (jika ada)
    if (isset($error_message)) {
        echo "<p>$error_message</p>";
    }
    ?>

    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <br>

        <input type="submit" value="Login">
    </form>
</body>

</html>