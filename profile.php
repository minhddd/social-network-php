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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <a href="home.php">Home</a>
    <a class="active" href="profile.php?id=<?php echo $current_user_id; ?>">Profile</a>
    <a href="settings.php">Settings</a>
    <a href="signout.php">Logout</a>
</div>

<div class="container">

    <div class="profile-box">
        <div class="avatar"><?php echo htmlspecialchars($firstLetter); ?></div>

        <h1><?php echo htmlspecialchars($user["username"]); ?></h1>
        <p class="small-text">User profile information</p>

        <div class="profile-row">
            <strong>Username:</strong>
            <?php echo htmlspecialchars($user["username"]); ?>
        </div>

        <div class="profile-row">
            <strong>Full name:</strong>
            <?php echo htmlspecialchars($user["fullname"]); ?>
        </div>

        <div class="profile-row">
            <strong>Email:</strong>
            <?php echo htmlspecialchars($user["email"]); ?>
        </div>

        <div class="profile-row">
            <strong>Bio:</strong>
            <?php echo htmlspecialchars($user["bio"]); ?>
        </div>

        <br>

        <?php if ($profile_id == $current_user_id) { ?>
            <a class="button" href="settings.php">Edit profile</a>
        <?php } ?>

        <a class="button button-green" href="home.php">Back to home</a>
    </div>

</div>

</body>
</html>
