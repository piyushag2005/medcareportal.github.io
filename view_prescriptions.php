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

// Fetch prescriptions and associated medications
$prescription_sql = "SELECT p.id AS prescription_id, p.prescription_text, d.username AS doctor_name, p.created_at
                     FROM prescriptions p
                     JOIN doctors d ON p.doctor_id = d.id
                     WHERE p.patient_id = '$patient_id' ORDER BY p.created_at DESC";

$prescription_result = $conn->query($prescription_sql);

$prescriptions = [];
$medications = [];

// Fetch medications for each prescription
if ($prescription_result->num_rows > 0) {
    $prescription_ids = [];
    while ($row = $prescription_result->fetch_assoc()) {
        $prescription_ids[] = $row['prescription_id'];
        $prescriptions[] = $row;
    }

    if (!empty($prescription_ids)) {
        $medication_sql = "SELECT pm.prescription_id, pm.medication_name, pm.quantity, pm.frequency, pm.days 
                           FROM prescription_medications pm 
                           WHERE pm.prescription_id IN (" . implode(',', $prescription_ids) . ")";
        $medication_result = $conn->query($medication_sql);

        while ($med = $medication_result->fetch_assoc()) {
            $medications[$med['prescription_id']][] = $med;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Prescriptions</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        .container {
            width: 80%;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .order-btn {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 10px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        .order-btn:hover {
            background-color: #0056b3;
        }

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
    </style>
</head>
<body>

    <!-- Include Navbar -->
    <?php include('navbar_patient.php'); ?>

    <!-- Prescriptions Container -->
    <div class="container">
        <h2>Your Prescriptions</h2>

        <?php if (!empty($prescriptions)): ?>
            <?php foreach ($prescriptions as $prescription): ?>
                <div>
                    <h3>Doctor: <?php echo htmlspecialchars($prescription['doctor_name']); ?></h3>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($prescription['created_at']); ?></p>
                    <p><strong>Prescription Text:</strong><br><?php echo nl2br(htmlspecialchars($prescription['prescription_text'])); ?></p>

                    <?php if (!empty($medications[$prescription['prescription_id']])): ?>
                        <h4>Medications:</h4>
                        <table>
                            <tr>
                                <th>Medication Name</th>
                                <th>Quantity</th>
                                <th>Frequency</th>
                                <th>Days</th>
                                <th>Action</th>
                            </tr>
                            <?php foreach ($medications[$prescription['prescription_id']] as $medication): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($medication['medication_name']); ?></td>
                                    <td><?php echo htmlspecialchars($medication['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($medication['frequency']); ?></td>
                                    <td><?php echo htmlspecialchars($medication['days']); ?></td>
                                    <td>
                                        <!-- Replace spaces with %20 manually to avoid '+' -->
                                        <a href="https://www.netmeds.com/catalogsearch/result/<?php echo str_replace(' ', '%20', htmlspecialchars($medication['medication_name'])); ?>/all" target="_blank" class="order-btn">Order Online</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No prescriptions found.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <!-- <footer>
        <p>&copy; 2024 Medicare Portal. All rights reserved.</p>
    </footer> -->

</body>
</html>
