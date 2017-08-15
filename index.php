<?php 

  include 'dbconnect.php';
  include 'Parsedown.php';

  // Create connection
  $con = new mysqli($servername, $username, $password, $db);
  global $con;

  // Check connection
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    die();
  }

  $pid = 2;
  if (!empty($_GET['pid'])) {
    $pid = $_GET['pid'];
  }

  $sql = "SELECT id,title,parent FROM posts WHERE time IS NOT NULL ORDER BY sort ASC,title ASC";
  $postList = mysqli_query($con, $sql);

  $sql = "SELECT * FROM posts WHERE id = $pid LIMIT 1";
  if ($result = mysqli_query($con, $sql)) {
    $thisPost = mysqli_fetch_assoc($result);
  }

  $PD = new Parsedown();

  $parent = "";
  $topparent = "";
  $parentli = "";
  $topparentli = "";
  if ($thisPost['parent'] != 0) {
    $sql = "SELECT * FROM posts WHERE id = $thisPost[parent] LIMIT 1";
    $parent = mysqli_fetch_array(mysqli_query($con, $sql));
    $parentli = "<li><a href='?pid=$thisPost[parent]'>$parent[title]</a></li>";
    if (!empty($parent['parent']) && $parent['parent'] != 0) {
      $sql2 = "SELECT * FROM posts WHERE id = $parent[parent] LIMIT 1";
      $topparent = mysqli_fetch_array(mysqli_query($con, $sql2));
      $topparentli = "<li><a href='?pid=$parent[parent]'>$topparent[title]</a></li>";
    }
  }

  $banner = "https://ckmartinauthor.files.wordpress.com/2016/09/cropped-2016-banner.jpg";
  if (!empty($thisPost['banner'])) {
    $banner = $thisPost['banner'];
  } elseif (!empty($parent['banner'])) {
    $banner = $parent['banner'];
  } elseif (!empty($topparent['banner'])) {
    $banner = $topparent['banner'];
  }

  $booktitle = "";
  if ($thisPost['type'] == 'chapter' && !empty($parent)) {
    $booktitle = "<h1>$parent[title]</h1>";
  } elseif ($thisPost['type'] == 'book') {
    $booktitle = "<h1>$thisPost[title]</h1>";
  }

?>
<!DOCTYPE html>
<html>
	<head>
		<title>The Writings of Nathan Hare</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Marcellus|Marcellus+SC|Open+Sans|Passion+One" rel="stylesheet">
		<style>
			header {
				background-color: #0b5394; /*let user set*/
				position: fixed;
        top: 0;
				width: 100%;
				-moz-box-shadow:    3px 3px 25px 1px #222;
				-webkit-box-shadow: 3px 3px 25px 1px #222;
				box-shadow:         3px 3px 25px 1px #222;
				z-index: 4;
				height: 68px;
				line-height: 68px;
			}
			header a:link, header a:visited, header a:active {
				color: #ffffff;
				text-decoration: none;
				text-transform: uppercase;
			}
			div.site-title {
				padding-top: 5px;
				padding-bottom: 5px;
				line-height: initial;
			}
			div.site-title a h2 {
				margin: 0;
        font-family: 'Passion One', cursive;
				text-transform: none;
			}
			.small {
				font-size: 14px;
				text-transform: none;
			}
			nav.navlinks i.nav-menu {
				font-size: 35px;
				line-height: 68px;
				color: #ffffff;
				cursor: pointer;
			}
			ul.navlinks {
				list-style: none;
				position: relative;
				float: right;
			}
			ul.navlinks a {
				display: block;
			}
			ul.navlinks li {
				margin-left: 10px;
				position: relative;
				float: right;
				padding: 0 5px;
			}
			ul.navlinks li:hover {
				background-color: rgba(256,256,256, .06);
			}
			ul.navlinks li ul {
				display: none;
				line-height: initial;
				position: absolute;
				left: 0;
				background-color: #222;
				-moz-box-shadow:    3px 3px 25px 1px #222;
				-webkit-box-shadow: 3px 3px 25px 1px #222;
				box-shadow:         3px 3px 25px 1px #222;
				padding: 0;
				min-width: 175px;
			}
			ul.navlinks li:hover ul {
				display: block;
			}
			ul.navlinks ul li {
				display: block;
				width: 100%;
				text-align: left;
				border-top: 1px solid #444;
				border-left: 1px solid #444;
				border-right: 1px solid #000;
				border-bottom: 1px solid #000;
				padding: 7px;
			}
      ul.navlinks ul a {
				text-transform: none;
      }
			ul.navlinks ul ul {
				top: 0px;
				left: 100%;
			}
			ul.navlinks ul ul li {
				z-index: 5;
			}
			header nav.navlinks ul.navlinks li ul li ul {
				display: none;
			}
			header nav.navlinks ul.navlinks li ul li:hover > ul {
				display:block;
			}
      #toggle-nav {
				background-color: #222222;
				min-width: 250px;
				position: fixed;
				top: 0;
				right: 0;
				height: 100vh;
				padding-top: 68px;
				z-index: 3;
				overflow-y: auto;
				overflow-x: hidden;
				white-space: nowrap;
				border-left: 1px solid #444;
				border-right: 1px solid #000;
				display: none;
			}
			#toggle-nav ul {
				list-style: none;
				padding: 0;
			}
			#toggle-nav li {
				color: #ffffff;
				line-height: 50px;
				margin: 0;
				border-top: 1px solid #444;
				border-bottom: 1px solid #000;
			}
			#toggle-nav a {
				display: block;
				padding: 0 10px;
				color: #dddddd;
				font-size: 14px;
			}
			#toggle-nav a:hover {
				text-decoration: none;
				background-color: rgba(256,256,256, .06);
			}
			#toggle-nav a:active #toggle-nav a:visited {
				text-decoration: none;
			}
			#toggle-nav ul ul, #toggle-nav ul ul ul {
				display: none;
			}
			div.jumbotron {
    <?="background-image: url($banner);";?>
				background-color: #ffffff;
				background-repeat: no-repeat;
				background-position: center center;
				background-size: cover;
				padding-top: 89px;
				height: 450px;
				z-index: 2;
			}
			body {
				background-color: #fcfcfc;
				font-size: 1.7em;
        font-family: 'Open Sans', sans-serif;
        padding-top: 50px;
			}
      h1, h2, h3, h4 {
        font-family: 'Passion One', cursive;
      }
			div.jumbotron {
				margin-bottom: 0;
			}
			div.jumbotron h1 {
				display: inline;
				color: #ffffff;
				background: rgba(0, 0, 0, .4);
				padding: 10px;
        font-family: 'Marcellus SC', serif;
				border-radius: 5px;
			}
			section.content-wrapper {
				background-color: #ffffff;
				padding: 25px;
				margin-top: -50px;
				margin-bottom: 50px;
				-moz-box-shadow:    0 30px 50px 0 rgba(1,1,1,.15);
				-webkit-box-shadow: 0 30px 50px 0 rgba(1,1,1,.15);
				box-shadow: 0 30px 50px 0 rgba(1,1,1,.15);
			}
			div.share-btns img {
				width: 24px;
				border: 0;
			}
			div.share-btns a {
				text-decoration: none;
			}
			footer {
				margin-bottom: 50px;
			}
		</style>
	</head>
	<body>

		<header>
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-sm-10 col-xs-10 site-title">
						<a href="./">
							<span class="small">The Writings Of</span>
							<h2>Nathan Hare</h2>
						</a>
					</div>
					<nav class="col-md-8 col-sm-2 col-xs-2 navlinks small text-right">
						<i class="glyphicon glyphicon-menu-hamburger nav-menu hidden-lg hidden-md"></i>
						<ul class="navlinks hidden-sm hidden-xs">
              <?php

                $sql1 = "SELECT * FROM posts WHERE parent = 0 AND time IS NOT NULL ORDER BY sort DESC, title ASC";
                if ($result1 = mysqli_query($con, $sql1)) {
                  while ($topLink = mysqli_fetch_array($result1)){
                    echo "<li><a href='?pid=$topLink[id]'>$topLink[title]</a>";

                    $sql2 = "SELECT * FROM posts WHERE parent = '$topLink[id]' AND time IS NOT NULL ORDER BY sort ASC, title ASC";
                    if ($result2 = mysqli_query($con, $sql2)) {
                      echo "<ul>";
                      while ($sndLink = mysqli_fetch_array($result2)) {
                        echo "<li><a href='?pid=$sndLink[id]'>$sndLink[title]</a>";

                        $sql3 = "SELECT * FROM posts WHERE parent = '$sndLink[id]' AND time IS NOT NULL ORDER BY sort ASC, title ASC";
                        if ($result3 = mysqli_query($con, $sql3)) {
                          echo "<ul>";
                          while ($thrdLink = mysqli_fetch_array($result3)) {
                            echo "<li><a href='?pid=$thrdLink[id]'>$thrdLink[title]</a></li>";
                          }
                          echo "</ul>";
                        }

                        echo "</li>";  

                      }
                      echo "</ul>";

                    }

                    echo "</li>";  
                  }
                }
              ?>
<!--
							<li><a href="#">Stories <i class="glyphicon glyphicon-menu-down"></i></a>
								<ul>
									<li><a href="#">Marv's Tale<i class="glyphicon glyphicon-menu-right"></i></a>
										<ul>
											<li><a href="#">1. The Endless War</a></li>
											<li><a href="#">Part 2</a></li>
										</ul>
									</li>
									<li><a href="#">The Daegal Legends</a></li>
                  <li><a href="#">Short Stories</a></li>
								</ul>
							</li>
							<li><a href="#">Home</a></li>
-->
            </ul>
					</nav>
				</div>
			</div>
		</header>

    <nav class="toggle-nav" id="toggle-nav">
			<ul>
        <?php
          $sql1 = "SELECT * FROM posts WHERE parent = 0 AND time IS NOT NULL ORDER BY sort ASC, title ASC";
          if ($result1 = mysqli_query($con, $sql1)) {
            while ($topLink = mysqli_fetch_array($result1)){
              echo "<li><a href='?pid=$topLink[id]'>" . strtoupper($topLink['title']);

              $sql2 = "SELECT * FROM posts WHERE parent = '$topLink[id]' AND time IS NOT NULL ORDER BY sort ASC, title ASC";
              if ($result2 = mysqli_query($con, $sql2)) {
                if (mysqli_num_rows($result2) > 0) {echo " <i class='glyphicon glyphicon-menu-down dropdown'></i>";}
                echo "</a><ul>";
                while ($sndLink = mysqli_fetch_array($result2)) {
                  echo "<li><a href='?pid=$sndLink[id]'>&nbsp;&nbsp;&nbsp;&nbsp; $sndLink[title]";

                  $sql3 = "SELECT * FROM posts WHERE parent = '$sndLink[id]' AND time IS NOT NULL ORDER BY sort ASC, title ASC";
                  if ($result3 = mysqli_query($con, $sql3)) {
                    if (mysqli_num_rows($result3) > 0) {echo " <i class='glyphicon glyphicon-menu-down dropdown'></i>";}
                    echo "</a><ul>";
                    while ($thrdLink = mysqli_fetch_array($result3)) {
                      echo "<li><a href='?pid=$thrdLink[id]'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $thrdLink[title]</a></li>";

                    }
                    echo "</ul></li>";
                  }

                }
                echo "</ul></li>";
              } else { echo "</a>"; }

            }
          } else { echo "</a></li>"; }
                
          echo "</li>";
        ?>
<!--
				<li><a href="#">STORIES <i class="glyphicon glyphicon-menu-down dropdown"></i></a>
					<ul>
						<li><a href="#">&nbsp;&nbsp;&nbsp;&nbsp; Marv's Tale <i class="glyphicon glyphicon-menu-down dropdown"></i></a>
							<ul>
								<li><a href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1. The Endless War</a></li>
								<li><a href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2. Chapter 2</a></li>
							</ul>
						</li>
						<li><a href="#">&nbsp;&nbsp;&nbsp;&nbsp; The Daegal Legends</a></li>
						<li><a href="#">&nbsp;&nbsp;&nbsp;&nbsp; Short Stories</a></li>
					</ul>
				</li>
				<li><a href="#">SUPPORT THE AUTHOR</a></li>
-->
			</ul>
		</nav>

		<div class="jumbotron banner">
			<div class="container">
				<br /><br /><br /><br />
        <?=$booktitle;?>
			</div>
		</div>

		<section class="container content-wrapper">

      <?php 
        if ($thisPost['parent'] != 0) {
          echo "
            <div class='row'>
              <div class='col-xs-12'>
                <ol class='breadcrumb'>
                  $topparentli
                  $parentli
                  <li class='active'>$thisPost[title]</li>
                </ol>
              </div>
            </div>
          ";
        }
      ?>

      <?php include "$thisPost[type].php"; ?>

    </section>

		<footer class="text-muted small">
			<p class="text-center"></p>
			<p class="text-center">&#169; Copyright 2017, Nathan Hare</a>
		</footer>

		<!-- jQuery -->
		<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<!--<script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>-->
    <script type="text/javascript">
			$(document).ready(function() {

				$('.nav-menu').click(function(e) {
					var $this = $(this);
					if ($this.hasClass('glyphicon-menu-hamburger')) {
						$(this).removeClass('glyphicon-menu-hamburger');
						$(this).addClass('glyphicon-remove');
						$('#toggle-nav').slideDown('fast');
					} else {
						$(this).addClass('glyphicon-menu-hamburger');
						$(this).removeClass('glyphicon-remove');
						$('#toggle-nav').slideUp('fast');
					}
				});

				$('#toggle-nav i.dropdown').click(function(e) {
					e.preventDefault();
					var $this = $(this);
					if ($this.hasClass('glyphicon-menu-down')) {
						$this.parent('a').siblings('ul').slideDown();
						$this.removeClass('glyphicon-menu-down');
						$this.addClass('glyphicon-menu-up');
					} else {
						$this.parent('a').siblings('ul').slideUp();
						$this.removeClass('glyphicon-menu-up');
						$this.addClass('glyphicon-menu-down');
					}
				});

				$('section, .jumbotron, footer').click(function() {
					$('.nav-menu').addClass('glyphicon-menu-hamburger');
					$('.nav-menu').removeClass('glyphicon-remove');
					$('#toggle-nav').slideUp();
				});

			});
		</script>
	</body>
</html>
