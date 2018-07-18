<?php
session_start();
require_once('../includes/request-key.php');
require_once('../includes/db-helper.php');

$status = 0;
$message = '';

if(!isset($_SESSION[RequestKey::$DATA_USER_KEY]) && !isset($_SESSION[RequestKey::$DATA_USER_LEVEL])) {
  header('Location: ../.');
}
else if ($_SESSION[RequestKey::$DATA_USER_LEVEL] != 0) {
  //unautorize
}
else {
  $db = new DBHelper();
  $user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY]);

  if(isset($_POST['submit'])) {
    $string = $uid."-".strtotime("now");
    $fkey = $db->String2Hex($string);

    $target_dir = "../assets/user_file/user/".$user->user_key."/";
    if (!file_exists($target_dir)) {
      mkdir($target_dir, 0777, true);
    }
    $target_file1 = $target_dir . basename($_FILES["fileUpload"]["name"]);
    $target_file = $target_dir . strval($fkey);
    // $uploadOk = 1;
    $file_extension = strtolower(pathinfo($target_file1,PATHINFO_EXTENSION));
    $file_name = pathinfo($target_file1,PATHINFO_FILENAME);
    $file_size = number_format($_FILES["fileUpload"]["size"] / 1024, 2);

    $array = array();
    $array[RequestKey::$DATA_FILE_KEY]  = $db->escapeInput($fkey);
    $array[RequestKey::$DATA_FILE_NAME] = $db->escapeInput($file_name);
    $array[RequestKey::$DATA_FILE_SIZE] = $db->escapeInput($file_size);
    $array[RequestKey::$DATA_USER_ID] = $db->escapeInput($uid);
    //verif
    if ($user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY])) {
      if($file_extension == "pdf") {
        $array[RequestKey::$DATA_USER_ID] = $user->user_id;
        if (!($db->fileNameExist($file_name,$array[RequestKey::$DATA_USER_ID]))) {
          if($db->isQuotaEnough($array[RequestKey::$DATA_USER_ID],$file_size)){
            if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $target_file)) {
              if ($db->saveFile($array)) {
                $status = 1;
              }
              else {
                $status = 2;
                $message = $db->strBadQuery;
              }
            }
            else {
              $status = 2;
              $message = "Failed saving file";
            }
          }
          else{
            $status = 2;
            $message = "Limited file Quota!";
          }
        }
        else {
          $status = 2;
          $message = "Same file name exist!";
        }
      }
      else {
        $status = 2;
        $message = "Wrong format file!";
      }
    }
    else {
      $status = 2;
      $message = $db->accessForbidden;
    }
  }

}
?>
<!DOCTYPE html>
<html>
<head>

  <title>Upload</title>

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
        <!-- Breadcrumb-->
        <div class="breadcrumb-holder container-fluid">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href=".">Dashboard</a></li>
            <li class="breadcrumb-item active">Upload</li>
          </ul>
        </div>
        <!-- Dashboard Header Section    -->
        <section class="dashboard-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-body">
                    <form class="form-inline" method="post" enctype="multipart/form-data">
                      <div class="form-group row">
                        <label for="fileInput" class="col-sm-3 form-control-label">File input</label>
                        <div class="col-sm-9">
                          <input name="fileUpload" id="fileUpload" type="file" class="form-control-file">
                        </div>
                      </div>
                      <div class="form-group row">
                        <button name="submit" type="submit" class="btn btn-primary">Submit</button>
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

  <script>
  <?php
  echo 'var status = '.$status.'; var message = "'.$message.'";';
  ?>
  $( document ).ready(function() {
    if (status == 1) {
      swal("Success!","","success");
    }
    else if (status == 2) {
      swal("Failed!",message,"error");
    }
  });
  </script>
</body>
</html>
