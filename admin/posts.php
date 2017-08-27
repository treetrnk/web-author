<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this post?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
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
                  <form action='index.php?page=posts' method='post'>
                    <input type='hidden' name='action' value='deletePost' />
                    <input type='hidden' name='id' value='$row[id]' />
                    <button type='submit' class='btn btn-xs btn-danger'><i class='glyphicon glyphicon-remove'></i></button>
                  </form>
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
