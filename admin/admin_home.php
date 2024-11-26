<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch data for dashboard
$totalUsers = $totalDoctors = $totalAppointments = $totalFeedbacks = 0;

// Example queries:
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$totalDoctors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM doctors"))['total'];
$totalAppointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments"))['total'];
$totalFeedbacks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM feedbacks"))['total'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .dashboard-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .dashboard-card .card-body {
            background-color: #fff;
            color: #333;
            padding: 25px;
            text-align: center;
        }
        .card-title {
            font-size: 1.5rem;
            color: #333;
        }
        .card-text {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        .row.mt-4 {
            margin-top: 30px;
        }
        .container.mt-5 {
            margin-top: 50px;
        }
        h2 {
            font-size: 2.5rem;
            color: #333;
        }
    </style>
</head>
<body>

<?php
include('admin_navbar.php');
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Admin Dashboard</h2>
    <div class="row mt-4">
        <!-- Total Users Card -->
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h5 class="card-title"> Users</h5>
                    <p class="card-text"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Doctors Card -->
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h5 class="card-title"> Doctors</h5>
                    <p class="card-text"><?php echo $totalDoctors; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Appointments Card -->
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h5 class="card-title"> Appointments</h5>
                    <p class="card-text"><?php echo $totalAppointments; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Feedbacks Card -->
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h5 class="card-title"> Feedbacks</h5>
                    <p class="card-text"><?php echo $totalFeedbacks; ?></p>
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
