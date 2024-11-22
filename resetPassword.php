<?php 
include 'Includes/dbcon.php';
session_start();

if (isset($_POST['submit'])) {

    $emailAddress = $_POST['emailAddress'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($newPassword == $confirmPassword) {
        $query = "INSERT INTO tblresetpassword (emailAddress,oldPassword,newPassword,confirmPassword) VALUES('$emailAddress','$oldPassword','$newPassword','$confirmPassword')";
        $rs = $conn->query($query);

        $_SESSION['userId'] = $Id;
        $_SESSION['emailAddress'] = $emailAddress;
        $_SESSION['oldPassword'] = $oldPassword;
        $_SESSION['newPassword'] = $newPassword;
        $_SESSION['confirmPassword'] = $confirmPassword;

        echo "<script type = \"text/javascript\">
        window.location = (\"resetPassword.php\")
        </script>";
    }
    else {
        echo "<div class='alert alert-danger' role='alert' id='error'>
        Passwords do not match!
        </div>";
        echo "<script type='text/javascript'>
        setTimeout(function() {
            document.getElementById('error').style.display='none';
        }, 5000);
        </script>";
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
                                            const emailRegex =
                                                /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
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

                                        if (!validateEmail(emailInput.value)) {
                                            emailInput.classList.add('is-invalid');
                                            document.getElementById('email-error').innerText =
                                                'Invalid email format';
                                            document.getElementById('email-error').style.display = 'block';
                                            isValid = false;
                                        } else {
                                            emailInput.classList.remove('is-invalid');
                                            document.getElementById('email-error').style.display = 'none';
                                        }


                                        if (oldPasswordInput.value.length < 6) {
                                            oldPasswordInput.classList.add('is-invalid');
                                            document.getElementById('old-password-error').innerText =
                                                'Old password must be at least 6 characters long';
                                            isValid = false;
                                        } else {
                                            oldPasswordInput.classList.remove('is-invalid');
                                        }

                                        if (newPasswordInput.value.length < 6) {
                                            newPasswordInput.classList.add('is-invalid');
                                            document.getElementById('new-password-error').innerText =
                                                'New password must be at least 6 characters long';
                                            isValid = false;
                                        } else {
                                            newPasswordInput.classList.remove('is-invalid');
                                        }

                                        if (confirmPasswordInput.value !== newPasswordInput.value) {
                                            confirmPasswordInput.classList.add('is-invalid');
                                            document.getElementById('confirm-password-error').innerText =
                                                'New password and confirm password do not match';
                                            isValid = false;
                                        } else {
                                            confirmPasswordInput.classList.remove('is-invalid');
                                        }

                                        if (!isValid) {
                                            e.preventDefault();
                                        }
                                    });
                                    </script>

                                    <?php

			                    ?>


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