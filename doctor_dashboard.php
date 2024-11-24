<?php
session_start();
if (!isset($_SESSION['doctor_username'])) {
    header("Location: doctor_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <style>
        /* Global styling */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f9f9f9; /* Light gray background */
    margin: 0;
    padding: 0;
    color: #333; /* Dark text color */
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* Styling for the navbar */
nav {
    background-color: #222; /* Dark background for the navbar */
    padding: 12px 0; /* Reduced padding for a more compact navbar */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);

    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

/* Navbar list and links styling */
nav ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
}

nav ul li {
    margin: 0 12px;
}

nav ul li a {
    color: white;
    text-decoration: none;
    font-size: 16px; /* Smaller font size */
    padding: 10px 20px; /* Slightly reduced padding */
    display: inline-block;
    text-transform: uppercase;
    font-weight: 500;
    transition: background-color 0.3s ease, transform 0.3s ease, color 0.3s ease;
    border-radius: 5px;
}

/* Hover effect */
nav ul li a:hover {
    background-color: #444;
    transform: scale(1.05);
    color: #FFD700;
}

/* Active state for the navbar */
nav ul li a.active {
    background-color: #007BFF;
    color: white;
}

/* Body container */
.container {
    margin: 120px auto 20px; /* Adjust margin to avoid overlap with the sticky navbar */
    padding: 20px;
    max-width: 900px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    flex: 1;
}

/* Cards for different sections */
.card {
    background: #ffffff;
    padding: 18px;
    margin: 18px 0;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.card a {
    color: #007BFF;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    padding: 5px 0;
    font-size: 14px;
    transition: color 0.3s ease;
}

.card a:hover {
    color: #0056b3;
}

/* Footer */
footer {
    text-align: center;
    padding: 8px;
    background-color: #222;
    color: white;
    margin-top: 30px;
}

/* Media Queries for Responsiveness */
@media (max-width: 768px) {
    nav ul {
        flex-direction: column;
        align-items: center;
    }

    nav ul li {
        margin-bottom: 12px;
    }

    .container {
        margin: 80px auto 20px; /* Adjusted margin to prevent overlap with sticky navbar */
        padding: 15px;
    }

    .card {
        margin: 15px 0;
    }

    .card a {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    nav ul li a {
        font-size: 14px;
    }

    .container {
        padding: 10px;
    }

    .card a {
        font-size: 12px;
    }
}
    </style>
</head>
<body>
    <!-- Include the navbar -->
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Welcome, Dr. <?php echo htmlspecialchars($_SESSION['doctor_username']); ?>!</h1>
        
        <div class="card">
            <h2>Manage Your Appointments</h2>
            <p><a href="view_appointments.php">View Appointments</a> - View all your scheduled appointments.</p>
        </div>

        <div class="card">
            <h2>Write a Prescription</h2>
            <p><a href="write_prescription.php">Write Prescription</a> - Write a prescription for your patients.</p>
        </div>

        <div class="card">
            <h2>Prescription History</h2>
            <p><a href="prescription_history.php">Prescription History</a> - View all past prescriptions.</p>
        </div>

        <div class="card">
            <h2>Update Profile</h2>
            <p><a href="update_profile_doctor.php">Update Profile</a> - Edit your personal details and credentials.</p>
        </div>
    </div>

    <!-- Footer
    <footer>
        <p>&copy; 2024 Medical Portal. All rights reserved.</p>
    </footer> -->
</body>
</html>
