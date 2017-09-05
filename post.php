<div class="row">
  <div class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 content">
    
    <h1><?=$thisPost['title'];?></h1>
    <p class="pull-right text-muted" id="theme-select">
      <small>Theme:</small>
      <small>
        <a href="?theme=light" id="light-theme" title="Light Theme">
          <img src="/images/light-theme.png" />
        </a>
        <a href="?theme=dark" id="dark-theme" title="Dark Theme">
          <img src="/images/dark-theme.png" />
        </a>
      </small>
    </p><br />
    <p class="text-muted"><small><?=$date;?></small></p>
    <nav class="clearfix">
      <ul class="pager">
        <?="$prevChapLi $nextChapLi";?> 
      </ul>
    </nav>
    <div class="content">
      <?=$PD->text($thisPost['body']);?>
    </div>
  
    <br /><br />
    <div class="row">
      <div class="col-sm-6 col-xs-12">
        <p>
        <?php
          if (!empty($thisPost['tags'])) {
            echo "<i class='glyphicon glyphicon-tag'></i> ";
            $tags = explode(",", $thisPost['tags']);
            foreach($tags as $tag) {
              echo "<a href='/search/?tag=$tag' class='label label-default'>$tag</a> ";
            }
          }
        ?>
        </p>
      </div>
      <div class="col-sm-6 col-xs-12 text-right share-btns">
        <small class="text-muted">Share:</small>
        <?=$sharebtns;?>
      </div>
    </div>
    <nav aria-label="...">
      <ul class="pager">
        <?="$prevChapLi $nextChapLi";?> 
      </ul>
    </nav>
  </div>
  <aside class="col-md-3 col-md-offset-0 col-sm-10 col-sm-offset-1 col-xs-12">
    <?php include "toc.php"; ?>
  </aside>
</div>
<div class="row">
  <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
    <h2>Comments</h2>

    <div id="disqus_thread"></div>
    <script>

    /**
    *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
    *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables*/
    
    var disqus_config = function () {
      this.page.url = <?="'http://nathanhare.net$thisPost[location]'";?>;  // Replace PAGE_URL with your page's canonical URL variable
      this.page.identifier = <?="'$pid'";?>; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
    };

    (function() { // DON'T EDIT BELOW THIS LINE
    var d = document, s = d.createElement('script');
    s.src = 'https://the-writings-of-nathan-hare.disqus.com/embed.js';
    s.setAttribute('data-timestamp', +new Date());
    (d.head || d.body).appendChild(s);
    })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>

  </div>
</div>
