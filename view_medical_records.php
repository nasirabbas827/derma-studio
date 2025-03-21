<?php
session_start();
include('config.php');

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Fetch patient's medical records
$patient_id = $_SESSION["id"];
$sql = "SELECT patient_records.record_id, patient_records.visit_date, patient_records.diagnosis, 
        patient_records.prescription, patient_records.lab_tests, patient_records.notes, 
        doctors.full_name AS doctor_name 
        FROM patient_records 
        INNER JOIN doctors ON patient_records.doctor_id = doctors.doctor_id 
        WHERE patient_records.patient_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $patient_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Medical Records</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
<a href="" class="btn btn-dark float-right mb-4" onclick="window.print()">Print</a>

    <h2 class="text-center">My Medical Records</h2>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Record ID</th>
                        <th>Doctor</th>
                        <th>Visit Date</th>
                        <th>Diagnosis</th>
                        <th>Prescription</th>
                        <th>Lab Tests</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['record_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['visit_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['diagnosis']); ?></td>
                            <td><?php echo htmlspecialchars($row['prescription']); ?></td>
                            <td><?php echo htmlspecialchars($row['lab_tests']); ?></td>
                            <td><?php echo htmlspecialchars($row['notes']); ?></td>
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
