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

// Fetch patient details
$sql = "SELECT id, username, email, password, age FROM patients WHERE username = '$patient_username'";
$patient_result = $conn->query($sql);

// Check if the query returned any results
if ($patient_result && $patient_result->num_rows > 0) {
    $patient = $patient_result->fetch_assoc();
} else {
    // Handle the case when no data is found
    echo "No patient data found. Please log in again.";
    exit;  // Stop execution if no patient data found
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and escape input values
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_password = $_POST['password'];
    $new_age = (int)$_POST['age']; // Get the age from the form and cast it to an integer

    // Check if password is provided; otherwise, retain the old password
    if (!empty($new_password)) {
        $new_password = password_hash($new_password, PASSWORD_DEFAULT); // Hash the new password
    } else {
        $new_password = $patient['password']; // Use existing password if not changed
    }

    // Update patient profile
    $update_sql = "UPDATE patients 
                   SET username = '$new_username', email = '$new_email', password = '$new_password', age = '$new_age' 
                   WHERE id = '{$patient['id']}'";

    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['patient_username'] = $new_username; // Update session username
        echo "Profile updated successfully!";
        header("Refresh: 1; url=patient_dashboard.php"); // Redirect to dashboard
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
    <title>Update Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navigation Bar -->
    <?php include('navbar_patient.php'); ?>

    <!-- Update Profile Container -->
    <div class="container">
        <h2>Update Profile - <?php echo htmlspecialchars(isset($patient['username']) ? $patient['username'] : ''); ?></h2>

        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars(isset($patient['username']) ? $patient['username'] : ''); ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars(isset($patient['email']) ? $patient['email'] : ''); ?>" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="New Password (Leave empty to keep existing)" ><br>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars(isset($patient['age']) ? $patient['age'] : ''); ?>" required><br>

            <button type="submit">Update Profile</button>
        </form>
    </div>

    <!-- Footer
    <footer>
        <p>&copy; 2024 Medicare Portal. All rights reserved.</p>
    </footer> -->

</body>
</html>
