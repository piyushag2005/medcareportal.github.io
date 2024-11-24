<?php
session_start();
if (!isset($_SESSION['doctor_username'])) {
    header("Location: doctor_login.php");
    exit;
}

$doctor_username = $_SESSION['doctor_username'];

// Fetch doctor id
$conn = new mysqli('localhost', 'root', '', 'medicare_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id FROM doctors WHERE username = '$doctor_username'";
$doctor_result = $conn->query($sql);
$doctor = $doctor_result->fetch_assoc();
$doctor_id = $doctor['id'];

// Fetch appointments for the doctor
$appointment_sql = "SELECT a.id, p.username AS patient_name, a.appointment_date, a.status 
                    FROM appointments a 
                    JOIN patients p ON a.patient_id = p.id
                    WHERE a.doctor_id = '$doctor_id'";

$appointment_result = $conn->query($appointment_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    // Update appointment status based on action (confirm, cancel, complete)
    if ($action === 'confirm') {
        $update_sql = "UPDATE appointments SET status = 'confirmed' WHERE id = '$appointment_id'";
    } elseif ($action === 'cancel') {
        $update_sql = "UPDATE appointments SET status = 'cancelled' WHERE id = '$appointment_id'";
    } elseif ($action === 'complete') {
        $update_sql = "UPDATE appointments SET status = 'completed' WHERE id = '$appointment_id'";
    }

    if ($conn->query($update_sql) === TRUE) {
        echo "Appointment updated successfully!";
        header("Refresh: 1; url=doctor_dashboard.php"); // Refresh page to see updated status
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
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

        h1, h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
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
            font-size: 16px;
            padding: 10px 20px;
            display: inline-block;
            text-transform: uppercase;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.3s ease, color 0.3s ease;
            border-radius: 5px;
        }

        nav ul li a:hover {
            background-color: #444;
            transform: scale(1.05);
            color: #FFD700;
        }

        nav ul li a.active {
            background-color: #007BFF;
            color: white;
        }

        /* Body container */
        .container {
            margin: 120px auto 20px;
            padding: 20px;
            max-width: 900px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        /* Table styling */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #ffffff;
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table td {
            background-color: #f9f9f9;
        }

        table tr:nth-child(even) td {
            background-color: #f2f2f2;
        }

        table tr:hover td {
            background-color: #e0e0e0;
        }

        form {
            display: inline-block;
            margin-right: 10px;
        }

        /* Button styles */
        button {
            padding: 8px 16px;
            border: none;
            background-color: #4CAF50;
            color: white;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        button:disabled {
            background-color: #c0c0c0;
            cursor: not-allowed;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 8px;
            background-color: #222;
            color: white;
            margin-top: 30px;
        }

        /* Style for no appointments available message */
        p {
            text-align: center;
            color: #999;
            font-size: 16px;
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
                margin: 80px auto 20px;
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

<?php include('navbar.php'); ?>

    <div class="container">
        <h1>Welcome, Dr. <?php echo htmlspecialchars($_SESSION['doctor_username']); ?>!</h1>

        <h2>Appointments</h2>
        <?php if ($appointment_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while ($appointment = $appointment_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                        <td>
                            <?php if ($appointment['status'] == 'pending'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <button type="submit" name="action" value="confirm">Confirm</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <button type="submit" name="action" value="cancel">Cancel</button>
                                </form>
                            <?php elseif ($appointment['status'] == 'confirmed'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <button type="submit" name="action" value="complete">Complete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No appointments available.</p>
        <?php endif; ?>
    </div>

    <!-- <footer>
        <p>&copy; 2024 Medical Portal. All rights reserved.</p>
    </footer> -->

</body>
</html>
