<?php

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