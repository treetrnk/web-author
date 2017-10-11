<?php 
  $edit = false;
  $action = "Add";
  $engAction = "addPost";
  if (!empty($_GET['pid'])) {
    $pid = $_GET['pid'];
    $edit = true;
    $action = "Edit";
    $engAction = "editPost";
  }

  $title = "";
  $parent = "";
  $banner = "";
  $tags = "";
  $type = "";
  $body = "";
  $location = "";

  if ($action == 'Add') {
    if (!empty($_POST['title'])){        $title        = $_POST['title']; }
    if (!empty($_POST['parent'])){       $parent       = $_POST['parent']; }
    if (!empty($_POST['banner'])){       $banner       = $_POST['banner']; }
    if (!empty($_POST['tags'])){         $tags         = $_POST['tags']; }
    if (!empty($_POST['type'])){         $type         = $_POST['type']; }
    if (!empty($_POST['body'])){         $body         = $_POST['body']; }
    if (!empty($_POST['sidebar'])){      $sidebar      = $_POST['sidebar']; }
    if (!empty($_POST['location'])){     $location     = $_POST['location']; }
    $actionPage = "/admin/";


  } elseif ($action == "Edit") {
    if (!empty($_GET['ver'])) {
      $result = mysqli_query($con, "SELECT * FROM postsVer WHERE postid = $pid AND id = $_GET[ver] LIMIT 1");
      $row = mysqli_fetch_assoc($result);
      $row['time'] = NULL;
    } else {
      $result = mysqli_query($con, "SELECT * FROM posts WHERE id = $pid LIMIT 1");
      $row = mysqli_fetch_assoc($result);
    }

    if (!empty($row['title'])){        $title        = $row['title']; }
    if (!empty($row['parent'])){       $parent       = $row['parent']; }
    if (!empty($row['banner'])){       $banner       = $row['banner']; }
    if (!empty($row['tags'])){         $tags         = $row['tags']; }
    if (!empty($row['type'])){         $type         = $row['type']; }
    if (!empty($row['body'])){         $body         = $row['body']; }
    if (!empty($row['sidebar'])){      $sidebar      = $row['sidebar']; }
    if (!empty($row['location'])){     $location     = $row['location']; }
    $actionPage = "/admin/?page=edit&pid=$_GET[pid]";
  }

  switch ($type) {
    case 'page':
      $page = 'selected';
      break;
    case 'story':
      $story = 'selected';
      break;
    case 'chapter':
      $chapter = 'selected';
      break;
    case 'blog':
      $blog = 'selected';
      break;
    case 'post':
      $post = 'selected';
      break;
  }


  echo "
    <form class='form' action='$actionPage' method='post'>
      <input type='hidden' name='action' value='$engAction' />
      <div class='pull-right col-md-3 col-sm-6  col-xs-8'>
  ";
  if ($edit) {

    echo "
        <label class='control-label'>Version</label>
        <select class='form-control'id='versions'>
    ";

    $versql = "SELECT * FROM postsVer WHERE postid = $pid ORDER BY lastUpdate DESC";
    if ($verresult = mysqli_query($con, $versql)) {
      $first = true;
      while ($ver = mysqli_fetch_array($verresult)) {
        $selected = "";
        if (!empty($_GET['ver']) && $ver['id'] == $_GET['ver']) {
          $selected = 'selected';
        }
        if ($first) {
          echo "<option value='/admin/?page=edit&pid=$pid' $selected>" . date_format(date_create($ver['lastUpdate']), "M. j, Y - g:i A") . " (Current)</option>";
          $first = false;
        } else {
          echo "<option value='/admin/?page=edit&pid=$pid&ver=$ver[id]' $selected>" . date_format(date_create($ver['lastUpdate']), "M. j, Y - g:i A") . "</option>";
        }
      }
    }

    echo "</select>";
  }

  echo "
      </div>
      <h1>$action Post</h1>
  ";
  
  if ($edit) { 
    echo "<input type='hidden' name='id' value='$_GET[pid]' />"; 
    $urlcode = "";
    if (!empty($row['time'])) { 
      $time = date_format(date_create($row['time']), "D M d, Y - h:i:s A");
    }
    if (empty($row['time'] || strtotime($row['time']) > strtotime(date("Y-m-d H:i:s")))) {
      $urlcode = "?preview=" . urlencode(password_hash($row['title'], PASSWORD_DEFAULT));
    }
    echo "<p><a href='$row[location]$urlcode' target='preview'><i class='glyphicon glyphicon-eye-open'></i> View Post</a></p>";
  }

  echo "
      <div class='form-body'>
        <div class='row'>
          <div class='col-xs-12'>
            <label class='control-label'>Title</label>
            <input type='text' name='title' value='$title' class='form-control' />
          </div>
        </div>
        <div class='row'>
          <div class='col-sm-6 col-xs-12'>
            <label class='control-label'>Parent</label>
            <select name='parent' class='form-control' />
              <option value=''>(none)</option>";

              if ($result = mysqli_query($con, "SELECT * FROM posts ORDER BY $locOrder, sort, title")) {
                // Add the following to the query above to limit it to first and second tier posts only.
                // WHERE (length(location)-length(replace(location, '/', ''))) < 4 
                while ($curpost = mysqli_fetch_assoc($result)) {
                  if (!($edit && $curpost['id'] == $_GET['pid'])) {
                    $selected = "";
                    if ($parent == $curpost['id']) { $selected = "selected"; }
                    echo "<option value='$curpost[id]' $selected >$curpost[title] ($curpost[location])</option>";
                  }
                }
              }
    
  echo " 
            </select>
          </div>
          <div class='col-sm-6 col-sm-12'>
            <label class='control-label'>Banner Image </label>
            <i class='glyphicon glyphicon-question-sign text-muted' data-toggle='tooltip' title='Defaults to parent&#39;s banner image if left blank.'></i>
            <input type='text' name='banner' value='$banner' placeholder='http://example.com/sample.png' class='form-control' />
          </div>
        </div>
        <div class='row'>
          <div class='col-sm-6 col-xs-12'>
            <label class='control-label'>Tags</label>
            <i class='glyphicon glyphicon-question-sign text-muted' data-toggle='tooltip'  title='Comma separated list (i.e. - sports,hockey,canada). '></i>
            <input type='text' name='tags' value='$tags' class='form-control' />
          </div>
          <div class='col-sm-6 col-xs-12'>
            <label class='control-label'>Page Type</label>
            <select name='type' class='form-control'>
              <option value='page' $page >Page - No comments, tags, ToC</option>
              <option value='story' $story >Story - Description page for the Chapters under it</option>
              <option value='chapter' $chapter>Chapter - Child to Story type</option>
              <option value='blog' $blog>Blog - Lists the blog Posts under it</option>
              <option value='post' $post>Post - A blog post to be listed on a parent Blog</option>
            </select>
          </div>
        </div>
        <div class='row'>
          <div class='col-xs-12'>
            <label class='control-label'>Sidebar</label>
            <textarea name='sidebar' class='form-control' data-provide='markdown' rows='5'>$sidebar</textarea>
          </div>
        </div>
        <div class='row'>
          <div class='col-xs-12'>
            <span class='pull-right text-muted' id='display_count'> </span>
            <label class='control-label'>Body</label>
            <textarea name='body' class='form-control' id='md-input' data-provide='markdown' rows='15'>$body</textarea>
          </div>
        </div>
      </div>
      <div class='form-actions'>
        <div class='row'>
          <div class='col-xs-12'>
  ";

  $disabled = "";
  if (!empty($_GET['ver'])) {
    $disabled = "disabled='disabled'";
  }

  if ($edit && !empty($row['time'])) {
    echo "
            <button type='submit' name='publish' value='unpublish' class='btn btn-warning' $disabled>
              <i class='glyphicon glyphicon-eye-close'></i> Unpublish</button>
    ";
  } else {
    echo "
            <button type='submit' name='publish' value='y' class='btn btn-success' $disabled>
              <i class='glyphicon glyphicon-check'></i> Publish</button>
    ";
  }
  
  echo "
            <button type='submit' name='publish' value='n' class='btn btn-default'>
              <i class='glyphicon glyphicon-floppy-disk'></i> Save
            </button>
            <a href='?page=posts' class='btn btn-default'>
              <i class='glyphicon glyphicon-minus-sign'></i> Cancel
            </a>
          </div>
        </div>
      </div>
    </form>
  ";

?> 

  <script>
    $(document).ready(function() {
    
      $('#versions').change(function() {
        window.location.href= $('#versions').val();
      }); 
    
      var words = $('#md-input').val().match(/[^*#\s]+/g).length;
      $('#display_count').text("("+words+" words)");
      
      $("#md-input").on('keyup', function(e) {
        var words = this.value.match(/[^*$\s]+/g).length;
        $('#display_count').text("("+words+" words)");
      });

    });
  </script>
