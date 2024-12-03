<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE--------------------------------------------------

if (isset($_POST['save'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $otherName = $_POST['otherName'];
    $admissionNumber = $_POST['admissionNumber'];
    $classId = $_POST['classId'];
    $classArmId = $_POST['classArmId'];
    $studentImage1 = $_POST['studentImage1']; // Base64 string
    $studentImage2 = $_POST['studentImage2']; // Base64 string
    $dateCreated = date("Y-m-d");

    // Validate Admission Number
    $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE admissionNumber ='$admissionNumber'");
    $ret = mysqli_fetch_array($query);

    if ($ret > 0) { 
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;' id='statusMsg'>This Admission NO Already Exists!</div>";
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
        // Save images to the server
        $imagePath1 = saveImage($studentImage1, $admissionNumber . '_1');
        $imagePath2 = saveImage($studentImage2, $admissionNumber . '_2');

        if ($imagePath1 && $imagePath2) {
            // Insert data into the database
            $query = mysqli_query($conn, "INSERT INTO tblstudents 
                (firstName, lastName, otherName, admissionNumber, classId, classArmId, studentImage1, studentImage2, dateCreated) 
                VALUES ('$firstName', '$lastName', '$otherName', '$admissionNumber', '$classId', '$classArmId', '$imagePath1', '$imagePath2', '$dateCreated')");

            if ($query) {
                $statusMsg = "<div class='alert alert-success' style='margin-right:700px;' id='successMsg'>Created Successfully!</div>";
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
        else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;' id='errorMsg'>Failed to save images!</div>";
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

// Function to save base64 image to server
function saveImage($base64String, $admissionNumber) {
    if (!empty($base64String)) {
        // Extract the base64 string and the file extension
        list($type, $data) = explode(';', $base64String);
        list(, $data) = explode(',', $data);
        $extension = str_replace('data:image/', '', $type);

        // Create a unique file name
        $fileName = $admissionNumber . '.' . $extension;
        return $fileName;

        // Define the file path
        //$filePath = $folder . '/' . $fileName;

        // Create the folder if it doesn't exist
        /*if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        // Save the file to the server
        if (file_put_contents($filePath, base64_decode($data))) {
            return $filePath; // Return the file path
        } else {
            return false; // Return false if saving failed
        }*/
    }
    return false;
}



//--------------------EDIT------------------------------------------------------------

 if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit")
	{
        $Id= $_GET['Id'];

        $query=mysqli_query($conn,"select * from tblstudents where Id ='$Id'");
        $row=mysqli_fetch_array($query);

        //------------UPDATE-----------------------------

        if(isset($_POST['update'])){
    
          $firstName=$_POST['firstName'];
          $lastName=$_POST['lastName'];
          $otherName=$_POST['otherName'];

          $admissionNumber=$_POST['admissionNumber'];
          $classId=$_POST['classId'];
          $classArmId=$_POST['classArmId'];
          $dateCreated = date("Y-m-d");

 $query=mysqli_query($conn,"update tblstudents set firstName='$firstName', lastName='$lastName',
    otherName='$otherName', admissionNumber='$admissionNumber',classId='$classId',classArmId='$classArmId'
    where Id='$Id'");
            if ($query) {

                $statusMsg = "<div class='alert alert-success' style='margin-right:700px;' id='successMsg'>Updated Successfully!</div>";
                echo "<script> 
                    setTimeout(function() {
                        var msg = document.getElementById('successMsg');
                        if (msg) {
                            msg.style.display = 'none';
                        }
                        window.location = (\"createStudents.php\")
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

  if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete")
	{
        $Id= $_GET['Id'];
        $classArmId= $_GET['classArmId'];

        $query = mysqli_query($conn,"DELETE FROM tblstudents WHERE Id='$Id'");

        if ($query == TRUE) {

            $statusMsg = "<div class='alert alert-success' style='margin-right:700px;' id='successMsg'>Deleted Successfully!</div>";
            echo "<script> 
                setTimeout(function() {
                    var msg = document.getElementById('successMsg');
                    if (msg) {
                        msg.style.display = 'none';
                    }
                    window.location = (\"createStudents.php\")
                }, 3000);
            </script>";
        }

        else{

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
    <?php include 'includes/title.php';?>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">



    <script>
    function classArmDropdown(str) {
        if (str == "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        } else {
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "ajaxClassArms2.php?cid=" + str, true);
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
                        <h1 class="h3 mb-0 text-gray-800">Create Students</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create Students</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Form Basic -->
                            <div class="card mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Create Students</h6>
                                    <?php echo $statusMsg; ?>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Firstname<span
                                                        class="text-danger ml-2">*</span></label>
                                                <input type="text" class="form-control" name="firstName"
                                                    value="<?php echo $row['firstName'];?>" id="exampleInputFirstName">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Lastname<span
                                                        class="text-danger ml-2">*</span></label>
                                                <input type="text" class="form-control" name="lastName"
                                                    value="<?php echo $row['lastName'];?>" id="exampleInputFirstName">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Other Name</label>
                                                <input type="text" class="form-control" name="otherName"
                                                    value="<?php echo $row['otherName'];?>" id="exampleInputFirstName">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Admission Number<span
                                                        class="text-danger ml-2">*</span></label>
                                                <input type="text" class="form-control" required name="admissionNumber"
                                                    value="<?php echo $row['admissionNumber'];?>"
                                                    id="exampleInputFirstName">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Select Class<span
                                                        class="text-danger ml-2">*</span></label>
                                                <?php
                        $qry= "SELECT * FROM tblclass ORDER BY className ASC";
                        $result = $conn->query($qry);
                        $num = $result->num_rows;		
                        if ($num > 0){
                          echo ' <select required name="classId" onchange="classArmDropdown(this.value)" class="form-control mb-3">';
                          echo'<option value="">--Select Class--</option>';
                          while ($rows = $result->fetch_assoc()){
                          echo'<option value="'.$rows['Id'].'" >'.$rows['className'].'</option>';
                              }
                                  echo '</select>';
                              }
                            ?>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Class Arm<span
                                                        class="text-danger ml-2">*</span></label>
                                                <?php
                                echo"<div id='txtHint'></div>";
                            ?>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Student Image 1<span class="text-danger ml-2">*</span></label>
                                                <div class="image-box" id="imageBox1" onclick="captureImage(1)">
                                                    <video id="video1" autoplay></video>
                                                    <canvas id="canvas1" style="display: none;"></canvas>
                                                </div>
                                                <input type="hidden" name="studentImage1" id="studentImage1">
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Student Image 2<span class="text-danger ml-2">*</span></label>
                                                <div class="image-box" id="imageBox2" onclick="captureImage(2)">
                                                    <video id="video2" autoplay></video>
                                                    <canvas id="canvas2" style="display: none;"></canvas>
                                                </div>
                                                <input type="hidden" name="studentImage2" id="studentImage2">
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

                            <!-- Input Group -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card mb-4">
                                        <div
                                            class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                            <h6 class="m-0 font-weight-bold text-primary">All Student</h6>
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
                                                        <th>Date Created</th>
                                                        <th>Edit</th>
                                                        <th>Delete</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                    <?php
                      $query = "SELECT tblstudents.Id,tblclass.className,tblclassarms.classArmName,tblclassarms.Id AS classArmId,tblstudents.firstName,
                      tblstudents.lastName,tblstudents.otherName,tblstudents.admissionNumber,tblstudents.dateCreated
                      FROM tblstudents
                      INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
                      INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId";
                      $rs = $conn->query($query);
                      $num = $rs->num_rows;
                      $sn=0;
                      $status="";
                      if($num > 0)
                      { 
                        while ($rows = $rs->fetch_assoc())
                          {
                             $sn = $sn + 1;
                            echo"
                              <tr>
                                <td>".$sn."</td>
                                <td>".$rows['firstName']."</td>
                                <td>".$rows['lastName']."</td>
                                <td>".$rows['otherName']."</td>
                                <td>".$rows['admissionNumber']."</td>
                                <td>".$rows['className']."</td>
                                <td>".$rows['classArmName']."</td>
                                 <td>".$rows['dateCreated']."</td>
                                <td><a href='?action=edit&Id=".$rows['Id']."'><i class='fas fa-fw fa-edit'></i></a></td>
                                <td><a href='?action=delete&Id=".$rows['Id']."'><i class='fas fa-fw fa-trash'></i></a></td>
                              </tr>";
                          }
                      }
                      else
                      {
                           echo   
                           "<div class='alert alert-danger' role='alert'>
                            No Record Found!
                            </div>";
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
                <!-- Footer -->
                <?php include "Includes/footer.php";?>
                <!-- Footer -->
            </div>
        </div>

        <!-- Scroll to top -->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Add some CSS to style the image preview boxes -->
<style>
   .image-box {
    width: 200px;
    height: 200px;
    border: 2px dashed #ccc;
    margin-bottom: 15px;
    background-color: #f9f9f9;
    cursor: pointer;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-box:hover {
    border-color: #007bff;
}

video {
    width: 200px;
    height: 200px;
    object-fit: cover;
    position: absolute;
    z-index: 10;
}

canvas {
    display: none;
}
</style>


<script>
    let currentBox = null;

function captureImage(boxNumber) {
    const video = document.getElementById(`video${boxNumber}`);
    const canvas = document.getElementById(`canvas${boxNumber}`);
    const imageBox = document.getElementById(`imageBox${boxNumber}`);

    currentBox = boxNumber;

    // Start the video stream
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.style.display = "block";
            video.srcObject = stream;

            // Wait for the video to be ready
            video.onloadedmetadata = () => {
                // Adjust canvas size to match the video
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Capture image after 3 seconds
                setTimeout(() => {
                    const context = canvas.getContext("2d");
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const imageData = canvas.toDataURL("image/png");

                    // Set the captured image as the background
                    imageBox.style.backgroundImage = `url(${imageData})`;
                    video.style.display = "none";

                    // Stop the webcam
                    stream.getTracks().forEach(track => track.stop());

                    // Store the image data in the hidden input field
                    document.getElementById(`studentImage${boxNumber}`).value = imageData;
                }, 3000);
            };
        })
        .catch(err => {
            alert("Unable to access webcam.");
            console.error(err);
        });
}

</script>



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