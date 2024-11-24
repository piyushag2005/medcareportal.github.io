<?php
session_start();

// Check if the doctor is logged in
if (!isset($_SESSION['doctor_username'])) {
    header("Location: doctor_login.php");
    exit;
}

$doctor_username = $_SESSION['doctor_username'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'medicare_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get doctor id based on logged-in username
$sql = "SELECT id FROM doctors WHERE username = '$doctor_username'";
$doctor_result = $conn->query($sql);
$doctor = $doctor_result->fetch_assoc();
$doctor_id = $doctor['id'];

// Fetch reports uploaded by the doctor
$sql = "SELECT pr.id, pr.report_type, pr.report_file, pr.upload_date, p.username AS patient_name
        FROM patient_reports pr
        JOIN patients p ON pr.patient_id = p.id
        WHERE pr.doctor_id = '$doctor_id'
        ORDER BY pr.upload_date DESC";

$report_result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patient Reports</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        /* Navbar Styling */
        nav {
            background-color: #333;
            color: white;
            padding: 10px;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        /* Table Styling */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Include Navbar -->
    <?php include('navbar_patient.php'); ?>

    <h2>Uploaded Patient Reports</h2>

    <?php if ($report_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Patient Name</th>
                <th>Report Type</th>
                <th>Uploaded On</th>
                <th>Download Report</th>
            </tr>
            <?php while ($row = $report_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['report_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['upload_date']); ?></td>
                    <td><a href="uploads/reports/<?php echo htmlspecialchars($row['report_file']); ?>" download>Download</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No reports uploaded yet.</p>
    <?php endif; ?>

</body>
</html>
