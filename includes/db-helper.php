<?php
class DBHelper{
  private $link;
  public $perPage         = 5;
  public $strBadRequest   = "bad request";
  public $strBadQuery     = "bad query";
  public $strNotFound     = "data not found";
  public $accessForbidden = "access forbidden";
  public $quota           = 51200;
  public $jateng          = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','71','72','73','74','75','76');

  function __construct() {
    require_once('connect.php');
    $conn = new Connect();
    $this->link = $conn->link;
  }

  function escapeInput($string) {
    return $this->link->real_escape_string($string);
  }

  function sendEmail($receiverEmail,$receiverName,$subject,$message) {
    require '../PHPMailer/PHPMailerAutoload.php';

		$mail = new PHPMailer;

		$mail->isSMTP();

		$mail->SMTPDebug = 0;

		$mail->Debugoutput = 'html';

		$mail->Host = 'smtp.gmail.com';

		$mail->Port = 587;

		$mail->SMTPSecure = 'tls';

		$mail->SMTPAuth = true;

		$mail->Username = "hafidhwirandi@gmail.com";

		$mail->Password = "hafidh423434";

		$mail->setFrom('hafidhwirandi@gmail.com', 'Admin Arsip');

		$mail->addAddress($receiverEmail, $receiverName);

		$mail->Subject = $subject;

    $mail->isHTML(true);

		$mail->msgHTML($message);

    if (!$mail->send()) {
      echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
      echo "Message sent!";
    }
  }
  //----------------------------------------------------------------------------

  //----------------------------------------------------------------------------
  //USER
  //***CREATE AND UPDATE USER
  function register($arrData) {
    $user_key       = $arrData[RequestKey::$DATA_USER_KEY];
    $user_name      = $arrData[RequestKey::$DATA_USER_NAME];
    $user_nik       = $arrData[RequestKey::$DATA_USER_NIK];
    $user_kk        = $arrData[RequestKey::$DATA_USER_KK];
    $user_email     = $arrData[RequestKey::$DATA_USER_EMAIL];
    $user_password  = $arrData[RequestKey::$DATA_USER_PASSWORD];
    $user_phone     = $arrData[RequestKey::$DATA_USER_PHONE];
    if ($result = $this->link->query("INSERT INTO user (user_key,user_name,user_nik,user_kk,user_email,user_password,user_phone) VALUES ('$user_key','$user_name','$user_nik','$user_kk','$user_email','$user_password','$user_phone')")) {
      return true;
    }
    return false;
  }

  //CONFIRM USER INSERTED EMAIL
  function confirmEmail($email,$key) {
    if ($result = $this->link->query("UPDATE user SET user_status = '1' WHERE user_email = '$email' AND user_key = '$key'")) {
      return true;
    }
    return false;
  }

  //CHANGE USER DETAIL
  function updateUser($array) {
    $user_id    = $array[RequestKey::$DATA_USER_ID];
    $user_name  = $array[RequestKey::$DATA_USER_NAME];
    $user_nik   = $array[RequestKey::$DATA_USER_NIK];
    $user_phone = $array[RequestKey::$DATA_USER_PHONE];
    $user_image = $array[RequestKey::$DATA_USER_IMAGE];
    $image      = $array[RequestKey::$DATA_IMAGE];
    if ($user_image != "" && $image != "") {
      if ($result = $this->link->query("UPDATE user SET user_name = '$user_name', user_nik = '$user_nik', user_phone = '$user_phone', user_image = '$user_image' WHERE user_id = '$user_id'")) {
        return true;
      }
    }
    else {
      if ($result = $this->link->query("UPDATE user SET user_name = '$user_name', user_nik = '$user_nik', user_phone = '$user_phone' WHERE user_id = '$user_id'")) {
        return true;
      }
    }
    return false;
  }

  //CHANGE USER PASSWORD
  function changePassword($uid,$new) {
    if ($result = $this->link->query("UPDATE user SET user_password = '$new' WHERE user_id = '$uid'")) {
      return true;
    }
    return false;
  }

  function changeLevelUser($ukey,$level) {
    if ($this->link->query("UPDATE user SET user_level = '$level' WHERE user_key = '$ukey'")) {
      return true;
    }
    return false;
  }

  function changeEmailUser($ukey,$email) {
    if ($this->link->query("UPDATE user set user_email = '$email' WHERE user_key = '$ukey'")) {
      return true;
    }
    return false;
  }

  //***READ USER
  function login($email,$password) {
    $password = sha1($password);
    if ($result = $this->link->query("SELECT * FROM user WHERE user_email = '$email' AND user_password = '$password'")) {
      $this->link->query("UPDATE user SET user_last_login = NOW() WHERE user_email = '$email' AND user_password = '$password'");
      if($result->num_rows > 0) {
        return $result->fetch_object();
      }
    }
    return false;
  }

  //CHECK NIK USED BY USER
  function isNIKExist($nik) {
    if ($result = $this->link->query("SELECT * FROM user WHERE user_nik = '$nik'")) {
      if ($result->num_rows > 0) {
        return true;
      }
    }
    return false;
  }

  //CHECK KK USED BY USER
  function isKKExist($kk) {
    if ($result = $this->link->query("SELECT * FROM user WHERE user_kk = '$kk'")) {
      if ($result->num_rows > 0) {
        return true;
      }
    }
    return false;
  }

  //CHECK NIK USED BY USER ON EDIT
  function isNIKExistOnEdit($nik,$uid) {
    if ($result = $this->link->query("SELECT * FROM user WHERE user_nik = '$nik' AND user_id != '$uid'")) {
      if ($result->num_rows > 0) {
        return true;
      }
    }
    return false;
  }

  //CHECK KK USED BY USER ON EDIT
  function isKKExistOnEdit($kk,$uid) {
    if ($result = $this->link->query("SELECT * FROM user WHERE user_kk = '$kk' AND user_id != '$uid'")) {
      if ($result->num_rows > 0) {
        return true;
      }
    }
    return false;
  }

  //CHECK EMAIL USED BY USER
  function isEmailExist($email) {
    if ($result = $this->link->query("SELECT * FROM user WHERE user_email = '$email'")) {
      if ($result->num_rows > 0) {
        return true;
      }
    }
    return false;
  }

  //ONE USER BY KEY
  function getUserByKey($ukey) {
    if ($result = $this->link->query("SELECT * FROM user WHERE user_key = '$ukey'")) {
      return $result->fetch_object();
    }
    return false;
  }

  //ONE USER BY ID
  function getUserById($uid) {
    if ($result = $this->link->query("SELECT * FROM user WHERE user_id = '$uid'")) {
      return $result->fetch_object();
    }
    return false;
  }

  //ONE USER BY EMAIL
  function getUserByEmail($email){
    if ($result = $this->link->query("SELECT * FROM user WHERE user_email = '$email'")) {
      return $result->fetch_object();
    }
    return false;
  }

  //ALL USER
  function getAllUsers($start,$lev) {
    if($lev == 1) {
      if ($result = $this->link->query("SELECT * FROM user WHERE user_level <> '1' AND user_level <> '2' ORDER BY user_id DESC LIMIT $start,$this->perPage")) {
        return $result;
      }
    }
    else if ($lev == 2) {
      if ($result = $this->link->query("SELECT * FROM user WHERE user_level <> '2' ORDER BY user_id DESC LIMIT $start,$this->perPage")) {
        return $result;
      }
    }
    return false;
  }

  function getAllUserByLevel($lev) {
    if($lev == 1) {
      if ($result = $this->link->query("SELECT * FROM user WHERE user_level <> '1' AND user_level <> '2' ORDER BY user_id DESC")) {
        return $result;
      }
    }
    else if ($lev == 2) {
      if ($result = $this->link->query("SELECT * FROM user WHERE user_level <> '2' ORDER BY user_id DESC")) {
        return $result;
      }
    }
    return false;
  }

  //ALL USER
  function getAllUser() {
    if ($result = $this->link->query("SELECT * FROM user ORDER BY user_id")) {
      return $result;
    }
    return false;
  }

  // SEARCH USER
  function searchUsers($keyword,$start,$level) {
    if($level == 1) {
      if ($result = $this->link->query("SELECT * FROM user WHERE user_level <> '1' AND user_level <> '2' AND (user_name LIKE '%$keyword%' OR user_nik LIKE '%$keyword%' OR user_kk LIKE '%$keyword%' OR user_email LIKE '%$keyword%' OR user_phone LIKE '%$keyword%') ORDER BY user_id DESC LIMIT $start,$this->perPage")) {
        return $result;
      }
    }
    else if ($level == 2) {
      if ($result = $this->link->query("SELECT * FROM user WHERE user_level <> '2' AND (user_name LIKE '%$keyword%' OR user_nik LIKE '%$keyword%' OR user_kk LIKE '%$keyword%' OR user_email LIKE '%$keyword%' OR user_phone LIKE '%$keyword%') ORDER BY user_id DESC LIMIT $start,$this->perPage")) {
        return $result;
      }
    }
    return false;
  }

  //COUNT USER
  function countUser($lev) {
    if($lev == 1) {
      if ($result = $this->link->query("SELECT COUNT(*) AS count FROM user WHERE user_level <> '1' AND user_level <> '2'")) {
        return $result;
      }
    }
    else if ($lev == 2) {
      if ($result = $this->link->query("SELECT COUNT(*) AS count FROM user WHERE user_level <> '2'")) {
        return $result;
      }
    }
    return false;
  }

  //CHECK QUOTA
  function isQuotaEnough($uid,$size) {
    if ($result = $this->link->query("SELECT user_quota FROM user WHERE user_id = '$uid'")) {
      $data = $result->fetch_object();
      if (((int) $size + (int) $data->user_quota) <= $this->quota) {
        return true;
      }
    }
    return false;
  }
  //----------------------------------------------------------------------------

  //----------------------------------------------------------------------------
  //FILE
  //***CREATE AND UPDATE FILE
  function saveFile($array) {
    $file_key   = $array[RequestKey::$DATA_FILE_KEY];
    $file_name  = $array[RequestKey::$DATA_FILE_NAME];
    $file_size  = $array[RequestKey::$DATA_FILE_SIZE];
    $user_id    = $array[RequestKey::$DATA_USER_ID];
    if ($result = $this->link->query("INSERT INTO user_file(file_key,file_name,file_size,user_id) VALUES ('$file_key','$file_name','$file_size','$user_id')")) {
      return true;
    }
    return false;
  }

  //***READ FILE
  //COUNT FILE
  function countFile() {
    if ($result = $this->link->query("SELECT COUNT(file_id) AS count,SUM(file_size) AS size FROM user_file")) {
      return $result->fetch_object();
    }
    return false;
  }

  function countFileUser($uid) {
    if ($result = $this->link->query("SELECT COUNT(*) AS count,SUM(file_size) AS size FROM user_file WHERE user_id = '$uid'")) {
      return $result->fetch_object();
    }
    return false;
  }

  //ONE FILE BY KEY
  function getFileByKey($fkey) {
    if ($result = $this->link->query("SELECT * FROM user_file WHERE file_key = '$fkey'")) {
      return $result->fetch_object();
    }
    return false;
  }

  //ALL FILES BY USER
  function getFilesUser($uid,$start) {
    if ($result = $this->link->query("SELECT * FROM user_file AS f INNER JOIN user AS u ON f.user_id = u.user_id WHERE f.user_id = '$uid' ORDER BY f.file_id DESC LIMIT $start,$this->perPage")) {
      return $result;
    }
    return false;
  }

  function getFilesUserAll($uid) {
    if ($result = $this->link->query("SELECT * FROM user_file AS f INNER JOIN user AS u ON f.user_id = u.user_id WHERE f.user_id = '$uid' ORDER BY f.file_id ")) {
      return $result;
    }
    return false;
  }
  //ALL FILES BY USER WITH KEYWORD
  function getFilesSearch($uid,$start,$keyword) {
    if ($result = $this->link->query("SELECT * FROM user_file AS f INNER JOIN user AS u ON f.user_id = u.user_id WHERE f.user_id = '$uid' AND f.file_name LIKE '%$keyword%' ORDER BY f.file_id DESC LIMIT $start,$this->perPage")) {
      return $result;
    }
    return false;
  }

  //CHECK FILE NAME
  function fileNameExist($filename,$uid) {
    if ($result = $this->link->query("SELECT * FROM user_file WHERE file_name = '$filename' AND user_id = '$uid'")) {
      if ($result->num_rows > 0) {
        return true;
      }
    }
    return false;
  }

  //CHANGE FILE NAME
  function changeFileName($array) {
    $file_key   = $array[RequestKey::$DATA_FILE_KEY];
    $file_name  = $array[RequestKey::$DATA_FILE_NAME];
    $user_id    = $array[RequestKey::$DATA_USER_ID];
    if ($result = $this->link->query("UPDATE user_file SET file_name = '$file_name' WHERE file_key = '$file_key' AND user_id = '$user_id'")) {
      return true;
    }
    else {
      return false;
    }
  }

  //***DELETE FILE
  function deleteFile($fkey,$uid) {
    if ($result = $this->link->query("DELETE FROM user_file WHERE file_key = '$fkey' AND user_id = '$uid'")) {
      return true;
    }
    return false;
  }
  //----------------------------------------------------------------------------

  function String2Hex($string){
      $hex='';
      for ($i=0; $i < strlen($string); $i++){
          $hex .= dechex(ord($string[$i]));
      }
      return $hex;
  }


  function Hex2String($hex){
      $string='';
      for ($i=0; $i < strlen($hex)-1; $i+=2){
          $string .= chr(hexdec($hex[$i].$hex[$i+1]));
      }
      return $string;
  }
}
?>
