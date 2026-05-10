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
<html>
<head>
    <title>Home page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <a class="active" href="home.php">Home</a>
    <a href="profile.php?id=<?php echo $current_user_id; ?>">Profile</a>
    <a href="settings.php">Settings</a>
    <a href="signout.php">Logout</a>
</div>

<div class="container">

    <div class="hero">
        <h1>Hello <?php echo htmlspecialchars($username); ?></h1>
        <p>Welcome to your social network home page.</p>
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
        echo "<div class='empty'>No strangers found.</div>";
    }

    while ($row = $strangers->fetch_assoc()) {
        $firstLetter = strtoupper(substr($row["username"], 0, 1));

        echo "<div class='card'>";
        echo "<div class='avatar'>" . htmlspecialchars($firstLetter) . "</div>";
        echo "<div class='username'>" . htmlspecialchars($row["username"]) . "</div>";
        echo "<div class='fullname'>" . htmlspecialchars($row["fullname"]) . "</div>";
        echo "<p class='bio'>" . htmlspecialchars($row["bio"]) . "</p>";
        echo "<a class='button button-green' href='make_friend.php?id=" . $row["id"] . "'>Add friend</a>";
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
        echo "<div class='empty'>No friends yet.</div>";
    }

    while ($row = $friends->fetch_assoc()) {
        $firstLetter = strtoupper(substr($row["username"], 0, 1));

        echo "<div class='card'>";
        echo "<div class='avatar'>" . htmlspecialchars($firstLetter) . "</div>";
        echo "<div class='username'>" . htmlspecialchars($row["username"]) . "</div>";
        echo "<div class='fullname'>" . htmlspecialchars($row["fullname"]) . "</div>";
        echo "<p class='bio'>" . htmlspecialchars($row["bio"]) . "</p>";
        echo "<a class='button' href='profile.php?id=" . $row["id"] . "'>View profile</a>";
        echo "</div>";
    }
    ?>
    </div>

</div>

</body>
</html>
