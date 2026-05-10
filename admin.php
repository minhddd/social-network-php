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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — SocialNet</title>
    <meta name="description" content="Admin panel for SocialNet — create new user accounts.">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <span class="navbar-brand">✦ SocialNet</span>
    <a class="active" href="admin.php">🛡️ Admin</a>
    <a href="signin.php">Sign In</a>
</div>

<div class="form-box" style="max-width:540px;">
    <div class="form-logo">
        <div class="form-logo-icon">🛡️</div>
    </div>
    <h1>Admin Panel</h1>
    <p class="subtitle">Create a new user account</p>

    <?php if ($message != "") { ?>
        <div class="message <?php echo $message_class; ?>">
            <?php echo $message_class === 'success' ? '✅' : '⚠️'; ?>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form method="post" action="admin.php">
        <div class="form-group">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Choose a username" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Set a password" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input id="fullname" type="text" name="fullname" placeholder="First and last name">
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email" placeholder="user@example.com">
        </div>

        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" placeholder="Short user bio..."></textarea>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="button" type="submit" style="flex:1;justify-content:center;">✦ Create Account</button>
            <a class="button button-outline" href="signin.php" style="flex:1;justify-content:center;text-align:center;">Go to Sign In →</a>
        </div>
    </form>
</div>

</body>
</html>
