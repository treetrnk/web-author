<div class="row">
  <div class="col-md-8 col-md-offset-2 col-sm-12 col-xs-12 content">
    
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
    <div class="content">
      <?=$page->formattedBody;?>
    </div>
  
    <br /><br />

  </div>
</div>
