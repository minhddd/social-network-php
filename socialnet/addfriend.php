<<?php
session_start();
require_once "../db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: /socialnet/signin.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];
$receiver_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Cannot add invalid user or yourself
if ($receiver_id <= 0 || $receiver_id == $current_user_id) {
    header("Location: /socialnet/index.php");
    exit();
}

// Check target user exists
$sql = "SELECT id FROM account WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    header("Location: /socialnet/index.php");
    exit();
}

// Check whether a request already exists in either direction
$sql = "
    SELECT id, status
    FROM friend_request
    WHERE 
        (sender_id = ? AND receiver_id = ?)
        OR
        (sender_id = ? AND receiver_id = ?)
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $current_user_id, $receiver_id, $receiver_id, $current_user_id);
$stmt->execute();
$check = $stmt->get_result();

if ($check->num_rows == 0) {
    // Create pending friend request
    $sql = "INSERT INTO friend_request (sender_id, receiver_id, status)
            VALUES (?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $current_user_id, $receiver_id);
    $stmt->execute();
}

header("Location: /socialnet/index.php");
exit();
?>
