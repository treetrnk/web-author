<?php 

require 'functions.php';
require 'class.php';
include 'Parsedown.php';

session_start();

getGetVars();
$GLOBALS['allposts'] = getAllPosts();
$page = new Page();

require 'templates/main.php';
