<?php
include('config.php');
session_start();

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

// Fetch tests data
$sqlTests = "SELECT * FROM tests";
$resultTests = mysqli_query($conn, $sqlTests);

// Fetch services data
$sqlServices = "SELECT * FROM services";
$resultServices = mysqli_query($conn, $sqlServices);

// Fetch about us data
$sqlAboutUs = "SELECT * FROM about_us";
$resultAboutUs = mysqli_query($conn, $sqlAboutUs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Derma Elixir Studio</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        
        /* Custom Styles */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        
        .jumbotron {
            height: 600px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('./images/hotel.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 0;
            border-radius: 0;
        }

        .jumbotron h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .jumbotron p {
            font-size: 1.8rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
        
        .section-title {
            position: relative;
            margin-bottom: 40px;
            padding-bottom: 15px;
            text-align: center;
            font-weight: 700;
            color: var(--secondary-color);
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-img-top {
            height: 250px;
            object-fit: cover;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .section-bg-light {
            background-color: #f8f9fa;
            padding: 80px 0;
        }
        
        .section-bg-primary {
            background-color: #e8f4fc;
            padding: 80px 0;
        }
        
        .search-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }
        
        .service-card {
            position: relative;
            overflow: hidden;
        }
        
        .service-card img {
            transition: transform 0.5s;
        }
        
        .service-card:hover img {
            transform: scale(1.05);
        }
        
        .contact-form {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 40px;
        }
        
        footer {
            background-color: var(--secondary-color);
            color: white;
            padding: 40px 0;
        }
        
        .feature-box {
            text-align: center;
            padding: 30px 20px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            height: 100%;
        }
        
        .feature-icon {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .about-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .about-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .about-card .card-text {
            flex: 1;
        }
        
        .doctor-specialization {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .doctor-experience {
            color: #666;
        }
        
        .doctor-card-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<!-- Hero Section -->
<div class="jumbotron text-center">
    <div class="container">
        <h1>Welcome to Derma Elixir Studio</h1>
        <p>Discover Quality Skin Care Services with Us</p>
        <a href="login.php" class="btn btn-primary btn-lg px-5 py-3 mt-3">
            <i class="fas fa-sign-in-alt mr-2"></i> Login to Explore
        </a>
    </div>
</div>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h4>Expert Doctors</h4>
                    <p>Our team consists of highly qualified and experienced Sking care specialists.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-clinic-medical"></i>
                    </div>
                    <h4>Modern Facilities</h4>
                    <p>State-of-the-art equipment and comfortable treatment environments.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4>Easy Appointments</h4>
                    <p>Book your appointment online and get treated at your convenience.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Doctors Section -->
<section class="section-bg-light">
    <div class="container">
        <h2 class="section-title">Our Specialist Doctors</h2>
        
        <div class="search-box">
            <form method="post" class="form">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search by name or specialization" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>
                    </div>
                </div>
            </form>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row">
            <?php if (empty($search) || !empty($error_message)): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="./doctor/<?php echo htmlspecialchars($row['profile_pic']); ?>" class="card-img-top" alt="Doctor Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['full_name']); ?></h5>
                                <p class="doctor-specialization">
                                    <i class="fas fa-stethoscope mr-2"></i> <?php echo htmlspecialchars($row['specialization']); ?>
                                </p>
                                <p class="doctor-experience">
                                    <i class="fas fa-briefcase mr-2"></i> Experience: <?php echo htmlspecialchars($row['experience']); ?>
                                </p>
                                <div class="doctor-card-footer">
                                    <a href="make_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus mr-1"></i> Appointment
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
                        <div class="card h-100">
                            <img src="./doctor/<?php echo htmlspecialchars($row['profile_pic']); ?>" class="card-img-top" alt="Doctor Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['full_name']); ?></h5>
                                <p class="doctor-specialization">
                                    <i class="fas fa-stethoscope mr-2"></i> <?php echo htmlspecialchars($row['specialization']); ?>
                                </p>
                                <p class="doctor-experience">
                                    <i class="fas fa-briefcase mr-2"></i> Experience: <?php echo htmlspecialchars($row['experience']); ?>
                                </p>
                                <div class="doctor-card-footer">
                                    <a href="make_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus mr-1"></i> Appointment
                                    </a>
                                    <a href="view_feedbacks.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-comments mr-1"></i> Feedbacks
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section-bg-primary">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($resultServices)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card service-card h-100">
                        <?php if (!empty($row['image'])): ?>
                            <img src="admin/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="Service Image">
                        <?php else: ?>
                            <img src="default-image.jpg" class="card-img-top" alt="Default Image">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['service_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Tests Section -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Diagnostic Tests</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($resultTests)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="fas fa-vial fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title text-center"><?php echo htmlspecialchars($row['test_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section class="section-bg-light">
    <div class="container">
        <h2 class="section-title">About Us</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($resultAboutUs)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card about-card">
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

<!-- Contact Us Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="section-title text-left">Contact Us</h2>
                <p class="mb-4">We'd love to hear from you. Please fill out the form below and we'll get back to you as soon as possible.</p>
                
                <div class="contact-info mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-map-marker-alt fa-2x text-primary mr-3"></i>
                        <div>
                            <h5 class="m-0">Address</h5>
                            <p class="m-0">123 Sking Care Street, Medical District, City</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-phone-alt fa-2x text-primary mr-3"></i>
                        <div>
                            <h5 class="m-0">Phone</h5>
                            <p class="m-0">+1 (123) 456-7890</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <i class="fas fa-envelope fa-2x text-primary mr-3"></i>
                        <div>
                            <h5 class="m-0">Email</h5>
                            <p class="m-0">info@dermaelixirstudio.com</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="contact-form">
                    <form action="#" method="post">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user mr-2"></i>Your Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope mr-2"></i>Your Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="message"><i class="fas fa-comment mr-2"></i>Your Message</label>
                            <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane mr-2"></i>Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Derma Elixir Studio</h5>
                <p>Providing quality Skin care services since 2010. Our mission is to improve the vision and Skin health of our community.</p>
                <div class="social-icons mt-3">
                    <a href="#" class="text-white mr-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white mr-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white mr-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white">Home</a></li>
                    <li><a href="#" class="text-white">About Us</a></li>
                    <li><a href="#" class="text-white">Services</a></li>
                    <li><a href="#" class="text-white">Doctors</a></li>
                    <li><a href="#" class="text-white">Contact</a></li>
                </ul>
            </div>
            
            <div class="col-md-4">
                <h5>Opening Hours</h5>
                <ul class="list-unstyled">
                    <li>Monday - Friday: 9:00 AM - 6:00 PM</li>
                    <li>Saturday: 9:00 AM - 4:00 PM</li>
                    <li>Sunday: Closed</li>
                </ul>
            </div>
        </div>
        
        <hr class="bg-light">
        
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Derma Elixir Studio. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


