<?php 
include 'Includes/dbcon.php';
session_start();

$errorMessages = [
    'userType' => '',
    'username' => '',
    'password' => ''
];

if (isset($_POST['login'])) {
    $userType = $_POST['userType'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $isValid = true;

    // Email validation
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $errorMessages['username'] = 'Invalid Email Format. Check Email!';
        $isValid = false;
    }

    // Username validation
    if (strlen($username) < 3 || strlen($username) > 30) {
        $errorMessages['username'] = 'Username should be between 3 and 20 characters!';
        $isValid = false;
    }

    // Password validation
    if (strlen($password) < 6 || !preg_match("/[a-z]/", $password) || !preg_match("/[A-Z]/", $password) || !preg_match("/\d/", $password)) {
        $errorMessages['password'] = 'Password must be at least 6 characters with at least one uppercase, one lowercase, and one digit!';
        $isValid = false;
    }

    if ($isValid) {
        $password = md5($password);

        // Authentication logic for Administrator
        if ($userType == "Administrator") {
            $query = "SELECT * FROM tbladmin WHERE emailAddress = '$username' AND password = '$password'";
            $rs = $conn->query($query);
            $num = $rs->num_rows;
            $rows = $rs->fetch_assoc();

            if ($num > 0) {
                $_SESSION['userId'] = $rows['Id'];
                $_SESSION['firstName'] = $rows['firstName'];
                $_SESSION['lastName'] = $rows['lastName'];
                $_SESSION['emailAddress'] = $rows['emailAddress'];
                echo "<script type='text/javascript'>window.location = ('Admin/index.php');</script>";
            } else {
                $errorMessages['password'] = 'Invalid Administrator Username/Password!';
            }
        }
        // Authentication logic for ClassTeacher
        elseif ($userType == "ClassTeacher") {
            $query = "SELECT * FROM tblclassteacher WHERE emailAddress = '$username' AND password = '$password'";
            $rs = $conn->query($query);
            $num = $rs->num_rows;
            $rows = $rs->fetch_assoc();

            if ($num > 0) {
                $_SESSION['userId'] = $rows['Id'];
                $_SESSION['firstName'] = $rows['firstName'];
                $_SESSION['lastName'] = $rows['lastName'];
                $_SESSION['emailAddress'] = $rows['emailAddress'];
                $_SESSION['password'] = $rows['password'];
                $_SESSION['classId'] = $rows['classId'];
                $_SESSION['classArmId'] = $rows['classArmId'];
                echo "<script type='text/javascript'>window.location = ('ClassTeacher/index.php');</script>";
            } else {
                $errorMessages['password'] = 'Invalid Class Teacher Username/Password!';
            }
        }
        // Authentication logic for Principal/Vice Principal
        elseif ($userType == "Principal/Vice Principal") {
            $query = "SELECT * FROM tblprincipal WHERE emailAddress = '$username' AND password = '$password'";
            $rs = $conn->query($query);
            $num = $rs->num_rows;
            $rows = $rs->fetch_assoc();

            if ($num > 0) {
                $_SESSION['userId'] = $rows['Id'];
                $_SESSION['firstName'] = $rows['firstName'];
                $_SESSION['lastName'] = $rows['lastName'];
                $_SESSION['emailAddress'] = $rows['emailAddress'];
                echo "<script type='text/javascript'>window.location = ('Principal/index.php');</script>";
            } else {
                $errorMessages['password'] = 'Invalid Principal/Vice Principal Username/Password!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Student Attendance Ms</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
         .error {
            color: red;
            font-size: 0.9em;
            display: block;
            width: 100%; /* Ensure error message spans full width */
            margin-top: 5px; /* Optional: Adds some spacing above the error */
         }
    </style>
</head>
<body class="bg-gradient-login" style="background-image: url('img/attendance.png');">
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
                                        <img src="img/logo/attnlg.jpg" style="width:100px;height:100px"><br><br>
                                        <h1 class="h4 text-gray-900 mb-4">Login Panel</h1>
                                    </div>
                                    <form class="user" method="Post" action="">
                                        <div class="form-group">
                                            <select required name="userType" class="form-control mb-3">
                                                <option value="">--Select User Roles--</option>
                                                <option value="Administrator" <?= isset($userType) && $userType == "Administrator" ? "selected" : "" ?>>Administrator</option>
                                                <option value="Principal" <?= isset($userType) && $userType == "Principal/Vice Principal" ? "selected" : "" ?>>Principal/Vice Principal</option>
                                                <option value="ClassTeacher" <?= isset($userType) && $userType == "ClassTeacher" ? "selected" : "" ?>>Teacher</option>
                                            </select>
                                            <div class="error"><?= $errorMessages['userType'] ?></div>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" required name="username" id="exampleInputEmail" placeholder="Enter Email Address" value="<?= htmlspecialchars($username ?? '') ?>">
                                            <div class="error"><?= $errorMessages['username'] ?></div>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" required class="form-control" id="exampleInputPassword" placeholder="Enter Password">
                                            <div class="error"><?= $errorMessages['password'] ?></div>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-success btn-block" value="Login" name="login" />
                                        </div>
                                    </form>
                                    <div class="text-center">
                                        <a href='resetPassword.php'>Reset Password</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <script>
        // Automatically hide error messages after 3 seconds
        setTimeout(function() {
            const errors = document.querySelectorAll('.error');
            errors.forEach(error => error.style.display = 'none');
        }, 5000);
    </script>
</body>
</html>
