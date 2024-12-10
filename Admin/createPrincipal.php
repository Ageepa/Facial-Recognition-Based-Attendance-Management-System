<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE--------------------------------------------------

if(isset($_POST['save'])){
    
  $firstName = $_POST['firstName'];
  $lastName = $_POST['lastName'];
  $position = $_POST['position'];
  $emailAddress = $_POST['emailAddress'];
  $phoneNo = $_POST['phoneNo'];
  $dateCreated = date("Y-m-d");

  $sampPass = "Principal123"; // Common password
  $sampPass_2 = md5($sampPass);

  $query = mysqli_query($conn, "SELECT * FROM tblprincipal WHERE emailAddress ='$emailAddress'");
  $ret = mysqli_fetch_array($query);

  if($ret > 0){ 

      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;' id='statusMsg'>This Email Address Already Exists!</div>";
      echo "<script>
      setTimeout(function() {
          var msg = document.getElementById('statusMsg');
          if (msg) {
              msg.style.display = 'none';
          }
      }, 3000);
    </script>";


  } 
    else {
      
      $query = mysqli_query($conn, "INSERT INTO tblprincipal(firstName, lastName, position, emailAddress, password, phoneNo, dateCreated) 
      VALUE('$firstName','$lastName','$position','$emailAddress','$sampPass_2','$phoneNo','$dateCreated')");

    if ($query) {

        $statusMsg = "<div class='alert alert-success'  style='margin-left:700px;' id='successMsg'>Created Successfully!</div>";
        echo "<script>
        setTimeout(function() {
            var msg = document.getElementById('successMsg');
            if (msg) {
                msg.style.display = 'none';
            }
        }, 3000);
      </script>";

    } 

    else {

        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;' id='errorMsg'>An error occurred while saving the student data!</div>";
        echo "<script>
            setTimeout(function() {
                var msg = document.getElementById('errorMsg');
                if (msg) {
                    msg.style.display = 'none';
                }
            }, 3000);
        </script>";

    }
  }
}

//---------------------------------------EDIT-------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];
    $query = mysqli_query($conn, "SELECT * FROM tblprincipal WHERE Id ='$Id'");
    $row = mysqli_fetch_array($query);

    if(isset($_POST['update'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $position = $_POST['position'];
        $emailAddress = $_POST['emailAddress'];
        $phoneNo = $_POST['phoneNo'];

        $query = mysqli_query($conn, "UPDATE tblprincipal SET firstName='$firstName', lastName='$lastName', position='$position', emailAddress='$emailAddress', phoneNo='$phoneNo' WHERE Id='$Id'");

        if ($query) {

            $statusMsg = "<div class='alert alert-success' style='margin-right:700px;' id='successMsg'>Updated Successfully!</div>";
                echo "<script> 
                    setTimeout(function() {
                        var msg = document.getElementById('successMsg');
                        if (msg) {
                            msg.style.display = 'none';
                        }
                        window.location = (\"createPrincipal.php\")
                    }, 3000);
                </script>";

        }
        else
        {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;' id='statusMsg'>An error Occurred!</div>";
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
}

//--------------------------------DELETE------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];
    $query = mysqli_query($conn, "DELETE FROM tblprincipal WHERE Id='$Id'");

    if ($query == TRUE) {
        echo "<script type = \"text/javascript\">
        window.location = (\"createPrincipal.php\")
        </script>"; 
    } else {
        $statusMsg = "<div class='alert alert-danger' style='center:700px;'>An error Occurred!</div>"; 
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Create Principal</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include "Includes/sidebar.php";?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "Includes/topbar.php";?>

             <!-- Container Fluid-->
             <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Create Principal</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create Principal</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Create Principal / Vice Principal</h6>
                                    <?php echo $statusMsg; ?>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Firstname<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" required name="firstName" value="<?php echo $row['firstName']; ?>">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Lastname<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" required name="lastName" value="<?php echo $row['lastName']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Position(indicate whether it is principal or vice principal)<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" required name="position" value="<?php echo $row['position']; ?>">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Email Address<span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" required name="emailAddress" value="<?php echo $row['emailAddress']; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Phone No<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" required name="phoneNo" value="<?php echo $row['phoneNo']; ?>">
                                            </div>
                                        </div>
                                        <?php
                    if (isset($Id))
                    {
                    ?>
                                        <button type="submit" name="update" class="btn btn-warning">Update</button>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <?php
                    } else {              
                    ?>
                                        <button type="submit" name="save"
                                            class="btn btn-primary">Save</button>&nbsp;&nbsp;
                                        <?php
                    }         
                    ?>
                                    </form>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">All Principals</h6>
                                </div>
                                <div class="table-responsive p-3">
                                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Position</th>
                                                <th>Email Address</th>
                                                <th>Phone No</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT * FROM tblprincipal";
                                            $rs = $conn->query($query);
                                            $num = $rs->num_rows;
                                            $sn = 0;
                                            if($num > 0) { 
                                                while ($rows = $rs->fetch_assoc()) {
                                                    $sn++;
                                                    echo "<tr>
                                                        <td>{$sn}</td>
                                                        <td>{$rows['firstName']}</td>
                                                        <td>{$rows['lastName']}</td>
                                                        <td>{$rows['position']}</td>
                                                        <td>{$rows['emailAddress']}</td>
                                                        <td>{$rows['phoneNo']}</td>
                                                        <td><a href='?action=edit&Id={$rows['Id']}'><i class='fas fa-fw fa-edit'></i></a></td>
                                                        <td><a href='?action=delete&Id={$rows['Id']}'><i class='fas fa-fw fa-trash'></i></a></td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<div class='alert alert-danger' style='margin-right:700px;'>No Record Found!</div>";
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

            <?php include "Includes/footer.php";?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable(); // ID From dataTable 
            $('#dataTableHover').DataTable(); // ID From dataTable with Hover
        });
    </script>
</body>
</html>
