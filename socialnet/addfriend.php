<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: /socialnet/signin.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];
$friend_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Validate: target user must exist and not be self
if ($friend_id > 0 && $friend_id !== $current_user_id) {

    // Check target user exists
    $sql  = "SELECT id FROM account WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $friend_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 1) {

        // Check not already friends
        $sql  = "SELECT id FROM friendship WHERE user_id = ? AND friend_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $current_user_id, $friend_id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows === 0) {
            // Insert both directions for easy querying
            $sql  = "INSERT INTO friendship (user_id, friend_id) VALUES (?, ?), (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiii", $current_user_id, $friend_id, $friend_id, $current_user_id);
            $stmt->execute();
        }
    }
}

// Redirect back to home
header("Location: /socialnet/index.php");
exit();
?>
