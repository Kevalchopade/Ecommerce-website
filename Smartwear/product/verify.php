<?php 

require('db.php');

if(isset($_GET['email']) && isset($_GET['verification_code'])){

  $query="SELECT * FROM `registered_users` WHERE `email`='$_GET[email]' AND `verification_code`='$_GET[verification_code]'";
  $result=mysqli_query($conn,$query);
  if($result){
    if(mysqli_num_rows($result)==1){
      $result_fetch=mysqli_fetch_assoc($result);
      if($result_fetch['is_verified']==0){
        $update="UPDATE `registered_users` SET `is_verified` ='1' WHERE `email`='$result_fetch[email]'";
        if(mysqli_query($conn,$update)){
          echo"<script>
      alert('email verified successful');
      window.location.href='../pages/home.php';
      </script>";
        }
        else{
          echo"<script>
      alert('cannot run query');
      window.location.href='../pages/home.php';
      </script>";
        }
      }
      else{
        echo"<script>
      alert('email already verified');
      window.location.href='../pages/home.php';
      </script>";
      }
    }
  }
  else{
    echo"<script>
      alert('Server Down');
      window.location.href='../pages/home.php';
      </script>";
  }

}


?>