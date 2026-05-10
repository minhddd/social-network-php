<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: /socialnet/signin.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];
$request_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($request_id > 0) {
    // Verify this request exists AND is addressed to the logged-in user
    $sql  = "SELECT * FROM friend_request WHERE id = ? AND receiver_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $request_id, $current_user_id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 1) {
        // Delete the request entirely
        $sql  = "DELETE FROM friend_request WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
    }
}

header("Location: /socialnet/index.php");
exit();
?>
