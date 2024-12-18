<?php
include '../Includes/dbcon.php';

if (isset($_GET['classArmId'])) {
    $classArmId = $_GET['classArmId'];

    // Prepare the SQL query to fetch students based on the selected class arm
    $query = "SELECT admissionNumber, CONCAT(firstName, ' ', lastName) AS fullName 
              FROM tblstudents 
              WHERE classArmId = ? 
              ORDER BY firstName ASC";

    // Use prepared statements to prevent SQL injection
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $classArmId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any students are found
        if ($result->num_rows > 0) {
            echo "<option value=''>--Select Student--</option>";
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['admissionNumber'] . "'>" . htmlspecialchars($row['fullName']) . "</option>";
            }
        } else {
            echo "<option value=''>No Students Found</option>";
        }

        $stmt->close();
    } else {
        echo "<option value=''>Error Fetching Data</option>";
    }
}

$conn->close();
?>
