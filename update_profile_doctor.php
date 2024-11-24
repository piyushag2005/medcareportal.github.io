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

// Fetch doctor details
$sql = "SELECT id, username, email FROM doctors WHERE username = '$doctor_username'";
$doctor_result = $conn->query($sql);
$doctor = $doctor_result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];

    // Update doctor profile
    $update_sql = "UPDATE doctors 
                   SET username = '$new_username', email = '$new_email', password = '$new_password' 
                   WHERE id = '{$doctor['id']}'";

    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['doctor_username'] = $new_username; // Update session username
        echo "Profile updated successfully!";
        header("Refresh: 1; url=doctor_dashboard.php"); // Redirect to dashboard
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
    <link rel="stylesheet" href="style2.css">
    <style>/* General page styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

h1 {
    text-align: center;
    color: #333;
    margin-top: 20px;
}

/* Container for the profile update form */
.container {
    width: 60%;
    margin: 40px auto;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

/* Form styling */
form {
    display: flex;
    flex-direction: column;
}

label {
    font-size: 16px;
    color: #555;
    margin: 10px 0 5px;
}

input[type="text"], input[type="email"], input[type="password"] {
    padding: 12px;
    font-size: 16px;
    margin-bottom: 20px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
    border-color: #4CAF50;
    outline: none;
}

/* Button styling */
button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 14px;
    font-size: 16px;
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

/* Footer styling */
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
    background-color: #222; /* Dark background for the navbar */
    padding: 12px 0; /* Reduced padding for a more compact navbar */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);

    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

/* Navbar list and links styling */
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
    font-size: 14px; /* Smaller font size */
    padding: 10px 20px; /* Slightly reduced padding */
    display: inline-block;
    text-transform: uppercase;
    font-weight: 300;
    transition: background-color 0.3s ease, transform 0.3s ease, color 0.3s ease;
    border-radius: 5px;
}

/* Hover effect */
nav ul li a:hover {
    background-color: #444;
    transform: scale(1.05);
    color: #FFD700;
}

/* Active state for the navbar */
nav ul li a.active {
    background-color: #007BFF;
    color: white;
}

/* Body container */
.container {
    margin: 120px auto 20px; /* Adjust margin to avoid overlap with the sticky navbar */
    padding: 20px;
    max-width: 900px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    flex: 1;
}

/* Cards for different sections */
.card {
    background: #ffffff;
    padding: 18px;
    margin: 18px 0;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.card a {
    color: #007BFF;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    padding: 5px 0;
    font-size: 14px;
    transition: color 0.3s ease;
}

.card a:hover {
    color: #0056b3;
}

/* Footer */
footer {
    text-align: center;
    padding: 8px;
    background-color: #222;
    color: white;
    margin-top: 30px;
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
        margin: 80px auto 20px; /* Adjusted margin to prevent overlap with sticky navbar */
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
        <h1>Update Profile - Dr. <?php echo htmlspecialchars($doctor['username']); ?></h1>

        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($doctor['username']); ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="New Password" required><br>

            <button type="submit">Update Profile</button>
        </form>
    </div>
    <!-- <footer>
        <p>&copy; 2024 Medical Portal. All rights reserved.</p>
    </footer> -->
</body>
</html>
