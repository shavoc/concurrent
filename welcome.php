<?php
include('db_connect.php');

// Check if the user is logging out
if (isset($_POST['logout'])) {
    if (isset($_COOKIE['auth_token'])) {
        // Remove token from database
        $token = $_COOKIE['auth_token'];
        $stmt = $conn->prepare("UPDATE customer SET status = NULL WHERE status = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->close();

        // Clear the auth_token cookie
        setcookie("auth_token", "", time() - 3600, "/", "", true, true);
    }

    // Destroy the session and redirect to login
    session_start();
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

// Check if the auth_token cookie exists
if (isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];
    $stmt = $conn->prepare("SELECT id, username FROM customer WHERE status = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "Welcome, " . htmlspecialchars($row['username']) . "!";
    } else {
        // Token is invalid, clear the cookie and redirect to login
        setcookie("auth_token", "", time() - 3600, "/", "", true, true);
        header("Location: index.php");
        exit;
    }

    $stmt->close();
} else {
    echo "No session found. Please log in.";
    header("Location: index.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body>

<h2>Welcome Page</h2>

<form method="POST" action="">
    <button type="submit" name="logout">Logout</button>
</form>

</body>
</html>
