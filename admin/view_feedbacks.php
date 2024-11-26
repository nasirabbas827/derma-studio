<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Initialize variables
$success_msg = $error_msg = "";

// Handle deletion of feedback
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_feedback'])) {
    $feedback_id = $_POST['feedback_id'];

    // Delete feedback from the database
    $sql = "DELETE FROM feedbacks WHERE feedback_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $feedback_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Feedback deleted successfully.";
    } else {
        $error_msg = "Error deleting feedback.";
    }
    mysqli_stmt_close($stmt);
}

// Fetch all feedbacks with associated appointment, patient, and doctor information
$sql = "SELECT feedbacks.feedback_id, feedbacks.rating, feedbacks.comment, feedbacks.created_at, 
               appointments.appointment_id, users.username AS patient_name, 
               doctors.full_name AS doctor_name
        FROM feedbacks
        INNER JOIN appointments ON feedbacks.appointment_id = appointments.appointment_id
        INNER JOIN users ON feedbacks.patient_id = users.id
        INNER JOIN doctors ON feedbacks.doctor_id = doctors.doctor_id";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Feedbacks</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>View Feedbacks</h2>
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success" role="alert"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger" role="alert"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    <table class="table">
        <thead>
            <tr>
                <th>Feedback ID</th>
                <th>Appointment ID</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['feedback_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['rating']); ?></td>
                    <td><?php echo htmlspecialchars($row['comment']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return confirmDelete();">
                            <input type="hidden" name="feedback_id" value="<?php echo $row['feedback_id']; ?>">
                            <button type="submit" name="delete_feedback" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- JavaScript Confirmation for Deletion -->
<script>
function confirmDelete() {
    return confirm("Are you sure you want to delete this feedback?");
}
</script>
</body>
</html>
