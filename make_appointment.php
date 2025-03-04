<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if doctor_id is provided in the query string
if (!isset($_GET['doctor_id'])) {
    header("location: index.php");
    exit;
}

// Get doctor_id from the query string
$doctor_id = $_GET['doctor_id'];

// Fetch doctor's information
$sql = "SELECT full_name FROM doctors WHERE doctor_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $doctor_name);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate appointment date
    $appointment_date = $_POST['appointment_date'];

    // Insert appointment into the database
    $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, created_at, updated_at) VALUES (?, ?, ?, 'pending', NOW(), NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $_SESSION["id"], $doctor_id, $appointment_date);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo '<div class="alert alert-success" role="alert">Appointment requested successfully.</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Failed to request appointment. Please try again later.</div>';
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Make Appointment</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
<div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">

    <h2 class="text-center">Make Appointment with <?php echo $doctor_name; ?></h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?doctor_id=<?php echo $doctor_id; ?>" method="post">
                <div class="form-group">
                    <label for="appointment_date">Select Appointment Date:</label>
                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" required min="<?= date('Y-m-d'); ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Request Appointment</button>
            </form>
        </div>
    </div>
</div>
</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
