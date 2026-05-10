<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: /socialnet/index.php");
    exit();
}

require_once '../db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $sql = "SELECT * FROM account WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password_hash"])) {
            $_SESSION["user_id"]   = $user["id"];
            $_SESSION["username"]  = $user["username"];
            $_SESSION["fullname"]  = $user["fullname"];
            header("Location: /socialnet/index.php");
            exit();
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "No account found with that username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In · SocialNet</title>
    <meta name="description" content="Sign in to your SocialNet account.">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="navbar">
    <span class="navbar-brand">✦ SocialNet</span>
    <a class="active" href="/socialnet/signin.php">Sign In</a>
    <a href="/admin/newuser.php">Admin</a>
</div>

<div class="form-box">
    <div class="form-logo">
        <div class="form-logo-icon">✦</div>
    </div>
    <h1>Welcome back</h1>
    <p class="subtitle">Sign in to continue to SocialNet</p>

    <?php if ($error !== ""): ?>
        <div class="message error">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="/socialnet/signin.php">
        <div class="form-group">
            <label for="username">Username</label>
            <input id="username" type="text" name="username"
                   placeholder="Enter your username"
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                   required autocomplete="username">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                   placeholder="Enter your password"
                   required autocomplete="current-password">
        </div>

        <button class="button" type="submit"
                style="width:100%;justify-content:center;padding:14px;margin-top:4px;">
            Sign In →
        </button>
    </form>
</div>

</body>
</html>
