<?php
session_start();
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

// Fetch patient id
$sql = "SELECT id FROM patients WHERE username = '$patient_username'";
$patient_result = $conn->query($sql);
$patient = $patient_result->fetch_assoc();
$patient_id = $patient['id'];

// Fetch appointments for the patient
$appointment_sql = "SELECT a.id, d.username AS doctor_name, a.appointment_date, a.status 
                    FROM appointments a 
                    JOIN doctors d ON a.doctor_id = d.id
                    WHERE a.patient_id = '$patient_id'";

$appointment_result = $conn->query($appointment_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Appointments</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Global Styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Navbar Styling */
        .navbar {
            background-color: #007bff;
            padding: 14px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 18px;
            display: inline-block;
        }

        .navbar a:hover {
            background-color: #575757;
        }

        .navbar a.active {
            background-color: #28a745;
        }

        /* Appointments Container Styling */
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 28px;
            margin-bottom: 20px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
        }

        th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td {
            font-size: 14px;
        }

        /* Footer Styling */
        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
                margin: 20px;
            }

            .navbar a {
                font-size: 16px;
            }

            table {
                font-size: 14px;
            }

            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    <!-- Include Navbar -->
    <?php include('navbar_patient.php'); ?>

    <!-- Appointments Container -->
    <div class="container">
        <h2>Your Appointments</h2>

        <?php if ($appointment_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Doctor Name</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                </tr>
                <?php while ($appointment = $appointment_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No appointments found.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <!-- Footer
    <footer>
        <p>&copy; 2024 Medical Portal. All rights reserved.</p>
    </footer> -->

</body>
</html>
