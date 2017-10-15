<div class="row">
  <div class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 content">
    
    <p class="pull-right text-muted" style="margin-top: 28px;" id="theme-select">
      <small class="hidden-xs">Theme:</small>
      <small>
        <a href="?theme=light" id="light-theme" title="Light Theme">
          <img src="/resources/images/light-theme.png" />
        </a>
        <a href="?theme=dark" id="dark-theme" title="Dark Theme">
          <img src="/resources/images/dark-theme.png" />
        </a>
      </small>
    </p>
    <h1><?=$page->title;?></h1>
    <!--<p class="text-muted"><small><?=$page->date;?></small></p>-->
    <div class="content">
      <?=$page->formattedBody;?>

      <br />
      <br />
      <?php
        $postsql = "SELECT * FROM posts WHERE parent = $page->id AND time < CURRENT_TIMESTAMP ORDER BY sort DESC, time DESC";
        if ($postresult = mysqli_query(dbConnect(), $postsql)) {
          while ($row = mysqli_fetch_array($postresult)) {
            $thisrow = new Page($row['id']);
            $rowdate = date_format(date_create($row['time']), "M. j, Y - g:i A");
            echo "
              <div class='panel panel-default'>
                <div class='panel-body'>
                  <p class='text-muted pull-right'><small>$rowdate</small></p>
                  <h3><a href='$row[location]'>$row[title]</a></h3>
                  <p>" . substr(strip_tags($thisrow->formattedBody), 0, 200) . " . . .</p>
            ";
            $thisrow->tags();
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
    <?php $page->tableOfContents(); ?>
  </aside>
</div>
