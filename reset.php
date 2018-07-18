<?php

require_once('includes/db-helper.php');
require_once('includes/json-key.php');
require_once('includes/request-key.php');

$db = new DBHelper();
$error = '';
$status = 0;
// echo $status;

if (isset($_POST['submit']) && isset($_GET['q'])) {
  $q        = $db->escapeInput($_GET['q']);
  $new      = sha1($db->escapeInput($_POST['new']));
  $confirm  = sha1($db->escapeInput($_POST['confirm']));
  if ($new === $confirm) {
    if ($user = $db->getUserByKey($q)) {
      $uid = $user->user_id;
      if ($db->changePassword($uid,$new)) {
        // $error = 'password telah diganti';
        $status = 1;
      }
      else {
        // $error = 'gagal menyimpan password, pastikan membuka link melalui email';
        $status = 2;
      }
    }
    else {
      // $error = 'access forbidden';
      $status = 3;
    }
  }
  else {
    // $error = 'password dan konfirmasi password berbeda';
    $status = 4;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Ubah password</title>
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
  <!-- <link rel="stylesheet" href="css/custom.css"> -->
  <!-- Favicon-->
  <link rel="shortcut icon" href="img/favicon.ico">
  <link rel="stylesheet" href="css/sweetalert2.min.css" id="theme-stylesheet">
  <script src="js/sweetalert2.all.min.js"></script>

  <!-- Tweaks for older IEs--><!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>
<body>
  <?=$error;?>
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
                    <input id="new" type="password" name="new" required="" class="input-material">
                    <label for="new" class="label-material">Password Baru</label>
                  </div>
                  <div class="form-group">
                    <input id="confirm" type="password" name="confirm" required="" class="input-material">
                    <label for="confirm" class="label-material">Konfirmasi Password Baru</label>
                  </div>
                  <input type="submit" name="submit" value="Reset Password" class="btn btn-primary">
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
  <?php
  // echo $status;
  echo '<script>var status = '.$status.';</script>';
  $status = 0;

  ?>
  <script>
  $(document).ready(function() {
    if (status == 1) {
      swal("Success!","Password berhasil direset silahkan login","success")
      .then((value) => {
        window.location.href = ".";
      });
    }
    else if (status == 2) {
      swal("Failed!","Pastikan membuka link melalui email","error");
    }
    else if (status == 3) {
      swal("Failed!","access forbidden","error");
    }
    else if (status == 4) {
      swal("Failed!","password dan konfirmasi password berbeda","error");
    }
    else if (status == 0) {
      swal("Failed!","cek","error");
    }
  });
  </script>
</body>
</html>
