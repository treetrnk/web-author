<?php 

  if (!empty($thisPost['sidebar'])) {
    $sidebar = $PD->text($thisPost['sidebar']);
  } elseif (!empty($parent) && ($thisPost['type'] == 'chapter' || $thisPost['type'] == 'post')) {
    $sidebar = $PD->text($parent['sidebar']);
  } else {
    $sidebar = "";
  }

  echo "$sidebar
       <!-- <ul> -->
  ";

/*
  if ($thisPost['type'] == 'story' || $thisPost['type'] == 'blog') {
    $sql = "SELECT * FROM posts WHERE parent = $thisPost[id] AND time IS NOT NULL";
  } else {
    $sql = "SELECT * FROM posts WHERE parent = $thisPost[parent] AND time IS NOT NULL";
  }

  if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_array($result)) {
      if ($row['id'] == $thisPost['id']) {
        echo "<li><b>$row[title]</b></li>";
      } else {
        echo "<li><a href='$row[location]'>$row[title]</a></li>";
      }
    }
  }
 */

  function findChildren($id, $children, $thisPostID) {
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

  if ($thisPost['type'] == 'story' || $thisPost['type'] == 'blog') {
    findChildren($thisPost['id'], $children, $thisPost['id']);
  } else {
    findChildren($thisPost['parent'], $children, $thisPost['id']);
  }
//  var_dump($children);

  //echo "</ul>";

?>
