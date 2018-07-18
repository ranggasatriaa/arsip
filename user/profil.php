<?php
session_start();
require_once('../includes/request-key.php');
require_once('../includes/db-helper.php');

if(!isset($_SESSION[RequestKey::$DATA_USER_KEY]) && !isset($_SESSION[RequestKey::$DATA_USER_LEVEL])) {
  header('Location: ../.');
}
else if ($_SESSION[RequestKey::$DATA_USER_LEVEL] != 0) {
  //unautorize
}
else {
  $db = new DBHelper();
  $user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY]);

}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Profil</title>

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
            <li><a href="upload.php"> <i class="fa fa-upload"></i>Upload </a></li>
          </ul>
        </nav>
        <div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Profil</h2>
            </div>
          </header>
          <!-- Breadcrumb-->
          <div class="breadcrumb-holder container-fluid">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href=".">Dashboard</a></li>
              <li class="breadcrumb-item active">Profil</li>
            </ul>
          </div>
          <!-- Dashboard Header Section    -->
          <section class="dashboard-header">
            <div class="container-fluid">
              <div class="row">
                <!-- Client Profile -->
                <div class="col-lg-12">
                  <div class="client card">
                    <div class="card-body text-center">
                      <div class="client-avatar"><img src="../assets/user_img/user/<?=($user->user_image != "") ?$user->user_image:'no_image_image.png' ?>" alt="..."  class="rounded-circle crop" style="height:100px; width: 100px; object-fit: contain;">
                        <div class="status bg-green"></div>
                      </div>
                      <div class="client-title">
                        <h3><?=$user->user_name;?></h3><span><?=$user->user_email;?></span>
                      </div>
                      <div class="client-info">
                        <div class="row">
                          <div class="col-lg-3 col-6"><strong>NIK</strong><br><small><?=$user->user_nik;?></small></div>
                          <div class="col-lg-3 col-6"><strong>KK</strong><br><small><?=$user->user_kk;?></small></div>
                          <div class="col-lg-3 col-6"><strong>Phone number</strong><br><small><?=$user->user_phone;?></small></div>
                          <div class="col-lg-3 col-6"><strong>Join date</strong><br><small><?=date("d M Y", strtotime($user->user_join_date))?></small></div>
                        </div>
                      </div>
                      <br>
                      <a href="edit_profil.php" class="btn btn-primary">Ubah profil</a>
                      <a href="edit_password.php" class="btn btn-primary">Ubah password</a>
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

  </body>
</html>
