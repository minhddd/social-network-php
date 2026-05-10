
<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signin.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];

if (!isset($_GET["id"])) {
    header("Location: home.php");
    exit();
}

$friend_id = intval($_GET["id"]);

if ($friend_id <= 0 || $friend_id == $current_user_id) {
    header("Location: home.php");
    exit();
}

$sql = "INSERT IGNORE INTO friendships (user_id, friend_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $current_user_id, $friend_id);
$stmt->execute();

header("Location: home.php");
exit();
?>
