<?php

require_once('../includes/db-helper.php');
require_once('../includes/json-key.php');
require_once('../includes/request-key.php');

$db = new DBHelper();

$response = array();
$data = array();
$response[JSONKey::$REQUEST_STATUS] = false;

if($_SERVER['REQUEST_METHOD']=='POST'){
  if (isset($_POST[RequestKey::$DATA_TIPE])) {
    switch ($_POST[RequestKey::$DATA_TIPE]) {
      //LOGIN
      case RequestKey::$DATA_LOGIN:
        $response[JSONKey::$GET_STATUS] = false;
        if(isset($_POST[RequestKey::$DATA_USER_EMAIL]) && isset($_POST[RequestKey::$DATA_USER_PASSWORD])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $email  = $db->escapeInput($_POST[RequestKey::$DATA_USER_EMAIL]);
          $pass   = $db->escapeInput($_POST[RequestKey::$DATA_USER_PASSWORD]);
          if ($user = $db->login($email,$pass)) {
            $data[JSONKey::$USER_ID]          = (int)$user->user_id;
            $data[JSONKey::$USER_KEY]         = $user->user_key;
            $data[JSONKey::$USER_NAME]        = $user->user_name;
            $data[JSONKey::$USER_NIK]         = $user->user_nik;
            $data[JSONKey::$USER_KK]          = $user->user_kk;
            $data[JSONKey::$USER_EMAIL]       = $user->user_email;
            $data[JSONKey::$USER_PHONE]       = $user->user_phone;
            $data[JSONKey::$USER_JOIN_DATE]   = $user->user_join_date;
            $data[JSONKey::$USER_IMAGE]       = $user->user_image;
            $data[JSONKey::$USER_LEVEL]       = (int)$user->user_level;
            $data[JSONKey::$USER_STATUS]      = (int)$user->user_status;
            $data[JSONKey::$USER_LAST_LOGIN]  = $user->user_last_login;
            $response[JSONKey::$DATA] = $data;
            $response[JSONKey::$GET_STATUS] = true;
          }
          else {
            $response[JSONKey::$ERROR_MESSAGE] = "login failed";
          }
        }
        else {
          $response[JSONKey::$ERROR_MESSAGE] = $db->strBadRequest;
        }
        break;
      //REGISTER
      case RequestKey::$DATA_REGISTRATION:
        $response[JSONKey::$SAVE_STATUS] = false;
        if(isset($_POST[RequestKey::$DATA_USER_KEY]) && isset($_POST[RequestKey::$DATA_USER_NAME]) && isset($_POST[RequestKey::$DATA_USER_NIK]) && isset($_POST[RequestKey::$DATA_USER_KK]) && isset($_POST[RequestKey::$DATA_USER_EMAIL]) && isset($_POST[RequestKey::$DATA_USER_PASSWORD]) && isset($_POST[RequestKey::$DATA_USER_PHONE])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $arrData[RequestKey::$DATA_USER_KEY]          = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          $arrData[RequestKey::$DATA_USER_NAME]         = $db->escapeInput($_POST[RequestKey::$DATA_USER_NAME]);
          $arrData[RequestKey::$DATA_USER_NIK]          = $db->escapeInput($_POST[RequestKey::$DATA_USER_NIK]);
          $arrData[RequestKey::$DATA_USER_KK]           = $db->escapeInput($_POST[RequestKey::$DATA_USER_KK]);
          $arrData[RequestKey::$DATA_USER_EMAIL]        = $db->escapeInput($_POST[RequestKey::$DATA_USER_EMAIL]);
          $arrData[RequestKey::$DATA_USER_PASSWORD]     = sha1($db->escapeInput($_POST[RequestKey::$DATA_USER_PASSWORD]));
          $arrData[RequestKey::$DATA_USER_PHONE]        = $db->escapeInput($_POST[RequestKey::$DATA_USER_PHONE]);
          if (!$db->isNIKExist($arrData[RequestKey::$DATA_USER_NIK])) {
            if (!$db->isEmailExist($arrData[RequestKey::$DATA_USER_EMAIL])) {
              if ($db->register($arrData)) {
                $response[JSONKey::$SAVE_STATUS] = true;
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = "register failed";
              }
            }
            else {
              $response[JSONKey::$ERROR_MESSAGE] = "email sudah digunakan";
            }
          }
          else {
            $response[JSONKey::$ERROR_MESSAGE] = "nik sudah digunakan";
          }
        }
        else {
          $response[JSONKey::$ERROR_MESSAGE] = $db->$strBadRequest;
        }
        break;
      //ALL USER
      case RequestKey::$DATA_ALL:
        $response[JSONKey::$GET_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_START]) && isset($_POST[RequestKey::$DATA_USER_KEY])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $start = $db->escapeInput($_POST[RequestKey::$DATA_START]);
          $start = $start * $db->perPage - $db->perPage;
          $ukey = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          if ($user = $db->getUserByKey($ukey)) {
            if((int)$user->user_level === 1) { //ARSIP
              if ($users = $db->getAllUsers($start,1)) {
                $response[JSONKey::$GET_STATUS] = true;
                $count = $users;
                $response[JSONKey::$JUMLAH_USER] = (int) $db->countUser(1)->fetch_object()->count;
                while ($user = $users->fetch_object()) {
                  $userData = array();
                  $userData[JSONKey::$USER_ID]          = (int)$user->user_id;
                  $userData[JSONKey::$USER_KEY]         = $user->user_key;
                  $userData[JSONKey::$USER_NAME]        = $user->user_name;
                  $userData[JSONKey::$USER_NIK]         = $user->user_nik;
                  $userData[JSONKey::$USER_KK]          = $user->user_kk;
                  $userData[JSONKey::$USER_EMAIL]       = $user->user_email;
                  $userData[JSONKey::$USER_PHONE]       = $user->user_phone;
                  $userData[JSONKey::$USER_JOIN_DATE]   = $user->user_join_date;
                  $userData[JSONKey::$USER_IMAGE]       = $user->user_image;
                  $userData[JSONKey::$USER_LEVEL]       = (int)$user->user_level;
                  $userData[JSONKey::$USER_STATUS]      = (int)$user->user_status;
                  $userData[JSONKey::$USER_LAST_LOGIN]  = $user->user_last_login;
                  array_push($data,$userData);
                }
                $response[JSONKey::$DATA] = $data;
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = $db->strBadQuery;
              }
            }
            else if ((int)$user->user_level === 2) { //ADMIN
              if ($users = $db->getAllUsers($start,2)) {
                $response[JSONKey::$GET_STATUS] = true;
                $count = $users;
                $response[JSONKey::$JUMLAH_USER] = (int) $db->countUser(2)->fetch_object()->count;
                while ($user = $users->fetch_object()) {
                  $userData = array();
                  $userData[JSONKey::$USER_ID]          = (int)$user->user_id;
                  $userData[JSONKey::$USER_KEY]         = $user->user_key;
                  $userData[JSONKey::$USER_NAME]        = $user->user_name;
                  $userData[JSONKey::$USER_NIK]         = $user->user_nik;
                  $userData[JSONKey::$USER_KK]          = $user->user_kk;
                  $userData[JSONKey::$USER_EMAIL]       = $user->user_email;
                  $userData[JSONKey::$USER_PHONE]       = $user->user_phone;
                  $userData[JSONKey::$USER_JOIN_DATE]   = $user->user_join_date;
                  $userData[JSONKey::$USER_IMAGE]       = $user->user_image;
                  $userData[JSONKey::$USER_LEVEL]       = (int)$user->user_level;
                  $userData[JSONKey::$USER_STATUS]      = (int)$user->user_status;
                  $userData[JSONKey::$USER_LAST_LOGIN]  = $user->user_last_login;
                  array_push($data,$userData);
                }
                $response[JSONKey::$DATA] = $data;
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = $db->strBadQuery;
              }
            }
            else {
              $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
            }
          }
          else {
            $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
          }
        }
        else {
          $response[JSONKey::$ERROR_MESSAGE] = $db->strBadRequest;
        }
        break;
      
        $response[JSONKey::$SAVE_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_USER_ID]) && isset($_POST[RequestKey::$DATA_USER_KEY])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $uid = $db->escapeInput($_POST[RequestKey::$DATA_USER_ID]);
          $ukey = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          if ($user = $db->getUserByKey($ukey)) {
            if ((int)$user->user_level === 1) {
              if ($d = $db->confirmUser($uid)) {
                $user = $db->getUserById($uid);

                $firebase = new Firebase();
                $push = new Push();

                $push->setTitle("Arsip");
                $push->setMessage("Akun anda telah di konfirmasi, mulai bertransaksi");
                $push->setImage("");
                $push->setType("confirm");

                $json = $push->getPush();
                $firebase->sendToTopic($user->user_username, $json);

                $response[JSONKey::$SAVE_STATUS] = true;
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = "konfirmasi account gagal";
              }
            }
            else {
              $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
            }
          }
          else {
            $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
          }
        }
        else {
          $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
        }
        break;
      //SEARCH USER
      case RequestKey::$DATA_SEARCH:
        $response[JSONKey::$GET_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_USER_KEY]) && isset ($_POST[RequestKey::$DATA_KEYWORD]) && isset($_POST[RequestKey::$DATA_START])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $start    = $db->escapeInput($_POST[RequestKey::$DATA_START]);
          $start    = $start * $db->perPage - $db->perPage;
          $ukey     = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          $keyword  = $db->escapeInput($_POST[RequestKey::$DATA_KEYWORD]);
          if ($user = $db->getUserByKey($ukey)) {
            if ((int) $user->user_level == 1 || (int) $user->user_level == 2) {
              if ($users = $db->searchUsers($keyword,$start,$user->user_level)) {
                $response[JSONKey::$GET_STATUS] = true;
                while ($user = $users->fetch_object()) {
                  $userData = array();
                  $userData[JSONKey::$USER_ID]          = (int)$user->user_id;
                  $userData[JSONKey::$USER_KEY]         = $user->user_key;
                  $userData[JSONKey::$USER_NAME]        = $user->user_name;
                  $userData[JSONKey::$USER_NIK]         = $user->user_nik;
                  $userData[JSONKey::$USER_KK]          = $user->user_kk;
                  $userData[JSONKey::$USER_EMAIL]       = $user->user_email;
                  $userData[JSONKey::$USER_PHONE]       = $user->user_phone;
                  $userData[JSONKey::$USER_JOIN_DATE]   = $user->user_join_date;
                  $userData[JSONKey::$USER_IMAGE]       = $user->user_image;
                  $userData[JSONKey::$USER_LEVEL]       = (int)$user->user_level;
                  $userData[JSONKey::$USER_STATUS]      = (int)$user->user_status;
                  $userData[JSONKey::$USER_LAST_LOGIN]  = $user->user_last_login;
                  array_push($data,$userData);
                }
                $response[JSONKey::$DATA] = $data;
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = $db->strBadQuery;
              }
            }
            else {
              $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
            }
          }
          else {
            $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
          }
        }
        else {
          $response[JSONKey::$ERROR_MESSAGE] = $db->strBadRequest;
        }
        break;
      // UPDATE USER
      case RequestKey::$DATA_UPDATE:
        $response[JSONKey::$SAVE_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_USER_KEY]) && isset($_POST[RequestKey::$DATA_USER_NAME]) && isset($_POST[RequestKey::$DATA_USER_NIK]) && isset($_POST[RequestKey::$DATA_USER_PHONE]) && isset($_POST[RequestKey::$DATA_IMAGE]) && isset($_POST[RequestKey::$DATA_USER_IMAGE])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $ukey = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          $array = array();
          $array[RequestKey::$DATA_USER_NAME]   = $db->escapeInput($_POST[RequestKey::$DATA_USER_NAME]);
          $array[RequestKey::$DATA_USER_NIK]    = $db->escapeInput($_POST[RequestKey::$DATA_USER_NIK]);
          $array[RequestKey::$DATA_USER_PHONE]  = $db->escapeInput($_POST[RequestKey::$DATA_USER_PHONE]);
          $array[RequestKey::$DATA_USER_IMAGE]  = $db->escapeInput($_POST[RequestKey::$DATA_USER_IMAGE]);
          $array[RequestKey::$DATA_IMAGE]       = $db->escapeInput($_POST[RequestKey::$DATA_IMAGE]);
          if ($array[RequestKey::$DATA_USER_IMAGE] != "" && $array[RequestKey::$DATA_IMAGE] != "") {
            $actualpath = "../assets/user_img/user/".$array[RequestKey::$DATA_USER_IMAGE];
            $image = str_replace('data:image/jpg;base64,','',$array[RequestKey::$DATA_IMAGE]);
            $image = str_replace('','+',$image);
            $data = base64_decode($image);
            file_put_contents($actualpath,$data);
          }
          if ($user = $db->getUserByKey($ukey)) {
            $array[RequestKey::$DATA_USER_ID] = $user->user_id;
            if ($db->updateUser($array)) {
              $response[JSONKey::$SAVE_STATUS] = true;
            }
            else {
              $response[JSONKey::$ERROR_MESSAGE] = "gagal menyimpan data";
            }
          }
          else {
            $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
          }
        }
        else if (isset($_POST[RequestKey::$DATA_USER_KEY]) && isset($_POST[RequestKey::$DATA_USER_PASSWORD])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $ukey = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          $pw   = sha1($db->escapeInput($_POST[RequestKey::$DATA_USER_PASSWORD]));
          if ($user = $db->getUserByKey($ukey)) {
            $uid = $user->user_id;
            if ($db->changePassword($uid,$pw)) {
              $response[JSONKey::$SAVE_STATUS] = true;
            }
            else {
              $response[JSONKey::$ERROR_MESSAGE] = "gagal mengubah password";
            }
          }
          else {
            $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
          }
        }
        else {
          $response[JSONKey::$ERROR_MESSAGE] = $db->strBadRequest;
        }
        break;
      //UPGRADER USER TO ARSIP
      case RequestKey::$DATA_UPGRADE:
        $response[JSONKey::$SAVE_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_USER_KEY]) && isset($_POST[RequestKey::$DATA_USER_CHANGE_KEY])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $ukey = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          $ckey = $db->escapeInput($_POST[RequestKey::$DATA_USER_CHANGE_KEY]);
          if ($user = $db->getUserByKey($ukey)) {
            if ((int) $user->user_level == 2) {
              if ($db->changeLevelUser($ckey,1)) {
                $response[JSONKey::$SAVE_STATUS] = true;
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = "level gagal diubah";
              }
            }
          }
          else {
            $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
          }
        }
        break;
      //UPGRADER USER TO ARSIP
      case RequestKey::$DATA_DOWNGRADE:
        $response[JSONKey::$SAVE_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_USER_KEY]) && isset($_POST[RequestKey::$DATA_USER_CHANGE_KEY])) {
          $ukey = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          $ckey = $db->escapeInput($_POST[RequestKey::$DATA_USER_CHANGE_KEY]);
          if ($user = $db->getUserByKey($ukey)) {
            if ((int) $user->user_level == 2) {
              if ($db->changeLevelUser($ckey,0)) {
                $response[JSONKey::$SAVE_STATUS] = true;
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = "level gagal diubah";
              }
            }
            else {
              $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
            }
          }
          else {
            $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
          }
        }
        break;
      //CHANGE USER EMAIL
      case RequestKey::$DATA_CHANGE:
        $response[JSONKey::$SAVE_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_USER_KEY]) && isset($_POST[RequestKey::$DATA_USER_EMAIL]) && isset($_POST[RequestKey::$DATA_USER_CHANGE_KEY])) {
          $ukey   = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          $email  = $db->escapeInput($_POST[RequestKey::$DATA_USER_EMAIL]);
          $ckey   = $db->escapeInput($_POST[RequestKey::$DATA_USER_CHANGE_KEY]);
          if ($user = $db->getUserByKey($ukey)) {
            if ((int) $user->user_level == 1) {
              if ($db->changeEmailUser($ckey,$email)) {
                $response[JSONKey::$SAVE_STATUS] = true;
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = "email gagal diubah";
              }
            }
            else {
              $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
            }
          }
          else {
            $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
          }
        }
        break;
      //DATA STATISTIC
      case RequestKey::$DATA_STATISTIC:
        $response[JSONKey::$GET_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_USER_KEY])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $ukey = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          if ($user = $db->getUserByKey($ukey)) {
            if ((int) $user->user_level == 1) {
              if ($users = $db->getAllUserByLevel($user->user_level)) {
                $response[JSONKey::$GET_STATUS] = true;
                while ($user = $users->fetch_object()) {
                  if(array_key_exists(substr($user->user_kk,2,2),$data)) {
                    $data[substr($user->user_kk,2,2)] += 1;
                  }
                  else {
                    $data[substr($user->user_kk,2,2)] = 1;
                  }
                }
                $response[JSONKey::$DATA] = $data;
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = 'gagal mengambil data statistik';  
              }
            }
            else if ((int) $user->user_level == 2) {
              if ($users = $db->getAllUserByLevel($user->user_level)) {
                $response[JSONKey::$GET_STATUS] = true;
                while ($user = $users->fetch_object()) {
                  if(array_key_exists(substr($user->user_kk,2,2),$data)) {
                    $data[substr($user->user_kk,2,2)] += 1;
                  }
                  else {
                    $data[substr($user->user_kk,2,2)] = 1;
                  }
                }
                $response[JSONKey::$DATA] = $data;
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = 'gagal mengambil data statistik';  
              }
            }
            else {
              $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
            }
          }
          else {
            $response[JSONKey::$ERROR_MESSAGE] = $db->accessForbidden;
          }
        }
        break;
    }
  }
  else {
    $response[JSONKey::$ERROR_MESSAGE] = $db->strBadRequest;
  }
}

echo json_encode($response);

?>
