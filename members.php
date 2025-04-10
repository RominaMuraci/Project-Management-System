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

// Redirect to login page if the user is not authenticated
if (!isset($_SESSION['login_session']) || !isset($_SESSION['userid'])) {
    header('Location: ../index.php');
    exit();
}

$userIdDb = $_SESSION['userid'];
$fullname  = $_SESSION['login_session'];
$username  = $_SESSION['useremail'];

// Allowed admin emails
$allowedAdmins = ['muraciromina@gmail.com'];

// Check if the user is an admin
$isAdmin = in_array($username, $allowedAdmins);

// If the user is not an admin, redirect to projects.php
//if (!$isAdmin) {
//    header("Location: projects.php");
//    exit(); // Ensure the script stops executing after the redirect
//}

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

        #submitProjectBtn {
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
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
            gap: 10px; /* Space between the buttons */
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
            margin-top: 20px;
            background-color: #f4f6f9; /* Light background for contrast */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
        }

        .table-responsive {
            overflow-x: auto; /* Ensure responsiveness */
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 10px 12px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-size: 16px;
            vertical-align: middle;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2; /* Light gray background for even rows */
        }

        .table tbody tr:hover {
            background-color: #e9ecef; /* Slightly darker gray on hover */
            cursor: pointer; /* Pointer cursor on hover */
        }

        .table input[type="text"] {
            width: 100%;
            padding: 8px 10px;
            box-sizing: border-box;
            font-size: 12px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }

        .table input[type="text"]:focus {
            border-color: #007bff; /* Blue border on focus */
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.25); /*  Light blue shadow on focus */
        }

        .table th i {
            margin-left: 5px;
            cursor: pointer;
            color: #ffffff; /* White icons in header */
        }

        .table th i:hover {
            color: #FFD700; /* Gold color on hover */
        }

        .table th div {
            max-width: 150px;
            margin: 0 auto;
        }

        .inner-table th, .inner-table td {
            padding: 3px !important;
        }





    </style>
</head>
<body>
<div id="wrapper">
    <?php include('include/sidebar.php'); ?>
    <div id="content-wrapper">
        <div class="container-fluid">
            <!-- Breadcrumbs -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Members</li>
            </ol>

            
            <div class="container-fluid">

                <!-- Add New Member Button with Tooltip -->
                <button class="btn btn-primary mb-3" data-toggle="collapse" data-target="#addMemberForm" title="Click to add a new member">
                    <i class="fas fa-user-plus"></i> Add New Member
                </button>

                <!-- <div id="preselected-container" class="container-fluid" style="display: none;"> -->

                <div id="addMemberForm" class="collapse">
                    <div class="card card-body" style="width: 40%;">
                        <form id="newMemberForm">
                            <div class="form-group">
                                <label for="memberSelect">Choose Member</label>
                                <select class="form-control" id="memberSelect" style="width: 100%;">
                                    <option value="">-- Select Member --</option>
                                </select>

                                <input type="hidden" id="adminId" value="<?php echo $_SESSION['userid']; ?>" />

                            </div>
                            <button type="submit" id="submitMemberBtn" class="btn btn-success btn-sm">Add New Member</button>
                        </form>
                    </div>
                </div>



                <div class="table-responsive">
                    <table id="Members" class="table table-striped table-bordered ">
                        <thead>
                        <tr>
                            <th>Members</th>
                            <th>Added at  </th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <thead>
                        <tr>
                            <th><input type="text" placeholder="Search Members" /></th>
                            <th><input type="text" placeholder="Search added at" /></th>
                            <th><input type="text" placeholder="Search Added by  " /></th>
                            <th></th> <!-- Empty for Actions column -->
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



    <script>
        $(document).ready(function () {
            // Initialize DataTable for Members
            const table = $('#Members').DataTable({
                processing: true,
                serverSide: false, // Use client-side processing
                paging: true,
                pageLength: 10,
                ajax: {
                    url: 'fetch_members.php',
                    dataSrc: function (json) {
                        if (json.status === 'success') {
                            return json.data;
                        } else {
                            console.warn('No data found:', json.message);
                            $('#Members tbody').html(`
                        <tr>
                            <td colspan="4" style="text-align: center; color: gray;">
                                No members available at the moment.
                            </td>
                        </tr>
                    `);
                            return [];
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'AJAX Error',
                            text: `Status: ${status}, Error: ${error}`
                        });
                    }
                },
                columns: [
                    { data: 'fullname', title: 'Member Name' },
                    { data: 'added_at', title: 'Added At' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                        <button class="button-red-rect delete-btn" data-id="${row.userid}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    `;
                        },
                        title: 'Actions'
                    }
                ],
                order: [[2, 'desc']], // Order by "Added At" (newest first)
            });

            // Handle new member form submission
            $('#newMemberForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                let userIds = $('#memberSelect').val(); // Get selected user IDs
                let adminId = $('#adminId').val(); // Get the admin ID from a hidden field or another source

                if (!userIds || userIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Please select at least one member!',
                    });
                    return;
                }

                if (!adminId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Admin ID is missing!',
                    });
                    return;
                }

                $.ajax({
                    url: 'add_members.php', // The PHP file handling insertion
                    method: 'POST',
                    data: {
                        user_ids: userIds, // Send array of user IDs
                        admin_id: adminId   // Send admin ID
                    },
                    success: function (response) {
                        try {
                            let data = JSON.parse(response);
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Members added successfully!',
                                }).then(() => {
                                    $('#newMemberForm')[0].reset();
                                    $('#addMemberForm').collapse('hide');
                                    table.ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message,
                                });
                            }
                        } catch (e) {
                            console.error('JSON Parse Error:', e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Invalid response from the server.',
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'There was an error adding the members.',
                        });
                    }
                });
            });

            // Delete member
// Example: Delete selected members
            $(document).on('click', '.delete-btn', function () {
                const userId = $(this).data('id');

                // Confirm with the user before deleting
                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    text: 'Do you really want to delete this member?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'remove_member.php',
                            method: 'POST',
                            data: {
                                user_ids: [userId], // Send the user ID as an array
                            },
                            success: function (response) {
                                const data = JSON.parse(response);
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'The member has been deleted.',
                                    });
                                    table.ajax.reload(); // Reload the table after deletion
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message,
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'An error occurred',
                                    text: 'There was an error deleting the member.',
                                });
                            }
                        });
                    }
                });
            });


            // Fetch users from the server for member selection
            $.ajax({
                url: "fetch_user_ids.php", // Adjust the path if needed
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.allUsers && response.allUsers.length > 0) {
                        var userSelect = $("#memberSelect");
                        response.allUsers.forEach(function (user) {
                            userSelect.append(
                                `<option value="${user.userid}">${user.fullname}</option>`
                            );
                        });
                    } else {
                        alert("No users found.");
                    }
                },
                error: function () {
                    alert("Failed to load users.");
                }
            });
        });
    </script>

</body>


</html>
