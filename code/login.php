<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // ====== CHANGE THESE TO YOUR DOMAIN DETAILS ======
    $ldap_server = "ldap://10.10.14.10"; // Domain Controller IP
    $domain = "arconsupport.com";
    $base_dn = "DC=arconsupport,DC=com";
    // ==================================================

    $ldap = ldap_connect($ldap_server);

    if ($ldap) {

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        // Bind using domain credentials
        $bind = @ldap_bind($ldap, $username . "@" . $domain, $password);

        if ($bind) {

            $_SESSION['user'] = $username;
            header("Location: dashboard.php");
            exit;

        } else {
            $error = "Invalid domain credentials.";
        }

    } else {
        $error = "Unable to connect to domain controller.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Productivity Tracker</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="light">

<div class="auth-container">
    <h2>Domain Login</h2>

    <?php if($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Domain Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

</div>

<script src="assets/app.js"></script>
</body>
</html>