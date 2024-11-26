<?php
include('config.php');
session_start();



// Check if doctor_id is provided in the URL
if (!isset($_GET['doctor_id'])) {
    header("location: patient_home.php");
    exit;
}

$doctor_id = $_GET['doctor_id'];

// Fetch feedbacks for the given doctor
$sql = "SELECT feedbacks.*, doctors.full_name AS doctor_name, users.username AS patient_name 
        FROM feedbacks 
        INNER JOIN doctors ON feedbacks.doctor_id = doctors.doctor_id 
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
    <title>View Feedbacks</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .checked {
            color: gold;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Feedbacks</h2>
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Feedback <?php echo htmlspecialchars($row['feedback_id']); ?></h5>
                        <p><strong>Doctor:</strong> <?php echo htmlspecialchars($row['doctor_name']); ?></p>
                        <p><strong>Patient:</strong> <?php echo htmlspecialchars($row['patient_name']); ?></p>
                        <p><strong>Rating:</strong> <?php echo getStarRating($row['rating']); ?></p>
                        <p><strong>Comment:</strong> <?php echo htmlspecialchars($row['comment']); ?></p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Function to generate star rating HTML based on the given rating
function getStarRating($rating) {
    $output = '<div class="star-rating">';
    $fullStars = floor($rating);
    $emptyStars = 5 - $fullStars;
    for ($i = 0; $i < $fullStars; $i++) {
        $output .= '<span class="fa fa-star checked"></span>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $output .= '<span class="fa fa-star"></span>';
    }
    $output .= '</div>';
    return $output;
}
?>
