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
          <li class="breadcrumb-item active"> To do list </li>
        </ol>





<button class="button-green-rect" data-bs-toggle="modal" data-bs-target="#addToDoModal">
    <i class="fas fa-plus"></i> Add Project
      </button>
   

<div class="table-responsive">
    <table id="TODO" class="table table-striped table-bordered ">
    <thead>
        <tr>
            <th>Project Name / Task name</th>
            <th>Created by  </th>
            <th> Description  </th>
            <th> Created at  </th>
            <th>Actions</th>
        </tr>
        </thead>
        <thead>
        <tr>
            <th><input type="text" placeholder="Search Project Name" /></th>
            <th><input type="text" placeholder="Search Created by " /></th>
            <th><input type="text" placeholder="Search Description " /></th>
            <th><input type="date" placeholder="Search Created at " /></th>
        
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



  <div class="modal fade" id="addToDoModal" tabindex="-1" aria-labelledby="addToDoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="addToDoModalLabel">Add New Project / Task</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body bg-light">
        <!-- Form for adding a new project/task -->
        <form id="addTodo" method="POST">
           <div class="row">
            <div class="col-md-12 mb-3">
              <label for="name" class="form-label">Project Name / Task Name</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Enter Project name/ Task name">
            </div>
            <div class="col-md-12 mb-3">
              <label for="name" class="form-label"> Description </label>
              <input type="text" class="form-control" id="description" name="description" placeholder=" Enter Description">
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


         <!-- Planned Duration -->
          <div class="mb-3">
            <label for="editDescription" class="form-label"> Description </label>
            <input type="text" class="form-control form-control-lg" id="editDescription">
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
    // Initialize DataTable
    const table = $('#TODO').DataTable({
        processing: true,
        serverSide: false, // Use client-side processing for now
        paging: true,
        pageLength: 10,
        ajax: {
            url: 'fetch_to_do_tasks.php',
            dataSrc: function (json) {
                if (json.status === 'success') {
                    return json.data;
                } else {
                    // No error popup, just console log for debugging
                    console.warn('No data found:', json.message);
                    
                    // Optionally, display a custom message in the table
                    $('#TODO tbody').html(`
                        <tr>
                            <td colspan="5" style="text-align: center; color: gray;">
                                No tasks available at the moment.
                            </td>
                        </tr>
                    `);
                    
                    return []; // Return empty data to DataTables
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
        }
,
        columns: [
            // Project/Task Name
            {
                data: 'project_name',
                render: function (data) {
                    return `<span style="font-weight: bold; color: blue;">${data}</span>`;
                },
            },
            // Created By
            { data: 'created_by' },
            // Comments
            { data: 'description' },
            // Created at
            { data: 'created_at' },
            // Actions (Edit/Delete)
            {
                data: null,
                render: function (data, type, row) {
                    return `
                        <button class="button-blue-rect edit-btn" data-id="${row.id}" data-name="${row.project_name}" data-description="${row.description}" data-created="${row.created_at}"><i class="fas fa-edit"></i></button>
                        <button class="button-red-rect delete-btn" data-id="${row.id}"><i class="fas fa-trash-alt"></i></button>
                    `;
                },
            },
        ],
        order: [[0, 'asc']], // Order by the first column
    });

    // Add new task/project
    $('#submitProjectBtn').on('click', function () {
        // Retrieve form field values
        const projectName = $('#name').val(); 
        const description = $('#description').val();

        if (projectName && description) {
            const formData = $('#addTodo').serialize(); // Serialize form data

            // Make an AJAX request to submit the form data to 'add_to_do_tasks.php'
            $.ajax({
                url: 'add_to_do_tasks.php',
                method: 'POST',
                data: formData,
                success: function (response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Task added successfully!',
                                text: 'The project/task was added successfully.',
                            }).then(() => {
                                table.ajax.reload(); // Reload table
                                $('#addToDoModal').modal('hide'); // Close the modal
                                $('#addTodo')[0].reset(); // Reset form
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error: ' + data.message,
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
                        text: 'There was an error submitting the form.',
                    });
                },
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Invalid input',
                text: 'Please fill in both project name and description.',
            });
        }
    });

    // Date column search logic
    $('#TODO thead input').on('keyup change', function () {
        const colIdx = $(this).parent().index();
        let searchValue = this.value;

        // Check if the column is date-related (created_at column, index 3)
        if (colIdx === 3) {
            searchValue = moment(searchValue, 'YYYY-MM-DD').format('YYYY-MM-DD'); // Format the date if necessary
        }

        table.column(colIdx).search(searchValue).draw(); // Apply column search
    });

    // Delete task/project
    $(document).on('click', '.delete-btn', function () {
        const projectId = $(this).data('id');

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'Do you really want to delete this project?',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_to_do.php',
                    method: 'POST',
                    data: { id: projectId },
                    success: function (response) {
                        const data = JSON.parse(response);
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'The project has been deleted.',
                            });
                            table.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error: ' + data.message,
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'An error occurred',
                            text: 'There was an error deleting the project.',
                        });
                    },
                });
            }
        });
    });
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
            $('#TODO_filter input').val(projectName).trigger('keyup'); // Apply search filter
        }, 500); // Delay to ensure DataTable loads first
    }


    // Edit project modal
    $(document).on('click', '.edit-btn', function () {
        const projectId = $(this).data('id');
        const projectName = $(this).data('name');
        const description = $(this).data('description');

        // Set the values in the edit modal
        $('#editProjectId').val(projectId);
        $('#editProjectName').val(projectName);
        $('#editDescription').val(description);

        // Show the edit modal
        $('#editProjectModal').modal('show');
    });

    // Save edited project
    $('#saveEditProjectBtn').on('click', function () {
        const projectId = $('#editProjectId').val();
        const projectName = $('#editProjectName').val();
        const description = $('#editDescription').val();

        $.ajax({
            url: 'edit_to_do.php',
            type: 'POST',
            data: { id: projectId, name: projectName, description: description  },
            success: function (response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'The project has been updated successfully.',
                    });
                    $('#editProjectModal').modal('hide');
                    table.ajax.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update the project.',
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the project.',
                });
            },
        });
    });
});
</script>






</body>

</html>
