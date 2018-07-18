<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Konfirmasi email</title>
</head>
<body style="font-family:Helvetica;height: 100%;margin: 0;padding: 0;width: 100%;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #f7f7f7;">
  <center>
    <div style="padding:0px;margin-top:20px; margin-bottom:20px; width:600px; background-color:#ffffff">
      <br>
      <?php
      require_once('includes/db-helper.php');

      $db = new DBHelper();

      if (isset($_GET['email']) && isset($_GET['q'])) {
        $email  = $db->escapeInput($_GET['email']);
        $key    = $db->escapeInput($_GET['q']);
        $user   = $db->getUserByEmail($email);
        if ((int)$user->user_status === 1) {
          ?>
          <h2>
            - Email telah dikonfirmasi -

          </h2>
          <?php
        }
        else {
          if ($db->confirmEmail($email,$key)) {
            ?>
            <h2>
              - Email telah dikonfirmasi -

            </h2>
            <?php
          }
          else {
            ?>
            <h2>
              - Terjadi kesalahan, pastikan membuka link dari email -

            </h2>
            <?php
          }
        }

      }
      else {
        ?>
        <h2>
          - Terjadi kesalahan, pastikan membuka link dari email -

        </h2>
        <?php
      }
      ?>
      <br>
    </div>
  </center>
</body>
</html>
