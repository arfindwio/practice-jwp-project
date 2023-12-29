<?php
session_start();

// Cek apakah pengguna sudah login
if (isset($_SESSION['id_admin'])) {
    header("Location: dashboard.php");
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

        header("Location: dashboard.php");
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="shadow-lg border p-5  rounded-4">
            <h2 class="text-center">Login Admin</h2>

            <?php
            if (isset($error_message)) {
                echo "<p class='text-danger'>$error_message</p>";
            }
            ?>

            <form method="post" action="login.php" class="pt-4">
                <div class="mb-3">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>