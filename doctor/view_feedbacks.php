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

// Fetch doctor's feedbacks
$doctor_id = $_SESSION["doctor_id"];
$sql = "SELECT feedbacks.*, appointments.appointment_id, users.username AS patient_name 
        FROM feedbacks 
        INNER JOIN appointments ON feedbacks.appointment_id = appointments.appointment_id 
        INNER JOIN users ON feedbacks.patient_id = users.id 
        WHERE feedbacks.doctor_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Feedbacks</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">My Feedbacks</h2>
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success" role="alert"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger" role="alert"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Feedback ID</th>
                        <th>Appointment ID</th>
                        <th>Patient</th>
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
    </div>
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
