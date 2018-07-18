<?php
session_start();
require_once('includes/request-key.php');
require_once('includes/db-helper.php');

$alert = false;

if(isset($_SESSION[RequestKey::$DATA_USER_KEY]) && isset($_SESSION[RequestKey::$DATA_USER_LEVEL])) {
  switch($_SESSION[RequestKey::$DATA_USER_LEVEL]) {
    case 0:
      header('Location: user/');
      break;
    case 1:
      header('Location: arsip/');
      break;
    case 2:
      header('Location: admin/');
      break;
    default:
      header('Location: .');
      break;
  }
}
else {
  $err_login = "";
  $success   = "";

  if (isset($_POST['submit'])) {
    if(isset($_POST[RequestKey::$DATA_USER_EMAIL])) {
      $db = new DBHelper();
      $email = $db->escapeInput($_POST[RequestKey::$DATA_USER_EMAIL]);

      if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $err_login = "Email tidak valid";
        }
      }
      else {
        $err_login = "Email tidak boleh kosong";
      }

      if (empty($err_login)) {
        $array = array();
        $array[RequestKey::$DATA_USER_EMAIL] = $email;
        $array[RequestKey::$DATA_TIPE]      = RequestKey::$DATA_RESET;

        $url = 'http://arsip.hafidhaulia.com/api/send-email.php';

        $ch = curl_init();

        $postString = http_build_query($array, '', '&');

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
          $success = true;
        }
        else {
          $err_login = $result;
        }
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
    <title>Forget Password</title>
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
                  <form id="login-form" method="post">
                    <div class="form-group">
                      <input id="login-email" type="email" name="<?=RequestKey::$DATA_USER_EMAIL?>" required="" class="input-material">
                      <label for="login-email" class="label-material">Email</label>
                    </div>
                    <?= (!empty($err_login)) ? '<p class="text-danger">'.$err_login.'</p>' : '' ?>
                    <?= (!empty($success)) ? '<p class="text-success">Check your email</p>' : '' ?>
                    <a href="." class="btn btn-secondary">Back</a>
                    <button id="reset" name="submit" class="btn btn-primary">Reset</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="copyrights text-center">
        <p>Design by <a href="https://bootstrapious.com/admin-templates" class="external">Bootstrapious</a>
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
  </body>
</html>
