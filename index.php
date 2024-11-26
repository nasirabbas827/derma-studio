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
<html>
<head>
    <title>Derma Elixir Studio</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
 <style>
.jumbotron {
            height: 500px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .jumbotron p {
            font-size: 1.5rem;
        }
        .card-img-top{
            height:250px;
            width: 100%;
        }
    </style>
</head>
<body>

<?php
include('navbar.php');
?>

<div class="jumbotron text-center">
    <h1>Welcome to Derma Elixir Studio</h1>
    <p>Discover Quality Eye Care Services with Us</p>
    <a href="login.php" class="btn btn-primary btn-lg">Login to Explore</a>
</div>

<div class="container mt-5">
    <h2 class="text-center">Approved Doctors</h2>
    <form method="post" class="form justify-content-center mt-3 mb-5">
        <input type="text" class="form-control" placeholder="Search by name or specialization" name="search" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="float-right m-2 btn btn-primary">Search</button>
    </form>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger mt-3" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    <div class="row mt-4">
        <?php if (empty($search) || !empty($error_message)): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="./doctor/<?php echo htmlspecialchars($row['profile_pic']); ?>" class="card-img-top" alt="Doctor Image" >
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['full_name']); ?></h5>
                            <p><strong>Specialization:</strong> <?php echo htmlspecialchars($row['specialization']); ?></p>
                            <p><strong>Experience:</strong> <?php echo htmlspecialchars($row['experience']); ?></p>
                            <a href="make_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-outline-primary ">Appointment</a>
                            <a href="view_feedbacks.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-outline-secondary ">Feedbacks</a>
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
                            <p><strong>Specialization:</strong> <?php echo htmlspecialchars($row['specialization']); ?></p>
                            <p><strong>Experience:</strong> <?php echo htmlspecialchars($row['experience']); ?></p>
                            <a href="make_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-outline-primary ">Request Appointment</a>
                            <a href="view_feedbacks.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-outline-secondary ">View Feedbacks</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>
<div class="container mt-5">
    <!-- Tests Section -->
    <div class="row mb-4 bg-light py-5 rounded">
        <div class="col-md-12 text-center">
            <h2 class="text-primary">Tests</h2>
        </div>
        <?php while ($row = mysqli_fetch_assoc($resultTests)): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['test_name']; ?></h5>
                        <p class="card-text"><?php echo $row['description']; ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Services Section -->
    <div class="row mb-4 bg-info py-5 rounded">
        <div class="col-md-12 text-center">
            <h2 class="text-white">Services</h2>
        </div>
        <?php while ($row = mysqli_fetch_assoc($resultServices)): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['service_name']; ?></h5>
                        <p class="card-text"><?php echo $row['description']; ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- About Us Section -->
    <div class="row mb-4 bg-light py-5 rounded">
        <div class="col-md-12 text-center">
            <h2 class="text-primary">About Us</h2>
        </div>
        <?php while ($row = mysqli_fetch_assoc($resultAboutUs)): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['title']; ?></h5>
                        <p class="card-text"><?php echo $row['content']; ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Contact Us Section -->
<div class="container mt-5">
    <div class="contact-form bg-light py-5 px-4 rounded shadow-sm">
        <h2 class="text-center text-primary">Contact Us</h2>
        <form action="#" method="post">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Your Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
    </div>
</div>


<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; 2024 Derma Elixir Studio. All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
