<?php 

  echo "<h2>Table of Contents</h2>
        <ul>
  ";

  if ($thisPost['type'] == 'book') {
    $sql = "SELECT * FROM posts WHERE parent = $thisPost[id] AND time IS NOT NULL";
  } else {
    $sql = "SELECT * FROM posts WHERE parent = $thisPost[parent] AND time IS NOT NULL";
  }

  if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_array($result)) {
      if ($row['id'] == $thisPost['id']) {
        echo "<li><b>$row[title]</b></li>";
      } else {
        echo "<li><a href='?pid=$row[id]'>$row[title]</a></li>";
      }
    }
  }

  echo "</ul>";

?>
