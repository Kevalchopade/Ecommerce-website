<?php
  require('db.php');
  session_start();
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  function sendMail($email, $verification_code){
    require("PHPMailer/PHPMailer.php");
    require("PHPMailer/SMTP.php");
    require("PHPMailer/Exception.php");

    $mail = new PHPMailer(true);
    try {
      // Debug mode - comment out in production
      // $mail->SMTPDebug = 2; // Enable verbose debug output
      
      // Server settings
      $mail->isSMTP();                                            
      $mail->Host       = 'smtp.gmail.com';                     
      $mail->SMTPAuth   = true;                                   
      $mail->Username   = 'smartwearservice2025@gmail.com';                     
      $mail->Password   = 'qakj vexo nogu rtcy';                               
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
      $mail->Port       = 465;                                    
  
      // Recipients
      $mail->setFrom('smartwearservice2025@gmail.com', 'SmartWear');
      $mail->addAddress($email);     
      
      // Content
      $mail->isHTML(true);                                  
      $mail->Subject = 'Email Verification from SmartWear';
      $mail->Body    = "Thanks for registration! Click the link below to verify your email: 
                       <a href='http://localhost:3000/product/verify.php?email=$email&verification_code=$verification_code'>
                       Verify Your Email</a>";
      $mail->AltBody = "Thanks for registration! Visit this link to verify your email: 
                       http://localhost:3000/product/verify.php?email=$email&verification_code=$verification_code";
  
      $mail->send();
      return ['success' => true, 'message' => ''];
    } catch (Exception $e) {
      error_log("Mail Error: " . $mail->ErrorInfo);
      return ['success' => false, 'message' => $mail->ErrorInfo];
    }
  }

  # For login
  if(isset($_POST['login'])){
    $email_username = mysqli_real_escape_string($conn, $_POST['email_username']);
    
    $query = "SELECT * FROM `registered_users` WHERE `email`='$email_username' OR `username`='$email_username'";
    $result = mysqli_query($conn, $query);
    
    if($result){
      if(mysqli_num_rows($result) == 1){
        $result_fetch = mysqli_fetch_assoc($result);
        if($result_fetch['is_verified'] == 1){
          if(password_verify($_POST['password'], $result_fetch['password']))
          {
            # If password matched
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $result_fetch['username'];
            header("location:../pages/home.php");
            exit();
          }
          else{
            # If incorrect password
            echo "<script>
            alert('Password incorrect');
            window.location.href='../pages/home.php';
            </script>";
          }
        }
        else{
          echo "<script>
          alert('Email not verified. Please check your inbox and spam folder for verification email.');
          window.location.href='../pages/home.php';
          </script>";
        }
      } else {
        echo "<script>
        alert('Email or username incorrect');
        window.location.href='../pages/home.php';
        </script>";
      }
    } else {
      echo "<script>
      alert('Database query error: " . mysqli_error($conn) . "');
      window.location.href='../pages/home.php';
      </script>";
    }
  }

  # For registration 
  if(isset($_POST['register'])){
    // Sanitize input
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $user_exist_query = "SELECT * FROM `registered_users` WHERE `username`='$username' OR `email`='$email'";
    $result = mysqli_query($conn, $user_exist_query);
    
    if($result){ 
      if(mysqli_num_rows($result) > 0){
        $result_fetch = mysqli_fetch_assoc($result);
        if($result_fetch['username'] == $username){
          echo "<script>
          alert('$username - Username already exists');
          window.location.href='../pages/home.php';
          </script>";
        } else {
          echo "<script>
          alert('$email - Email already registered');
          window.location.href='../pages/home.php';
          </script>";
        }
      } else {
        // Hash password using Bcrypt
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $verification_code = bin2hex(random_bytes(16));

        $query = "INSERT INTO `registered_users`(`full_name`, `username`, `email`, `password`, `verification_code`, `is_verified`) 
                 VALUES ('$fullname', '$username', '$email', '$password', '$verification_code', '0')";
        
        $db_result = mysqli_query($conn, $query);
        if($db_result) {
          // Send verification email
          $mail_result = sendMail($email, $verification_code);
          
          if($mail_result['success']) {
            echo "<script>
            alert('Registration successful! Please check your email (including spam folder) to verify your account.');
            window.location.href='../pages/home.php';
            </script>";
          } else {
            // Email sending failed but user was created in database
            echo "<script>
            alert('Account created but verification email could not be sent. Error: " . addslashes($mail_result['message']) . "');
            window.location.href='../pages/home.php';
            </script>";
          }
        } else {
          echo "<script>
          alert('Database error: " . mysqli_error($conn) . "');
          window.location.href='../pages/home.php';
          </script>";
        }
      }
    } else {
      echo "<script>
      alert('Database query error: " . mysqli_error($conn) . "');
      window.location.href='../pages/home.php';
      </script>";
    }
  }
?>