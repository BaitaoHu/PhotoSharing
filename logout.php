<?php
    // "Delete" cookie by setting it to the past
    setcookie('username', '', time() - 3600, '/');
    setcookie('ispro', '', time() - 3600, '/');
    header("location: index.php");
?>
