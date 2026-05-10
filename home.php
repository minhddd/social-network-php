<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signin.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home — SocialNet</title>
    <meta name="description" content="Your SocialNet home feed — discover people and connect with friends.">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <span class="navbar-brand">✦ SocialNet</span>
    <a class="active" href="home.php">🏠 Home</a>
    <a href="profile.php?id=<?php echo $current_user_id; ?>">👤 Profile</a>
    <a href="settings.php">⚙️ Settings</a>
    <a href="signout.php">🚪 Logout</a>
</div>

<div class="container">

    <div class="hero">
        <h1>Hello, <span><?php echo htmlspecialchars($username); ?></span> 👋</h1>
        <p>Welcome back to your social network. Discover people and stay connected.</p>
    </div>

    <h2 class="section-title">People you may know</h2>

    <div class="grid">
    <?php
    $sql = "
    SELECT * FROM users
    WHERE id != ?
    AND id NOT IN (
        SELECT friend_id FROM friendships WHERE user_id = ?
    )
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $current_user_id, $current_user_id);
    $stmt->execute();
    $strangers = $stmt->get_result();

    if ($strangers->num_rows == 0) {
        echo "<div class='empty'>🎉 No more people to discover — you know everyone!</div>";
    }

    while ($row = $strangers->fetch_assoc()) {
        $firstLetter = strtoupper(substr($row["username"], 0, 1));
        $bio = $row["bio"] ? htmlspecialchars($row["bio"]) : "<em style='opacity:0.5;'>No bio yet</em>";

        echo "<div class='card'>";
        echo "<div class='avatar'>" . htmlspecialchars($firstLetter) . "</div>";
        echo "<div class='username'>" . htmlspecialchars($row["username"]) . "</div>";
        echo "<div class='fullname'>" . htmlspecialchars($row["fullname"]) . "</div>";
        echo "<p class='bio'>" . $bio . "</p>";
        echo "<a class='button button-green' href='make_friend.php?id=" . $row["id"] . "'>+ Add Friend</a>";
        echo "</div>";
    }
    ?>
    </div>

    <h2 class="section-title">Your friends</h2>

    <div class="grid">
    <?php
    $sql = "
    SELECT users.*
    FROM users
    JOIN friendships ON users.id = friendships.friend_id
    WHERE friendships.user_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $friends = $stmt->get_result();

    if ($friends->num_rows == 0) {
        echo "<div class='empty'>👋 No friends yet — start adding people above!</div>";
    }

    while ($row = $friends->fetch_assoc()) {
        $firstLetter = strtoupper(substr($row["username"], 0, 1));
        $bio = $row["bio"] ? htmlspecialchars($row["bio"]) : "<em style='opacity:0.5;'>No bio yet</em>";

        echo "<div class='card'>";
        echo "<div class='avatar'>" . htmlspecialchars($firstLetter) . "</div>";
        echo "<div class='username'>" . htmlspecialchars($row["username"]) . "</div>";
        echo "<div class='fullname'>" . htmlspecialchars($row["fullname"]) . "</div>";
        echo "<p class='bio'>" . $bio . "</p>";
        echo "<a class='button' href='profile.php?id=" . $row["id"] . "'>View Profile →</a>";
        echo "</div>";
    }
    ?>
    </div>

</div>

</body>
</html>
