<?php
session_start();
include('config.php');

// Check if doctor is logged in, if not, redirect to login page
if (!isset($_SESSION["doctor_id"]) || empty($_SESSION["doctor_id"])) {
    header("location: doctor_login.php");
    exit;
}

// Check if record_id is set
if (isset($_POST['record_id'])) {
    $record_id = $_POST['record_id'];

    // Fetch patient_id to redirect back to the correct page after deletion
    $sql = "SELECT patient_id FROM patient_records WHERE record_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $record_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $record = mysqli_fetch_assoc($result);
    $patient_id = $record['patient_id'];

    // Delete the medical record
    $sql = "DELETE FROM patient_records WHERE record_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $record_id);
    if (mysqli_stmt_execute($stmt)) {
        header("location: view_medical_records.php?patient_id=" . $patient_id);
        exit;
    } else {
        $error_msg = "Error deleting medical record.";
    }
}
?>
