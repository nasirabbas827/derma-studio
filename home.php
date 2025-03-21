<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Initialize variables
$search = "";
$error_message = "";
$search_result = [];

// Handle search query
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = trim($_POST["search"]);
    if (!empty($search)) {
        // Search for doctors by name or specialization
        $sql = "SELECT * FROM doctors WHERE status = 'approved' AND (full_name LIKE ? OR specialization LIKE ?)";
        $stmt = mysqli_prepare($conn, $sql);
        $search_param = "%{$search}%";
        mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);
        mysqli_stmt_execute($stmt);
        $search_result = mysqli_stmt_get_result($stmt);

        // Check if any results found
        if (mysqli_num_rows($search_result) === 0) {
            $error_message = "No doctors found matching your search criteria.";
        }
    } else {
        $error_message = "Please enter a search term.";
    }
}

// Fetch all approved doctors if no search is performed or if there are no search results
if (empty($search) || !empty($error_message)) {
    $sql = "SELECT * FROM doctors WHERE status = 'approved'";
    $result = mysqli_query($conn, $sql);
}

// Fetch services data
$sqlServices = "SELECT * FROM services";
$resultServices = mysqli_query($conn, $sqlServices);

// Fetch about us data
$sqlAboutUs = "SELECT * FROM about_us";
$resultAboutUs = mysqli_query($conn, $sqlAboutUs);

// Get patient name
$patient_id = $_SESSION["id"];
$sqlPatient = "SELECT first_name FROM users WHERE id = ?";
$stmtPatient = mysqli_prepare($conn, $sqlPatient);
mysqli_stmt_bind_param($stmtPatient, "i", $patient_id);
mysqli_stmt_execute($stmtPatient);
$resultPatient = mysqli_stmt_get_result($stmtPatient);
$patientData = mysqli_fetch_assoc($resultPatient);
$patientName = $patientData ? $patientData['first_name'] : 'Patient';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Derma Elixir Studio</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        /* General Styling */
        body {
            font-family: 'Roboto', sans-serif;
            color: #333;
            background-color: #f8f9fa;
        }

        /* Section Styling */
        .section {
            padding: 60px 0;
        }

        .section-title {
            position: relative;
            margin-bottom: 40px;
            padding-bottom: 15px;
            text-align: center;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 3px;
            background-color: #007bff;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .welcome-banner h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .welcome-banner p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* Search Bar */
        .search-container {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
        }

        .search-bar {
            width: 100%;
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 12px 20px;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            outline: none;
            transition: all 0.3s ease-in-out;
        }

        .search-bar:focus {
            border-color: #0056b3;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        .search-button {
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
        }

        /* Doctor Cards */
        .doctor-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .doctor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .doctor-card img {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }

        .doctor-card .card-body {
            padding: 20px;
        }

        .doctor-card .card-title {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .doctor-card p {
            margin: 8px 0;
            color: #7f8c8d;
        }

        .doctor-card .btn {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 50px;
            margin-right: 5px;
            margin-top: 10px;
        }

        /* Services Section */
        .services-section {
            background: linear-gradient(135deg, #e8f4fc, #d1ecf1);
            padding: 60px 0;
        }

        .service-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .service-card img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .service-card .card-body {
            padding: 20px;
        }

        .service-card .card-title {
            font-size: 1.3rem;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 600;
        }

        /* About Us Section */
        .about-section {
            background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
            padding: 60px 0;
        }

        .about-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .about-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .about-card .card-body {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .about-card .card-title {
            font-size: 1.3rem;
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .about-card .card-text {
            flex: 1;
            color: #7f8c8d;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            padding: 40px 0;
            margin: 60px 0;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Error Message */
        .error-message {
            font-size: 1.2rem;
            color: #e74c3c;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #fdf2f2;
            border-radius: 10px;
            border-left: 5px solid #e74c3c;
        }

        /* Quick Actions */
        .quick-actions {
            margin-bottom: 40px;
        }

        .action-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .action-icon {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 15px;
        }

        .action-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .action-description {
            color: #7f8c8d;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<!-- Welcome Banner -->
<div class="container mt-5">
    <div class="welcome-banner">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1>Welcome, <?php echo htmlspecialchars($patientName); ?>!</h1>
                    <p>Manage your appointments and explore our services from your personal dashboard.</p>
                </div>
                <div class="col-md-4 text-md-right">
                    <a href="view_appointments.php" class="btn btn-light btn-lg">My Appointments</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="container quick-actions">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <h3 class="action-title">Book Appointment</h3>
                <p class="action-description">Schedule a new appointment with one of our specialists.</p>
                <a href="#find-doctor" class="btn btn-primary">Find Doctor</a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3 class="action-title">Appointment History</h3>
                <p class="action-description">View your past appointments and treatment history.</p>
                <a href="view_appointments.php" class="btn btn-primary">View History</a>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h3 class="action-title">Update Profile</h3>
                <p class="action-description">Update your personal information and preferences.</p>
                <a href="update_profile.php" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>
    </div>
</div>

<!-- Find Doctor Section -->
<div class="container" id="find-doctor">
    <h2 class="section-title">Find Your Doctor</h2>
    
    <div class="search-container">
        <form method="post" class="row">
            <div class="col-md-9 mb-3 mb-md-0">
                <input type="text" class="form-control search-bar" placeholder="Search by name or specialization" name="search" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-block search-button">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($error_message)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php if (empty($search) || !empty($error_message)): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card doctor-card">
                        <img src="./doctor/<?php echo htmlspecialchars($row['profile_pic']); ?>" class="card-img-top" alt="Doctor Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['full_name']); ?></h5>
                            <p><i class="fas fa-stethoscope mr-2 text-primary"></i><strong>Specialization:</strong> <?php echo htmlspecialchars($row['specialization']); ?></p>
                            <p><i class="fas fa-briefcase mr-2 text-primary"></i><strong>Experience:</strong> <?php echo htmlspecialchars($row['experience']); ?> years</p>
                            <div class="mt-3">
                                <a href="make_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus mr-1"></i> Make Appointment
                                </a>
                                <a href="view_feedbacks.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-comments mr-1"></i> Feedbacks
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <?php while ($row = mysqli_fetch_assoc($search_result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card doctor-card">
                        <img src="./doctor/<?php echo htmlspecialchars($row['profile_pic']); ?>" class="card-img-top" alt="Doctor Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['full_name']); ?></h5>
                            <p><i class="fas fa-stethoscope mr-2 text-primary"></i><strong>Specialization:</strong> <?php echo htmlspecialchars($row['specialization']); ?></p>
                            <p><i class="fas fa-briefcase mr-2 text-primary"></i><strong>Experience:</strong> <?php echo htmlspecialchars($row['experience']); ?> years</p>
                            <div class="mt-3">
                                <a href="make_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus mr-1"></i> Request Appointment
                                </a>
                                <a href="view_feedbacks.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-comments mr-1"></i> View Feedbacks
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Stats Section -->
<div class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Specialist Doctors</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">5000+</div>
                    <div class="stat-label">Happy Patients</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">10+</div>
                    <div class="stat-label">Years Experience</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">20+</div>
                    <div class="stat-label">Services Offered</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($resultServices)): ?>
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <?php if (!empty($row['image'])): ?>
                            <img src="admin/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="Service Image">
                        <?php else: ?>
                            <div class="text-center py-5 bg-light">
                                <i class="fas fa-clinic-medical fa-4x text-primary"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['service_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                            <a href="service_details.php?id=<?php echo $row['service_id']; ?>" class="btn btn-outline-primary mt-3">
                                <i class="fas fa-info-circle mr-1"></i> Learn More
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section class="about-section">
    <div class="container">
        <h2 class="section-title">About Us</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($resultAboutUs)): ?>
                <div class="col-md-4 mb-4">
                    <div class="about-card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['content']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>Derma Elixir Studio</h5>
                <p>Providing quality healthcare services since 2010.</p>
            </div>
            <div class="col-md-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="home.php" class="text-white">Home</a></li>
                    <li><a href="#find-doctor" class="text-white">Find Doctor</a></li>
                    <li><a href="view_appointments.php" class="text-white">My Appointments</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Contact Us</h5>
                <address>
                    <p><i class="fas fa-map-marker-alt mr-2"></i> 123 Medical Center Dr, Suite 100</p>
                    <p><i class="fas fa-phone mr-2"></i> (123) 456-7890</p>
                    <p><i class="fas fa-envelope mr-2"></i> info@dermaelixir.com</p>
                </address>
            </div>
        </div>
        <hr class="bg-light">
        <div class="text-center">
            <p>&copy; <?php echo date('Y'); ?> Derma Elixir Studio. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>