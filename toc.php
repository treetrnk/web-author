<?php 

  if (!empty($page->sidebar)) {
    $sidebar = $PD->text($page->sidebar);
  } elseif (!empty($parent) && ($page->type == 'chapter' || $page->type == 'post')) {
    $sidebar = $PD->text($parent['sidebar']);
  } else {
    $sidebar = "";
  }

  echo "$sidebar
       <!-- <ul> -->
  ";

/*
  if ($page->type == 'story' || $page->type == 'blog') {
    $sql = "SELECT * FROM posts WHERE parent = $page->id AND time IS NOT NULL";
  } else {
    $sql = "SELECT * FROM posts WHERE parent = $page->parent AND time IS NOT NULL";
  }

  if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_array($result)) {
      if ($row['id'] == $page->id) {
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

  if ($page->type == 'story' || $page->type == 'blog') {
    findChildren($page->id, $children, $page->id);
  } else {
    findChildren($page->parent, $children, $page->id);
  }
//  var_dump($children);

  //echo "</ul>";

?>
