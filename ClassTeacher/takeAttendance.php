<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';


$query = "SELECT tblclass.className, tblclassarms.classArmName 
FROM tblclassteacher
INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId
INNER JOIN tblclassarms ON tblclassarms.Id = tblclassteacher.classArmId
WHERE tblclassteacher.Id = '$_SESSION[userId]'";
$rs = $conn->query($query);
$rrw = $rs->fetch_assoc();

// Session and Term
$querey = mysqli_query($conn,"select * from tblsessionterm where isActive ='1'");
$rwws = mysqli_fetch_array($querey);
$sessionTermId = $rwws['Id'];

$dateTaken = date("Y-m-d");
$qurty = mysqli_query($conn,"select * from tblattendance where classId = '$_SESSION[classId]' and classArmId = '$_SESSION[classArmId]' and dateTimeTaken='$dateTaken' AND status = '1'");
$count = mysqli_num_rows($qurty);
$attendanceTaken = ($count > 0) ? 'true' : 'false'; // Boolean as a string for JavaScript

if ($count == 0) {
    $qus = mysqli_query($conn, "SELECT * FROM tblstudents WHERE classId = '$_SESSION[classId]' AND classArmId = '$_SESSION[classArmId]'");
    while ($ros = $qus->fetch_assoc()) {
        $existingCheck = mysqli_query($conn, "SELECT * FROM tblattendance WHERE admissionNo = '$ros[admissionNumber]' AND classId = '$_SESSION[classId]' AND classArmId = '$_SESSION[classArmId]' AND dateTimeTaken = '$dateTaken'");
        if (mysqli_num_rows($existingCheck) == 0) {
            mysqli_query($conn, "INSERT INTO tblattendance (admissionNo, classId, classArmId, sessionTermId, status, dateTimeTaken) 
                                 VALUES ('$ros[admissionNumber]', '$_SESSION[classId]', '$_SESSION[classArmId]', '$sessionTermId', '0', '$dateTaken')");
        }
    }
}


if (isset($_POST['save'])) {
    $admissionNo = $_POST['admissionNo'];
    $check = $_POST['check'];
    $N = count($admissionNo);
    $status = "";

    $qurty = mysqli_query($conn,"select * from tblattendance where classId = '$_SESSION[classId]' and classArmId = '$_SESSION[classArmId]' and dateTimeTaken='$dateTaken' and status = '1'");
    $count = mysqli_num_rows($qurty);

    if ($count > 0) {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;' id='statusMsg'>Attendance has been taken for today!</div>";
        echo "<script>
                setTimeout(function() {
                    var msg = document.getElementById('statusMsg');
                    if (msg) {
                        msg.style.display = 'none';
                    }
                }, 3000);
            </script>";
    } else { 
        for ($i = 0; $i < $N; $i++) {
            $admissionNo[$i];
            if (isset($check[$i])) { 
                mysqli_query($conn,"update tblattendance set status='1' where admissionNo = '$check[$i]'");
            }
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admissionNo']) && isset($_POST['status'])) {
    $admissionNo = $_POST['admissionNo'];
    $status = $_POST['status'];

    $query = "UPDATE tblattendance SET status = ? WHERE admissionNo = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $status, $admissionNo);

    if ($stmt->execute()) {
        echo "Attendance updated successfully!";
    } else {
        echo "Error updating attendance: " . $stmt->error;
    }

    $stmt->close();
    exit; // Prevent further output
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
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <script src="face-api.min.js"></script>
  <link href="css/ruang-admin.min.css" rel="stylesheet">

<style>

    canvas {
        position: absolute;
        
    }

    .video-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #video{
        border-radius:10px;
        box-shadow:#000;
    }


</style>

</head>
<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php"; ?>
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Today's Attendance (<?php echo date("m-d-Y"); ?>)</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">All Students</li>
            </ol>
          </div>

        <div id="messageDiv" style="display: none; margin-bottom: 20px;">
            <div class="alert alert-info" role="alert" id="messageContent"></div>
        </div>

          <!-- Dynamic Video Container -->
          <div class="video-container" style="display:none;">
            <video  id="video" width="600" height="450" autoplay></video>
            <canvas id="overlay"></canvas>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">All Students in Class (<?php echo $rrw['className']; ?> - <?php echo $rrw['classArmName']; ?>)</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table align-items-center table-hover" id="table_id">
                      <thead class="thead-light">
                        <tr>
                          <th>#</th>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Other Name</th>
                          <th>Admission No</th>
                          <th>Class</th>
                          <th>Class Arm</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $qus = mysqli_query($conn,"select * from tblstudents where classId = '$_SESSION[classId]' and classArmId = '$_SESSION[classArmId]'");
                        $count = 1;
                        while ($row = mysqli_fetch_array($qus)) {
                          echo "<tr>";
                          echo "<td>".$count."</td>";
                          echo "<td>".$row['firstName']."</td>";
                          echo "<td>".$row['lastName']."</td>";
                          echo "<td>".$row['otherName']."</td>";
                          echo "<td>".$row['admissionNumber']."</td>";
                          echo "<td>".$rrw['className']."</td>";
                          echo "<td>".$rrw['classArmName']."</td>";
                          echo "<td id='status-".$row['admissionNumber']."'>Absent</td>";
                          echo "</tr>";
                          $count++;
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <button type="button" class="btn btn-primary mt-3" id="startButton">Take Attendance</button>
            </div>
          </div>
        </div>
      </div>
      <?php include "Includes/footer.php"; ?>
    </div>
  </div>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
 
  <script>

    const video = document.getElementById("video");
    const videoContainer = document.querySelector(".video-container");
    const startButton = document.getElementById("startButton");
    const messageDiv = document.getElementById("messageDiv");
    const messageContent = document.getElementById("messageContent");
    
    let webcamStarted = false;
    let modelsLoaded = false;
    let attendanceTaken = <?php echo $attendanceTaken; ?>;
    let videoStream = null;

    console.log("faceapi object:", faceapi);

    // Load models
    Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri("http://localhost/models"),
        faceapi.nets.faceRecognitionNet.loadFromUri("http://localhost/models"),
        faceapi.nets.faceLandmark68Net.loadFromUri("http://localhost/models"),
    ]).then(() => {
        modelsLoaded = true;
    });

    // Function to display messages
    function displayMessage(message, type = "info") {
        messageDiv.style.display = "block";
        messageContent.className = `alert alert-${type}`;
        messageContent.textContent = message;

        // Automatically hide the message after 3 seconds
        setTimeout(() => {
            messageDiv.style.display = "none";
        }, 3000);
    }


    startButton.addEventListener("click", async () => {
        if (attendanceTaken) {
            displayMessage("Attendance has already been taken for today.", "warning");
            return;
        }
        videoContainer.style.display = "flex";
        if (!webcamStarted && modelsLoaded) {
            await startWebcam();
            webcamStarted = true;
        }
    });


    async function startWebcam() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: false,
            });
            video.srcObject = stream;
            videoStream = stream;
        } catch (error) {
            console.error("Error starting webcam:", error);
        }
    }

    async function loadLabeledImages() {
        const labels = [<?php 
            $labels = [];
            $dir = scandir('./labels');
            foreach ($dir as $file) {
                if (strpos($file, '.png') !== false) {
                    $labels[] = '"' . explode('_', $file)[0] . '"';
                }
            }
            echo implode(',', $labels);
        ?>];

        return Promise.all(
            labels.map(async (label) => {
                const descriptions = [];
                const img = await faceapi.fetchImage(`./labels/${label}_1.png`);
                const detection = await faceapi
                    .detectSingleFace(img)
                    .withFaceLandmarks()
                    .withFaceDescriptor();
                descriptions.push(detection.descriptor);
                return new faceapi.LabeledFaceDescriptors(label, descriptions);
            })
        );
    }

    video.addEventListener("play", async () => {
        const labeledDescriptors = await loadLabeledImages();
        const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);

        const canvas = faceapi.createCanvasFromMedia(video);
        videoContainer.appendChild(canvas);

        const displaySize = { width: video.width, height: video.height };
        faceapi.matchDimensions(canvas, displaySize);

        const context = canvas.getContext("2d");

        setInterval(async () => {
            try {
                const detections = await faceapi
                    .detectAllFaces(video)
                    .withFaceLandmarks()
                    .withFaceDescriptors();

                const resizedDetections = faceapi.resizeResults(detections, displaySize);

                context.clearRect(0, 0, canvas.width, canvas.height);

                resizedDetections.forEach((detection) => {
                    const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
                    if (bestMatch.label !== "unknown") {
                        const studentId = bestMatch.label.split("_")[0];
                        const box = detection.detection.box;
                        const confidence = (1 - bestMatch.distance).toFixed(2); // Convert distance to confidence (0 to 1 scale)
                        
                        context.strokeStyle = "blue";
                        context.lineWidth = 2;
                        context.strokeRect(box.x, box.y, box.width, box.height);
                        
                        context.fillStyle = "blue";
                        context.font = "16px Arial";
                        context.fillText(`${studentId} (${confidence})`, box.x, box.y - 10);

                        // Update UI and database
                        document.getElementById(`status-${studentId}`).innerText = "Present";
                        updateAttendanceInDatabase(studentId);
                    }
                });
            } catch (error) {
                console.error("Error detecting faces:", error);
            }
        }, 100);
    });

    function updateAttendanceInDatabase(admissionNo) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "", true); // POST request to the same script
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Send admission number and status to the backend
        xhr.send(`admissionNo=${admissionNo}&status=1`);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                console.log(`Attendance updated for ${admissionNo}: ${xhr.responseText}`);
            }
        };
    }

    function stopWebcam() {
    // Stop the video stream
    if (videoStream) {
        const tracks = videoStream.getTracks();
        tracks.forEach((track) => track.stop());
        video.srcObject = null;
        videoStream = null;
    }

    // Hide the video container
    videoContainer.style.display = "none";
    attendanceTaken = true;
    displayMessage("Attendance Has Been Taken Successfully", "success");

    // Show the student table (card-body)
    const studentTableContainer = document.querySelector(".card-body"); // Adjust selector if necessary
    studentTableContainer.style.display = "block"; // Make it visible again
}

// Listen for the "q" key to stop the webcam and restore the student table
document.addEventListener("keydown", (event) => {
    if (event.key === "q") {
        stopWebcam();
    }
});

</script>



</body>
</html>