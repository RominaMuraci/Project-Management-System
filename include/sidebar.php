<?php
// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Assuming you store user data in session after login
$userIdDb = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
$fullname = isset($_SESSION['login_session']) ? $_SESSION['login_session'] : '';
// $userAccess = $_SESSION['isadmin'];


// $allowedAdmins = ['admin@protech.com.al', 'erjonbejleri@protech.com.al'];
$allowedAdmins = ['admin@protech.com.al', 'erjonbejleri@protech.com.al','rominamuraci@protech.com.al'];
$username=$_SESSION['useremail'];
$isAdmin = in_array($username, $allowedAdmins);


// Redirect to login page if the user is not authenticated
if (!isset($_SESSION['login_session']) || !isset($_SESSION['userid'])) {
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks & Planning & Progress</title>
    <link href="css/sb-admin.css" rel="stylesheet">

    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- <link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet" /> -->

    <!-- <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script> -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">



    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>



        /* Sidebar toggled state */
        .sidebar.toggled {
            width: 80px;
        }

        /* Navigation links */
        .sidebar .nav-link {
            color: #dfe6ed;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .sidebar .nav-link:hover {
            background-color: rgb(46, 102, 153); /* Slightly brighter hover color */
            color: #fff;
        }

        /* Active link styling */
        .sidebar .nav-item.active > .nav-link {
            background-color: rgb(39, 130, 228);
            color: #fff;
        }

        /* Icons and labels */
        .sidebar i {
            margin-right: 15px;
            font-size: 1.3rem;
        }

        .sidebar.toggled .nav-link span {
            display: none;
        }

        .sidebar.toggled i {
            margin: auto;
            font-size: 1.5rem;
        }

        /* Sidebar footer (optional) */
        .sidebar-footer {
            margin-top: auto;
            padding: 15px;
            background-color: rgb(24, 45, 75);
            text-align: center;
            font-size: 0.85rem;
        }

        .sidebar-footer a {
            color: #dfe6ed;
            text-decoration: none;
        }


        /* Adjust content wrapper when sidebar is toggled */
        .sidebar.toggled + #content-wrapper {
            margin-left: 80px;
        }

        /* Scrollbar styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
        }

        .sidebar .nav-link i {
            color:rgb(130, 198, 253); /* Gold icon color */
            transition: color 0.3s ease; /* Smooth color transition */
        }

        .sidebar .nav-link:hover i {
            color:rgb(248, 215, 29); /* Red color on hover */
        }

        .sidebar .nav-item.active > .nav-link i {
            color:rgb(247, 213, 24); /* Green color for active links */
        }

    </style>

    </style>
</head>
<body>
<div id="wrapper">
    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">

        <li class="nav-item">
            <a class="nav-link" href="projects.php">
                <i class="fas fa-fw fa-folder"></i>
                <span>Projects</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="progress_hours_assign.php">
                <i class="fas fa-fw fa-spinner"></i>
                <span> Hours worked </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="archived_projects.php">
                <i class="fas fa-archive"></i>
                <span>  Archived Projects </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="to_do_tasks.php">
                <i class="fas fa-fw fa-tasks"></i>
                <span>TO DO</span>
            </a>
        </li>
        <?php if ($isAdmin): ?>
            <li class="nav-item">
                <a class="nav-link" href="members.php">
                    <i class="fas fa-users"></i> <!-- User Group Icon -->
                    <span>Members</span>
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link" href="../index.php">
                <i class="fas fa-fw fa-user"></i>
                <span> Billing system</span>
            </a>
        </li>


    </ul>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Load Moment.js (before datetime-moment.js) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<!-- Load DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Alertify.js CSS -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" /> -->

<!-- Alertify.js JavaScript -->
<!-- <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin.min.js"></script>
</body>
</html>
