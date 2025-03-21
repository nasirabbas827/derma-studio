<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if service_id is provided in the URL
if (!isset($_GET['service_id'])) {
    header("Location: view_services.php");
    exit;
}

// Fetch service details
$service_id = $_GET['service_id'];
$sql = "SELECT * FROM services WHERE service_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $service_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$existing_image = $row['image']; // Store existing image

// Update service details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = $_POST['service_name'];
    $description = $_POST['description'];
    $image_path = $existing_image;

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "services/";
        $image_name = basename($_FILES["image"]["name"]);
        $image_path = $target_dir . time() . "_" . $image_name; // Unique filename
        $image_type = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));

        // Validate image type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($image_type, $allowed_types)) {
            echo "<script>alert('Invalid file type. Only JPG, PNG, and GIF allowed.');</script>";
        } else {
            // Delete old image if exists
            if (!empty($existing_image) && file_exists($existing_image)) {
                unlink($existing_image);
            }
            move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
        }
    }

    // Update database
    $sql_update = "UPDATE services SET service_name = ?, description = ?, image = ? WHERE service_id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "sssi", $service_name, $description, $image_path, $service_id);
    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);

    header("Location: view_services.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Service</title>
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

    <h2>Edit Service</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="service_name">Service Name:</label>
            <input type="text" class="form-control" id="service_name" name="service_name" value="<?php echo htmlspecialchars($row['service_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($row['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Service Image:</label>
            <input type="file" class="form-control" id="image" name="image">
            <br>
            <?php if (!empty($existing_image)): ?>
                <img src="<?php echo htmlspecialchars($existing_image); ?>" alt="Current Image" width="100">
            <?php else: ?>
                <p>No image uploaded.</p>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Update Service</button>
    </form>
</div>
</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
