<?php
session_start();

if (isset($_SESSION['connected_id'])) {
    unset($_SESSION['connected_id']);
}
// session_unset();
session_destroy();
session_regenerate_id(true);
header('Location: login.php');
exit;
?>
