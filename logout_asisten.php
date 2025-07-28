<?php
// logout_asisten.php

session_start();

// Hapus semua variabel session
session_unset();

// Hancurkan session
session_destroy();

// Redirect ke halaman utama
header("Location: login_asisten.php");
exit();
?>
