<?php
include '../Includes/dbcon.php';

$classId = intval($_GET['classId']); // Changed 'cid' to 'classId'

$query = "SELECT * FROM tblclassarms WHERE classId = $classId";
$result = $conn->query($query);

echo '<option value="">--Select Class Arm--</option>';
while ($row = $result->fetch_assoc()) {
    echo '<option value="' . $row['Id'] . '">' . $row['classArmName'] . '</option>';
}
?>
