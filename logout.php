<?php

//Destroys session. Logs user out
session_start();
session_destroy();

$_SESSION = array();  // Removes content of $_SESSION

header("Location: login.php");
?>