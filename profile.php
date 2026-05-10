<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signin.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];

if (!isset($_GET["id"])) {
    $profile_id = $current_user_id;
} else {
    $profile_id = intval($_GET["id"]);
}

if ($profile_id != $current_user_id) {
    $sql = "SELECT * FROM friendships WHERE user_id = ? AND friend_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $current_user_id, $profile_id);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows == 0) {
        echo "You can only view your own profile or your friend's profile.";
        echo "<br><a href='home.php'>Back to home</a>";
        exit();
    }
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Profile not found.";
    echo "<br><a href='home.php'>Back to home</a>";
    exit();
}

$user = $result->fetch_assoc();
$firstLetter = strtoupper(substr($user["username"], 0, 1));
$isOwnProfile = ($profile_id == $current_user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user["username"]); ?>'s Profile — SocialNet</title>
    <meta name="description" content="View <?php echo htmlspecialchars($user["fullname"]); ?>'s profile on SocialNet.">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <span class="navbar-brand">✦ SocialNet</span>
    <a href="home.php">🏠 Home</a>
    <a class="active" href="profile.php?id=<?php echo $current_user_id; ?>">👤 Profile</a>
    <a href="settings.php">⚙️ Settings</a>
    <a href="signout.php">🚪 Logout</a>
</div>

<div class="container">

    <div class="profile-box">
        <div class="profile-header">
            <div class="avatar avatar-lg"><?php echo htmlspecialchars($firstLetter); ?></div>
            <h1 style="font-size:26px;font-weight:800;letter-spacing:-0.6px;margin-bottom:4px;">
                <?php echo htmlspecialchars($user["username"]); ?>
            </h1>
            <p class="small-text" style="margin-bottom:8px;"><?php echo htmlspecialchars($user["fullname"]); ?></p>
            <?php if ($isOwnProfile): ?>
                <span class="badge">Your Profile</span>
            <?php endif; ?>
        </div>

        <div class="divider"></div>

        <div class="profile-row">
            <strong>Username</strong>
            <span><?php echo htmlspecialchars($user["username"]); ?></span>
        </div>

        <div class="profile-row">
            <strong>Full Name</strong>
            <span><?php echo htmlspecialchars($user["fullname"]) ?: '<em style="opacity:0.4;">Not set</em>'; ?></span>
        </div>

        <div class="profile-row">
            <strong>Email</strong>
            <span><?php echo htmlspecialchars($user["email"]) ?: '<em style="opacity:0.4;">Not set</em>'; ?></span>
        </div>

        <div class="profile-row">
            <strong>Bio</strong>
            <span><?php echo $user["bio"] ? htmlspecialchars($user["bio"]) : '<em style="opacity:0.4;">No bio yet</em>'; ?></span>
        </div>

        <div class="profile-actions">
            <?php if ($isOwnProfile): ?>
                <a class="button" href="settings.php">✏️ Edit Profile</a>
            <?php endif; ?>
            <a class="button button-outline" href="home.php">← Back to Home</a>
        </div>
    </div>

</div>

</body>
</html>
