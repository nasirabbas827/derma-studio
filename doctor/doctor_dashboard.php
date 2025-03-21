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

// Fetch pending appointments count
$pending_appointments = 0;
$pending_sql = "SELECT COUNT(*) AS pending_count FROM appointments WHERE doctor_id = ? AND status = 'pending'";
if ($stmt = mysqli_prepare($conn, $pending_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_doctor_id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $pending_appointments);
        mysqli_stmt_fetch($stmt);
    }
    
    mysqli_stmt_close($stmt);
}

// Fetch completed appointments count
$completed_appointments = 0;
$completed_sql = "SELECT COUNT(*) AS completed_count FROM appointments WHERE doctor_id = ? AND status = 'completed'";
if ($stmt = mysqli_prepare($conn, $completed_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_doctor_id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $completed_appointments);
        mysqli_stmt_fetch($stmt);
    }
    
    mysqli_stmt_close($stmt);
}

// Fetch today's appointments
$today_appointments = [];
$today = date('Y-m-d');
$today_sql = "SELECT a.appointment_id, a.appointment_date, a.status, p.first_name as patient_name 
              FROM appointments a 
              JOIN users p ON a.patient_id = p.id 
              WHERE a.doctor_id = ? AND DATE(a.appointment_date) = ? 
              ORDER BY a.appointment_date ASC";
              
if ($stmt = mysqli_prepare($conn, $today_sql)) {
    mysqli_stmt_bind_param($stmt, "is", $param_doctor_id, $today);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $today_appointments[] = $row;
        }
    }
    
    mysqli_stmt_close($stmt);
}

// Fetch appointments for calendar
$appointments_data = [];
$sql = "SELECT a.appointment_date, a.status, p.first_name as patient_name 
        FROM appointments a 
        JOIN users p ON a.patient_id = p.id 
        WHERE a.doctor_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_doctor_id);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $color = '#3498db'; // Default blue
            if ($row['status'] == 'completed') {
                $color = '#2ecc71'; // Green for completed
            } else if ($row['status'] == 'cancelled') {
                $color = '#e74c3c'; // Red for cancelled
            } else if ($row['status'] == 'pending') {
                $color = '#f39c12'; // Orange for pending
            }
            
            $appointments_data[] = [
                'title' => $row['patient_name'] . ' | ' . ucfirst($row['status']),
                'start' => $row['appointment_date'],
                'color' => $color
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard | Derma Elixir Studio</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <link rel="stylesheet" href="../css/style.css">

    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 40px 0 30px;
            margin-bottom: 30px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-header img {
            display: block;
            margin: 0 auto;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }
        
        .dashboard-header img:hover {
            transform: scale(1.05);
        }
        
        .dashboard-header h1 {
            font-weight: 700;
            margin-top: 15px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        .dashboard-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .stat-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 25px;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card .card-body {
            padding: 25px;
        }
        
        .stat-card .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .stat-card .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .stat-card .stat-icon {
            font-size: 3rem;
            opacity: 0.2;
            position: absolute;
            bottom: 10px;
            right: 10px;
        }
        
        .profile-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 25px;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .profile-card .card-header {
            background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
            color: var(--secondary-color);
            font-weight: 600;
            border-bottom: none;
            padding: 15px 20px;
        }
        
        .profile-card .card-body {
            padding: 20px;
        }
        
        .profile-card .profile-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .profile-card .profile-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .profile-card .profile-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        
        .profile-card .profile-value {
            color: #555;
        }
        
        .action-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 25px;
            height: 100%;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .action-card .card-body {
            padding: 20px;
            text-align: center;
        }
        
        .action-card .action-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .action-card .card-title {
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .action-card .btn {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
        }
        
        .calendar-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            background-color: white;
        }
        
        .calendar-card .card-header {
            background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
            color: var(--secondary-color);
            font-weight: 600;
            border-bottom: none;
            padding: 15px 20px;
        }
        
        .calendar-card .card-body {
            padding: 20px;
        }
        
        #calendar {
            margin-top: 10px;
        }
        
        .fc-today {
            background-color: #e3f2fd !important;
        }
        
        .fc-event {
            border-radius: 5px;
            border: none;
            padding: 5px;
            font-size: 0.85em;
        }
        
        .today-appointments {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }
        
        .today-appointments .card-header {
            background: linear-gradient(135deg, #fff8e1, #ffecb3);
            color: var(--secondary-color);
            font-weight: 600;
            border-bottom: none;
            padding: 15px 20px;
        }
        
        .today-appointments .card-body {
            padding: 0;
        }
        
        .today-appointments .list-group-item {
            border-left: none;
            border-right: none;
            padding: 15px 20px;
        }
        
        .today-appointments .list-group-item:first-child {
            border-top: none;
        }
        
        .today-appointments .list-group-item:last-child {
            border-bottom: none;
        }
        
        .appointment-time {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .appointment-patient {
            font-weight: 600;
        }
        
        .appointment-status {
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending {
            background-color: #fff8e1;
            color: #f39c12;
        }
        
        .status-completed {
            background-color: #e8f5e9;
            color: #2ecc71;
        }
        
        .status-cancelled {
            background-color: #ffebee;
            color: #e74c3c;
        }
        
        .quick-actions {
            margin-bottom: 30px;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
            margin-top: 30px;
            color: #777;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<!-- Dashboard Header -->
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <!-- Profile Picture -->
                <img src="<?php echo !empty($profile_pic) ? './' . htmlspecialchars($profile_pic) : 'https://via.placeholder.com/150'; ?>" 
                     alt="Dr. <?php echo htmlspecialchars($full_name); ?>" 
                     class="rounded-circle" 
                     style="width: 150px; height: 150px; object-fit: cover;">
            </div>
            <div class="col-md-8 text-center text-md-left mt-4 mt-md-0">
                <!-- Doctor Information -->
                <h1>Welcome, Dr. <?php echo htmlspecialchars($full_name); ?></h1>
                <p><i class="fas fa-user-md mr-2"></i><?php echo htmlspecialchars($specialization); ?> Specialist with <?php echo htmlspecialchars($experiences); ?> years of experience</p>
                <div class="mt-3">
                    <a href="update_profile.php" class="btn btn-light mr-2">
                        <i class="fas fa-user-edit mr-1"></i> Edit Profile
                    </a>
                    <a href="view_appointments.php" class="btn btn-light">
                        <i class="fas fa-calendar-check mr-1"></i> View All Appointments
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Appointments</h5>
                    <p class="stat-value"><?php echo $total_appointments; ?></p>
                    <i class="fas fa-calendar-alt stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending Appointments</h5>
                    <p class="stat-value"><?php echo $pending_appointments; ?></p>
                    <i class="fas fa-clock stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed Appointments</h5>
                    <p class="stat-value"><?php echo $completed_appointments; ?></p>
                    <i class="fas fa-check-circle stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row quick-actions mt-4">
        <div class="col-12">
            <h4 class="mb-3">Quick Actions</h4>
        </div>
        <div class="col-md-3">
            <div class="card action-card">
                <div class="card-body">
                    <div class="action-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <h5 class="card-title">Manage Appointments</h5>
                    <a href="view_appointments.php" class="btn btn-primary">View</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card action-card">
                <div class="card-body">
                    <div class="action-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h5 class="card-title">Patient Records</h5>
                    <a href="view_appointments.php" class="btn btn-primary">Access</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card action-card">
                <div class="card-body">
                    <div class="action-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h5 class="card-title">View Feedbacks</h5>
                    <a href="view_feedbacks.php" class="btn btn-primary">Check</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Appointments -->
        <div class="col-md-4">
            <div class="card today-appointments">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-calendar-day mr-2"></i>Today's Appointments</span>
                    <span class="badge badge-primary"><?php echo count($today_appointments); ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($today_appointments)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-muted">No appointments scheduled for today.</p>
                        </div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($today_appointments as $appointment): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="appointment-time">
                                                <?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?>
                                            </div>
                                            <div class="appointment-patient">
                                                <?php echo htmlspecialchars($appointment['patient_name']); ?>
                                            </div>
                                        </div>
                                        <div>
                                            <?php 
                                                $statusClass = '';
                                                switch($appointment['status']) {
                                                    case 'pending':
                                                        $statusClass = 'status-pending';
                                                        break;
                                                    case 'completed':
                                                        $statusClass = 'status-completed';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'status-cancelled';
                                                        break;
                                                }
                                            ?>
                                            <span class="appointment-status <?php echo $statusClass; ?>">
                                                <?php echo ucfirst($appointment['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Profile Information -->
            <div class="card profile-card">
                <div class="card-header">
                    <i class="fas fa-id-card mr-2"></i>Profile Information
                </div>
                <div class="card-body">
                    <div class="profile-item">
                        <div class="profile-label">
                            <i class="fas fa-envelope mr-2 text-primary"></i>Email
                        </div>
                        <div class="profile-value">
                            <?php echo htmlspecialchars($email); ?>
                        </div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">
                            <i class="fas fa-stethoscope mr-2 text-primary"></i>Specialization
                        </div>
                        <div class="profile-value">
                            <?php echo htmlspecialchars($specialization); ?>
                        </div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">
                            <i class="fas fa-briefcase mr-2 text-primary"></i>Experience
                        </div>
                        <div class="profile-value">
                            <?php echo htmlspecialchars($experiences); ?> years
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calendar Section -->
        <div class="col-md-8">
            <div class="card calendar-card">
                <div class="card-header">
                    <i class="fas fa-calendar-alt mr-2"></i>Appointment Calendar
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Derma Elixir Studio. All rights reserved.</p>
    </div>
</footer>

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
            eventTextColor: '#fff',
            editable: false,
            eventLimit: true,
            eventRender: function(event, element) {
                element.find('.fc-title').html(event.title);
            },
            dayClick: function(date, jsEvent, view) {
                // You could add functionality to add appointments on day click
                console.log('Day clicked:', date.format());
            },
            eventClick: function(calEvent, jsEvent, view) {
                // You could add functionality to view appointment details on event click
                console.log('Event clicked:', calEvent);
            }
        });
    });
</script>
</body>
</html>