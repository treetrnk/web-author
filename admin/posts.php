<!-- Modal -->
<div class="modal fade" id="delMod" tabindex="-1" role="dialog" aria-labelledby="delModLabel">
  <form action="/admin/" method="post">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title" id="delModLabel">Delete Post?</h3>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="deletePost" />
          <input type="hidden" name="id" value="" />
          Are you sure you want to delete the post <b id="post-title"></b> from <code id="post-location"></code>?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="row">
  <div class="col-xs-12 content">
    <h1>Posts</h1>
    <a href="?page=edit" class="btn btn-primary"><i class="glyphon glyphicon-plus"></i> New Post</a><br /><br />
    <div class="col-xs-12">
      <table class="table table-hover table-responsive">
        <thead>
          <tr>
            <th>Actions</th>
            <th>Title</th>
            <th>Location</th>
            <th>Type</th>
            <th width="175">Posted</th>
            <th width="50"></th>
          </tr>
        </thead>
        <tbody>
      <?php
        if ($result = mysqli_query($con, "SELECT * FROM posts ORDER BY SUBSTRING(location, 1, LENGTH(location) - LENGTH(SUBSTRING_INDEX(REVERSE(location), '/', 2))) ASC, sort ASC, time ASC, title ASC")) {
          while ($row = mysqli_fetch_assoc($result)) {
            $urlcode = "";
            $class = "";
            if (!empty($row['time'])) { 
              $time = date_format(date_create($row['time']), "D M d, Y - h:i:s A");
            }
            if (empty($row['time'] || strtotime($row['time']) > strtotime(date("Y-m-d H:i:s")))) {
              $urlcode = "?preview=" . urlencode(password_hash($row['title'], PASSWORD_DEFAULT));
              $class = "warning";
            }
            echo "
              <tr class='$class'>
                <td>
                  <a href='?page=edit&pid=$row[id]' class='btn btn-default btn-sm'><i class='glyphicon glyphicon-pencil' title='Edit'></i></a>
                  <a href='$row[location]$urlcode' class='btn btn-default btn-sm' target='_blank' title='View'><i class='glyphicon glyphicon-eye-open'></i></a>
                </td>
                <th>$row[title]</th>
                <td>$row[location]</td>
                <td>" . ucfirst($row['type']) . "</td>
                <td>$row[time]</td>
                <td>
                  <button type='button' class='btn btn-xs btn-danger' data-toggle='modal' data-target='#delMod' data-id='$row[id]' data-title='$row[title]' data-location='$row[location]'><i class='glyphicon glyphicon-remove'></i></button>
                </td>
              </tr>
            ";
          }
        }
      ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<script>
  $(document).ready(function() {
   $("#delMod").on('show.bs.modal', function(e) {
      var button = $(e.relatedTarget);
      var postTitle = button.data('title');
      var postID = button.data('id');
      var postLocation = button.data('location');
      var modal = $(this);
      modal.find('#post-title').text(postTitle);
      modal.find('#post-location').text(postLocation);
      modal.find('input[name="id"]').val(postID);
    }); 
  });
</script>
