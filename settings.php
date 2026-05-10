<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signin.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];
$message = "";
$message_class = "";

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $bio = $_POST["bio"];
    $new_password = $_POST["new_password"];

    if ($new_password != "") {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE users 
                SET fullname = ?, email = ?, bio = ?, password_hash = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $fullname, $email, $bio, $password_hash, $current_user_id);
    } else {
        $sql = "UPDATE users 
                SET fullname = ?, email = ?, bio = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $fullname, $email, $bio, $current_user_id);
    }

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        $message_class = "success";

        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $message = "Update failed!";
        $message_class = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings — SocialNet</title>
    <meta name="description" content="Edit your SocialNet account settings and profile information.">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <span class="navbar-brand">✦ SocialNet</span>
    <a href="home.php">🏠 Home</a>
    <a href="profile.php?id=<?php echo $current_user_id; ?>">👤 Profile</a>
    <a class="active" href="settings.php">⚙️ Settings</a>
    <a href="signout.php">🚪 Logout</a>
</div>

<div class="form-box" style="max-width:540px;">
    <div class="form-logo">
        <div class="form-logo-icon">⚙️</div>
    </div>
    <h1>Account Settings</h1>
    <p class="subtitle">Update your personal information</p>

    <?php if ($message != "") { ?>
        <div class="message <?php echo $message_class; ?>">
            <?php echo $message_class === 'success' ? '✅' : '⚠️'; ?>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form method="post" action="settings.php">
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input id="fullname" type="text" name="fullname" value="<?php echo htmlspecialchars($user["fullname"]); ?>" placeholder="Your full name">
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>" placeholder="your@email.com">
        </div>

        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" placeholder="Tell people a little about yourself..."><?php echo htmlspecialchars($user["bio"]); ?></textarea>
        </div>

        <div class="form-group">
            <label for="new_password">New Password</label>
            <input id="new_password" type="password" name="new_password" placeholder="Leave blank to keep current password" autocomplete="new-password">
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="button" type="submit" style="flex:1;justify-content:center;">💾 Save Changes</button>
            <a class="button button-outline" href="profile.php?id=<?php echo $current_user_id; ?>" style="flex:1;justify-content:center;text-align:center;">View Profile →</a>
        </div>
    </form>
</div>

</body>
</html>
