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

// Fetch doctor ID
$doctor_sql = "SELECT id FROM doctors WHERE username = '$doctor_username'";
$doctor_result = $conn->query($doctor_sql);
$doctor = $doctor_result->fetch_assoc();
$doctor_id = $doctor['id'];

// Fetch patients assigned to the doctor
$patients_sql = "SELECT id, username FROM patients";
$patients_result = $conn->query($patients_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Patient</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        .patient-list {
            margin: 20px 0;
        }
        .patient-list a {
            display: block;
            padding: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            color: white;
            background-color: #4CAF50;
            text-align: center;
            border-radius: 5px;
        }
        .patient-list a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Select a Patient to Chat</h2>
        
        <?php if ($patients_result->num_rows > 0): ?>
            <div class="patient-list">
                <?php while ($patient = $patients_result->fetch_assoc()): ?>
                    <a href="doctor_chat.php?patient_id=<?php echo $patient['id']; ?>">
                        Chat with <?php echo htmlspecialchars($patient['username']); ?>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No patients are available.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
