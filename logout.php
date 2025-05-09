<?php

include("log.php");
    session_start();
    session_destroy();
    header("Location: index.php");
    exit;
?>
