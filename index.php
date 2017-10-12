<?php 

require 'functions.php';
require 'class.php';
include 'Parsedown.php';

session_start();

/*
if (!empty($_POST['subscribe']) || !empty($_POST['unsubscribe'])) {
  include 'subscribe.php';
}
*/

if (empty($pid)) {
  $pid = 2;
}

$search = !empty($search) ? $search : false;
$page = new Page($pid, $search);

require 'templates/main.php';

//var_dump($page);
