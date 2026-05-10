<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signin.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];
$message = "";
$message_class = "";

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $bio = $_POST["bio"];
    $new_password = $_POST["new_password"];

    if ($new_password != "") {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE users 
                SET fullname = ?, email = ?, bio = ?, password_hash = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $fullname, $email, $bio, $password_hash, $current_user_id);
    } else {
        $sql = "UPDATE users 
                SET fullname = ?, email = ?, bio = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $fullname, $email, $bio, $current_user_id);
    }

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        $message_class = "success";

        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $message = "Update failed!";
        $message_class = "error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <a href="home.php">Home</a>
    <a href="profile.php?id=<?php echo $current_user_id; ?>">Profile</a>
    <a class="active" href="settings.php">Settings</a>
    <a href="signout.php">Logout</a>
</div>

<div class="form-box">
    <h1>Account Settings</h1>
    <p class="small-text" style="text-align:center;">Edit your personal information</p>

    <?php if ($message != "") { ?>
        <div class="message <?php echo $message_class; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>

    <form method="post" action="settings.php">
        <label>Full name:</label>
        <input type="text" name="fullname" value="<?php echo htmlspecialchars($user["fullname"]); ?>">

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>">

        <label>Bio:</label>
        <textarea name="bio"><?php echo htmlspecialchars($user["bio"]); ?></textarea>

        <label>New password:</label>
        <input type="password" name="new_password" placeholder="Leave blank if you do not want to change password">

        <button type="submit">Save changes</button>
        <a class="button" href="profile.php?id=<?php echo $current_user_id; ?>">View profile</a>
    </form>
</div>

</body>
</html>
