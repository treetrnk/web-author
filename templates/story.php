<div class="row">
  <div class="col-md-7 col-md-offset-1 col-sm-12 col-xs-12 content">
    
    <h1><?=$page->title;?></h1>
    <p class="pull-right text-muted" id="theme-select">
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
    </p>
    <p>
      <small>
        <span class="text-muted">
          <?=$page->date;?><br />
          <span title="Word Count" data-toggle="tooltip" class="badge badge-default"># <?=$page->total_words?></span>
          <span title="Reading Time (200-150 wpm)" data-toggle="tooltip" class="badge badge-default">&#8987; <?=$page->total_read_time();?></span>
        </span>
        
      </small>
    <p class="text-muted"><small><?=$page->date;?></small></p>
    <div class="content">
      <?=$page->formattedBody;?>
    </div>
  
    <br /><br />
    <div class="row">
      <div class="col-sm-6 col-xs-12">
        <?=$page->tags();?>
      </div>
      <div class="col-sm-6 col-xs-12 text-right share-btns">
        <small class="text-muted">Share:</small>
        <?=getShareButtons($page->url);?>
      </div>
    </div><br />
    <nav aria-label="...">
      <ul class="pager">
<!--
        <?=$page->nextPrev();?> 
-->
      </ul>
    </nav>
  </div>
  <aside class="col-md-3 col-md-offset-0 col-sm-10 col-sm-offset-1 col-xs-12">
    <?php $page->tableOfContents(); ?>
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
      this.page.url = <?="'http://nathanhare.net$page->location'";?>;  // Replace PAGE_URL with your page's canonical URL variable
      this.page.identifier = "<?=$page->id;?>"; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
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

