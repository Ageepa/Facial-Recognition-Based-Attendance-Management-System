<?php 
include 'Includes/dbcon.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>Student Attendance Ms</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-login" style="background-image: url('img/logo/loral1.jpe00g');">
    <!-- Login Content -->
    <div class="container-login">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <h5 align="center">STUDENT ATTENDANCE MANAGEMENT SYSTEM</h5>
                                    <div class="text-center">
                                        <img src="img/logo/attnlg.jpg" style="width:100px;height:100px">
                                        <br><br>
                                        <h1 class="h4 text-gray-900 mb-4">Login Panel</h1>
                                    </div>
                                    <form class="user" method="Post" action="">
                                        <div class="form-group">
                                            <select required name="userType" class="form-control mb-3">
                                                <option value="">--Select User Roles--</option>
                                                <option value="Administrator">Administrator</option>
                                                <option value="Principal">Principal/Vice Principal</option>
                                                <option value="ClassTeacher">Teacher</option>

                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" required name="username"
                                                id="exampleInputEmail" placeholder="Enter Email Address">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" required class="form-control"
                                                id="exampleInputPassword" placeholder="Enter Password"><br>
                                            <a href='forgotPassword.php' style="float:right">Recover Password</a>
                                        </div><br><br>

                                        <div class="form-group">
                                            <input type="submit" class="btn btn-success btn-block" value="Login"
                                                name="login" />
                                        </div>

                                    </form>

                                    <?php

  if(isset($_POST['login'])){

    $userType = $_POST['userType'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    
    

    // Email validation
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='alert alert-danger' role='alert' id='error'>
        Invalid Email Format.Check Email !!!
        </div>";
        echo "<script type='text/javascript'>
        setTimeout(function() {
            document.getElementById('error').style.display='none';
        }, 5000);
        </script>";
        exit;
    }

    // Username validation (assuming username should be between 3 and 20 characters)
    if (strlen($username) < 3 || strlen($username) > 30) {
        echo "<div class='alert alert-danger' role='alert' id='error'>
        Username should be between 3 and 20 characters!
        </div>";
        echo "<script type='text/javascript'>
        setTimeout(function() {
            document.getElementById('error').style.display='none';
        }, 5000);
        </script>";
        exit;
    }

  
   // Password validation (assuming password should be at least 6 characters and contain at least one uppercase letter, one lowercase letter, and one digit)
   if (strlen($password) < 6 || !preg_match("/[a-z]/", $password) || !preg_match("/[A-Z]/", $password) || !preg_match("/\d/", $password)) {
    echo "<div class='alert alert-danger' role='alert' id='error'>
    Password should be at least 6 characters and contain at least one uppercase letter, one lowercase letter, and one digit!
    </div>";
    echo "<script type='text/javascript'>
    setTimeout(function() {
        document.getElementById('error').style.display='none';
    }, 5000);
    </script>";
    exit;
}

    $password = md5($password);
    
    if($userType == "Administrator"){

      $query = "SELECT * FROM tbladmin WHERE emailAddress = '$username' AND password = '$password'";
      $rs = $conn->query($query);
      $num = $rs->num_rows;
      $rows = $rs->fetch_assoc();

      if($num > 0){

        $_SESSION['userId'] = $rows['Id'];
        $_SESSION['firstName'] = $rows['firstName'];
        $_SESSION['lastName'] = $rows['lastName'];
        $_SESSION['emailAddress'] = $rows['emailAddress'];

        echo "<script type = \"text/javascript\">
        window.location = (\"Admin/index.php\")
        </script>";
      }

      else {
    if($userType == "Administrator"){
        echo "<div class='alert alert-danger' role='alert' id='error'>
        Invalid Administrator Username/Password!
        </div>";
        echo "<script type='text/javascript'>
        setTimeout(function() {
            document.getElementById('error').style.display='none';
        }, 3000);
        </script>";
    } else {
         echo "<div class='alert alert-danger' role='alert' id='error'>
        Invalid Username/Password!
        </div>";
        echo "<script type='text/javascript'>
        setTimeout(function() {
            document.getElementById('error').style.display='none';
        }, 3000);
        </script>";
    }
}
    

    }
    else if($userType == "ClassTeacher"){

      $query = "SELECT * FROM tblclassteacher WHERE emailAddress = '$username' AND password = '$password'";
      $rs = $conn->query($query);
      $num = $rs->num_rows;
      $rows = $rs->fetch_assoc();

      if($num > 0){

        $_SESSION['userId'] = $rows['Id'];
        $_SESSION['firstName'] = $rows['firstName'];
        $_SESSION['lastName'] = $rows['lastName'];
        $_SESSION['emailAddress'] = $rows['emailAddress'];
        $_SESSION['classId'] = $rows['classId'];
        $_SESSION['classArmId'] = $rows['classArmId'];

        echo "<script type = \"text/javascript\">
        window.location = (\"ClassTeacher/index.php\")
        </script>";
      }
      else{
    if($userType == "ClassTeacher"){
        echo "<div class='alert alert-danger' role='alert' id='error'>
        Invalid Class Teacher Username/Password!
        </div>";
        echo "<script type='text/javascript'>
        setTimeout(function() {
            document.getElementById('error').style.display='none';
        }, 3000);
        </script>";
        } 
        else {
            echo "<div class='alert alert-danger' role='alert' id='error'>
           Invalid Username/Password!
           </div>";
           echo "<script type='text/javascript'>
           setTimeout(function() {
               document.getElementById('error').style.display='none';
           }, 3000);
           </script>";
       }
      }
    }
    elseif ($userType == "Principal/Vice Principal") {

      $query = "SELECT * FROM tblprincipal WHERE emailAddress = '$username' AND password = '$password'";
      $rs = $conn->query($query);
      $num = $rs->num_rows;
      $rows = $rs->fetch_assoc();

      if($num > 0){

        $_SESSION['userId'] = $rows['Id'];
        $_SESSION['firstName'] = $rows['firstName'];
        $_SESSION['lastName'] = $rows['lastName'];
        $_SESSION['emailAddress'] = $rows['emailAddress'];

        echo "<script type = \"text/javascript\">
        window.location = (\"Principal/index.php\")
        </script>";
      }
      else{
        if($userType == "Principal/Vice Principal"){
            echo "<div class='alert alert-danger' role='alert' id='error'>
            Invalid Principal/Vice Principal Username/Password!
            </div>";
            echo "<script type='text/javascript'>
            setTimeout(function() {
                document.getElementById('error').style.display='none';
            }, 3000);
            </script>";
        } 
        else {
            echo "<div class='alert alert-danger' role='alert' id='error'>
           Invalid Username/Password!
           </div>";
           echo "<script type='text/javascript'>
           setTimeout(function() {
               document.getElementById('error').style.display='none';
           }, 3000);
           </script>";
        }
      }
        
    }
}
?>

                                    <div class="text-center">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Login Content -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
</body>

</html>