<?php

  session_start();
  date_default_timezone_set('America/New_York');

  include 'dbconnect.php';

  ini_set('display_errors', 'On'); 
  error_reporting(E_ALL | E_STRICT);

  $locprefix = "/srv/http";

  function escape_str($str) {
    
    $str = str_replace("'", "&#39;", $str);
    //$str = str_replace('"', '&quot;', $str);

    return $str;
  }

  function strRemove($list, $str) {
    foreach ($list as $char) {
      $str = str_replace($char, "", $str); 
    }
    return $str;
  }

  function rmdirRec($directory) {
    if (file_exists($directory)) {
      foreach(glob("{$directory}/*") as $file) {
          if(is_dir($file)) {
              rmdirRec($file);
          } else {
              unlink($file);
          }
      }
      rmdir($directory);
      return true;
    }
    return false;
  }

  if (!empty($_POST['action'])) {
    $action = $_POST['action'];
  } elseif (!empty($_GET['action'])) {
    $action = $_GET['action'];
  }

  $data = $_POST;

  if (!empty($action)){

    switch ($action) {
      case 'login': ///////////////////////////////////////////////////////////
        if (empty($data['username']) || empty($data['password'])) {
          $error = "Please provide both a username and password.";
        }

        $result = mysqli_query($con,"SELECT * FROM users WHERE username='$data[username]' LIMIT 1");
        $row = mysqli_fetch_assoc($result);

        /*
        var_dump($data);
        echo "<br />";
        var_dump($result);
        echo "<br />";
         */

        if (!$row) {
          //echo 'not result <br />';
          $error = "The username could not be found.";
        } elseif (!password_verify("$_POST[password]", $row['password'])) {
          $error = "Incorrect password.";
        } else {

          $_SESSION['userid'] = $row['id'];
          $_SESSION['username'] = $row['username'];
          $_SESSION['displayName'] = $row['displayName'];
          //var_dump($_SESSION);
          $success = "Logged in successfully.";

        }

        break;

      case 'logout': //////////////////////////////////////////////////////////
        $_SESSION = array();
        session_destroy();
        $success = "Logged out successfully.";
        break;

      case 'addPost': /////////////////////////////////////////////////////////
      case 'editPost': ////////////////////////////////////////////////////////

        //var_dump($_POST);
        //die();

        if (!empty($_POST['title']) 
          && !empty($_POST['body'])
        ) {
  
          $title = "";
          $body = "";
          $parent = "";
          $banner = "";
          $tags = "";
          $type = "";
          
          if (!empty($_POST['title'])) { $title = mysqli_real_escape_string($con, stripslashes(escape_str($_POST['title']))); }
          if (!empty($_POST['body'])) { $body = mysqli_real_escape_string($con, stripslashes(escape_str($_POST['body']))); }
          if (!empty($_POST['parent'])) { $parent = stripslashes($_POST['parent']); }
          if (!empty($_POST['banner'])) { $banner = stripslashes(escape_str($_POST['banner'])); }
          if (!empty($_POST['tags'])) { $tags = stripslashes(escape_str($_POST['tags'])); }
          if (!empty($_POST['type'])) { $type = stripslashes($_POST['type']); }

          $publish1 = "";
          $publish2 = "";

          $parentloc = "/";
          if ($parent != 0) {
            $parentsql = "SELECT location FROM posts WHERE id = $parent LIMIT 1";
            $parentdata = mysqli_fetch_array(mysqli_query($con, $parentsql));
            $parentloc = $parentdata['location'];
          }

          $toRemove = array("!", "&#39;", "@", "#", "$", "%", "^", "&", "*", "(", ")", "-", "_", "=", "+", "[", "{", "]", "}", ";", ":", "'", '"', ",", "<", ".", ">", "/", "?", "\\", "|", "`", "~");

          $thisFolder = strRemove($toRemove, $title);
          $thisFolder = strtolower(str_replace(" ", "-", $thisFolder));
          $location = "$parentloc$thisFolder/";

          $extrasql = "";
          if ($action == 'editPost') { $extrasql = " AND id != $_POST[id]"; }
          $uniqueLocSql = "SELECT * FROM posts WHERE location = '$location'$extrasql";
          
          if (mysqli_num_rows($uniqueResult = mysqli_query($con, $uniqueLocSql)) == 0) {

            if ($action == 'addPost') {
              if (!empty($_POST['publish'])) {
                $publish1 = ", time";
                $publish2 = ", CURRENT_TIMESTAMP";
              }

              //Insert into posts() VALUES();
              $sql = "
                INSERT INTO posts(
                  title,
                  body,
                  author,
                  parent,
                  banner,
                  tags,
                  type,
                  location$publish1
                ) VALUES (
                  '$title',
                  '$body',
                  '$_SESSION[userid]',
                  '$parent',
                  '$banner',
                  '$tags',
                  '$type',
                  '$location'$publish2
                )";

              $pid = mysqli_insert_id($con);

            } else {
              if ($_POST['publish'] == 'y') {
                $publish1 = ", time = CURRENT_TIMESTAMP";
              } elseif ($_POST['publish'] == 'unpublish') {
                $publish1 = ", time = NULL";
              }

              $oldLocSql = "SELECT location FROM posts WHERE id = $_POST[id] LIMIT 1";
              $oldLoc = mysqli_fetch_array(mysqli_query($con, $oldLocSql));

              if ($location != $oldLoc['location']) {
                if (file_exists($locprefix.$oldLoc['location'])) {
                  //rmdirRec($locprefix.$oldLoc['location']);
                }
              }

              $sql = "
                UPDATE posts SET
                  title='$title',
                  body='$body',
                  author='$_SESSION[userid]',
                  parent='$parent',
                  banner='$banner',
                  tags='$tags',
                  type='$type',
                  location='$location'$publish1
                WHERE id = $_POST[id]
              ";

              $pid = $_POST['id'];
            }

            $error = $location;
            mkdir($locprefix.$location, 0775, true) or die("Unable to create directory");             
            $myfile = fopen($locprefix.$location."index.php", "w") or die("Unable to write file");
            $txt = '<?php $pid=' . $pid . '; include "/index.php"; ?>';
            fwrite($myfile, $txt);
            fclose($myfile);

            if ($result = mysqli_query($con, $sql)) {
              $success = "Successfully saved post.";
              $page = "posts";
            } else { 
              $error = "The post could not be saved.<br />Error: <code>" . mysqli_error($con) . "</code><br />SQL: <code>$sql</code>";
            }

          } else {
            $notunique = mysqli_fetch_array($uniqueResult);
            $error = "The title is already in use. Please pick a differnt one.<br/>Match: <code>" . var_dump($notunique) . "</code>";
          }

        } else {
          $error = "One or more of the required fields are blank.";
        }  

        break;

      case 'deletePost': //////////////////////////////////////////////////////
        
        $sql = "DELETE FROM posts WHERE id = $_POST[id]";
        if ($result = mysqli_query($con, $sql)) {
          $success = "Post successfully deleted.";
        } else {
          $error = "Failed to delete post.<br />Error: <code>" . mysqli_error($con) . "</code><br />SQL: <code>$sql</code>";
        }

        break; 

    }

  }

?>
