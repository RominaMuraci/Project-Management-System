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

// Assuming you store user data in session after login
$userIdDb = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
$fullname = isset($_SESSION['login_session']) ? $_SESSION['login_session'] : '';
// $userAccess = $_SESSION['isadmin'];



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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php include('include/header.php');?>
    <style>
        /* Form styling */
        .form-label {
            font-weight: bold;
            color: #495057;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
        }

        /* Table styling */
        .table {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background-color: #007bff;
            color: #ffffff;
            text-align: center;
            position: sticky; /* Make headers sticky */
            top: 0; /* Stick to the top of the viewport */
            z-index: 100; /* Ensure headers stay above other content */
        }

        /* Container for scrollable table */
        .table-responsive {
            max-height: 80vh; /* 80% of the viewport height */
            overflow: auto; /* Enable scrolling */
        }


        .table tbody td {
            text-align: center;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .hours-cell {
            background-color: #f8f9fc;
            border: 1px solid #ced4da;
            border-radius: 3px;
            padding: 10px; /* Increased padding for better spacing */
            font-size: 1.2rem; /* Increased font size for bigger text */
        }


        .comments {
            background-color: #f8f9fc;
            border: 1px solid #ced4da;
            border-radius: 3px;
            padding: 5px;
        }

        /* Buttons styling */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        /* Year and Month Selectors */
        .year-selector, .month-selector {
            margin-bottom: 15px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .breadcrumb {
                font-size: 14px;
            }

            .form-label {
                font-size: 14px;
            }

            .table {
                font-size: 14px;
            }
        }

        .small-input {
            width: 150px; /* Adjust width as needed */
            height: 30px; /* Adjust height as needed */
            font-size: 0.9rem; /* Adjust font size */
        }




        /* Blue Rectangular Button */
        .button-blue-rect {
            appearance: none;
            background-color: #58b0e5;
            border-radius: 0.375rem; /* Slightly larger border-radius for consistency */
            border-style: none;
            box-shadow: #074977 0 -6px 6px inset; /* Adjusted shadow for larger button */
            box-sizing: border-box;
            color: #ffffff;
            cursor: pointer;
            display: inline-block;
            font-family: -apple-system, sans-serif;
            font-size: 1rem; /* Increased font size for a larger button */
            font-weight: 700;
            margin: 0;
            outline: none;
            padding: 0.375rem 1rem; /* Increased padding */
            text-align: center;
            text-decoration: none;
            transition: all 0.15s;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
        }

        .button-blue-rect:hover {
            background-color: #5fd7f1;
            box-shadow: #08657a 0 -6px 6px inset; /* Adjusted hover shadow */
            transform: scale(1.05); /* Slightly reduced hover scaling */
        }

        .button-blue-rect:active {
            transform: scale(1.02); /* Reduced active scaling */
        }

        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

    </style>
</head>
<body>
<div id="wrapper">
    <?php include('include/sidebar.php');?>

    <div id="content-wrapper">
        <div class="container-fluid">

            <!-- Breadcrumbs -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">Dashboard</a>
                </li>
                <li class="breadcrumb-item active"> Progress hours </li>
            </ol>

            <!-- Tooltip -->
            <i class="fas fa-info-circle ml-2" data-bs-toggle="tooltip"
               title="Select the year to filter data for the month and employee"
               style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#user-guide-modal"></i>

            <div class="row align-items-end mb-3">
                <!-- Button to Show/Hide Date Filters -->
                <div class="col-auto">
                    <button class="btn btn-secondary" id="toggleDateFilter" style="max-width: 180px;">
                        <i class="fas fa-calendar-alt"></i> Filter Date
                    </button>
                </div>

                <!-- Start & End Date Filter (Initially Hidden) -->
                <div class="col-auto" id="dateFilterContainer" style="display: none;">
                    <div class="row gx-2">
                        <div class="col-auto">
                            <label for="startDate" class="form-label small-label">Start Date :</label>
                            <input type="date" id="startDate" class="form-control" style="max-width: 150px;">
                        </div>
                        <div class="col-auto">
                            <label for="endDate" class="form-label small-label">End Date:</label>
                            <input type="date" id="endDate" class="form-control" style="max-width: 150px;">
                        </div>
                    </div>
                </div>


                <!-- Year & Month Selectors -->
                <div class="col-auto">
                    <div id="yearMonthContainer">
                        <div class="row gx-2">
                            <div class="col-auto">
                                <label for="yearSelect" class="form-label small-label">Year:</label>
                                <select id="yearSelect" class="form-control" style="max-width: 120px;">
                                    <option value="" disabled selected>Select year</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label for="monthSelect" class="form-label small-label">Month:</label>
                                <select id="monthSelect" class="form-control" style="max-width: 140px;">
                                    <option value="" disabled selected>Select month</option>
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Selector -->
                <div class="col-auto">
                    <label for="employee_select" class="form-label small-label">Employee:</label>
                    <select id="employee_select" class="form-control select2" style="max-width: 200px;">
                        <option value="" disabled selected>Select an employee</option>
                    </select>
                </div>


                <!-- Check Button -->
                <div class="col-auto">
                    <button class="button-blue-rect" id="checkBtn">
                        <i class="fas fa-check mr-2"></i> Check
                    </button>
                </div>


                <!-- Export Button -->
                <div class="col-auto">
                    <button class="btn-success" id="exportBtn">
                        <i class="fas fa-file-excel mr-2"></i> Export
                    </button>
                </div>

            </div>


            <!-- Table (New Row) -->
            <div class="table-responsive mt-3">
                <table id="ProgressMonths" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Date <i class="fas fa-info-circle" data-toggle="tooltip"
                                    title="This column contains a calendar of dates for each selected month"></i>
                        </th>
                        <th>Total hours <i class="fas fa-info-circle" data-toggle="tooltip"
                                           title="Total hours worked per row"></i>
                        </th>
                        <th>Others <i class="fas fa-info-circle" data-toggle="tooltip"
                                      title="Reasons why hours worked is zero"></i>
                        </th>
                        <th>Comments <i class="fas fa-info-circle" data-toggle="tooltip"
                                        title="User comments"></i>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Rows dynamically generated -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <?php include('include/footer.php');?>
    </div>
</div>

<!-- Modal HTML -->
<div id="user-guide-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">How to Use the Progress Tracker</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul>
                    <li>Select the Year and Month from the dropdowns.</li>
                    <li>Choose an Employee to view their progress.</li>
                    <li>Wait for the table to load with the selected employee’s data.</li>
                    <li>Click on hour cells to edit the number of hours worked.</li>
                    <li>Click on the Comments section to add any feedback.</li>
                    <li>Press <span style="color: red; font-weight: bold;">ENTER</span> to save your changes for your row.</li>
                    <li>Review the total hours worked at the end of each row.</li>
                    <li>Ensure all changes are saved before navigating away.</li>

                </ul>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got It!</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        var infoIcon = document.querySelector(".fa-info-circle");
        var userGuideModal = new bootstrap.Modal(document.getElementById("user-guide-modal"));

        infoIcon.addEventListener("click", function () {
            userGuideModal.show();
        });

        // Initialize Tooltip
        var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        var tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
    });
</script>


<script>
    $(document).ready(function () {

        $('[data-toggle="tooltip"]').tooltip();

// Initialize DataTable
        $('#ProgressMonths').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true
        });

// DOM elements and default date values
        const yearSelect = document.getElementById('yearSelect');
        const monthSelect = document.getElementById('monthSelect');
        const employeeSelect = $('#employee_select');
        const currentYear = new Date().getFullYear();
        const currentMonth = (new Date().getMonth() + 1).toString().padStart(2, '0');

// Initialize the employee select2
        $('#employee_select').select2({
            placeholder: "Select an employee",
            allowClear: true,
        });

// Dynamically populate the year dropdown
        for (let year = currentYear - 5; year <= currentYear + 5; year++) {
            $('#yearSelect').append(`<option value="${year}">${year}</option>`);
        }
// Set default year and month from the current date
        $('#yearSelect').val(currentYear);
        $('#monthSelect').val(new Date().getMonth() + 1);

// Fetch user data and populate the employee dropdown
        fetch('fetch_user_ids.php')
            .then(response => response.json())
            .then(data => {
                const employeeSelect = $('#employee_select');
                employeeSelect.empty(); // Clear previous options

                if (data.allUsers && data.allUsers.length > 0) {
                    if (data.allUsers.length === 1) {
                        // If only one user, add it as the only option
                        employeeSelect.append($('<option>', {
                            value: data.allUsers[0].userid,
                            text: data.allUsers[0].fullname
                        }));
                    } else {
                        // If multiple users, add a default placeholder option
                        employeeSelect.append($('<option>', {
                            value: '',
                            text: 'Select an employee',
                            disabled: true,
                            selected: true
                        }));

                        data.allUsers.forEach(user => {
                            employeeSelect.append($('<option>', {
                                value: user.userid,
                                text: user.fullname
                            }));
                        });
                    }

                    $('#selected_employee').val('');
                    $('#selected_employee_name').val('');
                    updateTable(); // Update table after loading user data
                } else {
                    console.warn('No user data found.');
                    updateTable();
                }
            })
            .catch(error => {
                console.error('Error fetching user data:', error);
                updateTable();
            });

// Event listener for the Check button
        $('#checkBtn').on('click', function () {
            updateTable();
        });

// The updateTable function uses the selected year and month to form start/end dates.
        function updateTable() {
            const employeeId = employeeSelect.val();
            const startDateInput = document.getElementById('startDate').value;
            const endDateInput = document.getElementById('endDate').value;

            // Get the selected year and month from the dropdowns
            const selectedYear = yearSelect.value;
            const selectedMonth = monthSelect.value;
            const selectedMonthPadded = selectedMonth.toString().padStart(2, '0');

            if (!employeeId) {
                console.warn("Employee not selected");
                return;
            }

            // Build start and end dates based on the selected year and month if no custom dates are provided
            let startDateToUse = startDateInput || `${selectedYear}-${selectedMonthPadded}-01`;
            let endDay = new Date(selectedYear, selectedMonth, 0).getDate();
            let endDateToUse = endDateInput || `${selectedYear}-${selectedMonthPadded}-${endDay}`;

            // Destroy the existing DataTable before updating
            if ($.fn.DataTable.isDataTable("#ProgressMonths")) {
                $("#ProgressMonths").DataTable().clear().destroy();
            }

            // Clear existing table content (headers and body)
            $("#ProgressMonths thead").empty();
            $("#ProgressMonths tbody").empty();

            // Fetch holidays
            // Fetch holidays
            fetch(`getHolidays.php?start_date=${startDateToUse}&end_date=${endDateToUse}`)
                .then(response => response.json())
                .then(holidayData => {
                    const holidays = holidayData.holidays || [];

                    // Fetch absence data
                    fetch(`fetch_absent_hours.php?employee_id=${employeeId}&start_date=${startDateToUse}&end_date=${endDateToUse}`)
                        .then(response => response.json())
                        .then(absenceData => {
                            const absences = absenceData.data?.vacation_employees || {};

                            // Fetch project hours with updated date range
                            let projectUrl = `fetch_projects_hours.php?employee_id=${employeeId}&start_date=${startDateToUse}&end_date=${endDateToUse}`;
                            fetch(projectUrl)
                                .then(response => response.json())
                                .then(projectData => {
                                    // Even if projectData.data is empty, generate the table using an empty array
                                    if (projectData.status === "success") {
                                        const projectHours = projectData.data || [];
                                        generateTableHeaders(projectHours);
                                        fetchTaskHours(employeeId, projectHours, holidays, absences, startDateToUse, endDateToUse);
                                    } else {
                                        console.warn("Project data not fetched successfully.");
                                        generateTableHeaders([]); // generate headers with no project columns
                                        fetchTaskHours(employeeId, [], holidays, absences, startDateToUse, endDateToUse);
                                    }

                                    // Reinitialize DataTable after table update
                                    $('#ProgressMonths').DataTable({
                                        paging: true,
                                        searching: true,
                                        ordering: true,
                                        info: true,
                                        autoWidth: false,
                                        responsive: true
                                    });
                                })
                                .catch(error => console.error("Error fetching project data:", error));
                        })
                        .catch(error => console.error("Error fetching absence data:", error));
                })
                .catch(error => console.error("Error fetching holidays:", error));
        }

        function fetchTaskHours(employeeId, projects, holidays, absences, startDateToUse, endDateToUse) {
            fetch(`fetch_tasks_hours.php?employee_id=${employeeId}&start_date=${startDateToUse}&end_date=${endDateToUse}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success" && data.data.length > 0) {
                        // Pass employeeId as the last parameter
                        populateTableData(data.data, projects, holidays, absences, employeeId, startDateToUse, endDateToUse);
                    } else {
                        console.warn("No task data found.");
                        populateTableData([], projects, holidays, absences, employeeId, startDateToUse, endDateToUse);
                    }
                })
                .catch(error => console.error("Error fetching task hours:", error));
        }
        function generateTableHeaders(projects, startDateToUse, endDateToUse) {
            // Clear existing headers to prevent duplication
            $('#ProgressMonths thead').empty();

            let tableHead = '<tr><th data-toggle="tooltip" title="This column contains calendar of dates for every month selected and the current date is visible">Date</th>';

            // Add columns for each project
            projects.forEach(project => {
                tableHead += `<th>${project.name}</th>`;
            });

            tableHead += '<th data-toggle="tooltip" title="This column contains total hours worked for every row that is calculated directly">Total</th>' +
                '<th data-toggle="tooltip" title="This column contains reasons why this hours is zero.">Others</th>' +
                '<th data-toggle="tooltip" title="This column contains comments that user can put.">Comments</th></tr>';

            $('#ProgressMonths thead').html(tableHead);

            // Re-initialize tooltips after populating the table
            $('[data-toggle="tooltip"]').tooltip();
        }



        var isAdmin = <?php echo json_encode($isAdmin); ?>;
        function populateTableData(tasks, projects, holidays, absences, employeeId, startDateToUse, endDateToUse) {
            const start = new Date(startDateToUse);
            const end = new Date(endDateToUse);
            const currentDate = new Date().toISOString().split('T')[0];
            let tableBody = '';

            // Loop through the days between the start and end date
            for (let date = new Date(start); date <= end; date.setDate(date.getDate() + 1)) {
                const year = date.getFullYear();
                const month = ('0' + (date.getMonth() + 1)).slice(-2);
                const day = ('0' + date.getDate()).slice(-2);
                const formattedDate = `${year}-${month}-${day}`;

                const isCurrentDate = (formattedDate === currentDate);
                const rowStyle = isCurrentDate ? 'font-weight: bold;' : '';

                // Find a holiday for the date (if any)
                const holiday = holidays.find(h => h.date === formattedDate);
                const holidayName = holiday ? holiday.name : '';

                // Check if the employee is absent on this date
                const isAbsent = absences[employeeId] && absences[employeeId].indexOf(formattedDate) !== -1;

                // Begin row with the date cell
                tableBody += `<tr data-date="${formattedDate}" style="${rowStyle}"><td>${formattedDate}</td>`;

                let dailyTotal = 0;
                let comment = '';

                if (isAbsent) {
                    // Absent branch: show "VMS- DAY OFF" in each project cell
                    projects.forEach(() => {
                        tableBody += `<td class="hours-cell" style="background-color: #f8d7da;" data-populated="true">VMS- DAY OFF</td>`;
                    });
                    tableBody += `<td class="total-hours" style="background-color: rgb(235, 245, 252);  font-weight: bold;">0</td>`;
                    tableBody += `<td class="others" style="background-color: #f8d7da;">VMS- DAY OFF</td>`;
                    tableBody += `<td class="comments"></td>`;
                } else if (holidayName) {
                    // Holiday branch: display holiday info in each project cell
                    projects.forEach(() => {
                        tableBody += `<td class="hours-cell" style="background-color: rgb(253, 249, 195); font-weight: bold;" data-populated="true">${holidayName}</td>`;
                    });
                    tableBody += `<td class="total-hours" style="background-color: rgb(235, 245, 252);  font-weight: bold;">0</td>`;
                    tableBody += `<td class="others" style="background-color: rgb(253, 249, 195); font-weight: bold;">${holidayName}</td>`;
                    tableBody += `<td class="comments" contenteditable="true"></td>`;
                } else {
                    // Normal branch: show task hours
                    projects.forEach(project => {
                        const task = tasks.find(task => task.work_date === formattedDate && task.project_id == project.id);
                        const hours = task ? task.hours : 0;
                        comment = task ? task.comments : '';
                        dailyTotal += parseFloat(hours);

                        // Add data-populated="true" to cells that have any value (including 0)
                        tableBody += `<td class="hours-cell" data-project-id="${project.id}" contenteditable="true" data-populated="${hours > 0 ? 'true' : 'false'}">${hours}</td>`;
                    });
                    tableBody += `<td class="total-hours" style="background-color: rgb(235, 245, 252); font-weight: bold;">${dailyTotal}</td>`;
                    tableBody += `<td class="others"></td>`;
                    tableBody += `<td class="comments" contenteditable="true">${comment || ''}</td>`;
                }

                tableBody += `</tr>`;
            }

            $('#ProgressMonths tbody').html(tableBody);

            // Re-initialize tooltips after table update
            $('[data-toggle="tooltip"]').tooltip();

            // Disable editing for already populated cells (including 0) if user is not admin
            if (!isAdmin) {
                $('.hours-cell[data-populated="true"]').attr('contenteditable', 'false');
            }
        }






        function saveHours(row) {
            // Get the date from the row's data attributes
            const date = row.data('date');

            // Get the comments for the row (now it's inline editable)
            const comments = row.find('.comments').text();  // Get the text from the comments cell

            // Get the hours data from the row
            const projectData = [];

            row.find('.hours-cell').each(function () {
                const projectId = $(this).data('project-id');
                const hours = parseFloat($(this).text()) || 0;
                projectData.push({
                    project_id: projectId,
                    hours_worked: hours
                });
            });

            // Log the request payload for debugging
            console.log("Request Payload:", JSON.stringify({
                date: date,
                comments: comments,
                projectData: projectData
            }));

            // Send the updated hours to the server
            fetch('save_task_hours.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    date: date,
                    comments: comments,
                    projectData: projectData,  // Send project data in the request
                }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        console.log('Hours saved successfully');
                        Swal.fire({
                            title: 'Success!',
                            text: 'Data saved successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        console.error('Error saving hours:', data.message);
                        Swal.fire({
                            title: 'Save Error',
                            text: 'There was an error saving the data.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error saving hours:', error);
                    Swal.fire({
                        title: 'Save Error',
                        text: 'There was an error saving the data.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }

        function addEventListeners() {
            // Update total hours when hours are edited (contenteditable)
            $(document).on('input', '.hours-cell', function () {
                const row = $(this).closest('tr');
                let total = 0;

                // Loop through the hours cells to calculate the new total
                row.find('.hours-cell').each(function () {
                    const hours = parseFloat($(this).text()) || 0; // Get text from contenteditable cells
                    total += hours;
                });

                // Update the total hours in the last column of the row
                row.find('.total-hours').text(total.toFixed(2));
            });

            // Save hours when Enter key is pressed
            $(document).on('keypress', '.hours-cell, .comments', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault(); // Prevent the default action (creating a new line)
                    const row = $(this).closest('tr');
                    saveHours(row); // Call saveHours with the row as the argument
                }
            });
        }

// Initialize event listeners
        addEventListeners();


    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    document.getElementById('exportBtn').addEventListener('click', function () {
        const table = document.getElementById('ProgressMonths');
        const employeeName = document.getElementById('employee_select')?.selectedOptions[0]?.textContent?.trim() || 'Employee';
        const rows = table.querySelectorAll('tr');

        // Convert table rows to a 2D array (AOA)
        const data = Array.from(rows).map(row =>
            Array.from(row.querySelectorAll('th, td')).map(cell => cell.innerText)
        );

        // Create a worksheet from the AOA
        const ws = XLSX.utils.aoa_to_sheet(data);

        // Create a workbook and append the worksheet
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Employee Data");

        // Export the workbook using the employee name in the filename
        const fileName = `${employeeName.replace(/\s+/g, '_')}_Hours_Worked.xlsx`;
        XLSX.writeFile(wb, fileName);
    });
</script>

<script>
    document.getElementById("toggleDateFilter").addEventListener("click", function() {
        let filterContainer = document.getElementById("dateFilterContainer");
        let yearMonthContainer = document.getElementById("yearMonthContainer");

        if (filterContainer.style.display === "none") {
            filterContainer.style.display = "block";  // Show Date Filter
            yearMonthContainer.style.display = "none";  // Hide Year & Month
        } else {
            filterContainer.style.display = "none";  // Hide Date Filter
            yearMonthContainer.style.display = "block";  // Show Year & Month
        }
    });
</script>

</body>
</html>
