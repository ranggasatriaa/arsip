<script>
function rename() {
  alert("Nama File berhasil di rubah");
  window.location.href = "index.php";
}
</script>

<?php
session_start();
error_reporting(0);
if (!isset($_SESSION['user_id'])){
  header('location:../index.php');
  exit;
}elseif($_SESSION['user_level'] != 1){
  header('location:../unautorize.php');
  exit;
}else{
  // echo "success";
  include('../template/header.php');

  // if (isset($_POST[RequestKey::$DATA_FILE_NAME]) && isset($_POST[RequestKey::$DATA_FILE_KEY]) && isset($_POST[RequestKey::$DATA_USER_ID])) {
  if (($_GET[RequestKey::$DATA_FILE_NAME] != "") && ($_GET[RequestKey::$DATA_FILE_KEY] != "") && ($_GET[RequestKey::$DATA_USER_ID] != "")) {

    // echo "masuk";
    $array = array();
    $array[RequestKey::$DATA_FILE_KEY] = $_GET[RequestKey::$DATA_FILE_KEY];
    $array[RequestKey::$DATA_FILE_NAME] = $_GET[RequestKey::$DATA_FILE_NAME];
    $array[RequestKey::$DATA_USER_ID] = $_GET[RequestKey::$DATA_USER_ID];
    $db->changeFileName($array);
    echo '<script type="text/javascript"> rename()</script>';
  }
  $file_key = $_GET['file-key'];
  $file = $db->getFileByKey($file_key);
}
?>

<title>Arsip Keluarga | Rename</title>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Rename File
    <small>Control panel</small>
  </h1>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-6">
      <div class="box box-warning">
        <form class="form" action="rename.php" method="get">
          <div class="box box-body">
            <div class="form-group">
              <label for="file_name">Nama File</label>
              <input class="form-control" type="text" name="file-name" value="<?php echo $file->file_name ?>">
              <input type="hidden" name="file-key" value="<?php echo $file->file_key?>">
              <input type="hidden" name="user-id" value="<?php echo $file->user_id?>">
            </div>
            <div class="form-group">
              <input class="btn btn-success" type="submit" name="submit" value="Selesai">
              <input class="btn btn-warning" type="reset" name="reset" value="reset">
              <a class="btn btn-danger pull-right" href="index.php">Batal</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>


  <?php include('../template/footer.php') ?>
