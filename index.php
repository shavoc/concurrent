<?php
session_start();
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password FROM customer WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {
            $token = bin2hex(random_bytes(32));
            $update_stmt = $conn->prepare("UPDATE customer SET status = ? WHERE id = ?");
            $update_stmt->bind_param("si", $token, $row['id']);
            $update_stmt->execute();
            $update_stmt->close();
            setcookie("auth_token", $token, time() + 3600, "/", "", true, true);
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id'];
            header("Location: welcome.php");
            exit;
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }

    $stmt->close();
}
$conn->close();
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

<form method="POST" action="">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>

</body>
</html>
