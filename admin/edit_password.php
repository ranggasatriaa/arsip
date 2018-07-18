<?php
session_start();
require_once('../includes/request-key.php');
require_once('../includes/db-helper.php');

//VALIDASI
$err_pw_1 = '';
$err_pw_2 = '';
$err_pw_3 = '';

$status = 0;
$message = '';

if(!isset($_SESSION[RequestKey::$DATA_USER_KEY]) && !isset($_SESSION[RequestKey::$DATA_USER_LEVEL])) {
  header('Location: ../.');
}
else if ($_SESSION[RequestKey::$DATA_USER_LEVEL] != 2) {
  //unautorize
}
else {
  //DB
  $db = new DBHelper();
  $user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY]);

  if (isset($_POST['submit'])) {
    if(isset($_POST['password-lama']) && isset($_POST['password-baru']) && isset($_POST['password-baru-2'])) {
      $password_lama    = $db->escapeInput($_POST['password-lama']);
      $password_baru    = $db->escapeInput($_POST['password-baru']);
      $password_baru_2  = $db->escapeInput($_POST['password-baru-2']);

      if (!preg_match("/^[a-zA-Z0-9]{8,20}$/",$password_lama)) {
        $err_pw_1 = "Input tidak valid";
      }

      if (!preg_match("/^[a-zA-Z0-9]{8,20}$/",$password_baru)) {
        $err_pw_2 = "Input tidak valid";
      }

      if (!preg_match("/^[a-zA-Z0-9]{8,20}$/",$password_baru_2)) {
        $err_pw_3 = "Input tidak valid";
      }

      if (empty($err_pw_1) && empty($err_pw_2) && empty($err_pw_3)) {
        if (sha1($password_lama) == $user->user_password) {
          if ($password_baru == $password_baru_2) {
            if($db->changePassword($user->user_id,sha1($password_baru))){
              $status = 1;
            }else {
              $status = 2;
              $message = $db->strBadQuery;
            }
          }else {
            $err_pw_3 = "Konfirmasi password tidak sesuai";
          }
        }
        else{
          $err_pw_1 = "Password lama salah";
        }
      }
    }
    else {
      $status = 2;
      $message = $db->strBadRequest;
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit password</title>

  <?php include('head.php'); ?>

</head>
<body>
  <div class="page">

    <?php include('main-navbar.php'); ?>

    <div class="page-content d-flex align-items-stretch">
      <!-- Side Navbar -->
      <nav class="side-navbar">
        <!-- Sidebar Header-->
        <div class="sidebar-header d-flex align-items-center">
          <div class="avatar"><img src="../assets/user_img/user/<?=($user->user_image != "") ?$user->user_image:'no_image_image.png' ?>" alt="..." class="img-fluid rounded-circle" style="height:55px; width: 55px; object-fit: contain;"></div>
          <div class="title">
            <h1 class="h4"><?=$user->user_name;?></h1>
          </div>
        </div>
        <!-- Sidebar Navidation Menus--><span class="heading">Main</span>
        <ul class="list-unstyled">
          <li><a href="."> <i class="icon-home"></i>Dashboard </a></li>
          <li class="active"><a href="profil.php"> <i class="icon-user"></i>Profil </a></li>
        </ul>
      </nav>
      <div class="content-inner">
        <!-- Page Header-->
        <header class="page-header">
          <div class="container-fluid">
            <h2 class="no-margin-bottom">Edit password</h2>
          </div>
        </header>
        <!-- Breadcrumb-->
        <div class="breadcrumb-holder container-fluid">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href=".">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="profil.php">Profil</a></li>
            <li class="breadcrumb-item active">Edit Password</li>
          </ul>
        </div>
        <!-- Dashboard Header Section    -->
        <section class="dashboard-header">
          <div class="container-fluid">
            <div class="row">
              <!-- Horizontal Form-->
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <form class="form-horizontal" method= "post">
                      <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Password Lama</label>
                        <div class="col-sm-9">
                          <input name="password-lama" type="password" placeholder="Password lama" class="form-control form-control-success"><small class="form-text <?=($err_pw_1 != "" ? 'text-danger' : '')?>"><?=($err_pw_1 != "" ? $err_pw_1 : 'Karakter huruf dan angka min.8 dan max 20.')?></small>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Password Baru</label>
                        <div class="col-sm-9">
                          <input name="password-baru" type="password" placeholder="Password baru" class="form-control form-control-success"><small class="form-text <?=($err_pw_2 != "" ? 'text-danger' : '')?>"><?=($err_pw_2 != "" ? $err_pw_2 : 'Karakter huruf dan angka min.8 dan max 20.')?></small>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Konfirmasi password</label>
                        <div class="col-sm-9">
                          <input name="password-baru-2" type="password" placeholder="Pasword" class="form-control form-control-warning"><small class="form-text <?=($err_pw_3 != "" ? 'text-danger' : '')?>"><?=($err_pw_3 != "" ? $err_pw_3 : 'Karakter huruf dan angka min.8 dan max 20.')?></small>
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3">
                          <a href="profil.php" class="btn btn-secondary">Cancel</a>
                          <button value="submit" name="submit" class="btn btn-primary">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <?php include('page-footer.php'); ?>

      </div>
    </div>
  </div>

  <?php include('foot.php'); ?>
  <?php
  echo '<script>var status = '.$status.'; var message = "'.$message.'";</script>';
  $status = 0;
  ?>

  <script>
  $(document).ready(function() {
    if (status == 1) {
      swal("Success!","","success").then((value) => {
        window.location.href = "profil.php";
      });
    }
    else if (status == 2) {
      swal("Failed!",message,"error");
    }
  });
  </script>

</body>
</html>
