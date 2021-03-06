<?php

// A trait that aims to treat the different variable that you get from your forms
// It will make sure that no script is injected for exemple
// Just pass your post global variable in the function
// If everything is OK you will get back an array otherwise a error message from where the function stopped

trait Validator {

// The validate function take the $_POST variable as an argument
  public function validateForm($assoArray) {
    // Start an error message in case the data from the form is not correct
    $error = "<h2>There is something wrong with your data : </h2>";
    $errorDetection = false;

    foreach ($assoArray as $key => $value) {

      // If the data is of string type clears the tags
      if (gettype($value) === "string") {
        $assoArray[$key] = filter_var($value, FILTER_SANITIZE_STRING);

        // If there is email or mail in the name check if the adress is valid and clear it
        if (preg_match( "#e?mail#i", $key)) {
          // $assoArray[$key] = filter_var($value, FILTER_SANITIZE_EMAIL);
          if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $error .= "<p>This email is not valid</p>";
            $errorDetection = true;
          }
        }

        // If there is site url or link in the name check if the url is valid and clear it
        elseif (preg_match( "#url|site|link#i", $key)) {
          $assoArray[$key] = filter_var($value, FILTER_SANITIZE_URL);
          if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $error .= "<p>This url is not valid</p>";
            $errorDetection = true;
          }
        }

        // If this is a telefon number keeps only the numbers and then check the format
        elseif (preg_match( "#phone|phonenumber|telefon|telephone|tel#i", $key)) {
          $assoArray[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
          if (!preg_match("#(\+[0-9]{2}\([0-9]\))?[0-9]{10}#", $value)) {
            $error .= "<p>This phone number is not valid</p>";
            $errorDetection = true;
          }
        }

        // If this is a password then it is hashed before insertion in database
        elseif (preg_match( "#pass|password|mdp|motdepass#i", $key)) {
          $assoArray[$key] = password_hash($value, PASSWORD_DEFAULT);
        }

      }

      // If the type is an array then each string element is treated to get ride of tags
      if (gettype($value) === "array") {
        foreach ($value as $key => $val) {
          if (gettype($val) === "string") {
            $$value[$key] = filter_var($val, FILTER_SANITIZE_STRING);
          }
        }
      }

    }
    // If no error has been detected then returns the sanitized array
    if (!$errorDetection) {
      return $assoArray;
    }
    // Otherwise return an error message
    else {
      return $error;
    }

  }

}

 ?>
