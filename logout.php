<?php
session_start();
require_once('includes/request-key.php');
unset($_SESSION[RequestKey::$DATA_USER_KEY]);
unset($_SESSION[RequestKey::$DATA_USER_LEVEL]);
header('Location: .');
?>
