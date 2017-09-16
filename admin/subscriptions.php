<?php

  $sql = "SELECT * FROM subs ORDER BY fname";

  $numSubs = 0;
  if ($result = mysqli_query($con, $sql)) {
    $numSubs = mysqli_num_rows($result);
  }

?>
<h1>Subscriptions</h1>

<h3><?=$numSubs;?> Subscribers</h3>

<textarea class="form-control" rows="7" readonly>
  <?php

    if ($result) {
      while ($sub = mysqli_fetch_array($result)) {
        echo "$sub[fname] $sub[lname] <$sub[email]>, ";
      }
    }

  ?>
</textarea>
