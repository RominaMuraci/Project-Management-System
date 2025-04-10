<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

if($_SESSION['isclient']){
    header("Location: ../clientbalance.php");
}
// Define the root directory and include the database connection
$rootDir = __DIR__ . '../../';
$conn = include($rootDir . 'config/connection.php');

// Check database connection
if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Fetch user data from session
$userIdDb = $_SESSION['userid'];
$fullname = $_SESSION['login_session'];


$allowedAdmins = ['muraciromina@gmail.com'];
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

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <?php include('include/header.php');?>
    <style>

        .progress-bar-container {
            width: 100%;
            background-color: #e0e0e0;
            border-radius: 5px;
            height: 20px;
        }

        .progress-bar {
            height: 100%;
            border-radius: 5px;
            background-color: #4caf50; /* Green color */
        }
        /* Blue Rectangular Button - Smaller Version */
        .button-blue-rect {
            appearance: none;
            background-color: #58b0e5;
            border-radius: 0.25rem; /* Reduced border-radius for a smaller look */
            border-style: none;
            box-shadow: #074977 0 -4px 4px inset; /* Reduced shadow for smaller button */
            box-sizing: border-box;
            color: #ffffff;
            cursor: pointer;
            display: inline-block;
            font-family: -apple-system, sans-serif;
            font-size: 0.875rem; /* Decreased font size */
            font-weight: 700;
            margin: 0;
            outline: none;
            padding: 0.25rem 0.5rem 0.5rem 0.5rem; /* Reduced padding */
            text-align: center;
            text-decoration: none;
            transition: all 0.15s;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
        }

        .button-blue-rect:hover {
            background-color: #5fd7f1;
            box-shadow: #08657a 0 -4px 4px inset; /* Adjusted hover shadow */
            transform: scale(1.1); /* Slightly reduced hover scaling */
        }

        .button-blue-rect:active {
            transform: scale(1.02); /* Reduced active scaling */
        }
        /* Yellow Rectangular Button - Smaller Version */
        .button-yellow-rect {
            appearance: none;
            background-color: #f5c542;
            border-radius: 0.25rem; /* Reduced border-radius for a smaller look */
            border-style: none;
            box-shadow: #a37400 0 -4px 4px inset; /* Adjusted shadow for yellow */
            box-sizing: border-box;
            color: #ffffff;
            cursor: pointer;
            display: inline-block;
            font-family: -apple-system, sans-serif;
            font-size: 0.875rem; /* Decreased font size */
            font-weight: 700;
            margin: 0;
            outline: none;
            padding: 0.25rem 0.5rem 0.5rem 0.5rem; /* Reduced padding */
            text-align: center;
            text-decoration: none;
            transition: all 0.15s;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
        }

        .button-yellow-rect:hover {
            background-color: #ffd64f;
            box-shadow: #b98b00 0 -4px 4px inset; /* Adjusted hover shadow */
            transform: scale(1.1); /* Slightly reduced hover scaling */
        }

        .button-yellow-rect:active {
            transform: scale(1.02); /* Reduced active scaling */
        }



        /* Red Rectangular Button - Smaller Version */
        .button-red-rect {
            appearance: none;
            background-color: #ff3131;
            border-radius: 0.25rem; /* Reduced border-radius for a smaller look */
            border-style: none;
            box-shadow: #a20505 0 -4px 4px inset; /* Reduced shadow for smaller button */
            box-sizing: border-box;
            color: #ffffff;
            cursor: pointer;
            display: inline-block;
            font-family: -apple-system, sans-serif;
            font-size: 0.875rem; /* Decreased font size */
            font-weight: 700;
            margin: 0;
            outline: none;
            padding: 0.25rem 0.5rem 0.5rem 0.5rem; /* Reduced padding */
            text-align: center;
            text-decoration: none;
            transition: all 0.15s;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
        }

        .button-red-rect:hover {
            background-color: #fc5151;
            box-shadow: #d00505 0 -4px 4px inset; /* Adjusted hover shadow */
            transform: scale(1.1); /* Slightly reduced hover scaling */
        }

        .button-red-rect:active {
            transform: scale(1.02); /* Reduced active scaling */
        }

        /* Smaller Green Rectangular Button with Bottom Margin */
        .button-green-rect {
            appearance: none;
            background-color: rgb(6, 150, 6);
            border-radius: 0.25rem;  /* Smaller border radius */
            border-style: none;
            box-shadow: rgb(12, 150, 12) 0 -4px 4px inset; /* Adjusted shadow size */
            box-sizing: border-box;
            color: #ffffff;
            cursor: pointer;
            display: inline-block;
            font-family: -apple-system, sans-serif;
            font-size: 0.875rem; /* Smaller font size */
            font-weight: 700;
            margin: 0;
            outline: none;
            padding: 0.25rem 0.75rem; /* Reduced padding */
            text-align: center;
            text-decoration: none;
            transition: all 0.15s;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            margin-bottom: 0.5rem; /* Added bottom margin */
        }

        .button-green-rect:hover {
            background-color: rgb(5, 156, 5);
            box-shadow: rgb(5, 133, 5) 0 -4px 4px inset; /* Adjusted shadow size */
            transform: scale(1.1);  /* Slightly reduced hover scaling */
        }

        .button-green-rect:active {
            transform: scale(1.05);  /* Slightly reduced active scaling */
        }


        /* Modal Custom Styles */
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        .modal-header {
            border-bottom: none;
        }

        .modal-footer {
            border-top: none;
        }


        .comment-count {
            font-size: 14px;
            padding: 5px 10px;
            background-color: #007bff; /* Primary color for the badge */
            color: white; /* Text color */
            border-radius: 12px;
            display: inline-block;
        }

        .button-group {
            display: flex;
            gap: 10px; /* Ensures space between buttons */
            justify-content: flex-start; /* Align buttons to the left */
            margin-top: 10px; /* Space above the buttons */
        }

        .custom-datepicker-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .custom-small-datepicker input {
            flex-grow: 1;
            margin-right: 10px;
        }

        .custom-datepicker:focus {
            outline: none;
            border-color: #2196F3;
            box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.2);
        }

        .add-date-btn {
            padding: 8px 12px;
            font-size: 10px;
            background-color: #0066cc;
            color: white;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-date-btn:hover {
            background-color: #004c99;
        }

        .add-date-btn i {
            margin-right: 5px;
        }

        /* Optional: Add some hover effect to the entire container */
        .custom-datepicker-container:hover {
            background-color: #f0f0f0;
            border-radius: 4px;
        }




        /* Table Styles */
        .table-container {
            margin-top: 1.25rem; /* 20px */
            background-color: #f4f6f9; /* Light background for contrast */
            border-radius: 0.625rem; /* 10px */
            box-shadow: 0 0 0.625rem rgba(0, 0, 0, 0.1); /* Soft shadow (10px) */
        }

        .table-responsive {
            overflow-x: auto; /* Ensure responsiveness */
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 0.625rem 0.75rem; /* 10px 12px */
            text-align: center;
            border: 0.0625rem solid #dee2e6; /* 1px */
            font-size: 1rem; /* 16px */
            vertical-align: middle;
        }



        .table input[type="text"] {
            width: 100%;

        }


    </style>
</head>
<body >
<div id="wrapper">
    <?php include('include/sidebar.php');?>
    <div id="content-wrapper">

        <div class="container-fluid">

            <!-- Breadcrumbs-->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">Dashboard</a>
                </li>
                <li class="breadcrumb-item active"> Projects,Tasks, Planning</li>
            </ol>

            <div id="alert-container"></div>



            <button class="button-green-rect" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                <i class="fas fa-plus"></i> Add Project
            </button>
            
            <button class="button-blue-rect dropdown-toggle" type="button" id="selectMemberDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user"></i> Select Member
            </button>
            <ul class="dropdown-menu" id="memberList" aria-labelledby="selectMemberDropdown">
                <li><a class="dropdown-item" href="#" onclick="filterProjects('')">All Members</a></li>
            </ul>




            <div class="table-responsive">
                <table id="Projects" class="table table-striped table-bordered ">
                    <thead>
                    <tr>
                        <th>Project Name / Task name</th>
                        <th>Kick-off date</th>
                        <th>Planned duration</th>
                        <th>Finished date </th>
                        <th>Weeks</th>
                        <th>Total Hours</th>
                        <th>Status</th>
                        <th>Created by  </th>
                        <th> Comments  </th>
                        <th class="actions-column">Actions</th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th><input type="text" placeholder="Search Project Name" /></th>
                        <th><input type="date" placeholder="Search Kick-off date" /></th>
                        <th><input type="date" placeholder="Search Planned duration" /></th>
                        <th><input type="date" placeholder="Search Finished date " /></th>
                        <th><input type="text" placeholder="Search Weeks " /></th>
                        <th><input type="text" placeholder="Search Total Hours " /></th>
                        <th><input type="text" placeholder="Search Status " /></th>
                        <th><input type="text" placeholder="Search Created by " /></th>
                        <th><input type="text" placeholder="Search Comments " /></th>

                        <th class="actions-column"></th> <!-- Empty for Actions column -->
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <!-- /.container-fluid -->

        <!-- Sticky Footer -->
        <?php include('include/footer.php');?>

    </div>
    <!-- /.content-wrapper -->

</div>
<!-- /#wrapper -->

<!-- Scroll to Top Button-->
<!-- <a class="scroll-to-top rounded" href="#page-top">
  <i class="fas fa-angle-up"></i>
</a> -->



<div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="addProjectModalLabel">Add New Project / Task</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <!-- Form for adding a new project/task -->
                <form id="addProjectForm" method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Project Name / Task Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Project name/ Task name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="kick_date" class="form-label">Kick-off Date</label>
                            <input type="date" class="form-control form-control-lg" id="kick_date" name="kick_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="planned_duration" class="form-label">Planned Duration</label>
                            <input type="date" class="form-control form-control-lg" id="planned_duration" name="planned_duration">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">   Close</button>
                        <button type="button" class="btn btn-primary" id="submitProjectBtn">Save Project</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="commentsModal" tabindex="-1" role="dialog" aria-labelledby="commentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentsModalLabel">Comments</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group comments-list">
                    <!-- Comments will be dynamically added here -->
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="editProjectModalLabel">Edit Project</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <!-- Form for editing project -->
                <form id="editProjectForm">
                    <input type="hidden" id="editProjectId">

                    <!-- Project Name -->
                    <div class="mb-3">
                        <label for="editProjectName" class="form-label">Project Name</label>
                        <input type="text" class="form-control form-control-lg" id="editProjectName" placeholder="Enter project name">
                    </div>

                    <!-- Kick-off Date -->
                    <div class="mb-3">
                        <label for="editKickOffDate" class="form-label">Kick-off Date</label>
                        <input type="date" class="form-control form-control-lg" id="editKickOffDate">
                    </div>

                    <!-- Planned Duration -->
                    <div class="mb-3">
                        <label for="editPlannedDuration" class="form-label">Planned Duration</label>
                        <input type="date" class="form-control form-control-lg" id="editPlannedDuration">
                    </div>

                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEditProjectBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {


        const isAdmin = <?php echo json_encode($isAdmin); ?>;

        // Initialize DataTable
        const table = $('#Projects').DataTable({
            processing: true,
            serverSide: false,
            paging: true,
            pageLength: 50,
            ajax: {
                url: 'fetch_projects.php',
                dataSrc: function (json) {
                    if (json.status === 'success') {

                        return json.data;
                    } else {
                        showAlert(`Error fetching data: ${json.message}`);
                        return [];
                    }
                },
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row) {
                        const finishDate = row.finished_date && row.finished_date !== "0000-00-00" ? new Date(row.finished_date) : null;
                        const plannedDuration = new Date(row.planned_duration);
                        let color = 'blue'; // Default color for "On Progress"

                        if (finishDate) {
                            color = finishDate > plannedDuration ? 'red' : 'green';
                        }

                        return `<span style="font-weight: bold; color: ${color};">${row.name}</span>`;
                    },
                },
                { data: 'kick_date' },
                { data: 'planned_duration' },
                {
                    data: 'finished_date',
                    render: function(data, type, row, meta) {
                        // If finished_date is already set, display it as plain text
                        if (data !== '0000-00-00') {
                            return `<span>${data}</span>`;  // Show the date as plain text
                        } else {
                            // Otherwise, show the datepicker and "Add" button
                            return `
            <div class="custom-datepicker-container">
                <input type="text"
                       class="datepicker custom-datepicker custom-small-datepicker"
                       value="${data !== '0000-00-00' ? data : ''}"
                       data-id="${row.id}"
                       placeholder="Select Date" />
                <button class="button-blue-rect add-date-btn"
                        data-id="${row.id}">
                    <i class="fas fa-calendar-alt"></i> Add
                </button>
            </div>
            `;
                        }
                    },
                    createdCell: function(cell, cellData, rowData, row, col, settings) {
                        // If the finished_date is already set (not '0000-00-00'), no need to initialize the datepicker
                        if (cellData !== '0000-00-00') {
                            return; // If it's already set, don't show the datepicker
                        }

                        const $datepicker = $(cell).find('.datepicker');
                        const $addButton = $(cell).find('.add-date-btn');

                        // Initialize Flatpickr for datepicker
                        $datepicker.flatpickr({
                            dateFormat: "Y-m-d",
                        });

                        // Bind click event to the "Add" button
                        $addButton.off('click').on('click', function() {
                            const taskId = $(this).data('id');
                            const selectedDate = $datepicker.val(); // Get selected date

                            if (selectedDate) {
                                // Perform AJAX to update finished date
                                $.ajax({
                                    url: 'update_finished_date.php', // Update this URL if necessary
                                    type: 'POST',
                                    data: { id: taskId, finished_date: selectedDate },
                                    success: function(response) {
                                        const data = JSON.parse(response);

                                        if (data.status === 'success') {
                                            // Show success alert
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Finished Date Updated',
                                                text: 'Finished date has been added successfully!',
                                            }).then(() => {
                                                // Reload the page after the user closes the success alert
                                                location.reload();
                                            });

                                            // Find the cell where the datepicker is
                                            const $cell = $addButton.closest('td');

                                            // Replace datepicker with plain text
                                            $cell.html(`<span>${selectedDate}</span>`);

                                            // Optionally, remove or hide the add button if you no longer want it
                                            $addButton.remove();
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'Error: ' + data.message,
                                            });
                                        }
                                    }
                                });
                            } else {
                                // Show SweetAlert for warning if no date is selected
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Please select a date',
                                    text: 'Warning: Please select a date before submitting.',
                                });
                            }

                        });
                    }
                },

                {
                    data: null,
                    render: function (data, type, row) {
                        const kickDate = new Date(row.kick_date);
                        const finishDate = (row.finished_date && row.finished_date !== "0000-00-00") ? new Date(row.finished_date) : null;

                        // Check if finished_date is valid
                        if (!finishDate) {
                            return ''; // Display "On Progress" if finished_date is not valid
                        }

                        const totalWeeks = Math.ceil((finishDate - kickDate) / (7 * 24 * 60 * 60 * 1000));
                        let weekBlocks = '';

                        for (let i = 1; i <= totalWeeks; i++) {
                            // Add a unique color for each week
                            const color = '#4caf50'; // You can customize this color
                            weekBlocks += `
                <span style="
                    display: inline-block;
                    padding: 5px 10px;
                    margin: 2px;
                    font-size: 12px;
                    color: #fff;
                    background-color: ${color};
                    border-radius: 5px;
                ">
                    Week ${i}
                </span>
            `;
                        }

                        return `<div style="display: flex; flex-wrap: wrap;">${weekBlocks}</div>`;
                    },
                },

                {
                    data: 'total_hours',
                    render: function(data) {
                        return `<span>${data} hrs</span>`;
                    }
                },


                {
                    data: null,
                    render: function (data, type, row) {
                        const finishDate = (row.finished_date && row.finished_date !== "0000-00-00") ? new Date(row.finished_date) : null;
                        const plannedDuration = new Date(row.planned_duration);

                        if (!finishDate) {
                            return `<span style="color: blue;">On Progress</span>`;
                        } else if (finishDate > plannedDuration) {
                            return `<span style="color: red;">Beyond Deadline</span>`;
                        } else {
                            return `<span style="color: green;">Completed Earlier</span>`;
                        }
                    },
                },
                {
                    data: 'created_by'
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
<div>
    <!-- Comments Container (initially hidden) -->
    <div style="max-height: 150px; overflow: auto; display: none;" class="comments-container" data-id="${row.id}">
        <p>Loading comments...</p>
    </div>

    <!-- Comment Count Badge (first row) -->
    <div style="margin-bottom: 10px;">
        <span class="badge badge-primary comment-count" data-id="${row.id}" style="cursor: pointer;" title="Hover to see comments">
        </span>
    </div>

    <!-- Buttons (second row) -->
    <div class="button-group">
        <button class="button-blue-rect toggle-comment-btn" data-bs-toggle="collapse" data-bs-target="#collapseComment${row.id}">
            <i class="fas fa-plus"></i>
        </button>
    </div>

    <!-- Collapse for adding comments -->
    <div id="collapseComment${row.id}" class="collapse mt-2">
        <textarea class="form-control comment-textarea" rows="2" placeholder="Enter your comment here..."></textarea>
        <div class="button-group mt-2">
            <button class="button-blue-rect submit-comment-btn" data-id="${row.id}">
                <i class="fas fa-plus"></i> Add
            </button>
        </div>
    </div>
</div>



        `;
                    },

                    createdCell: function (td, cellData, rowData) {
                        const projectId = rowData.id; // Get the project ID from the row data
                        const commentCountBadge = $(td).find('.comment-count');

                        // Fetch comments for the specific project
                        $.ajax({
                            url: 'fetch_comments.php', // Endpoint for fetching comments
                            type: 'GET',
                            dataType: 'json', // Expect JSON response
                            data: { project_id: projectId },
                            success: function (response) {
                                if (response.status === 'success' && Array.isArray(response.data)) {
                                    const commentCount = response.data.length;

                                    // Update comment count badge
                                    commentCountBadge.text(`${commentCount} ${commentCount === 1 ? 'comment' : 'comments'}`);
                                    commentCountBadge.attr('title', `${commentCount} ${commentCount === 1 ? 'comment' : 'comments'}`);

                                    // Show modal when badge is clicked
                                    commentCountBadge.off('click').on('click', function () {
                                        openCommentsModal(projectId, response.data); // Open modal with comments
                                    });
                                } else {
                                    // No comments available
                                    commentCountBadge.text('0 comments');
                                    commentCountBadge.attr('title', 'No comments yet');

                                    // Show empty modal when badge is clicked
                                    commentCountBadge.off('click').on('click', function () {
                                        openCommentsModal(projectId, []); // Open modal with no comments
                                    });
                                }
                            },
                            error: function () {
                                // Show error on badge
                                commentCountBadge.text('Error');
                                commentCountBadge.attr('title', 'Error loading comments');

                                // Show empty modal on error
                                commentCountBadge.off('click').on('click', function () {
                                    openCommentsModal(projectId, [], 'Error loading comments');
                                });
                            }
                        });
                    }

                },
                {
                    data: null,
                    title: 'Actions',
                    render: function (data, type, row) {
                        let isArchived = row.archived == 1;
                        let buttons = '';

                        // Only display buttons if admin and the project is not archived
                        if (isAdmin && !isArchived) {
                            buttons += `
        <button title="Archive" class="button-yellow-rect archive-button" data-id="${row.id}">
          <i class="fas fa-archive"></i>
        </button>
        <button title="Edit" class="button-blue-rect edit-btn"
          data-id="${row.id}"
          data-name="${row.name}"
          data-kickoff="${row.kick_date}"
          data-duration="${row.planned_duration}">
          <i class="fas fa-edit"></i>
        </button>
        <button title="Delete" class="button-red-rect delete-btn" data-id="${row.id}">
          <i class="fas fa-trash-alt"></i>
        </button>
      `;
                        }
                        return buttons;
                    },
                    // The column is only visible if the user is an admin
                    visible: isAdmin
                }

            ],
            order: [[1, 'desc']],
        });

        if (!isAdmin) {
            // Hide the header
            $('.actions-column').hide();
            // Hide all cells in the actions column
        }


// Function to get URL parameters
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(window.location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

// Get "name" parameter from URL
        var projectName = getUrlParameter('name');

// Apply filter if projectName exists
        if (projectName) {
            setTimeout(function () {
                $('#Projects_filter input').val(projectName).trigger('keyup'); // Apply search filter
            }, 500); // Delay to ensure DataTable loads first
        }

// Function to archive a project using SweetAlert for confirmation and fetch for AJAX
        function archiveProject(projectId) {
            Swal.fire({
                title: "Are you sure?",
                text: "You are about to archive this project!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, archive it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('update_archived_projects.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `project_id=${projectId}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Archived!',
                                    text: 'Project has been archived successfully.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload(); // Refresh page
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'There was an error archiving the project. Please try again.'
                            });
                        });
                }
            });
        }

// Delegated event binding for the archive button (works for dynamically added elements)
        $(document).on('click', '.archive-button', function () {
            let projectId = $(this).data('id');
            archiveProject(projectId);
        });



        $('#submitProjectBtn').on('click', function () {
            // Retrieve specific form field values for validation
            const plannedDuration = $('#planned_duration').val(); // Match ID in your modal
            const kickOffDate = $('#kick_date').val(); // Match ID in your modal

            // Ensure dates are valid and properly compared
            const plannedDate = new Date(plannedDuration);
            const kickOffDateParsed = new Date(kickOffDate);

            if (!isNaN(plannedDate) && !isNaN(kickOffDateParsed)) {
                // Validate that Planned Duration is greater than or equal to the Kick-Off Date
                if (plannedDate >= kickOffDateParsed) {
                    // Get the form data
                    const formData = $('#addProjectForm').serialize();

                    $.ajax({
                        url: 'add_projects.php', // This is the PHP file to handle the form data
                        method: 'POST',
                        data: formData,
                        success: function (response) {
                            try {
                                // Parse the response from PHP (if it's JSON)
                                const data = JSON.parse(response);

                                // Check the status from the server response
                                if (data.status === 'success') {
                                    // Show success message using SweetAlert
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Project added successfully!',
                                        text: 'The project/task was added successfully to the system.',
                                    }).then(() => {
                                        // Reload the page after the user closes the success alert
                                        location.reload();
                                    });

                                    // Clear form and close modal
                                    $('#addProjectModal').modal('hide');
                                    $('#addProjectForm')[0].reset();
                                } else {
                                    // Handle server error message and additional info (like failed emails)
                                    let errorMessage = 'Error: ' + data.message;

                                    // If there are failed recipients, include them in the error message
                                    if (data.failed_recipients && data.failed_recipients.length > 0) {
                                        errorMessage += '\nFailed to send email to the following recipients: ' + data.failed_recipients.join(', ');
                                    }

                                    // Show error message using SweetAlert
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: errorMessage,
                                    });
                                }
                            } catch (e) {
                                console.error('JSON Parse Error:', e);
                                // If the response could not be parsed as JSON, show an error
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'There was an error processing the response from the server. Please try again.',
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX Error:', error);
                            // If the request fails, show a generic error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'There was an error submitting the form. Please try again.',
                            });
                        }
                    });


                } else {
                    // Show validation error using SweetAlert
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Planned Duration must be greater than or equal to the Kick-Off Date.',
                    });
                }
            } else {
                // Show error for invalid date formats
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Dates',
                    text: 'Please enter valid dates in the correct format (YYYY-MM-DD).',
                });
            }
        });


        function openCommentsModal(projectId, comments, errorMessage = null) {
            const modal = $('#commentsModal');
            const commentsList = modal.find('.comments-list');

            // Clear previous comments
            commentsList.empty();

            // Check for errors
            if (errorMessage) {
                commentsList.append(`<li class="list-group-item text-danger">${errorMessage}</li>`);
            } else if (comments.length === 0) {
                commentsList.append('<li class="list-group-item">No comments available.</li>');
            } else {
                // Populate the modal with comments
                comments.forEach((comment) => {
                    // Check if the user is an admin
                    const deleteButton = isAdmin ? `
        <button class="btn btn-danger btn-sm delete-comment-btn" data-id="${comment.id}">
            <i class="fas fa-trash"></i> Delete
        </button>
    ` : '';  // If not an admin, don't include the delete button

                    const commentHtml = `
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>${comment.comment} <small class="text-muted">(${comment.created_at})</small></span>
            ${deleteButton}  <!-- Conditionally include the delete button -->
        </li>
    `;
                    commentsList.append(commentHtml);
                });


                // Attach event listener to delete buttons
                commentsList.find('.delete-comment-btn').off('click').on('click', function () {
                    const commentId = $(this).data('id');
                    deleteComment(projectId, commentId, $(this).closest('li'));
                });
            }

            // Show the modal
            modal.modal('show');
        }

        function deleteComment(projectId, commentId, commentElement) {
            // Confirm with SweetAlert before deletion
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Do you really want to delete this comment?',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX request to delete the comment
                    $.ajax({
                        url: 'delete_comment.php', // Backend endpoint for deletion
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            project_id: projectId,
                            comment_id: commentId,
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                // Notify the user of the successful deletion
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'The comment has been deleted successfully.',
                                }).then(() => {
                                    // Reload the page after the success alert is closed
                                    location.reload();
                                });
                            } else {
                                // Handle backend error response
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete the comment.',
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            // Log the error to the console for debugging
                            console.error('Error:', error);

                            // Show an error alert
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting the comment. Please try again.',
                            });
                        },
                    });
                }
            });
        }


        $('#Projects thead input').on('keyup change', function () {
            const colIdx = $(this).parent().index();
            let searchValue = this.value;

            // Check if the column is 1 or 2, which will use date search
            if (colIdx === 1 || colIdx === 2 || colIdx === 3  ) {
                // Optional: You can format the date to a standard format if needed
                // For example, using moment.js or your custom date format logic
                searchValue = moment(searchValue, 'YYYY-MM-DD').format('YYYY-MM-DD'); // Customize date format if necessary
            }

            // Apply column search with the appropriate value
            table.column(colIdx).search(searchValue).draw();
        });
// Toggle comment input
        $(document).on('click', '.toggle-comment-btn', function () {
            const target = $(this).data('target');
            $(target).collapse('toggle');
        });

// Submit comment
        $(document).on('click', '.submit-comment-btn', function () {
            const projectId = $(this).data('id');
            const comment = $(this).closest('.collapse').find('.comment-textarea').val();

            if (!comment.trim()) {
                // Show SweetAlert for empty comment
                Swal.fire({
                    icon: 'warning',
                    title: 'Comment required',
                    text: 'Please enter a comment before submitting.',
                });
                return;
            }

            $.ajax({
                url: 'add_comments.php',
                method: 'POST',
                data: {
                    id: projectId,
                    comment: comment,
                },
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        // Show SweetAlert for successful comment submission
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Comment added successfully!',
                        });
                        table.ajax.reload();
                    } else {
                        // Show SweetAlert for error in comment submission
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error: ' + data.message,
                        });
                    }
                },
                error: function (xhr, status, error) {
                    // Show SweetAlert for AJAX error
                    Swal.fire({
                        icon: 'error',
                        title: 'An error occurred',
                        text: 'Error: ' + error,
                    });
                },
            });
        });


        $(document).on('click', '.delete-btn', function () {
            const projectId = $(this).data('id'); // Get the project ID to delete

            // Confirm with SweetAlert before deletion
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'Do you really want to delete this project?',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX request to delete the project
                    $.ajax({
                        url: 'delete_projects.php', // Your PHP file to delete the project
                        method: 'POST',
                        data: { id: projectId },
                        success: function (response) {
                            const data = JSON.parse(response);
                            if (data.status === 'success') {
                                // Show SweetAlert for successful deletion
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'The project has been deleted.',
                                });
                                // Reload table to reflect the changes
                                table.ajax.reload(); // Or remove the row directly if needed
                            } else {
                                // Show SweetAlert for error in deletion
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error: ' + data.message,
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            // Show SweetAlert for AJAX error
                            Swal.fire({
                                icon: 'error',
                                title: 'An error occurred',
                                text: 'Error: ' + error,
                            });
                        },
                    });
                }
            });
        });

// Open the Edit Project modal and populate the fields
        $(document).on('click', '.edit-btn', function () {
            const projectId = $(this).data('id');
            const projectName = $(this).data('name');
            const kickOffDate = $(this).data('kickoff'); // This should be 'data-kickoff'
            const plannedDuration = $(this).data('duration');

            // Set the values in the modal
            $('#editProjectId').val(projectId);
            $('#editProjectName').val(projectName);
            $('#editKickOffDate').val(kickOffDate); // This will now correctly get the kick-off date
            $('#editPlannedDuration').val(plannedDuration);

            // Show the modal
            $('#editProjectModal').modal('show');
        });


// Handle the Save button click
        $('#saveEditProjectBtn').on('click', function () {
            const projectId = $('#editProjectId').val();
            const projectName = $('#editProjectName').val();
            const kickOffDate = $('#editKickOffDate').val();
            const plannedDuration = $('#editPlannedDuration').val();

            // Send the data via AJAX to update the project
            $.ajax({
                url: 'edit_projects.php', // Backend script for updating the project
                type: 'POST',
                data: {
                    id: projectId,
                    name: projectName,
                    kickOffDate: kickOffDate,
                    plannedDuration: plannedDuration,
                },
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'The project has been updated successfully.',
                        });
                        $('#editProjectModal').modal('hide');
                        // Reload the table or the content
                        table.ajax.reload(); // You can customize this according to your table refresh method
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update the project. ' + (data.message || ''),
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the project.',
                    });
                },
            });
        });
        

    });
    $(document).on("click", ".dropdown-item", function () {
        let memberName = $(this).text(); // Get clicked member's name
        filterProjects(memberName);
    });

    function filterProjects(memberName) {
        console.log("Filtering projects by:", memberName);

        let table = $('#Projects').DataTable(); // Ensure your table ID is correct

        if (memberName === "" || memberName === "All Members") {
            // Reset filter to show all projects
            table.column(7).search("").draw();
        } else {
            // Apply exact match filtering for the selected member
            table.column(7).search("^" + memberName + "$", true, false).draw();
        }
    }



    function fetchMembers() {
        $.ajax({
            url: "fetch_members.php", // PHP script that returns the member list
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    let members = response.data;
                    let memberList = $("#memberList");

                    // Clear existing items (except "All Members")
                    memberList.empty(); // Ensure dropdown updates dynamically
                    memberList.append(`<li><a class='dropdown-item' href='#' onclick="filterProjects('')">All Members</a></li>`);

                    // Append new members dynamically
                    members.forEach(member => {
                        let memberName = member.fullname;
                        let listItem = `<li><a class='dropdown-item' href='#' onclick="filterProjects('${memberName}')">${memberName}</a></li>`;
                        memberList.append(listItem);
                    });
                } else {
                    console.error("Failed to fetch members:", response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }

    // Call fetchMembers to load the list of members
    fetchMembers();

</script>






</body>

</html>
