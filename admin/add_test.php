<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Initialize variables
$test_name = $description = '';
$test_name_err = $description_err = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate test name
    if (empty(trim($_POST["test_name"]))) {
        $test_name_err = "Please enter the test name.";
    } else {
        $test_name = trim($_POST["test_name"]);
    }

    // Validate description
    $description = trim($_POST["description"]);

    // Check for errors before inserting into database
    if (empty($test_name_err) && empty($description_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO tests (test_name, description) VALUES (?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_test_name, $param_description);

            // Set parameters
            $param_test_name = $test_name;
            $param_description = $description;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to view tests page
                header("location: view_tests.php");
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Test</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-5">
    <h2>Add Test</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Test Name</label>
            <input type="text" name="test_name" class="form-control <?php echo (!empty($test_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $test_name; ?>">
            <span class="invalid-feedback"><?php echo $test_name_err; ?></span>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control"><?php echo $description; ?></textarea>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="view_tests.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
