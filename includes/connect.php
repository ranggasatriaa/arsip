<?php

require_once('db-config.php');

class Connect extends DbConfig{
  public $link;

  function __construct() {
    $this->connect();
  }

  function connect() {
    $this->link = mysqli_connect(DbConfig::$dbHost,DbConfig::$dbUser,DbConfig::$dbPass,DbConfig::$dbName);
    if (!$this->link) {
      die("can't connect to server or database");
    }
  }

  function close() {
    mysqli_close($this->link);
  }

}

?>
