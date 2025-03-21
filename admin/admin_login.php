<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT id FROM admins WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $_SESSION["usertype"] = "admin";
        $_SESSION["username"] = $username;
        $stmt->bind_result($admin_id);
        $stmt->fetch();
        $_SESSION["userid"] = $admin_id;
        header("Location: admin_home.php"); 
        exit;
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<style>
    /* Navbar Styling */
.navbar.navbar-dark.bg-dark {
    background: linear-gradient(135deg, #2c3e50, #3498db) !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 0.7rem 1rem;
}

/* Brand styling */
.navbar-brand {
    font-weight: 700;
    letter-spacing: 0.5px;
    font-size: 1.3rem;
    position: relative;
    padding-left: 28px;
}

/* Add medical icon before brand */
.navbar-brand:before {
    content: "\f7f2"; /* Font Awesome clinic icon */
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.2rem;
}

/* Navigation links */
.navbar-dark .navbar-nav .nav-link {
    color: rgba(255, 255, 255, 0.85);
    padding: 0.8rem 1rem;
    transition: all 0.3s ease;
    position: relative;
    font-weight: 500;
}

/* Hover effect for links */
.navbar-dark .navbar-nav .nav-link:hover {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.1);
}

/* Active link styling */
.navbar-dark .navbar-nav .active > .nav-link,
.navbar-dark .navbar-nav .nav-link.active,
.navbar-dark .navbar-nav .nav-link.show,
.navbar-dark .navbar-nav .show > .nav-link {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.15);
    font-weight: 600;
}

/* Add bottom border on hover and active */
.navbar-dark .navbar-nav .nav-link:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 3px;
    background-color: #ffffff;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.navbar-dark .navbar-nav .nav-link:hover:after,
.navbar-dark .navbar-nav .active > .nav-link:after,
.navbar-dark .navbar-nav .nav-link.active:after {
    width: 70%;
}

/* Toggler button styling */
.navbar-dark .navbar-toggler {
    border-color: rgba(255, 255, 255, 0.2);
    padding: 0.4rem 0.6rem;
    transition: all 0.3s ease;
}

.navbar-dark .navbar-toggler:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.navbar-dark .navbar-toggler:focus {
    outline: none;
}

/* Container for better spacing on larger screens */
@media (min-width: 992px) {
    .navbar > .container,
    .navbar > .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    /* Add space between nav items */
    .navbar-nav .nav-item {
        margin: 0 2px;
    }
}

/* Mobile styling */
@media (max-width: 991.98px) {
    .navbar-collapse {
        background-color: rgba(44, 62, 80, 0.97);
        padding: 1rem;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }
    
    .navbar-dark .navbar-nav .nav-link {
        padding: 0.7rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .navbar-dark .navbar-nav .nav-item:last-child .nav-link {
        border-bottom: none;
    }
    
    .navbar-dark .navbar-nav .nav-link:after {
        display: none;
    }
}
</style>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="../index.php">Derma Elixir Studio</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
</nav>

    <div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">

        <form class="login-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2 class="mb-4">Admin Login</h2>
            <?php if (isset($error)) { ?>
                <p class="text-danger"><?php echo $error; ?></p>
            <?php } ?>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
    </div>
    </div>

    <!-- Add Bootstrap JS scripts at the end of the body -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
