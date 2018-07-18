<?php
require_once('includes/request-key.php');
require_once('includes/db-helper.php');
$db = new DBHelper();

$status                = 0;
$err_nik              = "";
$err_kk               = "";
$err_name             = "";
$err_phone            = "";
$err_email            = "";
$err_password         = "";
$err_confirm_password = "";
$msg_success          = "";
$err_register         = "";

if (isset($_POST['submit'])) {
  if (isset($_POST[RequestKey::$DATA_USER_NIK])  && isset($_POST[RequestKey::$DATA_USER_KK]) && isset($_POST[RequestKey::$DATA_USER_NAME]) && isset($_POST[RequestKey::$DATA_USER_PHONE]) && isset($_POST[RequestKey::$DATA_USER_EMAIL]) && isset($_POST[RequestKey::$DATA_USER_PASSWORD]) && isset($_POST['user-password-2'])){
    $db = new DBHelper();
    $user_nik         = $db->escapeInput($_POST[RequestKey::$DATA_USER_NIK]);
    $user_kk          = $db->escapeInput($_POST[RequestKey::$DATA_USER_KK]);
    $user_name        = $db->escapeInput($_POST[RequestKey::$DATA_USER_NAME]);
    $user_phone       = $db->escapeInput($_POST[RequestKey::$DATA_USER_PHONE]);
    $user_email       = $db->escapeInput($_POST[RequestKey::$DATA_USER_EMAIL]);
    $user_password    = $db->escapeInput($_POST[RequestKey::$DATA_USER_PASSWORD]);
    $user_password_2  = $db->escapeInput($_POST['user-password-2']);

    if (!preg_match("/^[0-9]{16}$/",$user_nik)){
      $err_nik = "NIK 16 digit";
    }
    else {
      if (substr($user_nik,0,2) != "33") {
        $err_nik = "NIK bukan jawa tengah";
      }
      else if (in_array(substr($user_nik,0,2), $db->jateng)) {
        $err_nik = "NIK bukan jawa tengah";
      }
    }

    if (!preg_match("/^[0-9]{16}$/",$user_kk)){
      $err_kk = "KK 16 digit";
    }
    else {
      if (substr($user_kk,0,2) != "33") {
        $err_kk = "KK bukan jawa tengah";
      }
      else if (in_array(substr($user_kk,0,2), $db->jateng)) {
        $err_kk = "KK bukan jawa tengah";
      }
    }

    if (!preg_match("/^[a-zA-Z ]{1,50}$/",$user_name)) {
      $err_name = "Nama tidak valid";
    }

    if (!preg_match("/^[0-9]{1,15}$/",$user_phone)) {
      $err_phone = "Nomor telepon tidak valid";
    }

    if (!preg_match("/^[a-zA-Z0-9]{8,20}$/",$user_password)) {
      $err_password = "Password tidak valid";
    }

    if ($user_password != $user_password_2) {
      $err_confirm_password = "Konfirmasi password tidak sama";
    }

    if (empty($err_nik) && empty($err_kk) && empty($err_name) && empty($err_email) && empty($err_phone) && empty($err_password) && empty($err_confirm_password)) {
      //create key
      $string = strtotime("now").$user_name;
      $user_key = $db->String2Hex($string);

      $array = array();
      $array[RequestKey::$DATA_USER_KEY] = $user_key;
      $array[RequestKey::$DATA_USER_NAME] = $user_name;
      $array[RequestKey::$DATA_USER_NIK] = $user_nik;
      $array[RequestKey::$DATA_USER_KK] = $user_kk;
      $array[RequestKey::$DATA_USER_EMAIL] = $user_email;
      $array[RequestKey::$DATA_USER_PASSWORD] = sha1($user_password);
      $array[RequestKey::$DATA_USER_PHONE] = $user_phone;

      if (!$db->isNIKExist($array[RequestKey::$DATA_USER_NIK])) {
        if (!$db->isKKExist($array[RequestKey::$DATA_USER_KK])) {
          if (!$db->isEmailExist($array[RequestKey::$DATA_USER_EMAIL])) {
            if ($db->register($array)) {
              $arraydata = array();
              $arraydata[RequestKey::$DATA_TIPE]     = RequestKey::$DATA_REGISTRATION;
              $arraydata[RequestKey::$DATA_USER_KEY] = $array[RequestKey::$DATA_USER_KEY];

              $url = 'http://arsip.hafidhaulia.com/api/send-email.php';

              $ch = curl_init();

              $postString = http_build_query($arraydata, '', '&');

              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_POST, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

              // Execute post
              $result = curl_exec($ch);
              if ($result === FALSE) {
                  die('Curl failed: ' . curl_error($ch));
              }

              // Close connection
              curl_close($ch);

              if ($result == "Message sent!") {
                $status = 1;
                $msg_success = 'Registrasi berhasil, cek email';
              }
              else {
                $status = 2;
                $err_register = $result;
              }
            }
            else {
              $status = 2;
              $err_register = 'Registrasi gagal';
            }
          }
          else {
            $status = 2;
            $err_register = "email sudah terdaftar";
          }
        }
        else {
          $status = 2;
          $err_register = "kk sudah terdaftar";
        }
      }
      else {
        $status = 2;
        $err_register = "nik sudah terdaftar";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Registrasi Arsip Keluarga</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="css/fontastic.css">
    <!-- Google fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,700">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="img/favicon.ico">
    <link rel="stylesheet" href="css/sweetalert2.min.css" id="theme-stylesheet">
    <script src="js/sweetalert2.all.min.js"></script>
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
  </head>
  <body>
    <div class="page login-page">
      <div class="container d-flex align-items-center">
        <div class="form-holder has-shadow">
          <div class="row">
            <!-- Logo & Information Panel-->
            <div class="col-lg-6">
              <div class="info d-flex align-items-center">
                <div class="content">
                  <div class="logo">
                    <h1>Dashboard</h1>
                  </div>
                  <p>Arsip Keluarga</p>
                </div>
              </div>
            </div>
            <!-- Form Panel    -->
            <div class="col-lg-6 bg-white">
              <div class="form d-flex align-items-center">
                <div class="content">
                  <form id="register-form" method="post">
                    <div class="form-group" has-danger>
                      <input id="<?php echo RequestKey::$DATA_USER_NIK ?>" type="text" name="<?php echo RequestKey::$DATA_USER_NIK ?>" required class="input-material">
                      <label for="<?php echo RequestKey::$DATA_USER_NIK ?>" class="label-material">Nomor Induk Kependudukan</label>
                      <?= (!empty($err_nik)) ? '<p class="text-danger">'.$err_nik.'</p>' : '' ?>
                    </div>
                    <div class="form-group">
                      <input id="<?php echo RequestKey::$DATA_USER_KK ?>" type="text" name="<?php echo RequestKey::$DATA_USER_KK ?>" required class="input-material">
                      <label for="<?php echo RequestKey::$DATA_USER_KK ?>" class="label-material">Nomor Kartu Keluarga</label>
                      <?= (!empty($err_kk)) ? '<p class="text-danger">'.$err_kk.'</p>' : '' ?>
                    </div>
                    <div class="form-group">
                      <input id="<?php echo RequestKey::$DATA_USER_NAME ?>" type="text" name="<?php echo RequestKey::$DATA_USER_NAME ?>" required class="input-material">
                      <label for="<?php echo RequestKey::$DATA_USER_NAME ?>" class="label-material">Nama</label>
                      <?= (!empty($err_name)) ? '<p class="text-danger">'.$err_name.'</p>' : '' ?>
                    </div>
                    <div class="form-group">
                      <input id="<?php echo RequestKey::$DATA_USER_PHONE ?>" type="text" name="<?php echo RequestKey::$DATA_USER_PHONE ?>" required class="input-material">
                      <label for="<?php echo RequestKey::$DATA_USER_PHONE ?>" class="label-material">Nomor Telepon</label>
                      <?= (!empty($err_phone)) ? '<p class="text-danger">'.$err_phone.'</p>' : '' ?>
                    </div>
                    <div class="form-group">
                      <input id="<?php echo RequestKey::$DATA_USER_EMAIL ?>" type="email" name="<?php echo RequestKey::$DATA_USER_EMAIL ?>" required class="input-material">
                      <label for="<?php echo RequestKey::$DATA_USER_EMAIL ?>" class="label-material">Email</label>
                      <?= (!empty($err_email)) ? '<p class="text-danger">'.$err_email.'</p>' : '' ?>
                    </div>
                    <div class="form-group">
                      <input id="<?php echo RequestKey::$DATA_USER_PASSWORD ?>" type="password" name="<?php echo RequestKey::$DATA_USER_PASSWORD ?>" required class="input-material">
                      <label for="<?php echo RequestKey::$DATA_USER_PASSWORD ?>" class="label-material">Password</label>
                      <?= (!empty($err_password)) ? '<p class="text-danger">'.$err_password.'</p>' : '' ?>
                    </div>
                    <div class="form-group">
                      <input id="user-password-2" type="password" name="user-password-2" required class="input-material">
                      <label for="user-password-2" class="label-material">Konfirmasi Password</label>
                      <?= (!empty($err_confirm_password)) ? '<p class="text-danger">'.$err_confirm_password.'</p>' : '' ?>
                    </div>
                    <?= (!empty($err_register)) ? '<p class="text-danger">'.$err_register.'</p>' : '' ?>
                    <?= (!empty($msg_success)) ? '<p class="text-success">'.$msg_success.'</p>' : '' ?>
                    <input id="register" type="submit" name="submit" value="Register" class="btn btn-primary">
                  </form><small>Already have an account? </small><a href="." class="signup">Login</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="copyrights text-center">
        <p>Design by <a href="https://bootstrapious.com" class="external">Bootstrapious</a>
          <!-- Please do not remove the backlink to us unless you support further theme's development at https://bootstrapious.com/donate. It is part of the license conditions. Thank you for understanding :)-->
        </p>
      </div>
    </div>
    <!-- JavaScript files-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper.js/umd/popper.min.js"> </script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/jquery.cookie/jquery.cookie.js"> </script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
    <!-- Main File-->
    <script src="js/front.js"></script>
    <?php
    echo $status;
    echo '<script>var status = '.$status.';</script>';
    $status = 0;
    ?>
    <script>
    $(document).ready(function() {
      if (status == 1) {
        swal("Success!","Registrasi berhasil","success")
        .then((value) => {
          window.location.href = ".";
          });
      }
      else if (status == 2) {
        swal("Failed!","Pastikan inputan","error");
      }
    });
    </script>
  </body>
</html>
