<?php
session_start();
require_once('../includes/request-key.php');
require_once('../includes/db-helper.php');

if(!isset($_SESSION[RequestKey::$DATA_USER_KEY]) && !isset($_SESSION[RequestKey::$DATA_USER_LEVEL])) {
  header('Location: ../.');
}
else if ($_SESSION[RequestKey::$DATA_USER_LEVEL] != 0) {
  //ASK RELOGIN
}
else {
  // header('Content-Disposition: ../assets/user_file/user/'.$user->user_key.'/'. $file->file_key; filename="filetodownload.php"');

  $db = new DBHelper();
  $user = $db->getUserByKey($_SESSION[RequestKey::$DATA_USER_KEY]);
  $files = $db->getFilesUserAll($user->user_id);
  $totalFile = $db->countFileUser($user->user_id);

  if (isset($_GET['page']) && $_GET['page'] != "") {
    $start = $db->escapeInput($_GET['page']);
    $mulai = $start * $db->perPage - $db->perPage;
  }
  else {
    $start = 1;
    $mulai = $start * $db->perPage - $db->perPage;
  }

  $listfile = $db->getFilesUser($user->user_id, $mulai);

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
    $listfile = $db->getFilesSearch($user->user_id,$mulai,$keyword);
  }

}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Dashboard</title>

    <?php include('head.php'); ?>

    <script>
      var alert = 0;
      var alertDelete = 0;
    </script>
    <?php
    if (isset($_SESSION['file'])) {
      echo '<script> var alert = '.$_SESSION['file'].';</script>';
      unset($_SESSION['file']);
    }
    if (isset($_SESSION['delete'])) {
      echo '<script> var alertDelete = '.$_SESSION['delete'].';</script>';
      unset($_SESSION['delete']);
    }
    ?>
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
            <li><a href="upload.php"> <i class="fa fa-upload"></i>Upload </a></li>
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
                    <div class="icon bg-red"><i class="fa fa-file"></i></div>
                    <div class="text"><strong><?=$totalFile->count?></strong><br><small>File</small></div>
                  </div>
                  <div class="statistic d-flex align-items-center bg-white has-shadow">
                    <div class="icon bg-blue"><i class="fa fa-desktop"></i></div>
                    <div class="text"><strong><?=($totalFile->size/1000).' kb'?></strong><br><small>Quota</small></div>
                  </div>
                  <div class="statistic d-flex align-items-center bg-white has-shadow">
                    <div class="icon bg-green"><i class="fa fa-calendar"></i></div>
                    <div class="text"><strong><?=date("d M Y", strtotime($user->user_join_date))?></strong><br><small>Join date</small></div>
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
                              <th>Nama File</th>
                              <th>Waktu Upload</th>
                              <th>Control</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
                          if ((int)$start > -1) {
                            $i = ($start == 1 ? 1 : $start+($db->perPage-1));
                            while ($file = $listfile->fetch_object()) {
                              ?>
                              <tr>
                                <td><?=$i?></td>
                                <td><?=$file->file_name?></td>
                                <td><?=date("d M Y", strtotime($file->file_date))?></td>
                                <td>
                                  <button style="margin:1.5px 0px" type="button" data-toggle="modal" data-target="#modalRename" class="btn btn-primary middle btn-sm" data-name="<?=$file->file_name?>" data-date="<?=$file->file_date?>" data-size="<?=$file->file_size?>" data-key="<?=$file->file_key?>">Rename</button>
                                  <a style="margin:1.5px 0px" class="btn btn-secondary middle btn-sm" href="../assets/user_file/user/<?= $user->user_key ?>/<?= $file->file_key ?>" download="<?= $file->file_name ?>.pdf">Download</a>
                                  <button style="margin:1.5px 0px" type="button" data-toggle="modal" data-target="#modalDelete" class="btn btn-danger middle btn-sm" data-name="<?=$file->file_name?>" data-date="<?=$file->file_date?>" data-size="<?=$file->file_size?>" data-key="<?=$file->file_key?>">Delete</button>
                                </td>
                              </tr>
                              <?php
                              $i = $i + 1;
                            }
                          }
                          ?>
                          </tbody>
                        </table>
                        <?php
                          //PAGINATION
                          $total = $files->num_rows;
                          $pages = ceil($total/$db->perPage);
                          $threshold = 2;
                          $minNumber = $start - $threshold;
                          $maxNumber = $start + $threshold;
                          if ($listfile->num_rows != 0) {
                            if ($start > 1) {
                              $previous = $start - 1;
                              echo '<a class="btn btn-secondary btn-sm" href=?page='.$previous.(isset($_GET['search']) ? (!empty($_GET['search']) ? '' : '&search='.$_GET['search']) : '' ).'>previous</a>';
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

                          if ($listfile->num_rows < $db->perPage) {
                            $maxNumber == 1;
                          }

                          for ($i = $minNumber; $i <= $maxNumber; $i++){
                            if ($start == $i) {
                              echo ' <a class="btn btn-sm btn-primary" href=?page='.$i.(isset($_GET['search']) ? (empty($_GET['search']) ? '' : '&search='.$_GET['search']) : '' ).'><strong>'.$i.'</strong></a>';
                            }
                            else {
                              echo ' <a class="btn btn-sm btn-secondary" href=?page='.$i.(isset($_GET['search']) ? (empty($_GET['search']) ? '' : '&search='.$_GET['search']) : '' ).'>'.$i.'</a>';
                            }
                          }

                          if ($listfile->num_rows != 0) {
                            if ($start < $pages) {
                              $next = $start + 1;
                              echo ' <a class="btn btn-sm btn-secondary" href=?page='.$next.(isset($_GET['search']) ? (empty($_GET['search']) ? '' : '&search='.$_GET['search']) : '' ).'>next</a>';
                            }
                          }
                        ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <!-- Modal-->
          <div id="modalRename" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
            <div role="document" class="modal-dialog">
              <div class="modal-content">
                <form id="formrename" method="get" class="table">
                  <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Rename File</h4>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                  </div>
                  <div class="modal-body">
                    <div class="table-responsive">
                      <table class="table">
                        <tr>
                          <td>Nama file</td>
                          <td id="text-file-name"></td>
                        </tr>
                        <tr>
                          <td>Tanggal</td>
                          <td id="text-file-date"></td>
                        </tr>
                        <tr>
                          <td>Size</td>
                          <td id="text-file-size"></td>
                        </tr>
                      </table>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="form-group">
                      <label>Nama file baru</label>
                      <input type="text" placeholder="Nama file baru" name="file-name" class="form-control" id="file-name">
                    </div>
                    <input type="hidden" name="file-key" class="form-control" id="file-key">
                  </div>
                  <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                    <button class="btn btn-primary">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
            <div role="document" class="modal-dialog">
              <div class="modal-content">
                <form id="formDelete" method="get" class="table">
                  <div class="modal-header">
                    <h4 id="exampleModalLabel" class="modal-title">Delete File</h4>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                  </div>
                  <div class="modal-body">
                    <div class="table-responsive">
                      <table class="table">
                        <tr>
                          <td>Nama file</td>
                          <td id="text-file-name-delete"></td>
                        </tr>
                        <tr>
                          <td>Tanggal</td>
                          <td id="text-file-date-delete"></td>
                        </tr>
                        <tr>
                          <td>Size</td>
                          <td id="text-file-size-delete"></td>
                        </tr>
                      </table>
                    </div>
                    <input type="hidden" name="file-key" class="form-control" id="file-key-delete">
                  </div>
                  <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                    <button class="btn btn-primary">Delete</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <?php include('page-footer.php'); ?>

        </div>
      </div>
    </div>

    <?php include('foot.php'); ?>

    <script>
      $('#modalRename').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var modal = $(this)
        modal.find('#formrename').attr('action','rename.php?file-key='+button.data('key'));
        modal.find('.modal-body #text-file-name').text(button.data('name'))
        modal.find('.modal-body #text-file-date').text(button.data('date'))
        modal.find('.modal-body #text-file-size').text(button.data('size')/1000 + " kb")
        modal.find('.modal-body #file-name').val(button.data('name'))
        modal.find('.modal-body #file-key').val(button.data('key'))
      })
      $('#modalDelete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var modal = $(this)
        modal.find('#formDelete').attr('action','delete.php?file-key='+button.data('key'));
        modal.find('.modal-body #text-file-name-delete').text(button.data('name'))
        modal.find('.modal-body #text-file-date-delete').text(button.data('date'))
        modal.find('.modal-body #text-file-size-delete').text(button.data('size')/1000 + " kb")
        modal.find('.modal-body #file-name-delete').val(button.data('name'))
        modal.find('.modal-body #file-key-delete').val(button.data('key'))
      })
      $( document ).ready(function() {
        if (alert == 1) {
          swal("Success!","","success");
        }
        else if (alert == 2) {
          swal("Failed!","","error");
        }
        else if (alert == 3) {
          swal("File name exist!","","error");
        }
        else if (alert == 4) {
          swal("Forbidden access","","error");
        }

        if (alertDelete == 1) {
          swal("Success!","","success");
        }
        else if (alertDelete == 2) {
          swal("Failed!","","error");
        }
      });
    </script>

  </body>
</html>
