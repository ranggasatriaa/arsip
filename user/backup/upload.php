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
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Profil</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="robots" content="all,follow">
  <!-- Bootstrap CSS-->
  <link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome CSS-->
  <link rel="stylesheet" href="../vendor/font-awesome/css/font-awesome.min.css">
  <!-- Fontastic Custom icon font-->
  <link rel="stylesheet" href="../css/fontastic.css">
  <!-- Google fonts - Poppins -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,700">
  <!-- theme stylesheet-->
  <link rel="stylesheet" href="../css/style.default.css" id="theme-stylesheet">
  <!-- Custom stylesheet - for your changes-->
  <link rel="stylesheet" href="../css/custom.css">
  <!-- Favicon-->
  <link rel="shortcut icon" href="../img/favicon.ico">
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <!-- Tweaks for older IEs--><!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
  <script src="../js/moment.js"></script>

  <script>
  var alert = 0;
  </script>
  <?php

  if (isset($_SESSION['upload'])) {
    // echo "string";
    echo '<script> var alert = '.$_SESSION['upload'].';</script>';
    unset($_SESSION['upload']);
  }
  ?>
</head>
<body>
  <!-- <?php echo $_SESSION['upload'];?> -->
  <!-- Upload file code -->
  <?php
  if (isset($_POST[RequestKey::$DATA_USER_ID])) {
    $uid = $_POST[RequestKey::$DATA_USER_ID];
    $string = $uid."-".strtotime("now");
    $fkey = $db->String2Hex($string);

    //cek folder
    $target_dir = "../assets/user_file/user/".$user->user_key."/";
    if (!file_exists($target_dir)) {
      mkdir($target_dir, 0777, true);
    }
    // echo "string";
    // $_SESSION['upload'] = 6;
    // header('Location: upload.php');


    $target_file1 = $target_dir . basename($_FILES["fileUpload"]["name"]);
    $target_file = $target_dir . strval($fkey);
    // $uploadOk = 1;
    $file_extension = strtolower(pathinfo($target_file1,PATHINFO_EXTENSION));
    $file_name = pathinfo($target_file1,PATHINFO_FILENAME);
    $file_size = $_FILES["fileUpload"]["size"];

echo $file_name;
    $array = array();
    $array[RequestKey::$DATA_FILE_KEY]  = $db->escapeInput($fkey);
    $array[RequestKey::$DATA_FILE_NAME] = $db->escapeInput($file_name);
    $array[RequestKey::$DATA_FILE_SIZE] = $db->escapeInput($file_size);
    $array[RequestKey::$DATA_USER_ID] = $db->escapeInput($uid);
    //verif
    if($file_extension == "pdf") {
      if (!($db->fileNameExist($file_name,$uid))) {
        if($db->isQuotaEnough($uid,$file_size)){
          if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $target_file)) {
            if ($db->saveFile($array)) {
              //sukses
              $_SESSION['upload'] = 1;
              header('Location: upload.php');
            }
            else {
              //gagal memasukkan database
              $_SESSION['upload'] = 2;
              header('Location: upload.php');
            }
          }
          else {
            //gagal memindah file
            $_SESSION['upload'] = 2;
            header('Location: upload.php');
          }
        }
        else{
          //kuota tidak cukup
          $_SESSION['upload'] = 3;
          header('Location: upload.php');
        }
      }
      else {
        $_SESSION['upload'] = 4;
        header('Location: upload.php');
        //file telah ada
      }
    }
    else {
      $_SESSION['upload'] = 5;
      header('Location: upload.php');
      //Maaf, hanya dapat menerima file berekstensi pdf
    }
  }
  ?>
  <div class="page">
    <!-- Main Navbar-->
    <header class="header">
      <nav class="navbar">
        <!-- Search Box-->
        <div class="search-box">
          <button class="dismiss"><i class="icon-close"></i></button>
          <form id="searchForm" action="#" role="search">
            <input type="search" placeholder="What are you looking for..." class="form-control">
          </form>
        </div>
        <div class="container-fluid">
          <div class="navbar-holder d-flex align-items-center justify-content-between">
            <!-- Navbar Header-->
            <div class="navbar-header">
              <!-- Navbar Brand --><a href="index.html" class="navbar-brand">
              <div class="brand-text brand-big"><span>Arsip </span><strong>Keluarga</strong></div>
              <div class="brand-text brand-small"><strong>BD</strong></div></a>
              <!-- Toggle Button--><a id="toggle-btn" href="#" class="menu-btn active"><span></span><span></span><span></span></a>
            </div>
            <!-- Navbar Menu -->
            <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
              <!-- Logout    -->
              <li class="nav-item"><a href="../logout.php" class="nav-link logout">Logout<i class="fa fa-sign-out"></i></a></li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
    <div class="page-content d-flex align-items-stretch">
      <!-- Side Navbar -->
      <nav class="side-navbar">
        <!-- Sidebar Header-->
        <div class="sidebar-header d-flex align-items-center">
          <div class="avatar"><img src="../assets/user_img/user/<?=($user->user_image != "") ?$user->user_image:'no_image_image.png' ?>" alt="..." class="img-fluid rounded-circle"></div>
          <div class="title">
            <h1 class="h4"><?=$user->user_name;?></h1>
          </div>
        </div>
        <!-- Sidebar Navidation Menus--><span class="heading">Main</span>
        <ul class="list-unstyled">
          <li><a href="."> <i class="icon-home"></i>Dashboard </a></li>
          <li><a href="profil.php"> <i class="icon-user"></i>Profil </a></li>
          <li class="active"><a href="upload.php"> <i class="fa fa-upload"></i>Upload </a></li>
        </ul>
      </nav>
      <div class="content-inner">
        <!-- Page Header-->
        <header class="page-header">
          <div class="container-fluid">
            <h2 class="no-margin-bottom">Upload file</h2>
          </div>
        </header>
        <!-- Dashboard Header Section    -->
        <section class="dashboard-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <form class="form-inline" action="upload.php" method="post" enctype="multipart/form-data">
                      <div class="form-group row">
                        <label for="fileInput" class="col-sm-3 form-control-label">File input</label>
                        <div class="col-sm-9">
                          <input type="hidden" name="<?= RequestKey::$DATA_USER_ID ?>" value="<?= $user->user_id ?>">
                          <input name="fileUpload" id="fileUpload" type="file" class="form-control-file">
                        </div>
                      </div>
                      <div class="form-group row">
                        <button type="submit" class="btn btn-primary">Submit</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- Page Footer-->
        <footer class="main-footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6">
                <p>Dinas Kearsipan dan Perpustakaan</p>
              </div>
              <div class="col-sm-6 text-right">
                <p>Design by <a href="https://bootstrapious.com/admin-templates" class="external">Bootstrapious</a></p>
                <!-- Please do not remove the backlink to us unless you support further theme's development at https://bootstrapious.com/donate. It is part of the license conditions. Thank you for understanding :)-->
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
  </div>
  <!-- JavaScript files-->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/popper.js/umd/popper.min.js"> </script>
  <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="../vendor/jquery.cookie/jquery.cookie.js"> </script>
  <script src="../vendor/chart.js/Chart.min.js"></script>
  <script src="../vendor/jquery-validation/jquery.validate.min.js"></script>
  <!-- <script src="../js/charts-custom.js"></script> -->
  <!-- Main File-->
  <script src="../js/front.js"></script>

  <script>
  $( document ).ready(function() {
    if (alert == 1) {
      swal("Success!","","success");
    }
    else if (alert == 2) {
      swal("Failed!","","error");
    }
    else if (alert == 3) {
      swal("Limited file Quota!","","error");
    }
    else if (alert == 4) {
      swal("Same file name exist!","","error");
    }
    else if (alert == 5) {
      swal("Wrong format file!","","error");
    }
    else if (alert == 6) {
      swal("Cek","","error");
    }
  });
  </script>
</body>
</html>
