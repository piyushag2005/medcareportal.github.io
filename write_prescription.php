<?php
session_start();

// Check if doctor is logged in
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

// Get doctor ID
$sql = "SELECT id FROM doctors WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $doctor_username);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$doctor_id = $doctor['id'];
$stmt->close();

// Get patient list
$patient_sql = "SELECT id, username, age FROM patients";
$patient_result = $conn->query($patient_sql);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $age = $_POST['age'];
    $disease = $_POST['disease'];
    $diagnosis = $_POST['diagnosis'];
    $prescription_text = $_POST['prescription_text'];

    // Insert prescription
    $insert_sql = "INSERT INTO prescriptions (doctor_id, patient_id, age, disease, diagnosis, prescription_text, created_at) 
                   VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param('iiisss', $doctor_id, $patient_id, $age, $disease, $diagnosis, $prescription_text);

    if ($stmt->execute()) {
        $prescription_id = $stmt->insert_id;

        // Insert medications if provided
        if (!empty($_POST['medication_name'])) {
            foreach ($_POST['medication_name'] as $key => $medication_name) {
                $medication_qty = $_POST['medication_qty'][$key];
                $medication_frequency = $_POST['medication_frequency'][$key];
                $medication_days = $_POST['medication_days'][$key];

                $medication_sql = "INSERT INTO prescription_medications (prescription_id, medication_name, quantity, frequency, days) 
                                   VALUES (?, ?, ?, ?, ?)";
                $med_stmt = $conn->prepare($medication_sql);
                $med_stmt->bind_param('isisi', $prescription_id, $medication_name, $medication_qty, $medication_frequency, $medication_days);
                $med_stmt->execute();
            }
        }

        echo "Prescription successfully saved!";
        header("Refresh: 2; url=doctor_dashboard.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Prescription</title>
    <style>
        /* Global styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9; 
            margin: 0;
            padding: 0;
            color: #333; 
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

        /* Navbar styling */
        nav {
            background-color: #222; 
            padding: 12px 0;
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
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Write Prescription</h1>
        <form method="POST">
            <label for="patient_id">Select Patient:</label>
            <select name="patient_id" id="patient_id" required>
                <option value="">Select a patient</option>
                <?php while ($patient = $patient_result->fetch_assoc()): ?>
                    <option value="<?php echo $patient['id']; ?>">
                        <?php echo htmlspecialchars($patient['username']); ?> (Age: <?php echo $patient['age']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="age">Patient Age:</label>
            <input type="number" name="age" id="age" required>

            <label for="disease">Disease:</label>
            <input type="text" name="disease" id="disease" required>

            <label for="diagnosis">Diagnosis:</label>
            <textarea name="diagnosis" id="diagnosis" rows="4" required></textarea>

            <label for="prescription_text">Prescription Text:</label>
            <textarea name="prescription_text" id="prescription_text" rows="4" required></textarea>

            <h3>Medications</h3>
            <div id="medication-container">
                <div class="medication-row">
                    <input type="text" name="medication_name[]" placeholder="Medication Name" required>
                    <input type="number" name="medication_qty[]" placeholder="Quantity" required>
                    <input type="text" name="medication_frequency[]" placeholder="Frequency" required>
                    <input type="number" name="medication_days[]" placeholder="Days" required>
                    <button type="button" class="delete-btn" onclick="deleteMedication(this)">Delete</button>
                </div>
            </div>
            <button type="button" onclick="addMedication()">Add Medication</button>
            <button type="submit">Save Prescription</button>
        </form>
    </div>

    <script>
        function addMedication() {
            const container = document.getElementById('medication-container');
            const newRow = document.createElement('div');
            newRow.classList.add('medication-row');
            newRow.innerHTML = `
                <input type="text" name="medication_name[]" placeholder="Medication Name" required>
                <input type="number" name="medication_qty[]" placeholder="Quantity" required>
                <input type="text" name="medication_frequency[]" placeholder="Frequency" required>
                <input type="number" name="medication_days[]" placeholder="Days" required>
                <button type="button" class="delete-btn" onclick="deleteMedication(this)">Delete</button>
            `;
            container.appendChild(newRow);
        }

        function deleteMedication(button) {
            button.closest('.medication-row').remove();
        }
    </script>
</body>
</html>
