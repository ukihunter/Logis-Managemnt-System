<?php
// Start session and destroy it to log out the user
session_start();
session_destroy();
header('Location: ../login/login.php');
exit;
