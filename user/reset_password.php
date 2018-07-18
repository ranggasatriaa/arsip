<!--
// KURANG
// ----------------------------------------------------------------------------
// konfirmasi error kosong sama
// alert error
// ----------------------------------------------------------------------------
-->

<?php
session_start();
error_reporting(0);
if (!isset($_SESSION['user_id'])){
  header('location:../index.php');
  exit;
}elseif($_SESSION['user_level'] != 0 ){
  header('location:../unautorize.php');
  exit;
}else{
  include('../template/header.php');
  $user = $db->getUserByKey($_GET[RequestKey::$DATA_USER_KEY]);
}

?>
<script>
function reset() {
  alert("Reset password berhasil di rubah");
  window.location.href = "profil.php";
}

function konfirmasi_password_tak_sesuai()() {
  alert("Konfirmasi assword tidak sesuai");
  window.location.href = "reset_password.php?<?php echo RequestKey::$DATA_USER_KEY?>=<?php echo $user->user_key ?>";
}

function password_lama_tak_sesuai() {
  alert("Password lama tidak sesuai");
  window.location.href = "reset_password.php?<?php echo RequestKey::$DATA_USER_KEY?>=<?php echo $user->user_key ?>";
}
</script>
<?php

if(($_GET['password-lama'] != "") && ($_GET['password-baru'] != "") && ($_GET['password-baru-2'] != "") && ($_GET[RequestKey::$DATA_USER_KEY] != "")){
  $password_lama = $db->escapeInput($_GET['password-lama']);
  $password_baru = $db->escapeInput($_GET['password-baru']);
  $password_baru_2 = $db->escapeInput($_GET['password-baru-2']);

  // echo $password_lama." || ";
  // echo $password_baru." || ";
  // echo $password_baru_2." || ";

  if (sha1($password_lama) == $user->user_password) {
    if ($password_baru == $password_baru_2) {
      if($db->changePassword($user->user_id,sha1($password_baru))){
        echo '<script type="text/javascript"> reset()</script>';
      }
    }else {
      // echo "Konfirmasi password tidak sesuai";
      echo '<script type="text/javascript"> konfirmasi_password_tak_sesuai()</script>';
    }
  }else{
    echo '<script type="text/javascript"> password_lama_tak_sesuai()</script>';
    // echo "Password lama tidak sesuai";
  }
}
?>
<!-- Content Header (Page header) -->
<title>Arsip Keluarga | Reset Password</title>

<section class="content-header">
  <h1>
    Reset Password
    <small>Control panel</small>
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-warning">
        <div class="box box-body">
          <form class="" action="reset_password.php" method="get">
            <div class="form-group">
              <label>Password Lama</label>
              <input class="form-control" type="password" name="password-lama" value="" placeholder="Password Lama" required>
            </div>
            <div class="form-group">
              <label>Password Baru</label>
              <input class="form-control" type="password" name="password-baru" value="" placeholder="Password Baru" required>
            </div>
            <div class="form-group">
              <label>Ulangi Password Baru</label>
              <input class="form-control" type="password" name="password-baru-2" value="" placeholder="Ulangi Password Baru" required>
            </div>
            <div class="form-group">
              <input type="hidden" name="<?php echo RequestKey::$DATA_USER_KEY ?>" value="<?php echo $user->user_key ?>">
              <input class="btn btn-success" type="submit" name="sumbit" value="ubah">
              <a class="btn btn-danger pull-right" href="profil.php">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <?php include('../template/footer.php') ?>
