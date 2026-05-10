<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /socialnet/signin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About · SocialNet</title>
    <meta name="description" content="About this SocialNet application — student information.">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<!-- MenuBar -->
<div class="navbar">
    <span class="navbar-brand">✦ SocialNet</span>
    <a href="/socialnet/index.php">🏠 Home</a>
    <a href="/socialnet/setting.php">⚙️ Setting</a>
    <a href="/socialnet/profile.php">👤 Profile</a>
    <a class="active" href="/socialnet/about.php">ℹ️ About</a>
    <a href="/socialnet/signout.php">🚪 Sign Out</a>
</div>

<div class="container" style="max-width:640px;">

    <div class="profile-box" style="text-align:center;">
        <div style="font-size:52px;margin-bottom:20px;">🎓</div>
        <h1 style="font-size:28px;font-weight:800;letter-spacing:-0.6px;margin-bottom:6px;">
            About This App
        </h1>
        <p class="small-text" style="margin-bottom:32px;">SocialNet — a social networking web application</p>

        <div class="divider"></div>

        <div class="profile-row" style="justify-content:center;gap:32px;flex-wrap:wrap;">
            <div>
                <div class="small-text" style="text-transform:uppercase;letter-spacing:.08em;font-size:12px;font-weight:600;margin-bottom:6px;">Student Name</div>
                <div style="font-size:20px;font-weight:700;color:var(--text-primary);">Nguyen Hoang Minh</div>
            </div>
            <div style="width:1px;background:var(--border);align-self:stretch;"></div>
            <div>
                <div class="small-text" style="text-transform:uppercase;letter-spacing:.08em;font-size:12px;font-weight:600;margin-bottom:6px;">Student Number</div>
                <div style="font-size:20px;font-weight:700;color:var(--text-primary);">1695570</div>
            </div>
        </div>

        <div class="divider"></div>

        <p class="small-text" style="margin-top:8px;line-height:1.7;">
            Built with PHP, MySQL, and modern CSS.<br>
            Designed with a dark glassmorphism aesthetic.
        </p>

        <div class="profile-actions" style="justify-content:center;margin-top:28px;">
            <a class="button button-outline" href="/socialnet/index.php">← Back to Home</a>
        </div>
    </div>

</div>

</body>
</html>
