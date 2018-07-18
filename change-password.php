<?php

require_once('includes/db-helper.php');
require_once('includes/json-key.php');
require_once('includes/request-key.php');

$db = new DBHelper();
$error = '';

if (isset($_POST['submit']) && isset($_GET['q'])) {
  $q        = $db->escapeInput($_GET['q']);
  $new      = sha1($db->escapeInput($_POST['new']));
  $confirm  = sha1($db->escapeInput($_POST['confirm']));
  if ($new === $confirm) {
    if ($db->changePassword($q,$new)) {
      $error = 'password telah diganti';
    }
    else {
      $error = 'gagal menyimpan password, pastikan membuka link melalui email';
    }
  }
  else {
    $error = 'password dan konfirmasi password berbeda';
  }
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Ubah password</title>
  </head>
  <body>
    <?=$error;?>
    <form method="post">
      <input type="password" name="new" placeholder="password">
      <input type="password" name="confirm" placeholder="konfirmasi password">
      <input type="submit" name="submit" value="reset">
    </form>
  </body>
</html>
