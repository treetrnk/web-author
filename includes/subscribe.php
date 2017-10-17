<?php

  $_POST['g-recaptcha-response'] = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : "";  
  $_POST['coinhive-captcha-token'] = isset($_POST['coinhive-captcha-token']) ? $_POST['coinhive-captcha-token'] : "";  

  if (!empty($_POST['subscribe']) && !empty($_POST['email']) && (!empty($_POST['g-recaptcha-response'] || !empty($_POST['coinhive-captcha-token'])))) {

    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $fname = NULL;
    $lname = NULL;

    if (!empty($_POST['fname'])) { $fname = mysqli_real_escape_string($con, trim($_POST['fname'])); }
    if (!empty($_POST['lname'])) { $lname = mysqli_real_escape_string($con, trim($_POST['lname'])); }

    $chksql = "SELECT * FROM subs WHERE email = '$email'";
    if ($chkresult = mysqli_query($con, $chksql)) {
      if (mysqli_num_rows($chkresult) > 0) {
        $error = "Your email address is already subscribed.";
      } else {
        $sql = "INSERT INTO subs (fname, lname, email) VALUES ('$fname', '$lname', '$email')";
        if ($result = mysqli_query($con, $sql)) {
          $success = "You have successfully subscribed to the mailing list!";
        } else {
          $error = "An error occured while trying to add you to the mailing list.";
        }
      }
    }


  } elseif (!empty($_POST['unsubscribe'])) {
    if (!empty($_POST['email'])) {

      $email = mysqli_real_escape_string($con, trim($_POST['email']));

      $chksql = "SELECT * FROM subs WHERE email = '$email'";
      if ($chkresult = mysqli_query($con, $chksql)) {
        if (mysqli_num_rows($chkresult) > 0) {

          $sql = "DELETE FROM subs WHERE email = '$email'";
          if ($result = mysqli_query($con, $sql)) {
            $success = "$email has been successfully unsubscribed."; 
          } else {
            $error = "An error occurred when unsubscribing $email from the list.";
          }
        } else {
          $error = "$email is not currently subscribed.";
        }
      } else {
        $error = "Could not unsubscirbe $email from the list.";
      }
    } else {
      $error = "You must provide an email address to unsubscribe with.";
    }
      
  } else {

    $error = "Please provide your email address and click the captcha to subscribe.";
  }
?>
