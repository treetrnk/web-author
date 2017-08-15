<div class="row">
  <div class="col-md-9 col-sm-12 col-xs-12 content">
    
    <h1><?=$thisPost['title'];?></h1>
    <?=$PD->text($thisPost['body']);?>
  
    <br /><br />
    <div class="row">
      <div class="col-xs-6">
        <i class="glyphicon glyphicon-tag"></i>
        <span class="label label-default">tag</span>
        <span class="label label-default">another tag</span>
        <span class="label label-default">stuff</span>
      </div>
      <div class="col-xs-6 text-right share-btns">
        <small class="text-muted">Share:</small>
        <a href="#">
          <img src="https://lh3.googleusercontent.com/N-AY2XwXafWq4TQWfua6VyjPVQvTGRdz9CKOHaBl2nu2GVg7zxS886X5giZ9yY2qIjPh=w300" />
        </a>
        <a href="https://twitter.com/share?url=http%3A%2F%2Fopensourcepa.com%2F%3Fpage%3Dservices%26tab%3Dsolve"><!-- needs to be encoded with urlencode() -->
          <img src="http://emilypstewart.com/images/social-media-icons/Twitter-Logo.png" />
        </a>
        <a href="#">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Facebook_Circle.svg/2000px-Facebook_Circle.svg.png" />
        </a>
      </div>
    </div><br />
    <nav aria-label="...">
      <ul class="pager">
        <li class="previous"><a href="#"><span aria-hidden="true">&larr;</span> Previous Chapter</a></li>
        <li class="next"><a href="#">Next Chapter <span aria-hidden="true">&rarr;</span></a></li>
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
    /*
    var disqus_config = function () {
    this.page.url = PAGE_URL;  // Replace PAGE_URL with your page's canonical URL variable
    this.page.identifier = PAGE_IDENTIFIER; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
    };
    */
    (function() { // DON'T EDIT BELOW THIS LINE
    var d = document, s = d.createElement('script');
    s.src = 'https://EXAMPLE.disqus.com/embed.js';
    s.setAttribute('data-timestamp', +new Date());
    (d.head || d.body).appendChild(s);
    })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
  </div>
</div>

