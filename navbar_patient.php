<?php
// Check if a session is already started, and only start it if not.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Your existing code continues...
if (!isset($_SESSION['patient_username'])) {
    header("Location: patient_login.php");
    exit;
}

$patient_username = $_SESSION['patient_username'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'medicare_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the patient's ID
$patient_sql = "SELECT id FROM patients WHERE username = '$patient_username'";
$patient_result = $conn->query($patient_sql);
$patient = $patient_result->fetch_assoc();
$patient_id = $patient['id'];

// Fetch doctors that the patient can chat with
$doctor_sql = "SELECT id, username FROM doctors";
$doctor_result = $conn->query($doctor_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <style>
        /* Global Styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Navbar Styling */
        nav {
            background-color: #003366; /* Dark blue representing the school tie */
            padding: 10px 20px; /* Reduced padding */
            color: white;
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
            margin: 0 10px; /* Reduced margin */
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px; /* Smaller text size */
            font-weight: 400;
            padding: 6px 14px; /* Smaller padding */
            border-radius: 4px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        nav ul li a:hover {
            background-color: #005fa3;
            color: #f8f9fa;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav ul li a.active {
            background-color: #28a745;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav ul li a.logout {
            background-color: #dc3545;
        }

        nav ul li a.logout:hover {
            background-color: #c82333;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Doctor select dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #ffffff;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            color: black;
            padding: 8px 16px;
            text-decoration: none;
            display: block;
            background-color: #f1f1f1;
            transition: background-color 0.3s ease, color 0.3s ease, padding-left 0.3s ease;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
            color: #005fa3;
            padding-left: 20px;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
                align-items: flex-start;
            }
            nav ul li {
                margin: 5px 0;
            }
            nav ul li a {
                font-size: 14px; /* Smaller text for mobile */
                padding: 5px 10px; /* Smaller padding for mobile */
            }
        }

    </style>

</head>
<body>

<nav>
    <ul>
        <li><a href="patient_dashboard.php" class="active">Dashboard</a></li>
        <li><a href="book_appointment.php">Book Appointment</a></li>
        <li><a href="patient_view_appointments.php">View Appointments</a></li>
        <li><a href="view_prescriptions.php">View Prescriptions</a></li>
        <li><a href="update_profile_patient.php">Update Profile</a></li>
        <li><a href="view_reports.php">View Reports</a></li>

        <!-- Dropdown for selecting a doctor to chat with -->
        <li class="dropdown">
            <a href="#">Select Doctor</a>
            <div class="dropdown-content">
                <?php while ($doctor = $doctor_result->fetch_assoc()): ?>
                    <a href="patient_chat.php?doctor_id=<?php echo $doctor['id']; ?>">Dr. <?php echo htmlspecialchars($doctor['username']); ?></a>
                <?php endwhile; ?>
            </div>
        </li>

        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
</nav>

<?php
$conn->close();
?>

</body>
</html>
