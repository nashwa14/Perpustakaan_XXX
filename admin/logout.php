<?php
session_start();

// Hapus semua SESSION
session_unset();
session_destroy();

// Redirect ke halaman login
header("Location: ../login.php?logout=success");
exit;
