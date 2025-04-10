<?php


// Retrieve user session variables
$userIdDb = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
$fullname  = isset($_SESSION['login_session']) ? $_SESSION['login_session'] : 'Guest';
$userAccess = isset($_SESSION['isadmin']) ? $_SESSION['isadmin'] : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicon -->
    <link rel="icon" href="/billing-system/Task_tracker/include/task.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Tasks & Planning & Progress</title>
</head>
<body>
<nav class="navbar navbar-expand navbar-dark bg-dark static-top">
    <a class="navbar-brand mr-1" href="">Tasks & Planning & Progress</a>

    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
        <i class="fas fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
        <!-- Improved Info Box Visibility -->
        <li class="nav-item">
            <a href="infobox_pms.php" class="info-box1" id="infoBoxLink1" style="display: flex; align-items: center; margin-right: 15px;">
                <i class='bx bxs-info-circle bx-tada' style='font-size: 30px; color: #FFD700;'></i>

            </a>
        </li>

        <!-- User Info Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle fa-fw" style="color: #007bff;"></i>
                <span class="ml-1"><?php echo htmlspecialchars($fullname); ?></span>
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item text-dark"><b>Welcome:</b> <?php echo htmlspecialchars($fullname); ?></a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
</body>


<!-- Include jQuery, Popper.js, and Bootstrap JS for dropdown functionality -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
