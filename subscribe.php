<?php

  if (!empty($_POST['subscribe']) && !empty($_POST['email'])) {

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

        $url = "https://www.google.com/recaptcha/api/siteverify";
        if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
          //get verified response data
          $captcha = $_POST['g-recaptcha-response'];
          $data = array('secret' => $secretKey, 'response' => $captcha);

          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
          $verifyResponse = curl_exec($ch);
          curl_close($ch);

          $responseData = json_decode($verifyResponse, true);

          if ($responseData['success']) {
            $sql = "INSERT INTO subs (fname, lname, email) VALUES ('$fname', '$lname', '$email')";
            if ($result = mysqli_query($con, $sql)) {
              $success = "You have successfully subscribed to the mailing list!";
            } else {
              $error = "An error occured while trying to add you to the mailing list.";
            }
          } else {
            $error = "No robots allowed! 🤖";
          }
        } else {
          $error = "Please click on the reCAPTCHA box.";
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
      
  }

?>
