<?php
  session_start();
  if (empty($_SESSION['username'])) {
    header("Location: http://nathanhare.net/admin/");
    exit();
  }
  include "log.html";
?>
