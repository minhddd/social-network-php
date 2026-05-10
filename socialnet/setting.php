<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: /socialnet/signin.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];
$message = "";
$message_class = "";

// Fetch current user data
$sql  = "SELECT * FROM account WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = trim($_POST["description"]);

    $sql  = "UPDATE account SET description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $description, $current_user_id);

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        $message_class = "success";
        $user["description"] = $description; // reflect change immediately
    } else {
        $message = "Update failed: " . $conn->error;
        $message_class = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings · SocialNet</title>
    <meta name="description" content="Edit your SocialNet profile description.">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<!-- MenuBar -->
<div class="navbar">
    <span class="navbar-brand">✦ SocialNet</span>
    <a href="/socialnet/index.php">🏠 Home</a>
    <a class="active" href="/socialnet/setting.php">⚙️ Setting</a>
    <a href="/socialnet/profile.php">👤 Profile</a>
    <a href="/socialnet/about.php">ℹ️ About</a>
    <a href="/socialnet/signout.php">🚪 Sign Out</a>
</div>

<div class="form-box" style="max-width:540px;">
    <div class="form-logo">
        <div class="form-logo-icon">⚙️</div>
    </div>
    <h1>Settings</h1>
    <p class="subtitle">Edit your profile page content</p>

    <?php if ($message !== ""): ?>
        <div class="message <?php echo $message_class; ?>">
            <?php echo $message_class === 'success' ? '✅' : '⚠️'; ?>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="/socialnet/setting.php">
        <div class="form-group">
            <label for="description">Profile Description</label>
            <textarea id="description" name="description"
                      placeholder="Write something about yourself that others will see on your profile..."
                      style="min-height:160px;"><?php echo htmlspecialchars($user["description"] ?? ""); ?></textarea>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="button" type="submit" style="flex:1;justify-content:center;">
                💾 Save Changes
            </button>
            <a class="button button-outline" href="/socialnet/profile.php"
               style="flex:1;justify-content:center;text-align:center;">
                View My Profile →
            </a>
        </div>
    </form>
</div>

</body>
</html>
