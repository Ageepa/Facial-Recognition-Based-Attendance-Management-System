<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';
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
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">


  <script>
    function loadClassArms(classId) {
        if (classId == "") {
            document.getElementById("classArmDropdown").innerHTML = "<option value=''>Select Class Arm</option>";
            return;
        } else {
            if (window.XMLHttpRequest) {
                xmlhttp = new XMLHttpRequest();
            } else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("classArmDropdown").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "ajaxClassArms2.php?classId=" + classId, true);
            xmlhttp.send();
        }
    }
  </script>
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
            <h1 class="h3 mb-0 text-gray-800">View Class Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Class Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">View Class Attendance</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-4">
                        <label class="form-control-label">Select Class<span class="text-danger ml-2">*</span></label>
                        <select id="classDropdown" class="form-control" name="classId" onchange="loadClassArms(this.value)">
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
                          <option value="">Select Class Arm</option>
                        </select>
                      </div>

                      <div class="col-xl-4">
                        <label class="form-control-label">Select Date<span class="text-danger ml-2">*</span></label>
                        <input type="date" class="form-control" name="dateTaken" required>
                      </div>
                    </div>
                    <button type="submit" name="view" class="btn btn-primary">View Attendance</button>
                  </form>
                </div>
              </div>

              <!-- Input Group -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">Class Attendance</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Other Name</th>
                            <th>Admission No</th>
                            <th>Class</th>
                            <th>Class Arm</th>
                            <th>Session</th>
                            <th>Term</th>
                            <th>Status</th>
                            <th>Date</th>
                          </tr>
                        </thead>

                        <tbody>
                          <?php
                          if (isset($_POST['view'])) {
                            $classId = mysqli_real_escape_string($conn, $_POST['classId']);
                            $classArmId = mysqli_real_escape_string($conn, $_POST['classArmId']);
                            $dateTaken = mysqli_real_escape_string($conn, $_POST['dateTaken']);
                        
                            if (!empty($classId) && !empty($classArmId) && !empty($dateTaken)) {
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
                        
                                $rs = $conn->query($query);
                                $num = $rs->num_rows;
                                $sn = 0;
                        
                                if ($num > 0) {
                                    while ($rows = $rs->fetch_assoc()) {
                                        $status = $rows['status'] == '1' ? "Present" : "Absent";
                                        $colour = $rows['status'] == '1' ? "#00FF00" : "#FF0000";
                        
                                        $sn++;
                                        echo "<tr>
                                              <td>$sn</td>
                                              <td>{$rows['firstName']}</td>
                                              <td>{$rows['lastName']}</td>
                                              <td>{$rows['otherName']}</td>
                                              <td>{$rows['admissionNumber']}</td>
                                              <td>{$rows['className']}</td>
                                              <td>{$rows['classArmName']}</td>
                                              <td>{$rows['sessionName']}</td>
                                              <td>{$rows['termName']}</td>
                                              <td style='background-color:$colour'>$status</td>
                                              <td>{$rows['dateTimeTaken']}</td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<div class='alert alert-danger' role='alert'>No Record Found!</div>";
                                }
                            } else {
                                echo "<div class='alert alert-danger' role='alert'>Please fill in all fields!</div>";
                            }
                        }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
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

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <!-- Page level plugins -->
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>
</body>

</html>
