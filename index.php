<?php 
  
  session_start();

  $approot = "http://nathanhare.net";

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
  
  $postsArr = array();
  $sql = "SELECT * FROM posts";
  if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_array($result)) {
      $postsArr[$row['id']] = $row;
    }
  }

  $sql = "SELECT id,title,parent FROM posts WHERE time IS NOT NULL ORDER BY sort ASC,title ASC";
  $postList = mysqli_query($con, $sql);

  if (empty($pid)) {
    $pid = 2;
  }

  $PD = new Parsedown();

  if (!empty($_GET['theme'])) {
    $_SESSION['theme'] = $_GET['theme'];
  }
  $theme = [
      "bodybg" => "#fcfcfc",
      "contentbg" => "#ffffff",
      "color" => "#333333",
      "breadcrumb" => "",
      "pager" => ""
  ];
  if (!empty($_SESSION['theme']) && $_SESSION['theme'] == 'dark') {
      $theme = [
          "bodybg" => "#1c1c1c",
          "contentbg" => "#222222",
          "color" => "#aaaaaa",
          "breadcrumb" => "background-color: #333333",
          "pager" => "background-color: #2f2f2f;"
      ];
  } elseif (!empty($_SESSION['theme'])) {
      unset($_SESSION['theme']);
  }

  if (empty($search)) { $search = false; }

  if ($search) {

    $keyword = "";
    if (!empty($_GET['s'])) { $s = $_GET['s']; $keyword = $s; $urlkeyword = "?s=$s"; }
    if (!empty($_GET['tag'])) { $tag = $_GET['tag']; $keyword = $tag; $urlkeyword = "?tag=$tag"; }
    
    $thisPost['title'] = "Search";
    $thisPost['parent'] = 0;
    $thisPost['time'] = "";
    $thisPost['tags'] = "";
    $thisPost['location'] = "/search/";
    $banner = "http://i.imgur.com/wm1M89Q.jpg";
    $booktitle = "";
    $description = "Search results for $keyword.";

  } else {

    $thisPost = $postsArr[$pid];

    if (empty($thisPost['time']) || strtotime($thisPost['time']) > strtotime(date("Y-m-d H:i:s"))) {
      if (empty($_GET['preview']) || !password_verify($thisPost['title'], urldecode($_GET['preview']))) {
        $pid = 12;
        $thisPost = $postsArr[$pid];
      }
    }

    $parent = "";
    $topparent = "";
    $parentli = "";
    $topparentli = "";
    if ($thisPost['parent'] != 0) {
      $sql = "SELECT * FROM posts WHERE id = $thisPost[parent] LIMIT 1";
      $parent = mysqli_fetch_array(mysqli_query($con, $sql));
      $parentli = "<li><a href='$parent[location]'>$parent[title]</a></li>";
      if (!empty($parent['parent']) && $parent['parent'] != 0) {
        $sql2 = "SELECT * FROM posts WHERE id = $parent[parent] LIMIT 1";
        $topparent = mysqli_fetch_array(mysqli_query($con, $sql2));
        $topparentli = "<li><a href='$topparent[location]'>$topparent[title]</a></li>";
      }
    }

    $banner = "http://i.imgur.com/wm1M89Q.jpg";
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

  $description = substr(strip_tags($PD->text($thisPost['body'])), 0, 150) . "...";

    // Next Chapter
    $nextsql = "SELECT id,title,location FROM posts
      WHERE parent = $thisPost[parent]
        AND (sort > $thisPost[sort] OR time > '$thisPost[time]')
        AND id != $thisPost[id]
      ORDER BY sort ASC, time ASC
      LIMIT 1";
    $nextChapter = "";
    $nextChapLi = "<li class='next disabled'><a href='#'>Next Chapter <span aria-hidden='true'>&rarr;</span></a></li>";
    if ($nextresult = mysqli_query($con, $nextsql)) {
      $nextChapter = mysqli_fetch_array($nextresult);
      if (!empty($nextChapter)) {
        $nextChapLi = "<li class='next'><a href='$nextChapter[location]'>Next Chapter <span aria-hidden='true'>&rarr;</span></a></li>";
      }
    }
    // Previous Chapter
    $prevsql = "SELECT id,title,location FROM posts
      WHERE parent = $thisPost[parent]
        AND (sort < $thisPost[sort] OR time < '$thisPost[time]')
        AND id != $thisPost[id]
      ORDER BY sort DESC, time DESC
      LIMIT 1";
    $prevChapter = "";
    $prevChapLi = "<li class='previous disabled'><a href='#'><span aria-hidden='true'>&larr;</span> Previous Chapter</a></li>";
    if ($prevresult = mysqli_query($con, $prevsql)) {
      $prevChapter = mysqli_fetch_array($prevresult);
      if (mysqli_num_rows($prevresult)) {
        $prevChapLi = "<li class='previous'><a href='$prevChapter[location]'><span aria-hidden='true'>&larr;</span> Previous Chapter</a></li>";
      }
    }

    $sharebtns = "
      <a href='https://plus.google.com/share?url=$approot$thisPost[location]' target='_blank'><img src='http://rpg.nathanhare.net/images/googleplus-share.png' /></a> 
      <a href='https://facebook.com/sharer.php?u=$approot$thisPost[location]' target='_blank'><img src='http://rpg.nathanhare.net/images/facebook-share.png' /></a> 
      <a href='https://www.reddit.com/submit?url=$approot$thisPost[location]' target='_blank'><img src='http://rpg.nathanhare.net/images/reddit-share.png' /></a> 
      <a href='https://twitter.com/share?url=$approot$thisPost[location]' target='_blank'><img src='http://rpg.nathanhare.net/images/twitter-share.png' /></a> 
    ";

    $date = "";
    if (!empty($thisPost['time'])) {
      $date = date_format(date_create($thisPost['time']), "M. j, Y - g:i A");
    }

  }

?>
<!DOCTYPE html>
<html>
	<head>
  <title><?=$thisPost['title'];?> - Nathan Hare</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content=<?="'$description'";?> />

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content=<?="'$thisPost[title]'";?>>
    <meta itemprop="description" content=<?="'$description'"?>>
    <meta itemprop="image" content=<?="'$banner'";?>>

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@treetrnk">
    <meta name="twitter:title" content=<?="'$thisPost[title]'";?>>
    <meta name="twitter:description" content=<?="'$description'";?>>
    <meta name="twitter:creator" content="@treetrnk">
    <!-- Twitter summary card with large image must be at least 280x150px -->
    <meta name="twitter:image:src" content=<?="'$banner'";?>>

    <!-- Open Graph data -->
    <meta property="og:title" content=<?="'$thisPost[title]'";?> />
    <meta property="og:type" content="article" />
    <meta property="og:url" content=<?="'http://nathanhare.net$thisPost[location]'";?> />
    <meta property="og:image" content=<?="'$banner'";?> />
    <meta property="og:description" content=<?="'$description'";?> />
    <meta property="og:site_name" content="The Writings of Nathan Hare" />
    <meta property="article:published_time" content=<?="'$thisPost[time]'";?> />
    <meta property="article:modified_time" content=<?="'$thisPost[time]'";?> />
    <meta property="article:section" content="<?=$postArr[$thisPost['parent']]['title'];?>" />
    <meta property="article:tag" content=<?="'$thisPost[tags]'";?> />
    <meta property="fb:admins" content="Facebook numberic ID" />

    <link rel="shortcut icon" href="/images/favicon2.png" type="image/x-icon">
		<link href="/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link rel="stylesheet" href="/css/bootstrap-theme.min.css">
		<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Marcellus+SC|Montserrat:900|Open+Sans|Passion+One" rel="stylesheet">-->
    <!---<script src="https://use.fontawesome.com/0dabb168cf.js"></script>--->

    <?php include "css/css.php" ?>

	</head>
	<body>

		<header>
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-sm-10 col-xs-10 site-title">
						<a href="/">
							<span class="small">The Writings Of</span>
							<h2>Nathan Hare</h2>
						</a>
					</div>
					<nav class="col-md-8 col-sm-2 col-xs-2 navlinks small text-right">
						<i class="glyphicon glyphicon-menu-hamburger nav-menu hidden-lg hidden-md"></i>
						<ul class="navlinks hidden-sm hidden-xs">
              <li><a href="#" data-toggle="modal" data-target="#searchMod" id="searchbtn"> &nbsp;&nbsp; <i class="glyphicon glyphicon-search"></i> &nbsp;&nbsp; </a></li>
              <?php

                $sql1 = "SELECT * FROM posts WHERE parent = 0 AND time IS NOT NULL ORDER BY sort ASC, time ASC, title ASC";
                if ($result1 = mysqli_query($con, $sql1)) {
                  while ($topLink = mysqli_fetch_array($result1)){
                    echo "<li><a href='$topLink[location]'>$topLink[title]";

                    $sql2 = "SELECT * FROM posts WHERE parent = '$topLink[id]' AND time IS NOT NULL ORDER BY sort ASC, time ASC, title ASC";
                    if ($result2 = mysqli_query($con, $sql2)) {
                      if (mysqli_num_rows($result2) > 0) {
                        echo " <i class='glyphicon glyphicon-menu-down dropdown'></i></a><ul>";
                      } else {
                        echo "</a><ul>";
                      }
                      while ($sndLink = mysqli_fetch_array($result2)) {
                        echo "<li><a href='$sndLink[location]'>$sndLink[title]";

                        $sql3 = "SELECT * FROM posts WHERE parent = '$sndLink[id]' AND time IS NOT NULL ORDER BY sort ASC, time ASC, title ASC";
                        if ($result3 = mysqli_query($con, $sql3)) {
                          if (mysqli_num_rows($result3) > 0) {
                            echo " <i class='glyphicon glyphicon-menu-right dropdown'></i></a><ul>";
                          } else {
                            echo "</a><ul>";
                          }
                          while ($thrdLink = mysqli_fetch_array($result3)) {
                            echo "<li><a href='$thrdLink[location]'>$thrdLink[title]</a></li>";
                          }
                          echo "</ul>";
                        }

                        echo "</a></li>";  

                      }
                      echo "</ul>";

                    }

                    echo "</a></li>";  
                  }
                }
              ?>

            </ul>
					</nav>
				</div>
			</div>
		</header>

    <nav class="toggle-nav" id="toggle-nav">
      <form action="/search/" method="get"> 
        <div class="row">
          <div class="col-xs-12">
            <div class="input-group" id="searchtoggle">
              <input type="text" name="s" class="form-control" placeholder="Search" />
              <span class="input-group-btn">
                <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i>
              </span>
            </div>
          </div>
        </div>
      </form>
			<ul>
        <?php
          $sql1 = "SELECT * FROM posts WHERE parent = 0 AND time IS NOT NULL ORDER BY sort ASC, time ASC, title ASC";
          if ($result1 = mysqli_query($con, $sql1)) {
            while ($topLink = mysqli_fetch_array($result1)){
              echo "<li><a href='$topLink[location]'>" . strtoupper($topLink['title']);

              $sql2 = "SELECT * FROM posts WHERE parent = '$topLink[id]' AND time IS NOT NULL ORDER BY sort ASC, time ASC, title ASC";
              if ($result2 = mysqli_query($con, $sql2)) {
                if (mysqli_num_rows($result2) > 0) {echo " <i class='glyphicon glyphicon-menu-down dropdown'></i>";}
                echo "</a><ul>";
                while ($sndLink = mysqli_fetch_array($result2)) {
                  echo "<li><a href='$sndLink[location]'>&nbsp;&nbsp;&nbsp;&nbsp; $sndLink[title]";

                  $sql3 = "SELECT * FROM posts WHERE parent = '$sndLink[id]' AND time IS NOT NULL ORDER BY sort ASC, time ASC, title ASC";
                  if ($result3 = mysqli_query($con, $sql3)) {
                    if (mysqli_num_rows($result3) > 0) {echo " <i class='glyphicon glyphicon-menu-down dropdown'></i>";}
                    echo "</a><ul>";
                    while ($thrdLink = mysqli_fetch_array($result3)) {
                      echo "<li><a href='$thrdLink[location]'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $thrdLink[title]</a></li>";

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

			</ul>
		</nav>

		<div class="jumbotron banner">
			<div class="container">
				<br /><br /><br /><br />
        <?=$booktitle;?>
        <!--
        -->
			</div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="searchMod" tabindex="-1" role="dialog" aria-labelledby="searchModLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<form action="/search/" method="get">
							<div class="input-group">
								<input type="text" name="s" placeholder="Search" class="form-control" id="searchInput" />
								<span class="input-group-btn">
									<button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i></button>
								</span>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

    <button type="button" class="btn btn-primary" id="sub-btn"><i class="glyphicon glyphicon-envelope"></i> Subscribe</button>

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

      <?php 
        if ($search) {
          include "search.php";
        } else {
          include "$thisPost[type].php"; 
        }
      ?>

    </section>

		<footer class="text-muted small">
			<p class="text-center">&#169; Copyright 2017, Nathan Hare</a>
      <p class="text-center">
        "<a href="http://www.newgrounds.com/art/view/llamareaper/fantasy-landscape-2" target="_blank">Fantasy Landscape</a>" 
        and "<a href="http://www.newgrounds.com/art/view/llamareaper/pumpkinbutts" target="_blank">Pumpkinbutts.</a>"
        by <a href="http://atthespeedof.newgrounds.com/" target="_blank">Jason Coates</a> are licensed under 
        <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank">CC BY-NC-SA 3.0</a>
        and <a href="https://creativecommons.org/licenses/by-nc/3.0/" target="_blank">CC BY-NC 3.0</a>, respectively
      </p>
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

        $("#searchMod").on("shown.bs.modal", function() {
          $("#searchInput").focus(); 
        });

			});
		</script>
	</body>
</html>
