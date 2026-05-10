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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — SocialNet</title>
    <meta name="description" content="Sign in to your SocialNet account.">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <span class="navbar-brand">✦ SocialNet</span>
    <a href="admin.php">Admin</a>
    <a class="active" href="signin.php">Sign In</a>
</div>

<div class="form-box">
    <div class="form-logo">
        <div class="form-logo-icon">✦</div>
    </div>
    <h1>Welcome back</h1>
    <p class="subtitle">Sign in to your account to continue</p>

    <?php if ($error != "") { ?>
        <div class="message error">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php } ?>

    <form method="post" action="signin.php">
        <div class="form-group">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Enter your username" required autocomplete="username">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
        </div>

        <button class="button" type="submit" style="width:100%;justify-content:center;padding:14px;">Sign In</button>
    </form>
</div>

</body>
</html>
