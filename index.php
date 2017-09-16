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
  
  if (!empty($_POST['subscribe']) || !empty($_POST['unsubscribe'])) {
    include 'subscribe.php';
  }

  // Create Array of posts
  $postsArr = array();
  $sql = "SELECT * FROM posts ORDER BY id";
  if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_array($result)) {
      $postsArr[$row['id']] = $row;
    }
  }

  // Create Array of posts
  $children = [];
  $sql = "SELECT * FROM posts WHERE time < CURRENT_TIMESTAMP ORDER BY sort, time, title";
  if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_array($result)) {
      if (!isset($children["$row[parent]"])) {
        $children["$row[parent]"] = [];
      }
      $children["$row[parent]"][] = $row;
    }
  }
  global $children;

  //Get children of provided parent id
  function getChildren($parentid, $con) {
    $sql = "SELECT * FROM posts 
      WHERE parent = $parentid 
        AND time < CURRENT_TIMESTAMP 
      ORDER BY sort ASC, 
        time ASC, 
        title ASC";
    if ($result = mysqli_query($con, $sql)) {
      if (mysqli_num_rows($result) > 0) {
        return $result;
      }
    }
    return "";
  }

  //Create array of navigation links
  $navArr = [];
  $topLinks = getChildren(0, $con);
  if (!empty($topLinks)) {
    while ($topLink = mysqli_fetch_array($topLinks)) {

      $navArr["$topLink[id]"] = $topLink;
      $midLinks = getChildren($topLink['id'], $con);
      if (!empty($midLinks)) {
        while ($midLink = mysqli_fetch_array($midLinks)) {

          $navArr["$topLink[id]"]["children"]["$midLink[id]"] = $midLink;
          $btmLinks = getChildren($midLink['id'], $con);
          if (!empty($btmLinks)) {
            while ($btmLink = mysqli_fetch_array($btmLinks)) {

              $navArr["$topLink[id]"]["children"]["$midLink[id]"]["children"]["$btmLink[id]"] = $btmLink;

            }
          }

        }
      }

    }
  }

  $alltags = [];
  $tagsql = "SELECT tags FROM posts WHERE time < CURRENT_TIMESTAMP";
  if ($tagresult = mysqli_query($con, $tagsql)) {
    while ($row = mysqli_fetch_array($tagresult)) {
      if (!empty($row['tags'])) {
        $taggrp = explode(",", $row['tags']);
        foreach ($taggrp as $atag) {
          if (!in_array($atag, $alltags)){
            $alltags[] = $atag;
          }
        }
      } 
    }
  }

  sort($alltags, SORT_STRING);


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
      "pager" => "",
      "modal" => ""
  ];
  if (!empty($_SESSION['theme']) && $_SESSION['theme'] == 'dark') {
      $theme = [
          "bodybg" => "#1c1c1c",
          "contentbg" => "#222222",
          "color" => "#aaaaaa",
          "breadcrumb" => "background-color: #333333",
          "pager" => "background-color: #2f2f2f;",
          "modal" => ".modal-content{background-color:#222222;color:#aaaaaa;}"
      ];
  } elseif (!empty($_SESSION['theme'])) {
      unset($_SESSION['theme']);
  }

  if (empty($search)) { $search = false; }

  if ($search) {

    $keyword = "";
    $urlkeyword = "?a=a";
    $s = "";
    $tag = "";
    if (!empty($_GET['s'])) { $s = $_GET['s']; $keyword = $s; $urlkeyword = "?s=$s"; }
    if (!empty($_GET['tag'])) { $tag = $_GET['tag']; $keyword = $tag; $urlkeyword = "?tag=$tag"; }
    
    $thisPost['title'] = "Search";
    $thisPost['parent'] = 0;
    $thisPost['time'] = "";
    $thisPost['tags'] = "";
    $thisPost['location'] = "/search/";
    $banner = "http://i.imgur.com/wm1M89Q.jpg";
    $titlePrefix = "";
    $booktitle = "";
    $description = "Search results for $keyword.";
    $section = "";

  } else {

    $thisPost = $postsArr[$pid];

    if (empty($thisPost['time']) || strtotime($thisPost['time']) > strtotime(date("Y-m-d H:i:s"))) {
      if (empty($_GET['preview']) || !password_verify($thisPost['title'], urldecode($_GET['preview']))) {
        $pid = 12;
        $thisPost = $postsArr[$pid];
      }
    }

    $bcrumbs = ["<li class='active'>$thisPost[title]</li>"];
    $parent = "";
    $parents = [];

    if ($thisPost['parent'] != 0) {
      $parPost = $postsArr["$thisPost[parent]"];
      $parent = $postsArr["$thisPost[parent]"];
    }

    while (!empty($parPost)) {
      $parents[] = $parPost;
      array_unshift($bcrumbs, "<li><a href='$parPost[location]'>$parPost[title]</a></li>");

      if ($parPost['parent'] != 0) {
        $parPost = $postsArr["$parPost[parent]"];
      } else {
        $parPost = NULL;
      }
    }

    /*
    $parent = "";
    $topparent = "";
    $parentli = "";
    $topparentli = "";
    if ($thisPost['parent'] != 0) {
      $parent = $postsArr[$thisPost['parent']];
      $parentli = "<li><a href='$parent[location]'>$parent[title]</a></li>";
      if (!empty($parent['parent']) && $parent['parent'] != 0) {
        $topparent = $postsArr[$parent['parent']];
        $topparentli = "<li><a href='$topparent[location]'>$topparent[title]</a></li>";
      }
    }
    */

    $banner = "/images/writing-banner.jpg";

    if (!empty($thisPost['banner'])) {
      $banner = $thisPost['banner'];
    } else {
      foreach ($parents as $p) {
        if (!empty($p["banner"])) {
          $banner = $p["banner"];
          break;
        }
      }
    }


    /*
    if (!empty($thisPost['banner'])) {
      $banner = $thisPost['banner'];
    } elseif (!empty($parent['banner'])) {
      $banner = $parent['banner'];
    } elseif (!empty($topparent['banner'])) {
      $banner = $topparent['banner'];
    }
    */

    $booktitle = "";
    $titlePrefix = "";
    if (($thisPost['type'] == 'chapter' || $thisPost['type'] == 'post') && !empty($parent)) {
      $booktitle = "<h1>$parent[title]</h1>";
      $titlePrefix = "$parent[title]: ";
    } elseif ($thisPost['type'] == 'story') {
      $booktitle = "<h1>$thisPost[title]</h1>";
    }

  $description = substr(strip_tags($PD->text($thisPost['body'])), 0, 150) . "...";

    // Next Chapter
    $nextsql = "SELECT id,title,location FROM posts
      WHERE parent = $thisPost[parent]
        AND time < CURRENT_TIMESTAMP
        AND (sort > $thisPost[sort] OR time > '$thisPost[time]')
        AND id != $thisPost[id]
      ORDER BY sort ASC, time ASC
      LIMIT 1";
    $nextChapter = "";
    $nextChapLi = "<li class='next disabled'><a href='#'><small>Next <span aria-hidden='true'>&rarr;</span></small></a></li>";
    if ($nextresult = mysqli_query($con, $nextsql)) {
      $nextChapter = mysqli_fetch_array($nextresult);
      if (!empty($nextChapter)) {
        $nextChapLi = "<li class='next'><a href='$nextChapter[location]' id='nextPage'><small>Next <span aria-hidden='true'>&rarr;</span></small></a></li>";
      }
    }
    // Previous Chapter
    $prevsql = "SELECT id,title,location FROM posts
      WHERE parent = $thisPost[parent]
        AND time < CURRENT_TIMESTAMP
        AND (sort < $thisPost[sort] OR time < '$thisPost[time]')
        AND id != $thisPost[id]
      ORDER BY sort DESC, time DESC
      LIMIT 1";
    $prevChapter = "";
    $prevChapLi = "<li class='previous disabled'><a href='#'><small><span aria-hidden='true'>&larr;</span> Previous</small></a></li>";
    if ($prevresult = mysqli_query($con, $prevsql)) {
      $prevChapter = mysqli_fetch_array($prevresult);
      if (mysqli_num_rows($prevresult)) {
        $prevChapLi = "<li class='previous'><a href='$prevChapter[location]' id='prevPage'><small><span aria-hidden='true'>&larr;</span> Previous</a></small></li>";
      }
    }

    $sharebtns = "
      <a href='https://plus.google.com/share?url=$approot$thisPost[location]' target='_blank'><img src='/images/googleplus-share.png' /></a> 
      <a href='https://facebook.com/sharer.php?u=$approot$thisPost[location]' target='_blank'><img src='/images/facebook-share.png' /></a> 
      <a href='https://www.reddit.com/submit?url=$approot$thisPost[location]' target='_blank'><img src='/images/reddit-share.png' /></a> 
      <a href='https://twitter.com/share?url=$approot$thisPost[location]' target='_blank'><img src='/images/twitter-share.png' /></a> 
    ";

    $date = "";
    if (!empty($thisPost['time'])) {
      $date = date_format(date_create($thisPost['time']), "M. j, Y - g:i A");
    }

    $section = "";
    if (!empty($parents) ) {
      $section = $parents[0]["title"];
    }

    $thisPost['words'] = str_word_count(strip_tags($PD->text($thisPost['body'])));
    $readTimeHrs = intval($thisPost['words']/200/60);
    $readTimeMins = intval($thisPost['words']/200) - $readTimeHrs * 60;
    $readTimeSecs = intval($thisPost['words']/200*60) - intval($thisPost['words']/200)*60;
    $thisPost['readTime'] = "";
    if ($readTimeHrs > 0) {
      $thisPost['readTime'] .= "$readTimeHrs hrs. ";
    }
    if ($readTimeMins > 0) {
      $thisPost['readTime'] .= "$readTimeMins mins. ";
    }
    if ($readTimeSecs > 0) {
      $thisPost['readTime'] .= "$readTimeSecs secs.";
    }

    $thisPost['readTime'] = trim($thisPost['readTime']);

  }

?>
<!DOCTYPE html>
<html>
	<head>
  <title><?="$titlePrefix$thisPost[title]";?> - Nathan Hare</title>
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
    <meta property="article:section" content="<?=$section?>" />
    <meta property="article:tag" content=<?="'$thisPost[tags]'";?> />
    <meta property="fb:admins" content="Facebook numberic ID" />

    <link rel="shortcut icon" href="/images/favicon2.png" type="image/x-icon">
		<link href="/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <!---<script src="https://use.fontawesome.com/0dabb168cf.js"></script>--->

    <script src='https://www.google.com/recaptcha/api.js'></script>
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

                foreach ($navArr as $topLink){
                  echo "<li><a href='$topLink[location]'>$topLink[title]";

                  if (array_key_exists("children", $topLink) && !empty($topLink["children"])) {
                    echo " <i class='glyphicon glyphicon-menu-down'></i></a><div class='navdrop'><ul>";

                    foreach ($topLink["children"] as $midLink) {
                      echo "<li><a href='$midLink[location]'>$midLink[title]";

                      if (array_key_exists("children", $midLink) && !empty($midLink["children"])) {
                        echo " <i class='glyphicon glyphicon-menu-down dropdown'></i></a><ul>";

                        foreach ($midLink["children"] as $btmLink) {
                          echo "<li><a href='$btmLink[location]'>$btmLink[title]</a></li>";
                        }

                      } else {
                        echo "</a><ul>";
                      }
                      echo "</ul></li>";  
                    }

                  } else {
                    echo "</a><div class='hidden'><ul>";
                  }
                  echo "</ul><div></li>";  
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
            foreach ($navArr as $topLink){
              echo "<li><a href='$topLink[location]'>" . strtoupper($topLink['title']);

              if (array_key_exists("children", $topLink) && !empty($topLink["children"])) {
                echo " <i class='glyphicon glyphicon-menu-down dropdown'></i></a><ul>";
                foreach ($topLink["children"] as $midLink) {
                  echo "<li><a href='$midLink[location]'>&nbsp;&nbsp;&nbsp;&nbsp; $midLink[title]";

                  if (array_key_exists("children", $midLink) && !empty($midLink["children"])) {
                    echo " <i class='glyphicon glyphicon-menu-down dropdown'></i></a><ul>";
                    foreach ($midLink["children"] as $btmLink) {
                      echo "<li><a href='$btmLink[location]'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $btmLink[title]</a></li>";

                    }
                    echo "</ul></li>";
                  } else { echo "</a></li>"; }

                }
                echo "</ul></li>";
              } else { echo "</a></li>"; }

            }
          echo "</a></li>";
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
              <?php
                if (!empty($alltags)) {
                  echo "<br /><p><i class='glyphicon glyphicon-tags'></i> ";
                  foreach ($alltags as $alltag) {
                    $tagclass = "label-default";
                    echo "<a href='/search/?tag=$alltag' class='label $tagclass'>$alltag</a> ";
                  }
                  echo "</p>";
                }
              ?>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="subscribeMod" tabindex="-1" role="dialog" aria-labelledby="subscribeModLabel">
			<form class="form" action="" method="post">
        <input type="hidden" name="subscribe" value="yes" />
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h3 class="modal-title" id="subscribeModLabel"><i class="glyphicon glyphicon-envelope"></i> Subscribe!</h3>
						</div>
						<div class="modal-body">
							<small><p>Subscribe now to receive updates every time a new chapter is posted.</p></small>
							<div class="row">
								<div class="col-xs-12">
									<small><label class="control-label">Email <span class="text-danger"><b>*</b></span></label></small>
									<input type="email" placeholder="example@email.com" name="email" class="form-control" maxlength="200" required />
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<small><label class="control-label">Name</label> (Optional)</small>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6">
									<input type="text" name="fname" placeholder="First" class="form-control" maxlength="50" />
								</div>
								<div class="col-xs-6">
									<input type="text" name="lname" placeholder="Last" class="form-control" maxlength="50" />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-xs-12 text-center">
                  <div class="g-recaptcha" data-sitekey="6LcjGTAUAAAAAJx8FmzDgGMzuiRDElIgTRbkdqxG"></div>
								</div>
							</div>
            </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-primary">Subscribe</button>
						</div>
					</div>
				</div>
			</form>
		</div>

    <button type="button" class="close" id="sub-btn-close"><small><i class="glyphicon glyphicon-remove"></i></small></button>

    <button type="button" class="btn btn-primary" id="sub-btn" data-toggle="modal" data-target="#subscribeMod" title="Subscribe"><i class="glyphicon glyphicon-envelope"></i><span class="hidden-xs"> Subscribe</span></button>

		<section class="container content-wrapper">

      <?php 
        if ($thisPost['parent'] != 0) {
          echo "
            <div class='row'>
              <div class='col-xs-12'>
                <ol class='breadcrumb'>
          ";

          foreach($bcrumbs as $bc) {
            echo $bc;
          }

          /*
                  $topparentli
                  $parentli
                  <li class='active'>$thisPost[title]</li>
          */
          echo "
                </ol>
              </div>
            </div>
          ";
        }
      ?>

      <?php 
        if (!empty($error)) {
          echo "<div class='alert alert-danger' role='alert'><b>Error!</b> $error</div>";
        } elseif (!empty($success)) {
          echo "<div class='alert alert-success' role='alert'><b>Success! </b>$success</div>";
        }

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

				$('i.dropdown').click(function(e) {
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

        $(document).on('keydown', function(e) {
          //console.log(e.type);
          if (e.which == 37) {
            if ($("#prevPage").length != 0) {
              //console.log('prev');
              //$('#prevPage').trigger('click');
              window.location = $('#prevPage').attr('href');
            }
          } else if (e.which == 39) {
            if ($("#nextPage").length != 0) {
              //console.log('next');
              //$('#nextPage').trigger('click');
              window.location = $('#nextPage').attr('href');
            }
          }
        });

        $("#sub-btn-close").click(function() {
          $("#sub-btn-close").hide();
          $("#sub-btn").hide();
        });

        $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })

			});
		</script>
	</body>
</html>
