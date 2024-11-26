<?php
session_start();
include('config.php');

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if appointment_id is set
if (isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];

    // Delete the appointment from the database
    $sql = "DELETE FROM appointments WHERE appointment_id = ? AND patient_id = ? AND status = 'pending'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $_SESSION["id"]);
    if (mysqli_stmt_execute($stmt)) {
        header("location: my_appointments.php");
        exit;
    } else {
        $error_msg = "Error deleting appointment.";
    }
}
?>
