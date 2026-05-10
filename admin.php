<?php
require_once "db.php";

$message = "";
$message_class = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $bio = $_POST["bio"];

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password_hash, fullname, email, bio)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $password_hash, $fullname, $email, $bio);

    if ($stmt->execute()) {
        $message = "Account created successfully!";
        $message_class = "success";
    } else {
        $message = "Error: " . $conn->error;
        $message_class = "error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <a class="active" href="admin.php">Admin</a>
    <a href="signin.php">Sign in</a>
</div>

<div class="form-box">
    <h1 class="center">Admin Form</h1>
    <p class="center small-text">Create new user account</p>

    <?php if ($message != "") { ?>
        <div class="message <?php echo $message_class; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form method="post" action="admin.php">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Full name:</label>
        <input type="text" name="fullname">

        <label>Email:</label>
        <input type="email" name="email">

        <label>Bio:</label>
        <textarea name="bio"></textarea>

        <button class="button">Create Account</button>
        <a class="button button-blue" href="signin.php">Go to Sign in</a>
    </form>
</div>

</body>
</html>
