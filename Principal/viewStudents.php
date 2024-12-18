<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get classId and classArmId from the query string
$classId = intval($_GET['classId']);
$classArmId = intval($_GET['classArmId']);

// Fetch student records based on the selected class and class arm
$query = "
    SELECT tblstudents.Id, tblclass.className, tblclassarms.classArmName, tblstudents.firstName, 
           tblstudents.lastName, tblstudents.otherName, tblstudents.admissionNumber 
    FROM tblstudents 
    INNER JOIN tblclass ON tblclass.Id = tblstudents.classId 
    INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId 
    WHERE tblstudents.classId = $classId AND tblstudents.classArmId = $classArmId";

$result = $conn->query($query);
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

    function viewStudents() {
        const classId = document.getElementById('classDropdown').value;
        const classArmId = document.getElementById('classArmDropdown').value;

        if (classId && classArmId) {
            window.location.href = `viewStudents.php?classId=${classId}&classArmId=${classArmId}`;
        } else {
            alert("Please select both Class and Class Arm.");
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
                        <h1 class="h3 mb-0 text-gray-800">All Students in Classes</h1>

                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">All Students in Class</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Form Basic -->

                            <!-- Dropdowns for Classes and Class Arms -->
                            <div class="row mb-4">
                                <div class="col-md-5">
                                    <select id="classDropdown" class="form-control" onchange="loadClassArms(this.value)">
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
                                <div class="col-md-5">
                                    <select id="classArmDropdown" class="form-control">
                                        <option value="">Select Class Arm</option>
                                    </select>
                                </div>
                                
                            </div></br>

                            <div class="col-md-2">
                                    <button class="btn btn-primary" onclick="viewStudents()">View Students</button>
                            </div></br>

                            <!-- Input Group -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card mb-4">
                                        <div
                                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                            <h6 class="m-0 font-weight-bold text-primary">All Students in Class</h6>
                                        </div>
                                        <div class="table-responsive p-3">
                                            <table class="table align-items-center table-flush table-hover"
                                                id="dataTableHover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>First Name</th>
                                                        <th>Last Name</th>
                                                        <th>Other Name</th>
                                                        <th>Admission No</th>
                                                        <th>Class</th>
                                                        <th>Class Arm</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                    if ($result->num_rows > 0) {
                                                        $sn = 0;
                                                        while ($row = $result->fetch_assoc()) {
                                                            $sn++;
                                                            echo "
                                                            <tr>
                                                                <td>$sn</td>
                                                                <td>{$row['firstName']}</td>
                                                                <td>{$row['lastName']}</td>
                                                                <td>{$row['otherName']}</td>
                                                                <td>{$row['admissionNumber']}</td>
                                                                <td>{$row['className']}</td>
                                                                <td>{$row['classArmName']}</td>
                                                            </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='7' class='text-center'>No Records Found!</td></tr>";
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
                    <!---Container Fluid-->
                </div>
            </div>
            <?php include "Includes/footer.php";?>
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
        $(document).ready(function() {
            $('#dataTable').DataTable(); // ID From dataTable 
            $('#dataTableHover').DataTable(); // ID From dataTable with Hover
        });
        </script>
</body>

</html>