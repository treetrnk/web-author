<?php 
  
  session_start();

  $approot = "http://nathanhare.net";

  function config($key = '') {
    $config = parse_ini_file("/srv/config.ini");
    return isset($config[$key]) ? $config[$key] : null;
  }

  include 'dbconnect.php';
  include 'Parsedown.php';

  // Create connection
  $con = new mysqli(config('server'), config('username'), config('password'), config('db'));
  global $con;

  // Check connection
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    die();
  }

  $honeypot = "iamnotahuman" . rand(0, 99999999);
  
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
    $banner = "/images/pine1.png";
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

    $banner = "/images/pine1.png";

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
        $nextChapLi = "<li class='next'><a href='$nextChapter[location]' id='nextPage' rel='next'><small>Next <span aria-hidden='true'>&rarr;</span></small></a></li>";
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
        $prevChapLi = "<li class='previous'><a href='$prevChapter[location]' id='prevPage' rel='prev'><small><span aria-hidden='true'>&larr;</span> Previous</a></small></li>";
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
    /*
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
     */
    $thisPost['readTime'] = "< 1 min.";
    if ($thisPost['words'] > 224) {
      $slowRead = round($thisPost['words']/150);      
      $fastRead = round($thisPost['words']/200);      
      $thisPost['readTime'] = "$fastRead - $slowRead mins.";
    }


  }

