<?php
session_start();
include('config.php');

// Check if doctor is logged in, if not, redirect to login page
if (!isset($_SESSION["doctor_id"]) || empty($_SESSION["doctor_id"])) {
    header("location: doctor_login.php");
    exit;
}

// Get record_id from URL parameter
$record_id = $_GET['record_id'];

// Fetch the medical record details
$sql = "SELECT * FROM patient_records WHERE record_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $record_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$record = mysqli_fetch_assoc($result);

// Check if form is submitted for updating the record
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $visit_date = $_POST['visit_date'];
    $diagnosis = $_POST['diagnosis'];
    $prescription = $_POST['prescription'];
    $lab_tests = $_POST['lab_tests'];
    $notes = $_POST['notes'];

    // Update the medical record in the database
    $sql = "UPDATE patient_records SET visit_date = ?, diagnosis = ?, prescription = ?, lab_tests = ?, notes = ?, updated_at = NOW() WHERE record_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $visit_date, $diagnosis, $prescription, $lab_tests, $notes, $record_id);
    if (mysqli_stmt_execute($stmt)) {
        header("location: view_medical_records.php?patient_id=" . $record['patient_id']);
        exit;
    } else {
        $error_msg = "Error updating medical record.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Medical Record</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
<div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">

    <h2 class="text-center">Edit Medical Record</h2>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger" role="alert"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?record_id=' . $record_id; ?>" method="post">
        <div class="form-group">
            <label for="visit_date">Visit Date:</label>
            <input type="date" name="visit_date" class="form-control" value="<?php echo htmlspecialchars($record['visit_date']); ?>" required>
        </div>
        <div class="form-group">
            <label for="diagnosis">Diagnosis:</label>
            <input type="text" name="diagnosis" class="form-control" value="<?php echo htmlspecialchars($record['diagnosis']); ?>" required>
        </div>
        <div class="form-group">
            <label for="prescription">Prescription:</label>
            <input type="text" name="prescription" class="form-control" value="<?php echo htmlspecialchars($record['prescription']); ?>" required>
        </div>
        <div class="form-group">
            <label for="lab_tests">Lab Tests:</label>
            <input type="text" name="lab_tests" class="form-control" value="<?php echo htmlspecialchars($record['lab_tests']); ?>" required>
        </div>
        <div class="form-group">
            <label for="notes">Notes:</label>
            <textarea name="notes" class="form-control" required><?php echo htmlspecialchars($record['notes']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Record</button>
    </form>
</div>
</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
