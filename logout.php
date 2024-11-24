<?php
// Start the session
session_start();

// Destroy all session data
session_destroy();

// Redirect to the homepage or login page
header("Location: index.php");
exit;
?>
