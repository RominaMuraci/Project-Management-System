<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define the root directory and include the database connection
$rootDir = __DIR__ . '../../';
$conn = include($rootDir . 'config/connection.php');

// Check database connection
if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Assuming you store user data in session after login
$userIdDb = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
$fullname = isset($_SESSION['login_session']) ? $_SESSION['login_session'] : '';

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

        /* Styling for the months */
        .months-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px;
        }

        .month {
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .month:hover {
            background-color: #f0f0f0;
        }

        .current-month {
            background-color: #007bff;
            color: white;
        }
        .current-date {
    background-color: rgb(59, 153, 255); /* Blue background for the current date */
    font-weight: bold;
    color: #fff; /* White text color for better contrast */
    border: 2px solid #0056b3; /* Dark blue border around the cell */
    text-align: center; /* Center align text */
    box-shadow: 0 0 5px rgba(0, 0, 255, 0.3); /* Subtle blue shadow effect */
    font-family: 'Arial', sans-serif; /* Use a clean, modern font */
    font-size: 1.1em; /* Slightly larger font size */
    padding: 5px 10px; /* Add some padding for better spacing */
}

.month-btn {
    padding: 10px;
    margin: 5px;
    border: none;
    border-radius: 5px;
    background-color: #f0f0f0;
    cursor: pointer;
    transition: background-color 0.3s;
}

.month-btn:hover {
    background-color: #007bff;
    color: white;
}

.month-btn:focus {
    outline: none;
    background-color: #0056b3;
    color: white;
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
          <li class="breadcrumb-item active"> Progress hours </li>
        </ol>

        <div class="months-container">
    <form id="monthsForm" method="GET" action="fetch_tasks_hours.php" onsubmit="return false;"> <!-- Prevent form submission -->
        <button type="button" name="month" value="1" class="month-btn">January</button>
        <button type="button" name="month" value="2" class="month-btn">February</button>
        <button type="button" name="month" value="3" class="month-btn">March</button>
        <button type="button" name="month" value="4" class="month-btn">April</button>
        <button type="button" name="month" value="5" class="month-btn">May</button>
        <button type="button" name="month" value="6" class="month-btn">June</button>
        <button type="button" name="month" value="7" class="month-btn">July</button>
        <button type="button" name="month" value="8" class="month-btn">August</button>
        <button type="button" name="month" value="9" class="month-btn">September</button>
        <button type="button" name="month" value="10" class="month-btn">October</button>
        <button type="button" name="month" value="11" class="month-btn">November</button>
        <button type="button" name="month" value="12" class="month-btn">December</button>
        <input type="hidden" name="year" value="<?= date('Y'); ?>"> <!-- Add current year -->
    </form>
</div>



<div class="table-responsive">
    <table id="ProgressMonths" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Date</th>
               
                <!-- Dynamic headers for projects will go here -->
                <!-- Other project columns will appear here dynamically -->
                <th>Total</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows dynamically generated -->
        </tbody>
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
    const tableBody = $('#ProgressMonths tbody');
    const months = document.querySelectorAll('.month');

    // Set a default date (February 11, 2025)
    let currentDate = new Date(); // Months are 0-indexed, so 1 corresponds to February
    let currentMonth = currentDate.getMonth(); // Default to the current month (February)
    let currentYear = currentDate.getFullYear();

    const formattedCurrentDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;

    // Add 'current-month' class to the current month based on the currentDate
    if (months.length > 0) {
        months[currentMonth].classList.add('current-month');
    }

    // Add click event to months to update the currentMonth and currentYear dynamically
    months.forEach((monthElement, index) => {
        monthElement.addEventListener('click', function () {
            // Remove 'current-month' class from all months
            months.forEach(month => month.classList.remove('current-month'));

            // Add 'current-month' class to the clicked month
            monthElement.classList.add('current-month');

            // Update currentMonth based on the clicked index (0-based, so January is 0, February is 1, etc.)
            currentMonth = index;

            // You can optionally handle year changes here (if you want to move between months of different years).
            // For example, if you add a year selector, you would update `currentYear` here.

            fetchProjectsAndTaskHours(); // Reload table data for the selected month
        });
    });

    // Fetch both projects and task hours
    function fetchProjectsAndTaskHours() {
        $.when(
            // Fetch project data (for headers)
            $.ajax({
                url: 'fetch_projects_hours.php',
                type: 'GET',
                dataType: 'json'
            }),
            // Fetch task hours data (for table rows)
            $.ajax({
                url: 'fetch_tasks_hours.php',
                type: 'GET',
                data: { month: currentMonth + 1, year: currentYear }, // Pass month and year as params
                dataType: 'json'
            })
        ).done(function (projectsResponse, taskHoursResponse) {
            const projects = projectsResponse[0].data;
            const taskHours = taskHoursResponse[0].data;

            if (projectsResponse[0].status === 'success' && taskHoursResponse[0].status === 'success') {
                // Clear and regenerate the table
                const headerRow = $('#ProgressMonths thead tr').eq(0);
                headerRow.empty();
                tableBody.empty();

                // Add 'Date' column to the header first
                headerRow.append('<th>Date</th>');

                // Add project columns to the header
                projects.forEach(project => {
                    headerRow.append(`<th>${project.name}</th>`);
                });

                // Add 'Total' and 'Comments' columns to the header
                headerRow.append('<th>Total</th>');
                headerRow.append('<th>Comments</th>');

                // Generate rows for the selected month
                generateTableRows(projects, taskHours);
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Error fetching projects or task hours: ' + (projectsResponse[0].message || taskHoursResponse[0].message),
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }).fail(function (error) {
            console.error('Error fetching projects or task hours:', error);
            Swal.fire({
                title: 'AJAX Error',
                text: 'There was an error fetching project or task data.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }

    // Generate table rows with task hours data
    function generateTableRows(projects, taskHours) {
        // Calculate the number of days in the selected month
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

        // Loop through the days of the selected month
        for (let day = 1; day <= daysInMonth; day++) {
            const formattedDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            let rowClass = formattedDate === formattedCurrentDate ? 'current-date' : '';

            // Start creating the row
            let row = `<tr data-date="${formattedDate}" class="${rowClass}"><td>${formattedDate}</td>`;

            // Filter task hours for the current day
            const tasksForDay = taskHours.filter(task => task.work_date === formattedDate);

            let totalHours = 0;
            let rowComments = "";

            // Add a cell for each project
            projects.forEach(project => {
                const task = tasksForDay.find(task => task.project_id === project.id);
                const hoursWorked = task ? task.hours_worked : 0; // Default to 0 if no task data
                const comments = task ? task.comments : ""; // Default to empty if no comments

                totalHours += hoursWorked; // Add to total hours
                rowComments = rowComments || comments; // Use the first non-empty comment if available

                // Add an editable cell
                row += `<td contenteditable="true" class="hours-worked" data-project-id="${project.id}">${hoursWorked}</td>`;
            });

            // Add total hours and comments columns
            row += `
                <td class="total-hours">${totalHours}</td>
                <td contenteditable="true" class="comments">${rowComments}</td>
            </tr>`;

            // Append the row to the table body
            tableBody.append(row);
        }

        // Initialize DataTable after adding rows
        initializeDataTable();
        addEventListeners();
    }

    // Initialize DataTable
    function initializeDataTable() {
        // Destroy existing DataTable instance if it exists
        if ($.fn.DataTable.isDataTable('#ProgressMonths')) {
            $('#ProgressMonths').DataTable().destroy();
        }

        // Reinitialize DataTable
        $('#ProgressMonths').DataTable({
            paging: true,
            pageLength: 31,
        });
    }

    // Add event listeners for inline editing and saving
    function addEventListeners() {
        $(document).on('input', '.hours-worked', function () {
            const row = $(this).closest('tr');
            let total = 0;

            row.find('.hours-worked').each(function () {
                const hours = parseFloat($(this).text()) || 0;
                total += hours;
            });

            row.find('.total-hours').text(total);
        });

        $(document).on('keydown', '.hours-worked, .comments', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const row = $(this).closest('tr');
                saveRowData(row);
            }
        });
    }

    // Save row data to the database
    function saveRowData(row) {
        const date = row.data('date');
        const comments = row.find('.comments').text();
        const projectData = [];

        row.find('.hours-worked').each(function () {
            const projectId = $(this).data('project-id');
            const hoursWorked = parseFloat($(this).text()) || 0;
            projectData.push({ project_id: projectId, hours_worked: hoursWorked });
        });

        $.ajax({
            url: 'save_task_hours.php',
            type: 'POST',
            data: {
                date: date,
                comments: comments,
                projects: projectData,
            },
            success: function (response) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Data saved successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            },
            error: function (error) {
                Swal.fire({
                    title: 'Save Error',
                    text: 'There was an error saving the data.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            },
        });
    }

    // Fetch projects and task hours and initialize table
    fetchProjectsAndTaskHours();
});

</script>
</body>
</html>
