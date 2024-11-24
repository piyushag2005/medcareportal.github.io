<?php
session_start();
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

// Fetch doctor id
$sql = "SELECT id FROM doctors WHERE username = '$doctor_username'";
$doctor_result = $conn->query($sql);
$doctor = $doctor_result->fetch_assoc();
$doctor_id = $doctor['id'];

// Fetch prescription history along with medication details for the doctor
$prescription_sql = "SELECT p.id AS prescription_id, pt.username AS patient_name, p.created_at, m.medication_name, m.quantity, m.frequency, m.days
                     FROM prescriptions p
                     JOIN patients pt ON p.patient_id = pt.id
                     LEFT JOIN prescription_medications m ON p.id = m.prescription_id
                     WHERE p.doctor_id = '$doctor_id' 
                     ORDER BY p.created_at DESC";

$prescription_result = $conn->query($prescription_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription History</title>
    <link rel="stylesheet" href="style2.css">
    <style>
        /* General page styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h1, h3 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        /* Container for content */
        .container {
            width: 75%;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
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

        table td:first-child, table td:last-child {
            text-align: center;
        }

        /* Footer styles */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
        }

        footer p {
            margin: 0;
        }

        nav {
            background-color: #222; 
            padding: 12px 0px;
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
            font-size: 13px;
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

        /* Container for content */
        .container {
            margin: 120px auto 20px;
            padding: 20px;
            max-width: 700px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }

        input, select, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .medication-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .delete-btn {
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
            border-radius: 4px;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

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
    </style>
</head>
<body>
    <!-- Include the navbar -->
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Prescription History</h1>
        <h3>Prescriptions Written by Dr. <?php echo htmlspecialchars($_SESSION['doctor_username']); ?></h3>

        <?php if ($prescription_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Patient Name</th>
                    <th>Prescription Text</th>
                    <th>Medications</th>
                    <th>Date</th>
                </tr>
                <?php
                    $previous_prescription_id = null;
                    while ($prescription = $prescription_result->fetch_assoc()):
                        // Check if this prescription is the same as the previous one
                        if ($previous_prescription_id !== $prescription['prescription_id']):
                            if ($previous_prescription_id !== null):
                                echo "</tr>";  // Close the previous prescription row
                            endif;
                            echo "<tr>";
                            echo "<td rowspan='2'>" . htmlspecialchars($prescription['patient_name']) . "</td>";
                            echo "<td rowspan='2'>" . "Prescription for " . htmlspecialchars($prescription['patient_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($prescription['medication_name']) . " (Qty: " . htmlspecialchars($prescription['quantity']) . ", " . htmlspecialchars($prescription['frequency']) . " for " . htmlspecialchars($prescription['days']) . " days)</td>";
                            echo "<td rowspan='2'>" . htmlspecialchars($prescription['created_at']) . "</td>";
                            $previous_prescription_id = $prescription['prescription_id'];
                        else:
                            echo "<tr><td>" . htmlspecialchars($prescription['medication_name']) . " (Qty: " . htmlspecialchars($prescription['quantity']) . ", " . htmlspecialchars($prescription['frequency']) . " for " . htmlspecialchars($prescription['days']) . " days)</td></tr>";
                        endif;
                    endwhile;
                ?>
            </table>
        <?php else: ?>
            <p>No prescriptions written yet.</p>
        <?php endif; ?>
    </div>

    <!-- Footer
    <footer>
        <p>&copy; 2024 Medical Portal. All rights reserved.</p>
    </footer> -->
</body>
</html>
