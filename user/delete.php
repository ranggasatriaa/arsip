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
    if (isset($_GET[RequestKey::$DATA_FILE_KEY])) {
      $fkey = $db->escapeInput($_GET[RequestKey::$DATA_FILE_KEY]);
      $uid  = $user->user_id;
      if ($db->deleteFile($fkey,$uid)) {
        unlink('../assets/user_file/user/'.$user->user_key.'/'.$fkey);
        $_SESSION['delete'] = 1;
      }
      else {
        $_SESSION['delete'] = 2;
      }
    }
    else {
      $_SESSION['delete'] = 2;
    }  
  }
  else {
    $_SESSION['delete'] = 2;
  }

  header('Location: .');

}
?>