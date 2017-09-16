<?php

	$approot = 'http://nathanhare.net/';

	include "../dbconnect.php";
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

  $active = [
    "posts" => "",
    "navigation" => "",
  ];

  if ($page == 'posts') {
    $active['posts'] = "active";
  } elseif ($page == 'navigation') {
    $active['navigation'] = "active";
  } elseif ($page == 'subscriptions') {
    $active['subscriptions'] = "active";
  }

  $postsArr = array();
  $sql = "SELECT * FROM posts";
  if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_array($result)) {
      $postsArr[$row['id']] = $row;
    }
  }

  $pgTitle = ucfirst($page);

  $locOrder = "(SUBSTRING(location, 1, LENGTH(location) - LENGTH(SUBSTRING_INDEX(REVERSE(location), '/', 2))))"; 

?>

<!DOCTYPE html>
<html>
	<head>
  <title><?=$pgTitle;?> Admin - Nathan Hare</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/images/favicon-admin.png" type="image/x-icon">
		<link href="/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<!--<link href="/admin/css/bootstrap-cyborg.min.css" rel="stylesheet" media="screen">-->
		<!---
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		--->
    <link rel="stylesheet" type="text/css" media="screen" href="bootstrap-markdown/css/bootstrap-markdown.min.css">
    <!---<script src="https://use.fontawesome.com/0dabb168cf.js"></script>--->
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
				padding-bottom: 25px;
			}
			.logout {
				line-height: 50px;
			}
			.logout a {
				margin-left: 10px;
			}
      .form-actions {
        margin: 25px 0 0 0;
      }
      textarea.md-input {
        font-size: 1.1em;
        font-family: inherit;
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

    <?php if (!empty($debug)) { ?>
		<div class="container">
			<div class="alert alert-info" role="alert">
				<i class="glyphicon glyphicon-info-sign"></i>
				<b>Debug: </b>
				<?=$debug;?>
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
          <li class="<?=$active['posts'];?>"><a href="?page=posts">Posts <span class="sr-only">(current)</span></a></li>
						<li class="<?=$active['navigation'];?>"><a href="/admin/?page=navigation">Navigation</a></li>
            <li class="<?=$active['subscriptions'];?>"><a href="/admin/?page=subscriptions">Subscriptions</a></li>
						<li><a href="/admin/logs.php" target="logs">Logs</a></li>
						<li class="disabled"><a href="#">Users</a></li>
					</ul>
					<div class="nav navbar-nav navbar-right logout">
            <form action="/admin/" method="post" class="form">
              <input type="hidden" name="action" value="logout" />
              <button type="submit" class="btn btn-default" id="logout"><i class="glyphicon glyphicon-user"></i><span clas="hidden-xs"> Logout</span></a>
            </form>
					</div>
				</div>
			</div>
		</header>

		<!-- jQuery -->
		<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<section class="container content-wrapper">
			<?php include "$page.php" ?>
		</section>

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<!--<script src="http://code.jquery.com/jquery.js"></script>
		<script src="js/bootstrap.min.js"></script>-->
    <script src="bootstrap-markdown/js/markdown.js"></script>
    <script src="bootstrap-markdown/js/to-markdown.js"></script>
    <script src="bootstrap-markdown/js/bootstrap-markdown.js"></script>
    <script src="//rawgit.com/jeresig/jquery.hotkeys/master/jquery.hotkeys.js"></script>
    <script>
      $(document).ready(function() {

        $(function () {
          $("[data-toggle='tooltip']").tooltip();
        })

        $(function () {
          $("[data-toggle2='tooltip']").tooltip();
        })

        var words = $('#md-input').val().match(/[a-zA-Z.0-9/-]+/g).length;
        $('#display_count').text("("+words+" words)");
        
        $("#md-input").on('keyup', function(e) {
          var words = this.value.match(/[a-zA-Z.0-9/-]+/g).length;
          $('#display_count').text("("+words+" words)");
        });

      });
    </script>
  </body>
</html>

