<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: /socialnet/signin.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];
$username        = $_SESSION["username"];
$fullname        = $_SESSION["fullname"];

// Fetch all other users
$sql  = "SELECT id, username, fullname FROM account WHERE id != ? ORDER BY username ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$others = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home · SocialNet</title>
    <meta name="description" content="Your SocialNet home — see other users and navigate the app.">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<!-- MenuBar -->
<div class="navbar">
    <span class="navbar-brand">✦ SocialNet</span>
    <a class="active" href="/socialnet/index.php">🏠 Home</a>
    <a href="/socialnet/setting.php">⚙️ Setting</a>
    <a href="/socialnet/profile.php">👤 Profile</a>
    <a href="/socialnet/about.php">ℹ️ About</a>
    <a href="/socialnet/signout.php">🚪 Sign Out</a>
</div>

<div class="container">

    <!-- Hero: current user info -->
    <div class="hero">
        <div class="avatar avatar-lg" style="margin:0 auto 18px;">
            <?php echo strtoupper(substr($username, 0, 1)); ?>
        </div>
        <h1>Hello, <span><?php echo htmlspecialchars($username); ?></span> 👋</h1>
        <p style="margin-top:6px;font-size:15px;color:var(--text-secondary);">
            <?php echo htmlspecialchars($fullname) ?: '<em style="opacity:.5;">No full name set</em>'; ?>
        </p>
    </div>

    <!-- Other users list -->
    <h2 class="section-title">All Users</h2>

    <div class="grid">
        <?php if ($others->num_rows === 0): ?>
            <div class="empty">👥 No other users in the system yet.</div>
        <?php else: ?>
            <?php while ($row = $others->fetch_assoc()):
                $letter = strtoupper(substr($row["username"], 0, 1));
            ?>
                <div class="card">
                    <div class="avatar"><?php echo htmlspecialchars($letter); ?></div>
                    <div class="username"><?php echo htmlspecialchars($row["username"]); ?></div>
                    <div class="fullname"><?php echo htmlspecialchars($row["fullname"]) ?: '<em style="opacity:.5;">No name</em>'; ?></div>
                    <p class="bio" style="margin-top:10px;"></p>
                    <a class="button" href="/socialnet/profile.php?owner=<?php echo urlencode($row['username']); ?>">
                        View Profile →
                    </a>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
