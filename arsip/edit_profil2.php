<?php
session_start();
require_once('../includes/request-key.php');
require_once('../includes/db-helper.php');

if(!isset($_SESSION[RequestKey::$DATA_USER_KEY]) && !isset($_SESSION[RequestKey::$DATA_USER_LEVEL])) {
  header('Location: ../.');
}
else if ($_SESSION[RequestKey::$DATA_USER_LEVEL] != 1) {
  //unautorize
}
else {
  $db = new DBHelper();
  $user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY]);
}
//VALIDASI
$err_name = '';
$err_nik = '';
$err_kk = '';
$err_phone = '';
$err_image = '';

?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit profil</title>
  <?php include('head.php'); ?>
</head>
<body>

  <?php
  if(isset($_POST[RequestKey::$DATA_USER_ID]) && isset($_POST[RequestKey::$DATA_USER_NAME]) && isset($_POST[RequestKey::$DATA_USER_NIK]) && isset($_POST[RequestKey::$DATA_USER_PHONE])){
    //validasi tulisan
    if (strlen($_POST[RequestKey::$DATA_USER_NIK]) != 16){
      $err_nik = "NIK tidak valid, panjang NIK harus 16 digit";
    }
    //konfirmasi kk
    if (strlen($_POST[RequestKey::$DATA_USER_KK]) != 16){
      $err_kk = "Nomor KK tidak valid, panjang KK harus 16 digit";
    }
    //konfirmasi nama "^[a-zA-Z ]{1,50}$"
    if (!preg_match("/^[a-z A-Z]*$/",$_POST[RequestKey::$DATA_USER_NAME])) {
      $err_name = "Nama harus menggunakan huruf";
    }
    if (strlen($_POST[RequestKey::$DATA_USER_PHONE]) < 11 && strlen($_POST[RequestKey::$DATA_USER_PHONE]) > 12){
      $err_phone = "Nomor Telepon tidak valid";
    }elseif (substr($_POST[RequestKey::$DATA_USER_PHONE], 0, 1)) {
      $err_phone = "Nomor Telepon tidak valid";
    }
    echo "sebelum detec image ";
    if(isset($_FILES[RequestKey::$DATA_IMAGE])){
      echo "string ";
    }else {
      echo "fail ";
    }
    //cek ada image
    if($_POST[RequestKey::$DATA_IMAGE] != '' ){
      echo "masuk detect image";
      $uid = $_POST[RequestKey::$DATA_USER_ID];
      $string = $uid."-".strtotime("now");
      $fkey = $db->String2Hex($string);

      // //cek folder
      // if (!file_exists($target_dir)) {
      //   mkdir($target_dir, 0777, true);
      // }
      // $old_user = $db->getUserById($uid);
      $old_file = $_POST[RequestKey::$DATA_USER_IMAGE];
      $target_old_file = '../assets/user_img/user/'.$old_file;
      echo "oldfile: ".$old_file;
      $target_dir = "../assets/user_img/user/";
      $target_file = $target_dir . $_FILES[RequestKey::$DATA_IMAGE]["name"];
      // $target_file = $target_dir . strval($fkey);
      $file_extension = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
      $file_name = $_FILES[RequestKey::$DATA_IMAGE]["name"];
      $file_size = $_FILES[RequestKey::$DATA_IMAGE]["size"];
      echo "<br/>file detail:";
      echo "<br/> target1: ". $target_file;
      // echo "<br/> target: ". $target_file;
      echo "<br/> ext: ". $file_extension;
      echo "<br/> name: ". $file_name;
      echo "<br/> size: ". $file_size;

      //verif image
      if($file_size > 2097152){
        $err_image = "File is to big max 2Mb! ";
      }
      if ($db->fileNameExist($file_name,$uid)) {
        $err_image = "Same file name exist! ";
      }
      if($file_extension != "jpg" && $file_extension != "jpeg" && $file_extension != "png" && $file_extension != "bmp" && $file_extension != "gif") {
        $err_image = "Wrong file format (jpg, png, gif, bmp)";
      }
      echo "error image: ".$err_image;
      if(empty($err_name) && empty($err_nik) && empty($err_kk) && empty($err_phone) && empty($err_image)){
        echo "massuk err ";
        $array = array();
        $array[RequestKey::$DATA_USER_ID] = $db->escapeInput($_POST[RequestKey::$DATA_USER_ID]);
        $array[RequestKey::$DATA_USER_NAME] = $db->escapeInput($_POST[RequestKey::$DATA_USER_NAME]);
        $array[RequestKey::$DATA_USER_NIK] = $db->escapeInput($_POST[RequestKey::$DATA_USER_NIK]);
        $array[RequestKey::$DATA_USER_KK] = $db->escapeInput($_POST[RequestKey::$DATA_USER_KK]);
        $array[RequestKey::$DATA_USER_PHONE] = $db->escapeInput($_POST[RequestKey::$DATA_USER_PHONE]);
        $array[RequestKey::$DATA_USER_IMAGE] = $db->escapeInput($file_name);;
        $array[RequestKey::$DATA_IMAGE] = $_FILES[RequestKey::$DATA_IMAGE]["name"];
        echo "masuk err ";
        if (move_uploaded_file($_FILES[RequestKey::$DATA_IMAGE]["tmp_name"], $target_file)) {
          echo "masuk move ";
          if ($db->updateUser($array)) {
            if (file_exists($target_file)){
              unlink('../assets/user_img/user/'.$old_file);
            }
            //sukses
            // echo "sukses ";
            echo '<script> var alert = swal("Success!","","success");</script>';
            $_SESSION['edit_profil'] = 1;
            header('Location: profil.php');
          }
          else {
            //gagal memasukkan database
            echo '<script> var alert =   swal("Failed!","Gagal query","error");</script>';
            // $_SESSION['upload'] = 2;
            // header('Location: upload.php');
          }
        }
        else {
          //gagal memindah file
          echo '<script> var alert =   swal("Failed!","Gagal upload","error");</script>';
          // $_SESSION['upload'] = 2;
          // header('Location: upload.php');
        }

      }
    } //close detect image
    else {
      if(empty($err_name) && empty($err_nik) && empty($err_kk) && empty($err_phone)){

        $array = array();
        $array[RequestKey::$DATA_USER_ID] = $db->escapeInput($_POST[RequestKey::$DATA_USER_ID]);
        $array[RequestKey::$DATA_USER_NAME] = $db->escapeInput($_POST[RequestKey::$DATA_USER_NAME]);
        $array[RequestKey::$DATA_USER_NIK] = $db->escapeInput($_POST[RequestKey::$DATA_USER_NIK]);
        $array[RequestKey::$DATA_USER_KK] = $db->escapeInput($_POST[RequestKey::$DATA_USER_KK]);
        $array[RequestKey::$DATA_USER_PHONE] = $db->escapeInput($_POST[RequestKey::$DATA_USER_PHONE]);
        $array[RequestKey::$DATA_USER_IMAGE] = "";
        $array[RequestKey::$DATA_IMAGE] = "";


        if ($db->updateUser($array)) {
          // echo '<script> swal("Success!","Cek Inputan","success");</script>';
          $_SESSION['edit_profil'] = 1;
          header('Location: profil.php');

        }
        else {
          echo '<script> swal("Failed!","Cek Inputan","error");</script>';
          // $_SESSION['edit'] = 2;
          // header('Location: edit_profil.php');
        }
      }
      else {
        echo '<script> var alert = swal("Failed!!","Cek Inputan","error");</script>';
        // $_SESSION['edit'] = 3;
        // header('Location: edit_profil.php');
      }
    }
  }
  ?>
  <div class="page">
    <?php  include('main-navbar.php'); ?>
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
                          <small class="form-text <?=($err_name != "" ? 'text-danger' : '')?>"><?=($err_name != "" ? $err_name : 'Karakter huruf.')?></small>
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
                          <small class="form-text <?=($err_phone != "" ? 'text-danger' : '')?>"><?=($err_phone != "" ? $err_phone : 'Karakter angka 11-12 digit.')?></small>
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
