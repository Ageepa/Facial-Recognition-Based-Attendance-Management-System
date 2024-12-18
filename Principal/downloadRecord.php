<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

if (isset($_POST['download'])) {
    // Capture the input values
    $classId = mysqli_real_escape_string($conn, $_POST['classId']);
    $classArmId = mysqli_real_escape_string($conn, $_POST['classArmId']);
    $dateTaken = mysqli_real_escape_string($conn, $_POST['dateTaken']);

    // Validate input fields
    if (!empty($classId) && !empty($classArmId) && !empty($dateTaken)) {
        // Fetch attendance data
        $query = "
            SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, 
                   tblclass.className, tblclassarms.classArmName, tblsessionterm.sessionName, 
                   tblterm.termName, tblstudents.firstName, tblstudents.lastName, 
                   tblstudents.otherName, tblstudents.admissionNumber
            FROM tblattendance
            INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
            INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
            INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
            INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
            INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
            WHERE tblattendance.dateTimeTaken = '$dateTaken' 
              AND tblattendance.classId = '$classId' 
              AND tblattendance.classArmId = '$classArmId'";

        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            // Generate Excel file
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=attendance_$dateTaken.xls");
            header("Pragma: no-cache");
            header("Expires: 0");

            // Output column headers
            echo "First Name\tLast Name\tOther Name\tAdmission No\tClass\tClass Arm\tSession\tTerm\tStatus\tDate\n";

            // Output rows
            while ($row = $result->fetch_assoc()) {
                $status = $row['status'] == '1' ? "Present" : "Absent";
                echo "{$row['firstName']}\t{$row['lastName']}\t{$row['otherName']}\t{$row['admissionNumber']}\t{$row['className']}\t{$row['classArmName']}\t{$row['sessionName']}\t{$row['termName']}\t$status\t{$row['dateTimeTaken']}\n";
            }
            exit;
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;' id='statusMsg' role='alert'>No Record Found!</div>";
            echo "<script>
                setTimeout(function() {
                    var msg = document.getElementById('statusMsg');
                    if (msg) {
                        msg.style.display = 'none';
                    }
                }, 3000);
            </script>";
        }
    } else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;' id='statusMsg' role='alert'>Please fill in all fields!</div>";
        echo "<script>
                setTimeout(function() {
                    var msg = document.getElementById('statusMsg');
                    if (msg) {
                        msg.style.display = 'none';
                    }
                }, 3000);
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
  <title>Download Attendance</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>


<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php";?>
        <!-- Topbar -->

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Download Class Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Download Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Download Attendance</h6>
                  <?php echo isset($statusMsg) ? $statusMsg : ''; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-4">
                        <label class="form-control-label">Select Class<span class="text-danger ml-2">*</span></label>
                        <select id="classDropdown" class="form-control" name="classId" required>
                          <option value="">Select Class</option>
                          <?php
                          $classQuery = "SELECT * FROM tblclass";
                          $classResult = $conn->query($classQuery);
                          while ($classRow = $classResult->fetch_assoc()) {
                            echo "<option value='" . $classRow['Id'] . "'>" . $classRow['className'] . "</option>";
                          }
                          ?>
                        </select>
                      </div>

                      <div class="col-xl-4">
                        <label class="form-control-label">Select Class Arm<span class="text-danger ml-2">*</span></label>
                        <select class="form-control" name="classArmId" id="classArmDropdown" required>
                          <option value="">--Select Class Arm--</option>
                        </select>
                      </div>

                      <div class="col-xl-4">
                        <label class="form-control-label">Select Date<span class="text-danger ml-2">*</span></label>
                        <input type="date" class="form-control" name="dateTaken" required>
                      </div>
                    </div>
                    <button type="submit" name="download" class="btn btn-primary">Download Attendance</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include "Includes/footer.php";?>
      <!-- Footer -->
    </div>
  </div>

  <!-- Scripts -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>

  <!-- AJAX Logic for Class Arm Dropdown -->
  <script>
    $(document).ready(function () {
      $('#classDropdown').on('change', function () {
        var classId = $(this).val(); // Get selected class ID
        if (classId) {
          $.ajax({
            url: './ajaxclassArms2.php', // File handling the class arm logic
            type: 'GET',
            data: { classId: classId }, // Pass the class ID to the backend
            success: function (data) {
              $('#classArmDropdown').html(data); // Update the dropdown with options
            },
            error: function () {
              alert('Failed to fetch class arms. Please try again.');
            }
          });
        } else {
          $('#classArmDropdown').html('<option value="">--Select Class Arm--</option>');
        }
      });
    });
  </script>

  
</body>
</html>

