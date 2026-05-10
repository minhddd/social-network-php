<?php
session_start();

// Destroy all session data
$_SESSION = [];
session_destroy();

// Redirect to Sign In page
header("Location: /socialnet/signin.php");
exit();
?>
