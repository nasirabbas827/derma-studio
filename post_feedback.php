<?php
session_start();
include('config.php');

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if appointment_id and doctor_id are set
if (!isset($_GET['appointment_id']) || !isset($_GET['doctor_id'])) {
    header("location: my_appointments.php");
    exit;
}

$appointment_id = $_GET['appointment_id'];
$doctor_id = $_GET['doctor_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    
    // Insert feedback into the database
    $sql = "INSERT INTO feedbacks (appointment_id, patient_id, doctor_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiiss", $appointment_id, $_SESSION["id"], $doctor_id, $rating, $comment);
    if (mysqli_stmt_execute($stmt)) {
        header("location: view_appointments.php");
        exit;
    } else {
        $error_msg = "Error submitting feedback.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Feedback</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Post Feedback</h2>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger" role="alert"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?appointment_id=' . $appointment_id . '&doctor_id=' . $doctor_id; ?>" method="post">
        <div class="form-group">
            <label for="rating">Rating:</label>
            <input type="number" name="rating" class="form-control" min="1" max="5" required>
        </div>
        <div class="form-group">
            <label for="comment">Comment:</label>
            <textarea name="comment" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Feedback</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
