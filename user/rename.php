<?php
session_start();
require_once('../includes/request-key.php');
require_once('../includes/db-helper.php');

if(!isset($_SESSION[RequestKey::$DATA_USER_KEY]) && !isset($_SESSION[RequestKey::$DATA_USER_LEVEL])) {
  header('Location: ../.');
}
else if ($_SESSION[RequestKey::$DATA_USER_LEVEL] != 0) {
  //ASK RELOGIN
} else {
  $db = new DBHelper();

  if ($user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY])) {
    if (isset($_GET[RequestKey::$DATA_FILE_NAME]) && isset($_GET[RequestKey::$DATA_FILE_KEY])) {
      $array = array();
      $array[RequestKey::$DATA_FILE_KEY] = $db->escapeInput($_GET[RequestKey::$DATA_FILE_KEY]);
      $array[RequestKey::$DATA_FILE_NAME] = $db->escapeInput($_GET[RequestKey::$DATA_FILE_NAME]);
      $array[RequestKey::$DATA_USER_ID] = $user->user_id;
      if(!$db->fileNameExist($array[RequestKey::$DATA_FILE_NAME],$user->user_id)){
        if ($db->changeFileName($array)) {
          $_SESSION['file'] = 1;
        }
        else {
          $_SESSION['file'] = 2;
        }
      }
      else {
        $_SESSION['file'] = 3;
      }
    }
    else {
      $_SESSION['file'] = 2;
    }  
  }
  else {
    $_SESSION['file'] = 4;
  }

  header('Location: .');

}
?>