<?php
session_start();
require_once('../includes/request-key.php');
require_once('../includes/db-helper.php');

if(!isset($_SESSION[RequestKey::$DATA_USER_KEY]) && !isset($_SESSION[RequestKey::$DATA_USER_LEVEL])) {
  header('Location: ../.');
}
else if ($_SESSION[RequestKey::$DATA_USER_LEVEL] != 1) {
  //ASK RELOGIN
}
else {
  $db = new DBHelper();
  $user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY]);
  $users = $db->getAllUserByLevel($user->user_level);
  $totalFile = $db->countFile();

  if (isset($_GET['page']) && $_GET['page'] != "") {
    $start = $db->escapeInput($_GET['page']);
    $mulai = $start * $db->perPage - $db->perPage;
  }
  else {
    $start = 1;
    $mulai = $start * $db->perPage - $db->perPage;
  }

  $listuser = $db->getAllUsers($mulai,$user->user_level);

  if (isset($_GET['search'])) {
    if (isset($_GET['page']) && $_GET['page'] != "") {
      $start = $db->escapeInput($_GET['page']);
      $mulai = $start * $db->perPage - $db->perPage;
    }
    else {
      $start = 1;
      $mulai = $start * $db->perPage - $db->perPage;
    }
    $keyword  = $db->escapeInput($_GET['search']);
    $listuser = $db->searchUsers($keyword,$mulai,$user->user_level);
  }

  //data chart
  $row = array();
  $city_name = array();
  for ($i=1; $i <=76 ; $i++) {
    $row[$i] = 0;
  }
  for ($i=1; $i <=76 ; $i++) {
    $city_name[$i] = "";
  }
  $city_name[1] = "Kabupaten Cilacap";
  $city_name[2] = "Kabupaten Banyumas";
  $city_name[3] = "Kabupaten Purbalingga";
  $city_name[4] = "Kabupaten Banjarnegara";
  $city_name[5] = "Kabupaten Kebumen";
  $city_name[6] = "Kabupaten Purworejo";
  $city_name[7] = "Kabupaten Wonosobo";
  $city_name[8] = "Kabupaten Magelang";
  $city_name[9] = "Kabupaten Boyolali";
  $city_name[10] = "Kabupaten Klaten";
  $city_name[11] = "Kabupaten Sukoharjo";
  $city_name[12] = "Kabupaten Wonogiri";
  $city_name[13] = "Kabupaten Karanganyar";
  $city_name[14] = "Kabupaten Sragen";
  $city_name[15] = "Kabupaten Grobogan";
  $city_name[16] = "Kabupaten Blora";
  $city_name[17] = "Kabupaten Rembang";
  $city_name[18] = "Kabupaten Pati";
  $city_name[19] = "Kabupaten Kudus";
  $city_name[20] = "Kabupaten Jepara";
  $city_name[21] = "Kabupaten Demak";
  $city_name[22] = "Kabupaten Semarang";
  $city_name[23] = "Kabupaten Tegung";
  $city_name[24] = "Kabupaten Kedal";
  $city_name[25] = "Kabupaten Batang";
  $city_name[26] = "Kabupaten Pekalongan";
  $city_name[27] = "Kabupaten Pemalang";
  $city_name[28] = "Kabupaten Tegal";
  $city_name[29] = "Kabupaten Brebes";
  $city_name[30] = "Kota Magelang";
  $city_name[31] = "Kota Surakata";
  $city_name[32] = "Kota Salatiga";
  $city_name[33] = "Kota Semarang";
  $city_name[34] = "Kota Pekalongan";
  $city_name[35] = "Kota Tegal";


  while ($dataUser = $users->fetch_object()) {
    // echo $user->user_nik;
    // echo " ++>> ";
    $city_code = substr($dataUser->user_nik,2,2);
    // echo $city_code;
    switch ($city_code) {
      case 1:
      $row[1]=$row[1]+1;
      break;
      case 2:
      $row[2]=$row[2]+1;
      break;
      case 3:
      $row[3]=$row[3]+1;
      break;
      case 4:
      $row[4]=$row[4]+1;
      break;
      case 5:
      $row[5]=$row[5]+1;
      break;
      case 6:
      $row[6]=$row[6]+1;
      break;
      case 7:
      $row[7]=$row[7]+1;
      break;
      case 8:
      $row[8]=$row[8]+1;
      break;
      case 9:
      $row[9]=$row[9]+1;
      break;
      case 10:
      $row[10]=$row[10]+1;
      break;
      case 11:
      $row[11]=$row[11]+1;
      break;
      case 12:
      $row[12]=$row[12]+1;
      break;
      case 13:
      $row[13]=$row[13]+1;
      break;
      case 14:
      $row[14]=$row[14]+1;
      break;
      case 15:
      $row[15]=$row[15]+1;
      break;
      case 16:
      $row[16]=$row[16]+1;
      break;
      case 17:
      $row[17]=$row[17]+1;
      break;
      case 18:
      $row[18]=$row[18]+1;
      break;
      case 19:
      $row[19]=$row[19]+1;
      break;
      case 20:
      $row[20]=$row[20]+1;
      break;
      case 21:
      $row[21]=$row[21]+1;
      break;
      case 22:
      $row[22]=$row[22]+1;
      break;
      case 23:
      $row[23]=$row[23]+1;
      break;
      case 24:
      $row[24]=$row[24]+1;
      break;
      case 25:
      $row[25]=$row[25]+1;
      break;
      case 26:
      $row[26]=$row[26]+1;
      break;
      case 27:
      $row[27]=$row[27]+1;
      break;
      case 28:
      $row[28]=$row[28]+1;
      break;
      case 29:
      $row[29]=$row[29]+1;
      break;
      case 71:
      $row[30]=$row[30]+1;
      break;
      case 72:
      $row[31]=$row[31]+1;
      break;
      case 73:
      $row[32]=$row[32]+1;
      break;
      case 74:
      $row[33]=$row[33]+1;
      break;
      case 75:
      $row[34]=$row[34]+1;
      break;
      case 76:
      $row[35]=$row[35]+1;
      break;
      // default:
      // echo "Wilayah tidak ditemukan";
    }

    // echo " ++>> ";
    // echo "<br/>";
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Dashboard</title>

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
            <li class="active"><a href="."> <i class="icon-home"></i>Dashboard </a></li>
            <li><a href="profil.php"> <i class="icon-user"></i>Profil </a></li>
          </ul>
        </nav>
        <div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Dashboard</h2>
            </div>
          </header>
          <!-- Dashboard Header Section    -->
          <section class="dashboard-header">
            <div class="container-fluid">
              <div class="row">
                <!-- Statistics -->
                <div class="statistics col-lg-3 col-12">
                  <div class="statistic d-flex align-items-center bg-white has-shadow">
                    <div class="icon bg-red"><i class="fa fa-user"></i></div>
                    <div class="text"><strong><?=$users->num_rows?></strong><br><small>User</small></div>
                  </div>
                  <div class="statistic d-flex align-items-center bg-white has-shadow">
                    <div class="icon bg-blue"><i class="fa fa-file"></i></div>
                    <div class="text"><strong><?=$totalFile->count?></strong><br><small>File</small></div>
                  </div>
                  <div class="statistic d-flex align-items-center bg-white has-shadow">
                    <div class="icon bg-green"><i class="fa fa-desktop"></i></div>
                    <div class="text"><strong><?=($totalFile->size/1000).' kb';?></strong><br><small>Memory</small></div>
                  </div>
                </div>
                <div class="col-lg-9">
                  <div class="card">
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Data user</h3>
                    </div>
                    <div class="card-body">
                      <form method="get">
                        <div class="form-group">
                          <div class="input-group">
                            <input type="text" class="form-control" name="search" value="<?=(isset($_GET['search']) ? (empty($_GET['search']) ? '' : $_GET['search']) : '' )?>">
                            <div class="input-group-append">
                              <button class="btn btn-primary">Search</button>
                            </div>
                          </div>
                        </div>
                      </form>
                      <div class="table-responsive">
                        <table class="table table-striped table-hover">
                          <thead>
                            <tr>
                              <th>No</th>
                              <th>Nama User</th>
                              <th>Email</th>
                              <th>Status</th>
                              <th>Control</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
                          if ($listuser->num_rows != 0) {
                            if ((int)$mulai > -1) {
                              $i = $mulai + 1;
                              while ($datauser = $listuser->fetch_object()) {
                                ?>
                                <tr>
                                  <td><?php echo $i ?></td>
                                  <td><?php echo ucwords($datauser->user_name) ?></td>
                                  <td><?php echo $datauser->user_email ?></td>
                                  <td><?php echo ((int)$datauser->user_status == 1 ? '<p class="text-success">Verified</p>' : '<p class="text-danger">Not verified</p>' ) ?></td>
                                  <td>
                                    <a class="btn btn-primary middle btn-sm" href="edit_email.php?user-key=<?= $datauser->user_key ?>">Ubah email</a>
                                  </td>
                                </tr>
                                <?php
                                $i = $i + 1;
                              }
                            }
                          }
                          else {
                            ?>
                            <tr>
                              <td colspan="5">No data</td>
                            </tr>
                            <?php
                          }
                          ?>
                          </tbody>
                        </table>
                        <?php
                          //PAGINATION
                          $total = $users->num_rows;
                          $pages = ceil($total/$db->perPage);
                          $threshold = 2;
                          $minNumber = $start - $threshold;
                          $maxNumber = $start + $threshold;
                          if ($listuser->num_rows != 0) {
                            if ($start > 1) {
                              $previous = $start - 1;
                              echo '<a class="btn btn-secondary" href=?page='.$previous.(isset($_GET['search']) ? (!empty($_GET['search']) ? '' : '&search='.$_GET['search']) : '' ).'>previous</a>';
                            }
                            else {
                              $minNumber = 1;
                            }
                          }

                          if ($maxNumber > $pages) {
                            $maxNumber-= ($maxNumber-$pages);
                          }

                          if ($start <= $threshold) {
                            $minNumber = 1;
                          }

                          if ($listuser->num_rows < $db->perPage) {
                            $maxNumber == 1;
                          }

                          for ($i = $minNumber; $i <= $maxNumber; $i++){
                            if ($start == $i) {
                              echo ' <a class="btn btn-primary" href=?page='.$i.(isset($_GET['search']) ? (empty($_GET['search']) ? '' : '&search='.$_GET['search']) : '' ).'><strong>'.$i.'</strong></a>';
                            }
                            else {
                              echo ' <a class="btn btn-secondary" href=?page='.$i.(isset($_GET['search']) ? (empty($_GET['search']) ? '' : '&search='.$_GET['search']) : '' ).'>'.$i.'</a>';
                            }
                          }

                          if ($listuser->num_rows != 0) {
                            if ($start < $pages) {
                              $next = $start + 1;
                              echo ' <a class="btn btn-secondary" href=?page='.$next.(isset($_GET['search']) ? (empty($_GET['search']) ? '' : '&search='.$_GET['search']) : '' ).'>next</a>';
                            }
                          }
                        ?>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12">
                  <div class="bar-chart-example card">
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Grafik User</h3>
                    </div>
                    <div class="card-body">
                      <canvas id="barChartExample"></canvas>
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
      $(document).ready(function () {

        'use strict';

        // ------------------------------------------------------- //
        // Charts Gradients
        // ------------------------------------------------------ //
        var ctx1 = $("canvas").get(0).getContext("2d");
        var gradient1 = ctx1.createLinearGradient(150, 0, 150, 300);
        gradient1.addColorStop(0, 'rgba(133, 180, 242, 0.91)');
        gradient1.addColorStop(1, 'rgba(255, 119, 119, 0.94)');

        var gradient2 = ctx1.createLinearGradient(146.000, 0.000, 154.000, 300.000);
        gradient2.addColorStop(0, 'rgba(104, 179, 112, 0.85)');
        gradient2.addColorStop(1, 'rgba(76, 162, 205, 0.85)');

          // ------------------------------------------------------- //
          // Bar Chart
          // ------------------------------------------------------ //
          var BARCHARTEXMPLE    = $('#barChartExample');
          var barChartExample = new Chart(BARCHARTEXMPLE, {
              type: 'bar',
              options: {
                  scales: {
                      xAxes: [{
                          display: false,
                          gridLines: {
                              color: '#eee'
                          }
                      }],
                      yAxes: [{
                          display: true,
                          gridLines: {
                              color: '#eee'
                          }
                      }]
                  },
              },
              data: {
                  labels: [
                    <?php
                    for ($i=1; $i <=35 ; $i++) {
                      echo "'".$city_name[$i]."'";
                      echo ",";
                    }
                    ?>
                  ],
                  datasets: [
                      {
                          label: "Data user",
                          backgroundColor: [
                            <?php
                            for ($i=1; $i <=35 ; $i++) {
                              echo 'gradient1,';
                            }
                            ?>
                          ],
                          hoverBackgroundColor: [
                            <?php
                            for ($i=1; $i <=35 ; $i++) {
                              echo 'gradient1,';
                            }
                            ?>
                          ],
                          borderColor: [
                            <?php
                            for ($i=1; $i <=35 ; $i++) {
                              echo 'gradient1,';
                            }
                            ?>
                          ],
                          borderWidth: 1,
                          data: [
                            <?php
                            for ($i=1; $i <=35 ; $i++) {
                              echo $row[$i];
                              echo ",";
                            }
                            ?>
                          ],
                      }
                  ]
              }
          });

        });

    </script>

  </body>
</html>
