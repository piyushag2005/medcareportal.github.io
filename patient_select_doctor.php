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

// Fetch list of doctors
$doctor_sql = "SELECT id, username FROM doctors";
$doctor_result = $conn->query($doctor_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Doctor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .doctor-list {
            margin: 20px 0;
        }
        .doctor-item {
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .doctor-item button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .doctor-item button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Select a Doctor</h1>
        <div class="doctor-list">
            <?php while ($doctor = $doctor_result->fetch_assoc()): ?>
                <div class="doctor-item">
                    <span>Dr. <?php echo htmlspecialchars($doctor['username']); ?></span>
                    <form method="GET" action="patient_chat.php">
                        <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                        <button type="submit">Chat</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
