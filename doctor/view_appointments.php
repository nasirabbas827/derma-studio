<?php
session_start();
include('config.php');

// Check if the doctor is logged in, if not, redirect to the login page
if (!isset($_SESSION["doctor_id"]) || empty($_SESSION["doctor_id"])) {
    header("location: doctor_login.php");
    exit;
}

// Initialize variables
$success_msg = $error_msg = "";
$search_patient_name = $from_date = $to_date = "";

// Check if form is submitted for updating status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['status'];

    // Update appointment status in the database
    $sql = "UPDATE appointments SET status = ? WHERE appointment_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $appointment_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Appointment status updated successfully.";
    } else {
        $error_msg = "Error updating appointment status.";
    }
    mysqli_stmt_close($stmt);
}

// Check if form is submitted for updating date
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_date'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_date = $_POST['appointment_date'];

    // Update appointment date in the database
    $sql = "UPDATE appointments SET appointment_date = ? WHERE appointment_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_date, $appointment_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Appointment date updated successfully.";
    } else {
        $error_msg = "Error updating appointment date.";
    }
    mysqli_stmt_close($stmt);
}

// Fetch doctor's appointments with patient details, considering search filters
$doctor_id = $_SESSION["doctor_id"];

// Build the SQL query with search filters
$sql = "SELECT appointments.appointment_id, appointments.appointment_date, appointments.status, users.username AS patient_name, appointments.patient_id 
        FROM appointments 
        INNER JOIN users ON appointments.patient_id = users.id
        WHERE doctor_id = ?";

// Add filters for search by patient name and date range
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET['search_patient_name'])) {
        $search_patient_name = $_GET['search_patient_name'];
        $sql .= " AND users.username LIKE ?";
    }
    if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
        $from_date = $_GET['from_date'];
        $to_date = $_GET['to_date'];
        $sql .= " AND appointments.appointment_date BETWEEN ? AND ?";
    }
}

$stmt = mysqli_prepare($conn, $sql);

// Bind parameters dynamically based on filters
if (!empty($search_patient_name) && !empty($from_date) && !empty($to_date)) {
    $search_patient_name = "%" . $search_patient_name . "%";
    mysqli_stmt_bind_param($stmt, "isss", $doctor_id, $search_patient_name, $from_date, $to_date);
} elseif (!empty($search_patient_name)) {
    $search_patient_name = "%" . $search_patient_name . "%";
    mysqli_stmt_bind_param($stmt, "is", $doctor_id, $search_patient_name);
} elseif (!empty($from_date) && !empty($to_date)) {
    mysqli_stmt_bind_param($stmt, "iss", $doctor_id, $from_date, $to_date);
} else {
    mysqli_stmt_bind_param($stmt, "i", $doctor_id);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">My Appointments</h2>
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success" role="alert"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger" role="alert"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- Search Form -->
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form-inline mb-3">
        <div class="form-group mx-sm-2">
            <input type="text" name="search_patient_name" class="form-control" placeholder="Search by patient name" value="<?php echo htmlspecialchars($search_patient_name); ?>">
        </div>
        <div class="form-group mx-sm-2">
            <input type="date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($from_date); ?>">
        </div>
        <div class="form-group mx-sm-2">
            <input type="date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($to_date); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Appointment Table -->
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Patient</th>
                        <th>Previous Status</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                            <td>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                    <select name="status" class="form-control" required>
                                        <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $row['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="completed" <?php echo $row['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $row['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary btn-sm mt-2">Update Status</button>
                                </form>
                            </td>
                            <td>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                    <input type="datetime-local" name="appointment_date" class="form-control" 
                                           value="<?php echo date('Y-m-d\TH:i', strtotime($row['appointment_date'])); ?>" 
                                           min="<?php echo date('Y-m-d\TH:i'); ?>" required>
                                    <button type="submit" name="update_date" class="btn btn-primary btn-sm mt-2">Update Date</button>
                                </form>
                                <a href="add_medical_record.php?patient_id=<?php echo $row['patient_id']; ?>&appointment_id=<?php echo $row['appointment_id']; ?>" class="btn btn-secondary btn-sm mt-2">Add Medical Record</a>
                                <a href="view_medical_records.php?patient_id=<?php echo $row['patient_id']; ?>" class="btn btn-info btn-sm mt-2">View Medical Records</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
