<?php
// Initialize the session
session_start();

// Check if the doctor is logged in, if not then redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: doctor_login.php");
    exit;
}

// Include config file
include('config.php');

// Fetch doctor's information
$doctor_id = $_SESSION["doctor_id"];
// Fetch doctor's information
$sql = "SELECT full_name, email, specialization, experience, profile_pic FROM doctors WHERE doctor_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_doctor_id);
    $param_doctor_id = $doctor_id;

    // Execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $full_name, $email, $specialization, $experiences, $profile_pic);
        mysqli_stmt_fetch($stmt);
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }

    // Close statement
    mysqli_stmt_close($stmt);
}


// Fetch total appointments of the doctor
$total_appointments = 0;
$appointments_sql = "SELECT COUNT(*) AS total_appointments FROM appointments WHERE doctor_id = ?";
if ($stmt = mysqli_prepare($conn, $appointments_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_doctor_id);
    $param_doctor_id = $doctor_id;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $total_appointments);
        mysqli_stmt_fetch($stmt);
    } else {
        echo "Error fetching appointments.";
    }

    mysqli_stmt_close($stmt);
}

// Fetch appointments for calendar
$appointments_data = [];
$sql = "SELECT appointment_date, patient_id, status FROM appointments WHERE doctor_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_doctor_id);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $appointments_data[] = [
                'title' => 'Patient: ' . $row['patient_id'] . ' | ' . ucfirst($row['status']),
                'start' => $row['appointment_date'],
            ];
        }
    } else {
        echo "Error fetching appointment data.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <style>
        .dashboard-header {
            background-color: #007bff;
            color: white;
            padding: 20px 0;
            margin-bottom: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        #calendar {
            margin-top: 20px;
        }
        .dashboard-header img {
        display: block;
        margin: 0 auto;
    }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>
<div class="dashboard-header text-center">
    <!-- Profile Picture -->
    <img src="<?php echo htmlspecialchars($profile_pic); ?>" 
         alt="Doctor's Profile Picture" 
         class="rounded-circle mb-3" 
         style="width: 120px; height: 120px; object-fit: cover; border: 3px solid white;">

    <!-- Doctor Information -->
    <h1>Welcome, Dr. <?php echo htmlspecialchars($full_name); ?></h1>
    <p><?php echo htmlspecialchars($specialization); ?> Specialist with <?php echo htmlspecialchars($experiences); ?> years of experience.</p>
</div>


    <div class="container">
        <div class="row">
            <!-- Total Appointments Card -->
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Total Appointments</h5>
                        <p class="card-text"><?php echo $total_appointments; ?></p>
                    </div>
                </div>
            </div>

            <!-- Doctor Profile Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Profile Information</h5>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                        <p><strong>Specialization:</strong> <?php echo htmlspecialchars($specialization); ?></p>
                        <p><strong>Experience:</strong> <?php echo htmlspecialchars($experiences); ?> years</p>
                    </div>
                </div>
            </div>

            <!-- Logout Card -->
            <div class="col-md-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Logout</h5>
                        <a href="logout.php" class="btn btn-light btn-block">Sign Out</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div id="calendar"></div>
    </div>

    <!-- Dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

    <script>
        $(document).ready(function () {
            // Pass appointments data from PHP to JavaScript
            var appointments = <?php echo json_encode($appointments_data); ?>;

            // Initialize FullCalendar
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: appointments,
                eventColor: '#007bff',
                eventTextColor: '#fff',
                editable: false
            });
        });
    </script>
</body>
</html>
