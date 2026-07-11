<?php
// File: logout.php
// Fungsi: Mengakhiri session user (logout)
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
