<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: /socialnet/signin.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];

// Determine profile owner from query string or fall back to logged-in user
if (isset($_GET["owner"]) && trim($_GET["owner"]) !== "") {
    $owner_username = trim($_GET["owner"]);

    $sql  = "SELECT * FROM account WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $owner_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Owner not found — show error
        $not_found = true;
        $owner = null;
    } else {
        $not_found = false;
        $owner = $result->fetch_assoc();
    }
} else {
    // No query string — use logged-in user
    $not_found = false;
    $sql  = "SELECT * FROM account WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $owner = $stmt->get_result()->fetch_assoc();
}

$is_own_profile = ($owner && $owner["id"] == $current_user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $owner ? htmlspecialchars($owner["username"]) . "'s Profile" : "Profile"; ?> · SocialNet</title>
    <meta name="description" content="View user profile on SocialNet.">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<!-- MenuBar -->
<div class="navbar">
    <span class="navbar-brand">✦ SocialNet</span>
    <a href="/socialnet/index.php">🏠 Home</a>
    <a href="/socialnet/setting.php">⚙️ Setting</a>
    <a class="active" href="/socialnet/profile.php">👤 Profile</a>
    <a href="/socialnet/about.php">ℹ️ About</a>
    <a href="/socialnet/signout.php">🚪 Sign Out</a>
</div>

<div class="container">

<?php if ($not_found): ?>
    <div class="profile-box" style="text-align:center;padding:48px 40px;">
        <div style="font-size:48px;margin-bottom:16px;">🔍</div>
        <h1 style="font-size:22px;margin-bottom:10px;">User not found</h1>
        <p class="small-text" style="margin-bottom:24px;">
            No user with the username <strong>"<?php echo htmlspecialchars($_GET['owner']); ?>"</strong> exists.
        </p>
        <a class="button" href="/socialnet/index.php">← Back to Home</a>
    </div>

<?php else: ?>
    <div class="profile-box">
        <div class="profile-header">
            <div class="avatar avatar-lg">
                <?php echo strtoupper(substr($owner["username"], 0, 1)); ?>
            </div>
            <h1 style="font-size:26px;font-weight:800;letter-spacing:-0.6px;margin-bottom:4px;">
                <?php echo htmlspecialchars($owner["username"]); ?>
            </h1>
            <p class="small-text" style="margin-bottom:10px;">
                <?php echo htmlspecialchars($owner["fullname"]) ?: '<em style="opacity:.5;">No full name</em>'; ?>
            </p>
            <?php if ($is_own_profile): ?>
                <span class="badge">✦ Your Profile</span>
            <?php else: ?>
                <span class="badge" style="background:rgba(34,211,238,.12);color:#22d3ee;border-color:rgba(34,211,238,.25);">
                    👤 <?php echo htmlspecialchars($owner["username"]); ?>'s Profile
                </span>
            <?php endif; ?>
        </div>

        <div class="divider"></div>

        <div class="profile-row">
            <strong>Username</strong>
            <span><?php echo htmlspecialchars($owner["username"]); ?></span>
        </div>

        <div class="profile-row">
            <strong>Full Name</strong>
            <span><?php echo htmlspecialchars($owner["fullname"]) ?: '<em style="opacity:.4;">Not set</em>'; ?></span>
        </div>

        <div class="profile-row" style="align-items:flex-start;">
            <strong style="padding-top:2px;">Description</strong>
            <span style="line-height:1.7;white-space:pre-wrap;"><?php
                echo ($owner["description"] ?? "") !== ""
                    ? htmlspecialchars($owner["description"])
                    : '<em style="opacity:.4;">No description yet.</em>';
            ?></span>
        </div>

        <div class="profile-actions">
            <?php if ($is_own_profile): ?>
                <a class="button" href="/socialnet/setting.php">✏️ Edit Description</a>
            <?php endif; ?>
            <a class="button button-outline" href="/socialnet/index.php">← Back to Home</a>
        </div>
    </div>
<?php endif; ?>

</div>

</body>
</html>
