<?php include 'auth_check.php'; ?>
<?php
session_start();
session_destroy();
header("Location: signin.php");
exit;
?>