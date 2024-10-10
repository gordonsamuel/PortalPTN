<?php
session_start();

// Menghapus semua sesi
session_unset();
session_destroy();

// Redirect ke halaman welcome.php
header("Location: welcome.php");
exit();
