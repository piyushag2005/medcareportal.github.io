<?php
session_start();
if (!isset($_SESSION['patient_username'])) {
    header("Location: patient_login.php");
    exit;
}

$patient_username = $_SESSION['patient_username'];

// Fetch doctors
$conn = new mysqli('localhost', 'root', '', 'medicare_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM doctors";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle appointment booking
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];

    // Get patient_id
    $patient_sql = "SELECT id FROM patients WHERE username = '$patient_username'";
    $patient_result = $conn->query($patient_sql);
    $patient = $patient_result->fetch_assoc();
    $patient_id = $patient['id'];

    // Book the appointment
    $insert_sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date) 
                   VALUES ('$patient_id', '$doctor_id', '$appointment_date')";
    if ($conn->query($insert_sql) === TRUE) {
        echo "<div class='success-message'>Appointment booked successfully!</div>";
    } else {
        echo "<div class='error-message'>Error: " . $conn->error . "</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="style.css"> <!-- Linking the common styles -->
</head>
<body>

    <!-- Include the navbar for patient -->
    <?php include('navbar_patient.php'); ?>

    <div class="container">
        <h2>Book an Appointment</h2>
        
        <form method="POST">
            <label for="doctor">Select Doctor:</label><br>
            <select name="doctor_id" id="doctor" required>
                <option value="">Select a doctor</option>
                <?php while ($doctor = $result->fetch_assoc()): ?>
                    <option value="<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['username']); ?></option>
                <?php endwhile; ?>
            </select><br>

            <label for="appointment_date">Select Appointment Date and Time:</label><br>
            <input type="datetime-local" name="appointment_date" required><br>

            <button type="submit">Book Appointment</button>
        </form>
    </div>

    <!-- Footer -->
    <!-- <footer>
        <p>&copy; 2024 Medicare Portal. All rights reserved.</p>
    </footer> -->

</body>
</html>
