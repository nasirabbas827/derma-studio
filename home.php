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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        /* General Styling */
        body {
            background-color: #f9f9f9;
            font-family: 'Roboto', sans-serif;
        }

        /* Header */
        h2 {
            color: #2c3e50;
            font-weight: 700;
        }

        /* Search Bar */
        .search-bar-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }

        .search-bar {
            width: 100%;
            max-width: 600px;
            border: 2px solid #007bff;
            border-radius: 50px;
            padding: 10px 20px;
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
            margin-left: 10px;
            border-radius: 50px;
            padding: 10px 20px;
        }

        /* Doctor Cards */
        .doctor-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .doctor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .doctor-card img {
            height: 200px;
            object-fit: cover;
        }

        .doctor-card .card-body {
            padding: 20px;
            text-align: center;
        }

        .doctor-card .card-title {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .doctor-card p {
            margin: 5px 0;
            color: #7f8c8d;
        }

        .doctor-card .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 15px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .doctor-card .btn-primary:hover {
            background-color: #0056b3;
        }

        .doctor-card .btn-secondary {
            background-color: #95a5a6;
            border: none;
            padding: 10px 15px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .doctor-card .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        /* Error Message */
        .error-message {
            font-size: 1.2rem;
            color: #e74c3c;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Find Your Doctor</h2>
    <div class="search-bar-container">
        <form method="post" class="d-flex align-items-center w-100">
            <input type="text" class="form-control search-bar" placeholder="Search by name or specialization" name="search" value="<?php echo htmlspecialchars($search); ?>" required>
            <button type="submit" class="btn btn-primary search-button">Search</button>
        </form>
    </div>

    <?php if (!empty($error_message)): ?>
        <div class="error-message">
            <?php echo $error_message; ?>
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
                            <p><strong>Specialization:</strong> <?php echo htmlspecialchars($row['specialization']); ?></p>
                            <p><strong>Experience:</strong> <?php echo htmlspecialchars($row['experience']); ?> years</p>
                            <a href="make_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-primary">Request Appointment</a>
                            <a href="view_feedbacks.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-secondary">View Feedbacks</a>
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
                            <p><strong>Specialization:</strong> <?php echo htmlspecialchars($row['specialization']); ?></p>
                            <p><strong>Experience:</strong> <?php echo htmlspecialchars($row['experience']); ?> years</p>
                            <a href="make_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-primary ">Request Appointment</a>
                            <a href="view_feedbacks.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="btn btn-secondary ">View Feedbacks</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
