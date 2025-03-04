<?php
session_start();

// Check if the doctor is logged in, if not then redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: doctor_login.php");
    exit;
}

// Include config file
include('config.php');

// Define variables and initialize with empty values
$full_name = $email = $password = $specialization = $experience = "";
$profile_pic = "uploads/default-profile-pic.jpg";

// Fetch doctor's information
$doctor_id = $_SESSION["doctor_id"];
$sql = "SELECT full_name, email, specialization, experience, profile_pic FROM doctors WHERE doctor_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_doctor_id);
    $param_doctor_id = $doctor_id;

    // Execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $full_name, $email, $specialization, $experience, $profile_pic);
        mysqli_stmt_fetch($stmt);
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from form
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $specialization = $_POST["specialization"];
    $experience = $_POST["experience"];

    // Check if password is not empty
    if (!empty($password)) {
        // Hash the password
        $password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // If password is empty, keep the existing one
        $password = $_POST["current_password"];
    }

    // Upload profile picture if provided
    if (!empty($_FILES["profile_pic"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
        $profile_pic = $target_file;
    }

    // Update doctor's profile
    $sql = "UPDATE doctors SET full_name=?, email=?, password=?, specialization=?, experience=?, profile_pic=? WHERE doctor_id=?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssssi", $full_name, $email, $password, $specialization, $experience, $profile_pic, $doctor_id);
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to profile page after successful update
            header("location: update_profile.php");
            exit;
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
<div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">

    <h2 class="text-center">Update Profile</h2>
    <div class="mt-4">
        <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="img-fluid rounded-circle" style="max-width: 150px;">
    </div>
    <div class="mt-4">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo $full_name; ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <input type="hidden" name="current_password" value="<?php echo $password; ?>">
            </div>
            <div class="form-group">
                <label>Specialization</label>
                <input type="text" name="specialization" class="form-control" value="<?php echo $specialization; ?>">
            </div>
            <div class="form-group">
                <label>Experience</label>
                <input type="text" name="experience" class="form-control" value="<?php echo $experience; ?>">
            </div>
            <div class="form-group">
                <label>Profile Picture</label>
                <input type="file" name="profile_pic" class="form-control">
            </div>
            <div class="form-group text-center">
                <input type="submit" class="btn btn-primary" value="Update Profile">
            </div>
        </form>
    </div>
</div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
