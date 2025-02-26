<?php 
include 'Includes/dbcon.php';
session_start();

if (isset($_POST['submit'])) {
    // Retrieve data from form
    $email = mysqli_real_escape_string($conn, $_POST['emailAddress']);
    $oldPassword = mysqli_real_escape_string($conn, $_POST['oldPassword']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['newPassword']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);

    // Encrypt passwords using MD5
    $oldPasswordHash = md5($oldPassword);
    $newPasswordHash = md5($newPassword);
    $confirmPasswordHash = md5($confirmPassword);

    try {
        $isUserFound = false;

        // Check if email exists in tblclassteacher
        $queryClassTeacher = "SELECT * FROM tblclassteacher WHERE emailAddress = '$email' AND password = '$oldPasswordHash'";
        $resultClassTeacher = mysqli_query($conn, $queryClassTeacher);

        if (mysqli_num_rows($resultClassTeacher) > 0) {
            $isUserFound = true;
            $table = 'tblclassteacher';
        }

        // Check if email exists in tblprincipal (only if not found in tblclassteacher)
        if (!$isUserFound) {
            $queryPrincipal = "SELECT * FROM tblprincipal WHERE emailAddress = '$email' AND password = '$oldPasswordHash'";
            $resultPrincipal = mysqli_query($conn, $queryPrincipal);

            if (mysqli_num_rows($resultPrincipal) > 0) {
                $isUserFound = true;
                $table = 'tblprincipal';
            }
        }

        if ($isUserFound) {
            // Check if new password and confirm password match
            if ($newPassword === $confirmPassword) {
                // Update the password in the appropriate table
                $updatePasswordQuery = "UPDATE $table SET password = '$newPasswordHash' WHERE emailAddress = '$email'";
                mysqli_query($conn, $updatePasswordQuery);

                // Insert the new password into tblresetpassword
                $insertResetPasswordQuery = "INSERT INTO tblresetpassword (emailAddress, oldPassword, newPassword, confirmPassword) 
                                              VALUES ('$email', '$oldPasswordHash', '$newPasswordHash', '$confirmPasswordHash')";
                mysqli_query($conn, $insertResetPasswordQuery);

                // Success alert
                echo "<script>alert('Password Reset Successfully');</script>";
                echo "<script>window.location.href='index.php';</script>";
            } else {
                // Error: Passwords do not match
                echo "<script>alert('New Password and Confirm Password do not match');</script>";
            }
        } else {
            // Error: Invalid email or old password
            echo "<script>alert('Invalid Email or Old Password');</script>";
        }
    } catch (Exception $e) {
        // Error handling for any unexpected issues
        echo "<script>alert('An error occurred: " . $e->getMessage() . "');</script>";
    }
}

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
    <title>Student AMS - Forget Password</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-login" style="background-image: url('img/attendance.png');">
    <!-- Login Content -->
    <div class="container-login">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <div class="text-center">
                                        <img src="img/logo/attnlg.jpg" style="width:100px;height:100px">
                                        <br><br>
                                        <h1 class="h4 text-gray-900 mb-4">Reset Password</h1>
                                    </div>
                                    <form class="user" method="Post" action="" id="forgot-password-form">
                                        <div class="form-group">
                                            <input type="email" class="form-control" required name="emailAddress"
                                                id="exampleInputEmail" placeholder="Enter Email Address">
                                            <div class="invalid-feedback" style="display: none;" id="email-error"></div>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" required name="oldPassword"
                                                id="exampleInputPassword1" placeholder="Enter Old Password"
                                                minlength="8">
                                            <div class="invalid-feedback" id="old-password-error"></div>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" required name="newPassword"
                                                id="exampleInputPassword2" placeholder="Enter New Password"
                                                minlength="8">
                                            <div class="invalid-feedback" id="new-password-error"></div>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" required name="confirmPassword"
                                                id="exampleInputPassword3" placeholder="Confirm New Password"
                                                minlength="8">
                                            <a href="index.php" style="float:right;" size="10">Back to Login</a>
                                            <div class=" invalid-feedback" id="confirm-password-error">
                                            </div><br>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-block" value="Submit"
                                                name="submit" />
                                        </div>
                                    </form>

                                    <script>
                                    const form = document.getElementById('forgot-password-form');
                                    const emailInput = document.getElementById('exampleInputEmail');
                                    const oldPasswordInput = document.getElementById('exampleInputPassword1');
                                    const newPasswordInput = document.getElementById('exampleInputPassword2');
                                    const confirmPasswordInput = document.getElementById('exampleInputPassword3');

                                    form.addEventListener('submit', (e) => {
                                        let isValid = true;

                                        function validateEmail(email) {
                                            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                                            if (!emailRegex.test(email)) {
                                                return false;
                                            }
                                            const parts = email.split('@');
                                            if (parts.length !== 2) {
                                                return false;
                                            }
                                            const domain = parts[1];
                                            if (!domain.includes('.')) {
                                                return false;
                                            }
                                            return true;
                                        }

                                        // Email validation
                                        if (!validateEmail(emailInput.value)) {
                                            emailInput.classList.add('is-invalid');
                                            document.getElementById('email-error').innerText = 'Invalid email format';
                                            document.getElementById('email-error').style.display = 'block';
                                            isValid = false;
                                        } else {
                                            emailInput.classList.remove('is-invalid');
                                            document.getElementById('email-error').style.display = 'none';
                                        }

                                        // Old password validation
                                        if (oldPasswordInput.value.length < 6) {
                                            oldPasswordInput.classList.add('is-invalid');
                                            document.getElementById('old-password-error').innerText = 'Old password must be at least 6 characters long';
                                            isValid = false;
                                        } else {
                                            oldPasswordInput.classList.remove('is-invalid');
                                            document.getElementById('old-password-error').style.display = 'none';
                                        }

                                       // New password validation
                                       if (newPasswordInput.value.length < 6) {
                                            newPasswordInput.classList.add('is-invalid');
                                            document.getElementById('new-password-error').innerText = 'New password must be at least 6 characters long';

                                            // Remove error message after 3 seconds
                                            //setTimeout(() => {
                                                //document.getElementById('new-password-error').style.display = 'none';
                                            //}, 5000);

                                            isValid = false;
                                        } else {
                                            newPasswordInput.classList.remove('is-invalid');
                                            document.getElementById('new-password-error').style.display = 'none';
                                        }

                                        // Confirm password validation
                                        if (confirmPasswordInput.value !== newPasswordInput.value) {
                                            confirmPasswordInput.classList.add('is-invalid');
                                            document.getElementById('confirm-password-error').innerText = 'New password and confirm password do not match';

                                            // Remove error message after 3 seconds
                                            //setTimeout(() => {
                                                //document.getElementById('confirm-password-error').style.display = 'none';
                                            //}, 5000);

                                            isValid = false;
                                        } else {
                                            confirmPasswordInput.classList.remove('is-invalid');
                                            document.getElementById('confirm-password-error').style.display = 'none';
                                        }

                                        // Prevent form submission if any validation fails
                                        if (!isValid) {
                                            e.preventDefault();
                                        }
                                    });

                                    </script>

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