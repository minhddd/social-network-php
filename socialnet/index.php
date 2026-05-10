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

// Fetch friends (users I have a friendship with)
$sql  = "
    SELECT a.id, a.username, a.fullname
    FROM account a
    JOIN friendship f ON a.id = f.friend_id
    WHERE f.user_id = ?
    ORDER BY a.username ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$friends = $stmt->get_result();

// Fetch non-friends (other users not yet in friendship)
$sql  = "
    SELECT id, username, fullname
    FROM account
    WHERE id != ?
      AND id NOT IN (
          SELECT friend_id FROM friendship WHERE user_id = ?
      )
    ORDER BY username ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $current_user_id, $current_user_id);
$stmt->execute();
$strangers = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home · SocialNet</title>
    <meta name="description" content="Your SocialNet home — see your friends and discover new people.">
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

    <!-- People you may know -->
    <h2 class="section-title">People You May Know</h2>
    <div class="grid">
        <?php if ($strangers->num_rows === 0): ?>
            <div class="empty">🎉 You're connected with everyone in the system!</div>
        <?php else: ?>
            <?php while ($row = $strangers->fetch_assoc()):
                $letter = strtoupper(substr($row["username"], 0, 1));
            ?>
                <div class="card">
                    <div class="avatar"><?php echo htmlspecialchars($letter); ?></div>
                    <div class="username"><?php echo htmlspecialchars($row["username"]); ?></div>
                    <div class="fullname">
                        <?php echo htmlspecialchars($row["fullname"]) ?: '<em style="opacity:.5;">No name</em>'; ?>
                    </div>
                    <div style="display:flex;gap:10px;margin-top:18px;flex-wrap:wrap;">
                        <a class="button button-green"
                           href="/socialnet/addfriend.php?id=<?php echo $row['id']; ?>">
                            + Add Friend
                        </a>
                        <a class="button button-outline"
                           href="/socialnet/profile.php?owner=<?php echo urlencode($row['username']); ?>">
                            View Profile
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <!-- Friends list -->
    <h2 class="section-title">Your Friends</h2>
    <div class="grid">
        <?php if ($friends->num_rows === 0): ?>
            <div class="empty">👋 No friends yet — add some people above!</div>
        <?php else: ?>
            <?php while ($row = $friends->fetch_assoc()):
                $letter = strtoupper(substr($row["username"], 0, 1));
            ?>
                <div class="card">
                    <div class="avatar"><?php echo htmlspecialchars($letter); ?></div>
                    <div class="username"><?php echo htmlspecialchars($row["username"]); ?></div>
                    <div class="fullname">
                        <?php echo htmlspecialchars($row["fullname"]) ?: '<em style="opacity:.5;">No name</em>'; ?>
                    </div>
                    <div style="margin-top:18px;">
                        <a class="button"
                           href="/socialnet/profile.php?owner=<?php echo urlencode($row['username']); ?>">
                            View Profile →
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
