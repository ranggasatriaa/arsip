<?php

require_once('../includes/db-helper.php');
require_once('../includes/json-key.php');
require_once('../includes/request-key.php');

$db = new DBHelper();

if($_SERVER['REQUEST_METHOD']=='POST'){
  if (isset($_POST[RequestKey::$DATA_TIPE])) {
    switch ($_POST[RequestKey::$DATA_TIPE]) {
      case RequestKey::$DATA_REGISTRATION:
        if(isset($_POST[RequestKey::$DATA_USER_KEY])) {
          $ukey = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          $user = $db->getUserByKey($ukey);
          $body = file_get_contents('../assets/registrasi.html');
          $link = $_SERVER['SERVER_NAME']."/confirm.php?email=".$user->user_email."&q=".$user->user_key."";
          $body2 = str_replace('%link%',$link,$body);
          $db->sendEmail($user->user_email,$user->user_name,"Pendaftaran berhasil!",$body2);
        }
        break;
      case RequestKey::$DATA_RESET:
        if (isset($_POST[RequestKey::$DATA_USER_EMAIL])) {
          $email = $db->escapeInput($_POST[RequestKey::$DATA_USER_EMAIL]);
          if ($user = $db->getUserByEmail($email)) {
            if ((int)$user->user_status === 1) {
              $body = file_get_contents('../assets/reset_password.html');
              $url = $_SERVER['SERVER_NAME'].'/reset.php?q='.$user->user_key;
              $body2 = str_replace('%link%',$url,$body);
            }
            else {
              $body = file_get_contents('../assets/unreset.html');
              $link = "Email belum dikonfirmasi";
              $body2 = str_replace('%link%',$link,$body);
            }
          }
          else {
            $body = file_get_contents('../assets/unreset.html');
            $body2 = str_replace('%link%',$db->accessForbidden,$body);
          }
        }
        else {
          $body = file_get_contents('../assets/unreset.html');
          $body2 = str_replace('%link%',$db->strBadQuery,$body);
        }
        if ($user != null) {
          $db->sendEmail($user->user_email,$user->user_name,"Reset password!",$body2);
        }
        else {
          echo 'email not registered';
        }
        break;
    }
  }
}

?>
