<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $class = $_POST['class'];
  $classArm = $_POST['classArm'];
  $fromEmail = trim($_POST['from']);
  $toEmail = trim($_POST['to']);
  $message = $_POST['message'];

  // Log the selected email address for debugging
  echo "<script>console.log('Selected email: $toEmail');</script>";


  // Validate required fields
  if (!empty($class) && !empty($classArm) && !empty($fromEmail) && !empty($message) && !empty($toEmail)) {
      if (filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
          $subject = "Notification from School";
          $headers = "From: $fromEmail\r\n";
          $headers .= "Reply-To: $fromEmail\r\n";
          $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

          // Send the email
          if (mail($toEmail, $subject, nl2br($message), $headers)) {
              $statusMsg = "<div class='alert alert-success' style='margin-right:700px;' id='statusMsg' role='alert'>Email Sent Successfully</div>";
          } else {
              $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;' id='statusMsg' role='alert'>Failed to send email!</div>";
              echo "<script>console.log('Failed to send email to $toEmail');</script>";
          }
      } else {
          $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;' id='statusMsg' role='alert'>Invalid Email Address Provided!</div>";
      }
  } else {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;' id='statusMsg' role='alert'>Please Fill in all required fields!</div>";
  }

  echo "<script>
      setTimeout(function() {
          var msg = document.getElementById('statusMsg');
          if (msg) {
              msg.style.display = 'none';
          }
      }, 3000);
  </script>";
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
  <title>Send Notifications</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <script src="../vendor/jquery/jquery.min.js"></script>
</head>

<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php";?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php";?>

        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Send Notifications</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Send Notifications</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Send Notifications</h6>
                  <?php echo isset($statusMsg) ? $statusMsg : ''; ?>
                </div>
                <div class="card-body">
                  <form method="POST" action="">
                    <div class="form-group">
                      <label for="class">Select Class</label>
                      <select name="class" id="class" class="form-control" required>
                        <option value="">--Select Class--</option>
                        <?php
                        $query = "SELECT * FROM tblclass";
                        $result = $conn->query($query);
                        while ($row = $result->fetch_assoc()) {
                          echo '<option value="' . $row['Id'] . '">' . $row['className'] . '</option>';
                        }
                        ?>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="classArm">Select Class Arm</label>
                      <select name="classArm" id="classArm" class="form-control" required>
                        <option value="">--Select Class Arm--</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="to">To (Parent Email)</label>
                      <select name="to" id="to" class="form-control" required>
                        <option value="">--Select Parent Email--</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="from">From</label>
                      <select name="from" id="from" class="form-control" required>
                        <option value="">--Select Principal Email--</option>
                        <?php
                        $query = "SELECT emailAddress FROM tblprincipal";
                        $result = $conn->query($query);
                        while ($row = $result->fetch_assoc()) {
                          echo '<option value="' . $row['emailAddress'] . '">' . $row['emailAddress'] . '</option>';
                        }
                        ?>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="message">Message</label>
                      <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Send Email</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include "Includes/footer.php";?>
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
      
  <!-- Scripts -->
  <script>
    $(document).ready(function () {
      $('#class').change(function () {
        const classId = $(this).val();
        if (classId) {
          $.get('ajaxClassArms2.php?classId=' + classId, function (data) {
            $('#classArm').html(data);
          }).fail(function () {
            $('#classArm').html('<option value="">Error loading class arms</option>');
          });
        } else {
          $('#classArm').html('<option value="">--Select Class Arm--</option>');
        }
      });

      $('#classArm').change(function () {
        const classArmId = $(this).val();
        if (classArmId) {
          $.get('ajaxParentEmails.php?classArmId=' + classArmId, function (data) {
            $('#to').html(data);
          }).fail(function () {
            $('#to').html('<option value="">Error loading emails</option>');
          });
        } else {
          $('#to').html('<option value="">--Select Parent Email--</option>');
        }
      });
    });
  </script>
</body>
</html>
