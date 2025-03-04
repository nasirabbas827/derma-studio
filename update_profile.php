<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Fetch user details from the database
$sql = "SELECT id, first_name, last_name, username, email, mobile, cnic, state, city, age FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $fetched_id, $first_name, $last_name, $username, $email, $mobile, $cnic, $state, $city, $age);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Update user profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newFirstName = $_POST["first_name"];
    $newLastName = $_POST["last_name"];
    $newUsername = $_POST["username"];
    $newEmail = $_POST["email"];
    $newMobile = $_POST["mobile"];
    $newCnic = $_POST["cnic"];
    $newState = $_POST["state"];
    $newCity = $_POST["city"];
    $newAge = $_POST["age"];

    // Update user details in the database
    $update_query = "UPDATE users 
                     SET first_name = ?, last_name = ?, username = ?, email = ?, mobile = ?, cnic = ?, state = ?, city = ?, age = ? 
                     WHERE id = ?";
    
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "ssssssssii", $newFirstName, $newLastName, $newUsername, $newEmail, $newMobile, $newCnic, $newState, $newCity, $newAge, $user_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        echo "<div class='alert alert-success'>Profile updated successfully!</div>";
        // Update session data if needed
        $_SESSION["username"] = $newUsername;
    } else {
        echo "<div class='alert alert-danger'>Error updating profile: " . mysqli_error($conn) . "</div>";
    }

    mysqli_stmt_close($update_stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h2 class="text-center">Update Profile</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="mobile">Mobile #:</label>
                    <input type="text" class="form-control" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>" required>
                </div>
                <div class="form-group">
                    <label for="cnic">CNIC #:</label>
                    <input type="text" class="form-control" name="cnic" value="<?php echo htmlspecialchars($cnic); ?>" required>
                </div>
                <div class="form-group">
                    <label for="state">State:</label>
                    <input type="text" class="form-control" name="state" value="<?php echo htmlspecialchars($state); ?>" required>
                </div>
                <div class="form-group">
                    <label for="city">City:</label>
                    <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($city); ?>" required>
                </div>
                <div class="form-group">
                    <label for="age">Age:</label>
                    <input type="number" class="form-control" name="age" value="<?php echo htmlspecialchars($age); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
