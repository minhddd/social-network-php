<?php
require_once '../db.php';

$message = "";
$message_class = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $fullname = trim($_POST["fullname"]);

    if ($username === "" || $password === "") {
        $message = "Username and password are required.";
        $message_class = "error";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO account (username, password_hash, fullname) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $password_hash, $fullname);

        if ($stmt->execute()) {
            $message = "User \"" . htmlspecialchars($username) . "\" created successfully!";
            $message_class = "success";
        } else {
            if ($conn->errno === 1062) {
                $message = "Username already exists. Please choose a different one.";
            } else {
                $message = "Error: " . $conn->error;
            }
            $message_class = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Create User · SocialNet</title>
    <meta name="description" content="Admin panel to create new user accounts for SocialNet.">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="navbar">
    <span class="navbar-brand">✦ SocialNet</span>
    <a class="active" href="/admin/newuser.php">🛡️ Admin</a>
    <a href="/socialnet/signin.php">Sign In</a>
</div>

<div class="form-box" style="max-width:520px;">
    <div class="form-logo">
        <div class="form-logo-icon">🛡️</div>
    </div>
    <h1>Create New User</h1>
    <p class="subtitle">Admin panel — add a new account to the system</p>

    <?php if ($message !== ""): ?>
        <div class="message <?php echo $message_class; ?>">
            <?php echo $message_class === 'success' ? '✅' : '⚠️'; ?>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="/admin/newuser.php" autocomplete="off">
        <div class="form-group">
            <label for="username">Username <span style="color:#ef4444;">*</span></label>
            <input id="username" type="text" name="username"
                   placeholder="e.g. john_doe"
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                   required autocomplete="off">
        </div>

        <div class="form-group">
            <label for="password">Password <span style="color:#ef4444;">*</span></label>
            <input id="password" type="password" name="password"
                   placeholder="Set a strong password" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input id="fullname" type="text" name="fullname"
                   placeholder="First and last name"
                   value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;">
            <button class="button" type="submit" style="flex:1;justify-content:center;">
                ✦ Create Account
            </button>
            <a class="button button-outline" href="/socialnet/signin.php"
               style="flex:1;justify-content:center;text-align:center;">
                Go to Sign In →
            </a>
        </div>
    </form>
</div>

</body>
</html>
