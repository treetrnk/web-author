<?php 

require 'functions.php';
require 'class.php';
include 'Parsedown.php';

session_start();

getGetVars();
$GLOBALS['allposts'] = getAllPosts();
$page = new Page();
subscribe();

require 'templates/main.php';
