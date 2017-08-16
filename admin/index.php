<?php

	$approot = 'http://nathanhare.net/';

	include "dbconnect.php";
  // Create connection
  $con = new mysqli($servername, $username, $password, $db);
  global $con;

  // Check connection
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    die();
  }

	include "engine.php";

	$page = "posts";
	if (!empty($_GET['page'])) {
		$page = $_GET['page'];
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Admin - The Writings of Nathan Hare</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
		<!---
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		--->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" media="screen" href="bootstrap-markdown/css/bootstrap-markdown.min.css">
		<style>
			/*
			header {
				position: fixed;
				top: 0;
				width: 100%;
				margin: 0;
				background-color: #222222;
				color: #ffffff;
				height: 63px;
				overflow-y: hidden;
				white-space: nowrap;
			}
			header ul.nav-pills li {
				padding-top: 10px;
			}
			*/
			.navbar-static-top {
				position: fixed;
				width: 100%;
				top: 0;
				-moz-box-shadow:    3px 3px 25px 1px #222;
				-webkit-box-shadow: 3px 3px 25px 1px #222;
				box-shadow:         3px 3px 25px 1px #222;
			}
			a.navbar-brand:link, a.navbar-brand:active, a.navbar-brand:visited, a.navbar-brand:hover {
				color: #ffffff;
			}
			body {
				padding-top: 75px;
			}
			.logout {
				line-height: 50px;
			}
			.logout a {
				margin-left: 10px;
			}
      .form-actions {
        margin: 25px 0;
      }
		</style>
	</head>
	<body>

    <?php if (!empty($success)) { ?>
		<div class="container">
			<div class="alert alert-success" role="alert">
				<i class="glyphicon glyphicon-ok-sign"></i>
				<b>Success!</b>
				<?=$success;?>
			</div>
		</div>
		<?php } ?>

		<?php if (!empty($error)) { ?>
		<div class="container">
			<div class="alert alert-danger" role="alert">
				<i class="glyphicon glyphicon-exclamation-sign"></i>
				<b>Error!</b>
				<?=$error;?>
			</div>
		</div>
		<?php } ?>

		<?php
			if (empty($_SESSION['username'])) {
				include "login.php";
				die();
			}
		?>

		<header class="navbar navbar-inverse navbar-static-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/admin/">Admin Panel</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="active"><a href="#">Posts <span class="sr-only">(current)</span></a></li>
						<li><a href="#">Navigation</a></li>
						<li class="disabled"><a href="#">Users</a></li>
					</ul>
					<div class="nav navbar-nav navbar-right logout">
						<a href="/admin/?action=logout" class="btn btn-default" id="logout"><i class="glyphicon glyphicon-user"></i><span clas="hidden-xs"> Logout</span></a>
					</div>
				</div>
			</div>
		</header>

		<section class="container content-wrapper">
			<?php include "$page.php" ?>
		</section>

		<!-- jQuery -->
		<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<!--<script src="http://code.jquery.com/jquery.js"></script>
		<script src="js/bootstrap.min.js"></script>-->
    <script src="bootstrap-markdown/js/markdown.js"></script>
    <script src="bootstrap-markdown/js/to-markdown.js"></script>
    <script src="bootstrap-markdown/js/bootstrap-markdown.js"></script>
    <script src="//rawgit.com/jeresig/jquery.hotkeys/master/jquery.hotkeys.js"></script>
  </body>
</html>

