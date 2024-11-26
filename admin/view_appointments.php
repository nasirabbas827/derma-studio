<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Initialize search variables
$search_patient_name = $from_date = $to_date = "";

// Build the SQL query with optional search filters
$sql = "SELECT appointments.appointment_id, appointments.appointment_date, appointments.status, 
                appointments.created_at, appointments.updated_at, 
                doctors.full_name AS doctor_name, users.username AS patient_name
        FROM appointments
        INNER JOIN doctors ON appointments.doctor_id = doctors.doctor_id
        INNER JOIN users ON appointments.patient_id = users.id
        WHERE 1=1"; // Use "1=1" to easily append additional conditions

// Apply search filters
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

// Prepare and execute the query
$stmt = mysqli_prepare($conn, $sql);

// Bind parameters dynamically based on filters
if (!empty($search_patient_name) && !empty($from_date) && !empty($to_date)) {
    $search_patient_name = "%" . $search_patient_name . "%";
    mysqli_stmt_bind_param($stmt, "sss", $search_patient_name, $from_date, $to_date);
} elseif (!empty($search_patient_name)) {
    $search_patient_name = "%" . $search_patient_name . "%";
    mysqli_stmt_bind_param($stmt, "s", $search_patient_name);
} elseif (!empty($from_date) && !empty($to_date)) {
    mysqli_stmt_bind_param($stmt, "ss", $from_date, $to_date);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Appointments</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-5">
    <h2>View Appointments</h2>

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

    <!-- Appointments Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Appointment Date</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
