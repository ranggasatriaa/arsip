<?php
session_start();
require_once('../includes/request-key.php');
require_once('../includes/db-helper.php');

if(isset($_SESSION[RequestKey::$DATA_USER_KEY]) && !isset($_SESSION[RequestKey::$DATA_USER_LEVEL])) {
  header('Location: ../.');
}
else {
  $db = new DBHelper();
  if ($user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY])) {
    if ((int)$user->user_level == 2) {
      if ($changedUser = $db->getUserByKey($_GET[RequestKey::$DATA_USER_KEY])) {
        if ($db->changeLevelUser($changedUser->user_key,1)) {
          $_SESSION['status'] = 1;
        }
        else {
          $_SESSION['status'] = 2;
          $_SESSION['message'] = $db->strBadQuery;
        }
      }
      else {
        $_SESSION['status'] = 2;
        $_SESSION['message'] = $db->strBadRequest;
      }
    }
    else {
      $_SESSION['status'] = 2;
      $_SESSION['message'] = $db->accessForbidden;  
    }
  }
  else {
    $_SESSION['status'] = 2;
    $_SESSION['message'] = $db->accessForbidden;
  }
  header('Location: .');
}
?>