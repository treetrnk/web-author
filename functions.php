<?php 
  
  // Get configuration variables ///////////////////////////////////////////////
  function config($key = '') {
    $config = parse_ini_file("/srv/config.ini");
    return isset($config[$key]) ? $config[$key] : null;
  }

  // Create DB connection //////////////////////////////////////////////////////
  function dbConnect() {
    $con = new mysqli(config('server'), config('username'), config('password'), config('db'));
    // Check connection
    if (mysqli_connect_errno()) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
      die();
    }
    return $con;
  }

  // Set GET variables that Apache hides ///////////////////////////////////////
  function getGetVars() {
    $uri = explode('?', $_SERVER['REQUEST_URI']);
    if (!empty($uri[1])) {
      $getAssignments = explode('&', $uri[1]);
      foreach ($getAssignments as $assignment) {
        $var = explode('=', $assignment);
        $_GET["$var[0]"] = urldecode($var[1]);
      }
    }
  }

  // Create Array of all posts /////////////////////////////////////////////////
  function getAllPosts($id="") {
    $postsArr = [];
    $sql = "SELECT * FROM posts ORDER BY id";
    if ($result = mysqli_query(dbConnect(), $sql)) {
      while ($row = mysqli_fetch_array($result)) {
        $postsArr[$row['id']] = $row;
      }
    }
    if (!empty($id)) {
      return in_array("$id", $postsArr) ? $postsArr["$id"] : '';
    }
    return $postsArr;
  }

  // Get details of a post /////////////////////////////////////////////////////
  function getPostArray($id) {
    //var_dump($GLOBALS['allposts']);
    return in_array("$id", $GLOBALS['allposts']) ? $GLOBALS['allposts']["$id"] : '';
  }

  // Create Array of posts arranged by parent it ///////////////////////////////
  function getChildrenArray() {
    $children = [];
    $sql = "SELECT * FROM posts WHERE time < CURRENT_TIMESTAMP ORDER BY sort, time, title";
    if ($result = mysqli_query(dbConnect(), $sql)) {
      while ($row = mysqli_fetch_array($result)) {
        if (!isset($children["$row[parent]"])) {
          $children["$row[parent]"] = [];
        }
        $children["$row[parent]"][] = $row;
      }
    }
    return $children;
  }

  //Get children of provided parent id /////////////////////////////////////////
  function getChildren($parentid) {
    $sql = "SELECT * FROM posts 
      WHERE parent = $parentid 
        AND time < CURRENT_TIMESTAMP 
      ORDER BY sort ASC, 
        time ASC, 
        title ASC";
    if ($result = mysqli_query(dbConnect(), $sql)) {
      if (mysqli_num_rows($result) > 0) {
        return $result;
      }
    }
    return "";
  }

  // Get posts by location /////////////////////////////////////////////////////
  function getPostByLocation($location) {
    $allposts = getAllPosts();
    $location = strtolower(trim($location));
    $location = explode('/', $location);
    $temploc = implode('/', $location);
    $location = $temploc;

    foreach ($allposts as $post) {
      if (in_array($location, $post)) {
        $postDetails = $post;
        break;
      }
    }

    return !empty($postDetails) ? $postDetails : '';
  }

  //Create array of navigation links ///////////////////////////////////////////
  function createNavLinks() {
    $navArr = [];
    $topLinks = getChildren(0);
    if (!empty($topLinks)) {
      while ($topLink = mysqli_fetch_array($topLinks)) {

        $navArr["$topLink[id]"] = $topLink;
        $midLinks = getChildren($topLink['id']);
        if (!empty($midLinks)) {
          while ($midLink = mysqli_fetch_array($midLinks)) {

            $navArr["$topLink[id]"]["children"]["$midLink[id]"] = $midLink;
            $btmLinks = getChildren($midLink['id']);
            if (!empty($btmLinks)) {
              while ($btmLink = mysqli_fetch_array($btmLinks)) {

                $navArr["$topLink[id]"]["children"]["$midLink[id]"]["children"]["$btmLink[id]"] = $btmLink;

              }
            }

          }
        }

      }
    }
    return $navArr;
  }

  // Get an array of all tags //////////////////////////////////////////////////
  function allTags() {
    $alltags = [];
    $tagsql = "SELECT tags FROM posts WHERE time < CURRENT_TIMESTAMP";
    if ($tagresult = mysqli_query(dbConnect(), $tagsql)) {
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
    return $alltags;
  }

  // Get an array of all tags //////////////////////////////////////////////////
  function getAllTags ($selected="") {
    $alltags = allTags();

    if (!empty($alltags)) {
      echo "<br /><p><i class='glyphicon glyphicon-tags'></i> ";
      foreach ($alltags as $tag) {
        $tagclass = $tag == $selected ? "label-primary" : "label-default";
        echo "<a href='/search/?tag=$tag' class='label $tagclass'>$tag</a> ";
      }
      echo "</p>";
    }
  }

  // Get the theme array ///////////////////////////////////////////////////////
  function theme($key="") {
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
    return $theme["$key"];
  }

  // Get a list of all of share buttons ////////////////////////////////////////
  function getShareButtons($url) {
    $sharebtns = "
      <a href='https://plus.google.com/share?url=$url' target='_blank'><img src='/resources/images/googleplus-share.png' /></a> 
      <a href='https://facebook.com/sharer.php?u=$url' target='_blank'><img src='/resources/images/facebook-share.png' /></a> 
      <a href='https://www.reddit.com/submit?url=$url' target='_blank'><img src='/resources/images/reddit-share.png' /></a> 
      <a href='https://twitter.com/share?url=$url' target='_blank'><img src='/resources/images/twitter-share.png' /></a> 
    ";
    return $sharebtns;
  }

  // Get top nav ///////////////////////////////////////////////////////////////
  function getTopNav() {
    $navArr = createNavLinks();
    
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
  }

  // Get side nav //////////////////////////////////////////////////////////////
  function getSideNav() {
    $navArr = createNavLinks();
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
  }

  // Get Children of a post ////////////////////////////////////////////////////
  function findChildren($id, $thisPostID) {
    $children = getChildrenArray();
    if (!empty($children["$id"])) {
      echo "<ul>";
      foreach ($children["$id"] as $child) {
        if ($child["id"] == $thisPostID) {
          echo "<li><b>$child[title]</b>";
        } else {
          echo "<li><a href='$child[location]'>$child[title]</a>";
        }
        if (!empty($children["$child[id]"])) {
          findChildren($child["id"], $children, $thisPostID);
        }
        echo "</li>";
      }
      echo "</ul>"; 
    }
  }

