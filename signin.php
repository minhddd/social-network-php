<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: home.php");
    exit();
}

require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password_hash"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];

            header("Location: home.php");
            exit();
        } else {
            $error = "Wrong password!";
        }
    } else {
        $error = "Username not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign in</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <a href="admin.php">Admin</a>
    <a class="active" href="signin.php">Sign in</a>
</div>

<div class="form-box">
    <h1 class="center">Sign in</h1>
    <p class="center small-text">Login to your account</p>

    <?php if ($error != "") { ?>
        <div class="message error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php } ?>

    <form method="post" action="signin.php">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button class="button" type="submit">Login</button>

    </form>
</div>

</body>
</html>
