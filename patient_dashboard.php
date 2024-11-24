<?php
session_start();
if (!isset($_SESSION['patient_username'])) {
    header("Location: patient_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <style>
        /* Global Styling */
       /* Global Styling */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

/* Navbar Styling */
nav {
    background-color: #003366; /* Dark blue for the navbar */
    padding: 15px 20px;
    color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

nav ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

nav ul li {
    margin: 0 15px;
}

nav ul li a {
    color: white;
    text-decoration: none;
    font-size: 18px;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 5px;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

nav ul li a:hover {
    background-color: #005fa3; /* Hover effect */
    color: #f8f9fa; /* White text on hover */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Light shadow for elevation */
}

nav ul li a.active {
    background-color: #28a745; /* Active link with green color */
}

nav ul li a.logout {
    background-color: #dc3545; /* Red color for logout */
}

nav ul li a.logout:hover {
    background-color: #c82333;
}

/* Dashboard Container */
.container {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

/* Header */
h1 {
    font-size: 36px;
    color: #333;
    margin-top: 20px;
}

.welcome-text {
    font-size: 24px;
    color: #444;
    text-align: center;
    margin-bottom: 30px;
}

/* Buttons and Links Styling */
.dashboard-links {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.dashboard-links a {
    background-color: #003366; /* Same dark blue as navbar */
    color: white;
    text-decoration: none;
    padding: 15px 30px;
    border-radius: 5px;
    font-size: 18px;
    width: 200px;
    text-align: center;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.dashboard-links a:hover {
    background-color: #005fa3; /* Hover color for the links */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Soft shadow effect */
}

/* Footer */
footer {
    background-color: #003366; /* Same dark blue for footer */
    color: white;
    padding: 20px;
    text-align: center;
    position: fixed;
    width: 100%;
    bottom: 0;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .dashboard-links a {
        width: 100%;
        padding: 12px;
    }
}

    </style>
</head>
<body>

    <!-- Include the navbar for patient -->
    <?php include('navbar_patient.php'); ?>

    <div class="container">
        <h1>Welcome to Your Dashboard</h1>
        <p class="welcome-text">Hello, <?php echo htmlspecialchars($_SESSION['patient_username']); ?>! Here’s your personalized dashboard.</p>

        <div class="dashboard-links">
            <a href="book_appointment.php">Book an Appointment</a>
            <a href="patient_view_appointments.php">View Appointments</a>
            <a href="view_prescriptions.php">View Prescriptions</a>
            <a href="update_profile_patient.php">Update Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Footer
    <footer>
        <p>&copy; 2024 Medical Portal. All rights reserved.</p>
    </footer> -->

</body>
</html>
