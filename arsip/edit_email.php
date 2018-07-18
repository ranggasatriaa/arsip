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
  //VALIDASI
  $err_email = "";

  //DB
  $db = new DBHelper();
  $user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY]);
  $user_sebelum = $db->getUserByKey($_GET[RequestKey::$DATA_USER_KEY]);


}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit email</title>
  <?php include('head.php'); ?>
</head>
<body>
  <?php
  if (isset($_POST['submit'])) {
    if(isset($_POST[RequestKey::$DATA_USER_EMAIL]) && isset($_GET[RequestKey::$DATA_USER_KEY])) {
      $email    = $db->escapeInput($_POST[RequestKey::$DATA_USER_EMAIL]);
      $user_key = $db->escapeInput($_GET[RequestKey::$DATA_USER_KEY]);

      if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $err_email = "Email tidak valid";
        }
      }
      else {
        $err_email = "Email tidak boleh kosong";
      }

      if (empty($err_email) && !empty($user_key)) {
        if($db->changeEmailUser($user_key,$email)){
          ?>
          <script> var alert =   swal({
            title: "Sukses!",
            text: "email berhasil dirubah",
            icon: "success",
            button: "OK",
          }).then((value) => {
            window.location.href = "."
          });
          </script>
          <?php
          // echo '<script type="text/javascript">alert("berhasil mengubah email")</script>';
        }
        else {
          // echo '<script type="text/javascript">alert("gagal mengubah email")</script>';
          echo '<script> var alert = swal("Failed!","gagal mengubah email","error");</script>';
        }
      }
      else {
        // echo '<script type="text/javascript">alert("'.$db->strBadRequest.'")</script>';
        echo '<script> var alert = swal("Failed!","'.$db->strBadRequest.'","error");</script>';
      }
    }
    else {
      echo '<script> var alert =   swal("Failed!","'.$db->strBadRequest.'","error");</script>';

      // echo '<script type="text/javascript">alert("'.$db->strBadRequest.'")</script>';
    }
  }

   ?>
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
          <li class="active"><a href="."> <i class="icon-home"></i>Dashboard </a></li>
          <li><a href="profil.php"> <i class="icon-user"></i>Profil </a></li>
        </ul>
      </nav>
      <div class="content-inner">
        <!-- Page Header-->
        <header class="page-header">
          <div class="container-fluid">
            <h2 class="no-margin-bottom">Edit Email</h2>
          </div>
        </header>
        <!-- Dashboard Header Section    -->
        <section class="dashboard-header">
          <div class="container-fluid">
            <div class="row">
              <!-- Basic Form-->
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-body">
                    <form method="post">
                      <div class="form-group">
                        <label class="form-control-label">Email</label>
                        <input type="email" placeholder="Email Address" class="form-control" name="<?=RequestKey::$DATA_USER_EMAIL?>" value="<?=$user_sebelum->user_email  ?>"required>
                        <small class="form-text text-danger"><?=($err_email != "" ? $err_email : '')?></small>
                      </div>
                      <div class="form-group">
                        <a href="." class="btn btn-secondary">Cancel</a>
                        <button type="submit" value="submit" name="submit" class="btn btn-primary">Simpan</button>
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

</body>
</html>
