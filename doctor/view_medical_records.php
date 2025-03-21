<?php
session_start();
include('config.php');

// Check if doctor is logged in
if (!isset($_SESSION["doctor_id"]) || empty($_SESSION["doctor_id"])) {
    header("location: doctor_login.php");
    exit;
}

// Get patient_id from URL parameter
$patient_id = $_GET['patient_id'] ?? 0;

$sql = "SELECT patient_records.*, users.username AS patient_name, doctors.full_name AS doctor_name 
        FROM patient_records 
        INNER JOIN users ON patient_records.patient_id = users.id 
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
    <title>Patient Medical Records</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>


<div class="container mt-5">
<a href="" class="btn btn-dark float-right mb-4" onclick="window.print()">Print</a>

    <h2 class="text-center">Medical Records</h2>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Record ID</th>
                        <th>Visit Date</th>
                        <th>Doctor</th>
                        <th>Diagnosis</th>
                        <th>Prescription</th>
                        <th>Lab Tests</th>
                        <th>Notes</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['record_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['visit_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['diagnosis']); ?></td>
                                <td><?php echo htmlspecialchars($row['prescription']); ?></td>
                                <td><?php echo htmlspecialchars($row['lab_tests']); ?></td>
                                <td><?php echo htmlspecialchars($row['notes']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                                <td>
                                    <a href="edit_medical_record.php?record_id=<?php echo $row['record_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <form action="delete_medical_record.php" method="post" style="display:inline;">
                                        <input type="hidden" name="record_id" value="<?php echo $row['record_id']; ?>">
                                        <button type="submit" class="mt-2 btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-danger">No medical records found for this patient.</td>
                        </tr>
                    <?php endif; ?>
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
