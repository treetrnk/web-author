<div class="row">
  <div class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 content">
    
    <p class="pull-right text-muted" style="margin-top: 28px;" id="theme-select">
      <small class="hidden-xs">Theme:</small>
      <small>
        <a href="?theme=light" id="light-theme" title="Light Theme">
          <img src="/images/light-theme.png" />
        </a>
        <a href="?theme=dark" id="dark-theme" title="Dark Theme">
          <img src="/images/dark-theme.png" />
        </a>
      </small>
    </p>
    <h1><?=$thisPost['title'];?></h1>
    <!--<p class="text-muted"><small><?=$date;?></small></p>-->
    <div class="content">
      <?=$PD->text($thisPost['body']);?>

      <br />
      <br />
      <?php
        $postsql = "SELECT * FROM posts WHERE parent = $thisPost[id] AND time < CURRENT_TIMESTAMP ORDER BY sort DESC, time DESC";
        if ($postresult = mysqli_query($con, $postsql)) {
          while ($row = mysqli_fetch_array($postresult)) {
            $rowdate = date_format(date_create($row['time']), "M. j, Y - g:i A");
            echo "
              <div class='panel panel-default'>
                <div class='panel-body'>
                  <p class='text-muted pull-right'><small>$rowdate</small></p>
                  <h3><a href='$row[location]'>$row[title]</a></h3>
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
  
    <br />
  </div>
  <aside class="col-md-3 col-md-offset-0 col-sm-10 col-sm-offset-1 col-xs-12">
    <?php include "toc.php"; ?>
  </aside>
</div>
