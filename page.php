<div class="row">
  <div class="col-md-8 col-md-offset-2 col-sm-12 col-xs-12 content">
    
    <p class="pull-right text-muted" style="margin-top: 28px;" id="theme-select">
      <small class="hidden-xs">Theme:</small>
      <small>
        <a href="?theme=light" id="light-theme" title="Light Theme"> </a>
        <a href="?theme=dark" id="dark-theme" title="Dark Theme"> </a>
      </small>
    </p>
    <h1><?=$thisPost['title'];?></h1>
    <div class="content">
      <?=$PD->text($thisPost['body']);?>
    </div>
  
    <br /><br />

  </div>
</div>
