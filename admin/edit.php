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
    if (!empty($_POST['location'])){     $location     = $_POST['location']; }
    $actionPage = "/admin/";


  } elseif ($action == "Edit") {
    $result = mysqli_query($con, "SELECT * FROM posts WHERE id = $pid LIMIT 1");
    $row = mysqli_fetch_assoc($result);

    if (!empty($row['title'])){        $title        = $row['title']; }
    if (!empty($row['parent'])){       $parent       = $row['parent']; }
    if (!empty($row['banner'])){       $banner       = $row['banner']; }
    if (!empty($row['tags'])){         $tags         = $row['tags']; }
    if (!empty($row['type'])){         $type         = $row['type']; }
    if (!empty($row['body'])){         $body         = $row['body']; }
    if (!empty($row['location'])){     $location     = $row['location']; }
    $actionPage = "/admin/?page=edit&pid=$_GET[pid]";
  }

  switch ($type) {
    case 'page':
      $page = 'selected';
      break;
    case 'book':
      $book = 'selected';
      break;
    case 'chapter':
      $chapter = 'selected';
      break;
    case 'page':
      $blog = 'selected';
      break;
    case 'page':
      $post = 'selected';
      break;
  }

  echo "
    <h1>$action Post</h1>
    <form class='form' action='$actionPage' method='post'>
      <input type='hidden' name='action' value='$engAction' />
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
    echo "<p><a href='$row[location]$urlcode' target='_blank'>View Post</a></p>";
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

              if ($result = mysqli_query($con, "SELECT * FROM posts WHERE (length(location)-length(replace(location, '/', ''))) < 4")) {
                while ($curpost = mysqli_fetch_assoc($result)) {
                  if (!($edit && $curpost['id'] == $_GET['pid'])) {
                    $selected = "";
                    if ($parent == $curpost['id']) { $selected = "selected"; }
                    echo "<option value='$curpost[id]' $selected >$curpost[title]</option>";
                  }
                }
              }
    
  echo " 
            </select>
          </div>
          <div class='col-sm-6 col-sm-12'>
            <label class='control-label'>Banner Image </label>
            <a href='#' class='badge' data-toggle='tooltip' title='Defaults to parent&#39;s banner image if left blank.'>?</a>
            <input type='text' name='banner' value='$banner' placeholder='http://example.com/sample.png' class='form-control' />
          </div>
        </div>
        <div class='row'>
          <div class='col-sm-6 col-xs-12'>
            <label class='control-label'>Tags</label>
            <a href='#' class='badge'data-toggle='tooltip'  title='Comma separated list (i.e. - sports,hockey,canada). '>?</a>
            <input type='text' name='tags' value='$tags' class='form-control' />
          </div>
          <div class='col-sm-6 col-xs-12'>
            <label class='control-label'>Page Type</label>
            <select name='type' class='form-control'>
              <option value='page' $page >Page - No comments, tags, ToC</option>
              <option value='book' $book >Book - Descrition page for the Chapters under it</option>
              <option value='chapter' $chapter>Chapter - Child to Book type</option>
              <option value='blog' $blog>Blog - Lists the blog Posts under it</option>
              <option value='post' $post>Post - A blog post to be listed on a parent Blog</option>
            </select>
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

  if ($edit && !empty($row['time'])) {
    echo "
            <button type='submit' name='publish' value='unpublish' class='btn btn-warning'>
              <i class='glyphicon glyphicon-eye-close'></i> Unpublish</button>
    ";
  } else {
    echo "
            <button type='submit' name='publish' value='y' class='btn btn-success'>
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
