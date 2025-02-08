<?php
include '../Includes/dbcon.php';

if (isset($_GET['classArmId'])) {
    $classArmId = $_GET['classArmId'];

    // Query to fetch parent emails based on classArmId
    $query = "SELECT parentsemailAddress FROM tblstudents WHERE classArmId = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $classArmId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<option value="">--Select Parent Email--</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['parentsemailAddress'] . '">' . $row['parentsemailAddress'] . '</option>';
        }
    } else {
        echo '<option value="">No parent emails found for this class arm</option>';
    }
}
?>
