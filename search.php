<div class="row">
  <div class="col-md-10 col-md-offset-1 col-sm-12">
    <p class="pull-right text-muted" id="theme-select">
      <small class="hidden-xs">Theme:</small>
      <small>
        <a href="<?=$urlkeyword;?>&theme=light" id="light-theme" title="Light Theme">
          <img src="/images/light-theme.png" />
        </a>
        <a href="<?=$urlkeyword;?>&theme=dark" id="dark-theme" title="Dark Theme">
          <img src="/images/dark-theme.png" />
        </a>
      </small>
    </p>
    <h1>Search</h1>
    <br />
    <form class="form" action="/search/" method="get">
      <div class="input-group">
        <input type="text" name="s" value="<?=$keyword;?>" placeholder="Search" class="form-control" />
        <div class="input-group-btn">
          <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i></button>
        </div>
      </div>
    </form>
    <br />
    <?php
      if (!empty($tag)) {
        echo "<h2>Results for &quot;$tag&quot; tag</h2>";
        $sql = "SELECT * FROM posts WHERE time IS NOT NULL AND time < CURRENT_TIMESTAMP AND tags LIKE '%$tag%' AND (type = 'chapter' OR type = 'post') ORDER BY time DESC";
      } else {
        echo "<h2>Results for &quot;$s&quot;</h2>";
        $sql = "SELECT * FROM posts WHERE time IS NOT NULL AND time < CURRENT_TIMESTAMP AND (body LIKE '%$s%' OR title LIKE '%$s%') ORDER BY time DESC";
      }

      if ($result = mysqli_query($con, $sql)) {
        while ($row = mysqli_fetch_array($result)) {
          $rowdate = "";
          if ($row['type'] == "chapter" || $row['type'] == "post") {
            $rowdate = date_format(date_create($row['time']), "M. j, Y - g:i A");
          }
          echo "
            <div class='panel panel-default'>
              <div class='panel-body'>
                <p class='text-muted pull-right'><small>$rowdate</small></p>
                <h3>" . ucfirst($row['type']) . ": <a href='$row[location]'>$row[title]</a></h3>
                <p>" . substr(strip_tags($PD->text($row['body'])), 0, 200) . " . . .</p>
          ";
          if (!empty($row['tags']) && ($row['type'] == 'chapter' || $row['type'] == 'post')) { 
            echo "<p><i class='glyphicon glyphicon-tag'></i> ";
            $tags = explode(",", $row['tags']);
            foreach($tags as $tag) {
              echo "<a href='/search/?tag=$tag' class='label label-default'>$tag</a> ";
            }
            echo "</p>"; 
          }
          echo "
              </div>
            </div>
          ";
        }
      }
    ?>

  </div>
</div>
