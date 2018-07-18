<?php
session_start();
require_once('../includes/request-key.php');
require_once('../includes/db-helper.php');

$err_name = '';
$err_nik = '';
$err_kk = '';
$err_phone = '';
$err_image = '';

$status = 0;
$message = '';

if(!isset($_SESSION[RequestKey::$DATA_USER_KEY]) && !isset($_SESSION[RequestKey::$DATA_USER_LEVEL])) {
  header('Location: ../.');
}
else if ($_SESSION[RequestKey::$DATA_USER_LEVEL] != 2) {
  //unautorize
}
else {
  $db = new DBHelper();
  $user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY]);

  //VALIDASI
  if (isset($_POST['submit'])) {
    if(isset($_POST[RequestKey::$DATA_USER_NAME]) && isset($_POST[RequestKey::$DATA_USER_NIK]) && isset($_POST[RequestKey::$DATA_USER_KK]) && isset($_POST[RequestKey::$DATA_USER_PHONE])){
      $user_nik   = $db->escapeInput($_POST[RequestKey::$DATA_USER_NIK]);
      $user_kk    = $db->escapeInput($_POST[RequestKey::$DATA_USER_KK]);
      $user_name  = $db->escapeInput($_POST[RequestKey::$DATA_USER_NAME]);
      $user_phone = $db->escapeInput($_POST[RequestKey::$DATA_USER_PHONE]);

      if (!preg_match("/^[a-zA-Z ]{1,50}$/",$user_name)) {
        $err_name = "Nama tidak valid";
      }

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

      if (!preg_match("/^[0-9]{1,15}$/",$user_phone)) {
        $err_phone = "Nomor telepon tidak valid";
      }

      if($_FILES[RequestKey::$DATA_IMAGE]["name"] != ''){
        $string = $user->user_id."-".strtotime("now");
        $fkey   = $db->String2Hex($string);

        $target_dir = "../assets/user_img/user/";
        $target_file = $target_dir.$_FILES[RequestKey::$DATA_IMAGE]["name"];
        $file_extension = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $file_name = $_FILES[RequestKey::$DATA_IMAGE]["name"];
        $file_size = $_FILES[RequestKey::$DATA_IMAGE]["size"];

        if($file_size > 2097152){
          $err_image = "File is to big max 2Mb! ";
        }

        if($file_extension != "jpg" && $file_extension != "jpeg" && $file_extension != "png") {
          $err_image = "Wrong file format (jpg, png, jpeg)";
        }
      }

      if(empty($err_name) && empty($err_nik) && empty($err_kk) && empty($err_phone) && empty($err_image)){
        $array = array();
        $array[RequestKey::$DATA_USER_NAME]   = $db->escapeInput($_POST[RequestKey::$DATA_USER_NAME]);
        $array[RequestKey::$DATA_USER_NIK]    = $db->escapeInput($_POST[RequestKey::$DATA_USER_NIK]);
        $array[RequestKey::$DATA_USER_KK]     = $db->escapeInput($_POST[RequestKey::$DATA_USER_KK]);
        $array[RequestKey::$DATA_USER_PHONE]  = $db->escapeInput($_POST[RequestKey::$DATA_USER_PHONE]);
        $array[RequestKey::$DATA_USER_IMAGE]  = (isset($fkey) ? $fkey.'.'.$file_extension : '');
        $array[RequestKey::$DATA_IMAGE]       = (isset($fkey) ? $_FILES[RequestKey::$DATA_IMAGE]["name"] : '');

        if ($checkUser = $db->getUserByKey($user->user_key)) {
          $array[RequestKey::$DATA_USER_ID] = $checkUser->user_id;
          if (!$db->isNIKExistOnEdit($array[RequestKey::$DATA_USER_NIK],$checkUser->user_id)) {
            if (!$db->isKKExistOnEdit($array[RequestKey::$DATA_USER_KK],$checkUser->user_id)) {
              if ($db->updateUser($array)) {
                if ($array[RequestKey::$DATA_USER_IMAGE] != "" && $array[RequestKey::$DATA_IMAGE] != "") {
                  if (move_uploaded_file($_FILES[RequestKey::$DATA_IMAGE]["tmp_name"], $target_dir.$fkey.'.'.$file_extension)) {
                    if ($user->user_image != "") {
                      unlink('../assets/user_img/user/'.$user->user_image);
                    }
                  }
                }
                $status = 1;
                $user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY]);
              }
              else {
                $status = 2;
                $message = $db->strBadQuery;
              }
            }
            else {
              $status = 2;
              $message = "kk sudah terdaftar";
            }
          }
          else {
            $status = 2;
            $message = "nik sudah terdaftar";
          }
        }
        else {
          $status = 2;
          $message = $db->accessForbidden;
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
  <title>Edit profil</title>
  <?php include('head.php'); ?>
</head>
<body>

  <div class="page">

    <?php include('main-navbar.php'); ?>

    <!-- Side Navbar -->
    <div class="page-content d-flex align-items-stretch">

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
            <h2 class="no-margin-bottom">Edit profil</h2>
          </div>
        </header>
        <!-- Breadcrumb-->
        <div class="breadcrumb-holder container-fluid">
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href=".">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="profil.php">Profil</a></li>
            <li class="breadcrumb-item active">Edit Profil</li>
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
                    <form class="form-horizontal" action="edit_profil.php" method= "post" enctype="multipart/form-data">
                      <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Foto</label>
                        <div class="col-sm-4">
                          <img id="img_prev" src="../assets/user_img/user/<?=($user->user_image != "") ?$user->user_image:'no_image_image.png' ?>" alt="..." class="img-fluid" style="margin-bottom:10px">
                        </div>
                        <div class="col-sm-5">
                        </div>
                        <div class="col-sm-3">
                        </div>
                        <div class="col-sm-9">
                          <input id="image" type="file" name="<?= RequestKey::$DATA_IMAGE ?>" value="">
                          <small class="form-text <?=($err_image != "" ? 'text-danger' : '')?>"><?=($err_image != "" ? $err_image : 'File gambar max 2Mb.')?></small>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Nama</label>
                        <div class="col-sm-9">
                          <input id="inputHorizontalSuccess" name="<?= RequestKey::$DATA_USER_NAME ?>" type="text" placeholder="Nama" class="form-control form-control-success" value="<?= $user->user_name?>" required>
                          <small class="form-text <?=($err_name != "" ? 'text-danger' : '')?>"><?=($err_name != "" ? $err_name : 'Karakter huruf dan spasi.')?></small>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Nomor Induk Kependudukan</label>
                        <div class="col-sm-9">
                          <input id="inputHorizontalSuccess" name="<?= RequestKey::$DATA_USER_NIK ?>" type="text" placeholder="NIK" class="form-control form-control-success" value="<?= $user->user_nik?>" required>
                          <small class="form-text <?=($err_nik != "" ? 'text-danger' : '')?>"><?=($err_nik != "" ? $err_nik : 'Karakter angka 16 digit.')?></small>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Nomor Kartu Keluarga</label>
                        <div class="col-sm-9">
                          <input id="inputHorizontalWarning" name="<?= RequestKey::$DATA_USER_KK ?>" type="text" placeholder="KK" class="form-control form-control-warning" value="<?= $user->user_kk?>" required>
                          <small class="form-text <?=($err_kk != "" ? 'text-danger' : '')?>"><?=($err_kk != "" ? $err_kk : 'Karakter angka 16 digit.')?></small>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Nomor Telepon</label>
                        <div class="col-sm-9">
                          <input id="inputHorizontalWarning" name="<?= RequestKey::$DATA_USER_PHONE ?>" type="text" placeholder="Nomor telepon" class="form-control form-control-warning"value="<?= $user->user_phone?>" required>
                          <small class="form-text <?=($err_phone != "" ? 'text-danger' : '')?>"><?=($err_phone != "" ? $err_phone : 'Karakter angka max. 15 digit.')?></small>
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3">
                          <input type="hidden" name="<?= RequestKey::$DATA_USER_ID ?>" value="<?= $user->user_id ?>">
                          <?php if($user->user_image != ''){
                            echo '<input type="hidden" name="'. RequestKey::$DATA_USER_IMAGE .'" value="'.$user->user_image .'">';
                          }
                          ?>
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

  <script>
  function readURL(input){
    var reader = new FileReader();
    reader.onload = function(e){
      $('#img_prev').attr('src',e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
  $("#image").change(function(){
    readURL(this);
  })
</script>

</body>
</html>
