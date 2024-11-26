<?php
session_start();
include('config.php');

// Check if doctor is logged in, if not, redirect to login page
if (!isset($_SESSION["doctor_id"]) || empty($_SESSION["doctor_id"])) {
    header("location: doctor_login.php");
    exit;
}

// Initialize variables
$success_msg = $error_msg = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_SESSION["doctor_id"];
    $visit_date = $_POST['visit_date'];
    $diagnosis = $_POST['diagnosis'];
    $prescription = $_POST['prescription'];
    $lab_tests = $_POST['lab_tests'];
    $notes = $_POST['notes'];

    // Insert medical record into the database
    $sql = "INSERT INTO patient_records (patient_id, doctor_id, visit_date, diagnosis, prescription, lab_tests, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iisssss", $patient_id, $doctor_id, $visit_date, $diagnosis, $prescription, $lab_tests, $notes);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Medical record added successfully.";
        header("location: view_appointments.php");
        
    } else {
        $error_msg = "Error adding medical record.";
    }
    mysqli_stmt_close($stmt);
}

// Get patient_id and appointment_id from URL parameters
$patient_id = $_GET['patient_id'];
$appointment_id = $_GET['appointment_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Medical Record</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2 class="text-center">Add Medical Record</h2>
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success" role="alert"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger" role="alert"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                <div class="form-group">
                    <label for="visit_date">Visit Date</label>
                    <input type="datetime-local" name="visit_date" class="form-control" min="<?php echo date('Y-m-d\TH:i'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="diagnosis">Diagnosis</label>
                    <textarea name="diagnosis" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="prescription">Prescription</label>
                    <textarea name="prescription" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="lab_tests">Lab Tests</label>
                    <textarea name="lab_tests" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Medical Record</button>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
