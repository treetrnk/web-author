<h1>Navigtion</h1>
<h2>Sort Order</h2>
<form method='post' action='/admin/?page=navigation'>
  <input type='hidden' name='action' value='sort' />

<?php

  $sql = "SELECT *, (SUBSTRING(location, 1, LENGTH(location) - LENGTH(SUBSTRING_INDEX(REVERSE(location), '/', 2)))) as parentLoc FROM posts ORDER BY parentLoc ASC, sort ASC, time ASC, title ASC";

  if ($result = mysqli_query($con, $sql)) { 
    $curPL = "";
    while ($row = mysqli_fetch_array($result)) {
      if ($row['parentLoc'] != $curPL) {
        echo "
            </div>
            <div class='col-sm-4 col-xs-12'>
              <br />
              <h3>$row[parentLoc]</h3>
        ";
        $curPL = $row['parentLoc'];
      }
      $hidden = "";
      if (empty($row['time'])) { $hidden = "<i class='glyphicon glyphicon-eye-close'></i> "; }

      echo "
        <div class='row'>
          <div class='col-sm-4 col-xs-4'>
            <input type='number' name='$row[id]' value='$row[sort]' class='form-control text-center' required />
          </div>
          <div class='col-sm-8 col-xs-8' style='line-height:33px;'>
            $hidden$row[title]
          </div>
        </div>
      ";

    }
  }

?>

    </div>
    <div class="col-xs-12">
      <br /><br />
      <button type="submit" class="btn btn-primary">Save</button>
    </div>
  </div>

</form>
