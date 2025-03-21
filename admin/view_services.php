<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Delete service if service_id is provided in the URL
if (isset($_GET['delete_service_id'])) {
    $delete_service_id = $_GET['delete_service_id'];

    // Fetch image path before deleting
    $sql_image = "SELECT image FROM services WHERE service_id = ?";
    $stmt_image = mysqli_prepare($conn, $sql_image);
    mysqli_stmt_bind_param($stmt_image, "i", $delete_service_id);
    mysqli_stmt_execute($stmt_image);
    mysqli_stmt_bind_result($stmt_image, $image_path);
    mysqli_stmt_fetch($stmt_image);
    mysqli_stmt_close($stmt_image);

    // Delete the image file
    if (!empty($image_path) && file_exists($image_path)) {
        unlink($image_path);
    }

    // Delete the service from database
    $sql_delete = "DELETE FROM services WHERE service_id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $delete_service_id);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);

    header("Location: view_services.php");
    exit;
}

// Fetch services from database
$sql = "SELECT * FROM services";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Services</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-5">
<a href="add_services.php" class="m-3 float-right btn btn-success">Add Service</a>

    <h2>Services</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Service ID</th>
                <th>Service Name</th>
                <th>Description</th>
                <th>Image</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['service_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'services/default.png'; ?>" 
                             alt="Service Image" width="80" height="80">
                    </td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td><?php echo $row['updated_at']; ?></td>
                    <td>
                        <a href="edit_service.php?service_id=<?php echo $row['service_id']; ?>" class="btn btn-primary btn-sm mb-2">Edit</a>
                        <a href="?delete_service_id=<?php echo $row['service_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
