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
    $response[JSONKey::$REQUEST_STATUS] = true;
    switch ($_POST[RequestKey::$DATA_TIPE]) {
      //ADD FILE
      case RequestKey::$DATA_ADD:
        $response[JSONKey::$SAVE_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_USER_KEY]) && isset($_POST[RequestKey::$DATA_FILE_KEY]) && isset($_POST[RequestKey::$DATA_FILE_NAME]) && isset($_POST[RequestKey::$DATA_FILE_SIZE])) {
          $array = array();
          $ukey                               = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          $array[RequestKey::$DATA_FILE_KEY]  = $db->escapeInput($_POST[RequestKey::$DATA_FILE_KEY]);
          $array[RequestKey::$DATA_FILE_NAME] = $db->escapeInput($_POST[RequestKey::$DATA_FILE_NAME]);
          $array[RequestKey::$DATA_FILE_SIZE] = $db->escapeInput($_POST[RequestKey::$DATA_FILE_SIZE]);
          if ($user = $db->getUserByKey($ukey)) {
            $array[RequestKey::$DATA_USER_ID] = $user->user_id;
            $target_path = '../assets/user_file/user/'.$ukey.'/';
            if(!is_dir($target_path)){
                mkdir($target_path, 0755, true);
            }
            $target_path = $target_path.$array[RequestKey::$DATA_FILE_KEY];
            if ($db->isQuotaEnough($array[RequestKey::$DATA_USER_ID],$array[RequestKey::$DATA_FILE_SIZE])) {
              if (!$db->fileNameExist($array[RequestKey::$DATA_FILE_NAME],$user->user_id)) {
                try {
                  if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
                    if ($file = $db->saveFile($array)) {
                      $response[JSONKey::$SAVE_STATUS] = true;
                    }
                    else {
                      $response[JSONKey::$ERROR_MESSAGE] = "Gagal menyimpan file";
                    }
                  }
                  else {
                    $response[JSONKey::$ERROR_MESSAGE] = "Gagal menyimpan file";
                  }
                } catch (Exception $e) {
                  $response[JSONKey::$ERROR_MESSAGE] = $e->getMessage;
                } 
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = "Nama file sudah digunakan";
              }
            }
            else {
              $response[JSONKey::$ERROR_MESSAGE] = "Kuota tidak mencukupi";
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
      //READ FILE
      case RequestKey::$DATA_LIST:
        $response[JSONKey::$GET_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_START]) && isset($_POST[RequestKey::$DATA_USER_KEY])) {
          $start = $db->escapeInput($_POST[RequestKey::$DATA_START]);
          $start = $start * $db->perPage - $db->perPage;
          $response[JSONKey::$REQUEST_STATUS] = true;
          $ukey = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          if ($user = $db->getUserByKey($ukey)) {
            $uid = $user->user_id;
            if ($files = $db->getFilesUser($uid,$start)) {
              $response[JSONKey::$GET_STATUS] = true;
              while ($file = $files->fetch_object()) {
                $file_data = array();
                $file_data[JSONKey::$FILE_ID]   = (int)$file->file_id;
                $file_data[JSONKey::$FILE_KEY]  = $file->file_key;
                $file_data[JSONKey::$FILE_NAME] = $file->file_name;
                $file_data[JSONKey::$FILE_DATE] = $file->file_date;
                array_push($data,$file_data);
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
          $response[JSONKey::$ERROR_MESSAGE] = $db->strBadRequest;
        }
        break;
      //SEARCH FILE
      case RequestKey::$DATA_SEARCH:
        $response[JSONKey::$GET_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_START]) && isset($_POST[RequestKey::$DATA_USER_KEY]) && isset($_POST[RequestKey::$DATA_KEYWORD])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $start    = $db->escapeInput($_POST[RequestKey::$DATA_START]);
          $start    = $start * $db->perPage - $db->perPage;
          $ukey     = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          $keyword  = $db->escapeInput($_POST[RequestKey::$DATA_KEYWORD]);
          if ($user = $db->getUserByKey($ukey)) {
            $uid = $user->user_id;
            if ($files = $db->getFilesSearch($uid,$start,$keyword)) {
              $response[JSONKey::$GET_STATUS] = true;
              while ($file = $files->fetch_object()) {
                $file_data = array();
                $file_data[JSONKey::$FILE_ID]   = (int)$file->file_id;
                $file_data[JSONKey::$FILE_KEY]  = $file->file_key;
                $file_data[JSONKey::$FILE_NAME] = $file->file_name;
                $file_data[JSONKey::$FILE_DATE] = $file->file_date;
                array_push($data,$file_data);
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
          $response[JSONKey::$ERROR_MESSAGE] = $db->strBadRequest;
        }
        break;
      //CHANGE FILE NAME
      case RequestKey::$DATA_CHANGE:
        $response[JSONKey::$SAVE_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_USER_KEY]) && isset($_POST[RequestKey::$DATA_FILE_KEY]) && isset($_POST[RequestKey::$DATA_FILE_NAME])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $array = array();
          $ukey                               = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          $array[RequestKey::$DATA_FILE_KEY]  = $db->escapeInput($_POST[RequestKey::$DATA_FILE_KEY]);
          $array[RequestKey::$DATA_FILE_NAME] = $db->escapeInput($_POST[RequestKey::$DATA_FILE_NAME]);
          if ($user = $db->getUserByKey($ukey)) {
            $array[RequestKey::$DATA_USER_ID] = $user->user_id;
            if(!$db->fileNameExist($array[RequestKey::$DATA_FILE_NAME],$user->user_id)){
              if ($file = $db->changeFileName($array)) {
                $response[JSONKey::$SAVE_STATUS] = true;
              }
              else {
                $response[JSONKey::$ERROR_MESSAGE] = $db->strBadQuery;
              }
            }
            else {
              $response[JSONKey::$ERROR_MESSAGE] = "Nama file sudah digunakan";
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
      //DELETE FILE
      case RequestKey::$DATA_DELETE:
        $response[JSONKey::$SAVE_STATUS] = false;
        if (isset($_POST[RequestKey::$DATA_FILE_KEY]) && isset($_POST[RequestKey::$DATA_USER_KEY])) {
          $response[JSONKey::$REQUEST_STATUS] = true;
          $fkey = $db->escapeInput($_POST[RequestKey::$DATA_FILE_KEY]);
          $ukey = $db->escapeInput($_POST[RequestKey::$DATA_USER_KEY]);
          if ($user = $db->getUserByKey($ukey)) {
            $uid = $user->user_id;
            if ($db->deleteFile($fkey,$uid)) {
              unlink('../assets/user_file/user/'.$ukey.'/'.$fkey);
              $response[JSONKey::$SAVE_STATUS] = true;
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
          $response[JSONKey::$ERROR_MESSAGE] = $db->strBadRequest;
        }
        break;
    }
  }
}

echo json_encode($response);

?>
