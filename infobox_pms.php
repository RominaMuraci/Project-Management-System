<?php
session_start();

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

if($_SESSION['isclient']){
    header("Location: ../clientbalance.php");
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            font-size: 28px;
        }
        h2 {
            color: #555;
            font-size: 24px;
            margin-top: 20px;
        }
        p, li {
            color: #444;
            font-size: 16px;
            line-height: 1.6;
        }
        ul {
            list-style-type: none;
            padding-left: 0;
        }
        ul li {
            margin-bottom: 10px;
        }
        ol {
            padding-left: 20px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
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
            
            
            <h1> Project  Management System - User Manual</h1>
            
            <p><strong>Introduction</strong></p>
            <p>Welcome to the Project Management System! This platform helps members efficiently manage their work hours and projects.</p>
            <h2>Table of Contents</h2>
            <ul>
                
                <li><a href="#section1">1. Projects</a></li>
                <li><a href="#section2">2. Hours Worked</a></li>
                <li><a href="#section3">3. Archived Projects </a></li>
                <li><a href="#section4">4. TO DO</a></li>
                <li><a href="#section5">5. Members</a></li>
                <li><a href="#section5">6. Billing System</a></li>
            </ul>
            
            <h2 id="section1">1. Projects</h2>
            <p>To view all projects, follow these steps:</p>
            <ol>
                <li><strong>Adding a Project:</strong>
                    <ul>
                        <li>Press the <strong>Add Project</strong> button to enter the <em>Project Name</em>, <em>Task Name</em>, <em>Kick-off Date</em> (start date), and <em>Planned Duration</em> (estimated completion date).</li>
                        <li><span style="color: red;">All fields are required</span> to be filled.</li>
                        <li>Press the <strong>Save Project</strong> button to add the project.</li>
                        <li><strong>Email Notification:</strong> Every time a new project is added, an <strong>email is sent to the admin</strong> for tracking purposes.</li>
                    </ul>
                </li>
                <li><strong>Updating Project Status:</strong>
                    <ul>
                        <li>Once a project is completed, you must enter the <em>Finished Date</em>.</li>
                        <li><strong>Weeks</strong> - Displays the number of weeks the project took to complete.</li>
                        <li><strong>Total Hours</strong> - Displays the total hours worked on the project.</li>
                        <li><strong>Email Notification:</strong> An <strong>email is sent to the admin</strong> whenever a project is marked as ongoing.</li>
                    </ul>
                </li>
                <li><strong>Project Completion Status</strong> (based on the Finished Date):
                    <ul>
                        <li><em style="color: green;">Completed Earlier</em> - If the project is finished on time.</li>
                        <li><em style="color: red;">Beyond Deadline</em> - If the project is completed after the planned deadline.</li>
                        <li><em style="color: blue;">On Progress</em> - If the project is still ongoing.</li>
                    </ul>
                </li>
                <li><strong>Comments:</strong>
                    <ul>
                        <li>Press the <strong>+</strong> sign to add a new comment.</li>
                        <li>Click on the <em>"X comments"</em> text to view existing comments for the project.</li>
                    </ul>
                </li>
                <li><strong>Actions (Admin Only):</strong>
                    <ul>
                        <li><strong>Archive</strong> - Moves completed projects to the <em>Archived Projects</em> page.</li>
                        <li><strong>Edit</strong> - Allows editing the <em>Project Name, Kick-off Date, and Planned Duration</em>.</li>
                        <li><strong>Delete</strong> - Permanently removes a project.</li>
                    </ul>
                </li>
            </ol>
            
            
            
            <h2 id="section2">2. Hours Worked</h2>
            <p>To track hours worked for each project during the month, follow these steps:</p>
            <ol>
                <li><strong>Adding Hours Worked:</strong>
                    <ul>
                        <li>Enter the hours worked for each project during the month.</li>
                        <li><span style="color: red;">Careful</span> - Once you edit a cell, you can’t edit it anymore. <strong>(Only Admin can edit it)</strong>.</li>
                        <li>Press <strong><span style="color: red;">ENTER</span></strong> to save changes in every row.</li>
                        <li><strong style="color: red;">Please don't forget to add hours worked!</strong></li>
                    </ul>
                </li>
                <li><strong>Filter Data:</strong>
                    <ul>
                        <li>Select the **Start Date** and **End Date** to view the data for a specific period.</li>
                        <li><strong>Archived Projects</strong> are also included in the report.</li>
                        <li>Select an **Employee** to view their progress.</li>
                        <li>Press the **Check** button to display the filtered results.</li>
                    </ul>
                </li>
                <li><strong>Data Display:</strong>
                    <ul>
                        <li><strong>Date</strong> - Displays the days of the selected month.</li>
                        <li><strong>Generated Headers</strong> - Shows the projects worked on.</li>
                        <li><strong>Total Hours</strong> - Displays the total hours worked. <span style="color: red;">(Cannot be edited, calculated automatically)</span>.</li>
                        <li>Example: If an employee works **5 + 2 + 0.5 + 0.5** hours, the total is **8 hours**.</li>
                    </ul>
                </li>
                <li><strong>Special Cases:</strong>
                    <ul>
                        <li><strong>VMS - DAY OFF</strong> - When an employee takes a day off through the **Vacation and Management System**.</li>
                        <li><strong>National Holidays</strong> - Example: <em>Festat e Vitit te Ri</em>.</li>
                        <li>In both cases, **Total Hours = 0**.</li>
                    </ul>
                </li>
                <li><strong>Comments:</strong>
                    <ul>
                        <li>Displays comments added by users.</li>
                    </ul>
                </li>
                <li><strong>Exporting Data:</strong>
                    <ul>
                        <li>Press the **Export** button to download the table data as an **Excel file**.</li>
                    </ul>
                </li>
            </ol>
            <h2 id="section3">3. Archived Projects</h2>
            <p>To view archived projects, follow these steps:</p>
            <ol>
                <li><strong>Archived Projects</strong> - These are projects that have been completed and moved to the archive by an admin.</li>
            </ol>
            
            
            <h2 id="section4">4. TO DO</h2>
            <p>To manage your to-do list, follow these steps:</p>
            <ol>
                <li><strong>Adding a To-Do Item:</strong>
                    <ul>
                        <li>Enter the <em>Project Name</em> and <em>Task Name</em>.</li>
                        <li>Add a <strong>Description</strong> for the task.</li>
                    </ul>
                </li>
                <li><strong>Editing and Deleting:</strong>
                    <ul>
                        <li>Users can <strong>Edit</strong> existing to-do items.</li>
                        <li>Users can <strong>Delete</strong> tasks if they are no longer needed.</li>
                    </ul>
                </li>
            </ol>
            
            
            <h2 id="section5">5. Members</h2>
            <p>The Members page allows the admin to manage team members and track their ongoing projects.</p>
            <ol>
                <li><strong>Adding Members:</strong>
                    <ul>
                        <li>The admin can add new members to the system.</li>
                    </ul>
                </li>
                <li><strong>Checking Ongoing Projects:</strong>
                    <ul>
                        <li>If a member has no ongoing projects, an <strong>email notification</strong> is sent to them.</li>
                    </ul>
                </li>
            </ol>
            
            
            <h2 id="section6">6. Billing System</h2>
            <p>For users who need to access billing-related information:</p>
            <ol>
                <li>Go to the Billing System section via the link at the bottom of the sidebar.</li>
            </ol>
            
            <h2>Conclusion</h2>
            <p>Projects Management System is designed to make managing work hours and projects  simple and effective. By following this user manual, you should be able to navigate the system easily,</p>
        
        </div>
        
        
        
        <?php include('include/footer.php');?>
    
    </div>
    
</div>

<script>
</script>

</body>

</html>
