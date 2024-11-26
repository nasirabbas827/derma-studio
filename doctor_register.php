<?php
include('config.php');

// Define variables and initialize with empty values
$username = $password = $email = $full_name = $clearance_certificate = $certificate_number = "";
$username_err = $password_err = $email_err = $full_name_err = $clearance_certificate_err = $certificate_number_err = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Check if username already exists in the database
        $sql = "SELECT doctor_id FROM doctors WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = trim($_POST["username"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $username_err = "This username is already taken.";
        } else {
            $username = trim($_POST["username"]);
        }
        mysqli_stmt_close($stmt);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email address.";
    } else {
        $email = trim($_POST["email"]);
        // Check if email already exists in the database
        $sql = "SELECT doctor_id FROM doctors WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $email_err = "This email address is already taken.";
        }
        mysqli_stmt_close($stmt);
    }

    // Validate full name
    if (empty(trim($_POST["full_name"]))) {
        $full_name_err = "Please enter your full name.";
    } else {
        $full_name = trim($_POST["full_name"]);
    }

    // Validate certificate number
    if (empty(trim($_POST["certificate_number"]))) {
        $certificate_number_err = "Please enter the certificate number.";
    } else {
        $certificate_number = trim($_POST["certificate_number"]);
    }

    // Validate and upload clearance certificate
if (!empty($_FILES["clearance_certificate"]["name"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["clearance_certificate"]["name"]);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Allowed file types
    $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf");

    if (in_array($fileType, $allowed_types)) {
        if ($fileType == "pdf") {
            // Handle PDF file
            if (move_uploaded_file($_FILES["clearance_certificate"]["tmp_name"], $target_file)) {
                $clearance_certificate = $target_file;
            } else {
                $clearance_certificate_err = "Sorry, there was an error uploading your file.";
            }
        } else {
            // Handle image file
            $check = getimagesize($_FILES["clearance_certificate"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["clearance_certificate"]["tmp_name"], $target_file)) {
                    $clearance_certificate = $target_file;
                } else {
                    $clearance_certificate_err = "Sorry, there was an error uploading your file.";
                }
            } else {
                $clearance_certificate_err = "File is not a valid image.";
            }
        }
    } else {
        $clearance_certificate_err = "Please upload a PDF or an image file (jpg, jpeg, png, gif).";
    }
} else {
    $clearance_certificate_err = "Please upload a clearance certificate.";
}

    // If no errors, insert the doctor into the database
    if (empty($username_err) && empty($password_err) && empty($email_err) && empty($full_name_err) && empty($clearance_certificate_err) && empty($certificate_number_err)) {
        $sql = "INSERT INTO doctors (username, password, email, full_name, clearance_certificate, certificate_number, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssss", $param_username, $param_password, $param_email, $param_full_name, $param_clearance_certificate, $param_certificate_number);
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;
            $param_full_name = $full_name;
            $param_clearance_certificate = $clearance_certificate;
            $param_certificate_number = $certificate_number;

            if (mysqli_stmt_execute($stmt)) {
                echo '<div class="alert alert-success" role="alert">Doctor registered successfully. Your status is pending approval.</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Something went wrong. Please try again later.</div>';
            }
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Registration</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-5">
        <h2 class="text-center">Doctor Registration</h2>
        <p class="text-center">Please fill in your details to register as a doctor.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control <?php echo (!empty($full_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $full_name; ?>">
                <span class="invalid-feedback"><?php echo $full_name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Certificate Number</label>
                <input type="text" name="certificate_number" class="form-control <?php echo (!empty($certificate_number_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $certificate_number; ?>">
                <span class="invalid-feedback"><?php echo $certificate_number_err; ?></span>
            </div>
            <div class="form-group">
                <label>Clearance Certificate</label>
                <input type="file" name="clearance_certificate" class="form-control-file <?php echo (!empty($clearance_certificate_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $clearance_certificate_err; ?></span>
            </div>
            <div class="form-group text-center">
                <input type="submit" class="btn btn-primary" value="Register">
            </div>
        </form>

        <p class="text-center">Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
