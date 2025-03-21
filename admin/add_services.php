<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Initialize variables
$service_name = $description = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = $_POST['service_name'];
    $description = $_POST['description'];
    $image_path = '';

    // Handle file upload
    if (!empty($_FILES["service_image"]["name"])) {
        $target_dir = "services/"; // Folder to store images
        $image_name = basename($_FILES["service_image"]["name"]);
        $target_file = $target_dir . time() . "_" . $image_name; // Unique name
        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        if (in_array($image_type, $allowed_types)) {
            if (move_uploaded_file($_FILES["service_image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
            }
        }
    }

    // Insert service into database
    $sql = "INSERT INTO services (service_name, description, image, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $service_name, $description, $image_path);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect to services list page
    header("Location: view_services.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Service</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-5">
<div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">

    <h2>Add Service</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="service_name">Service Name:</label>
            <input type="text" class="form-control" id="service_name" name="service_name" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="service_image">Service Image:</label>
            <input type="file" class="form-control" id="service_image" name="service_image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Add Service</button>
    </form>
</div>
</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
