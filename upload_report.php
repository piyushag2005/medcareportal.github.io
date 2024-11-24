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

// Fetch all patients to display in the form
$patients_sql = "SELECT id, username FROM patients";
$patients_result = $conn->query($patients_sql);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_name = $_POST['patient_name']; // Get patient name from the form
    $report_type = $_POST['report_type']; // Get report type from the form

    // Get patient ID based on selected patient name
    $sql = "SELECT id FROM patients WHERE username = '$patient_name'";
    $patient_result = $conn->query($sql);
    $patient = $patient_result->fetch_assoc();
    $patient_id = $patient['id'];

    // Check if a file is uploaded
    if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] == 0) {
        $file_tmp = $_FILES['report_file']['tmp_name'];
        $file_name = $_FILES['report_file']['name'];
        $file_size = $_FILES['report_file']['size'];
        $file_type = $_FILES['report_file']['type'];

        // Specify the directory where files will be uploaded
        $upload_dir = 'uploads/reports/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create the folder if it doesn't exist
        }

        $file_path = $upload_dir . basename($file_name);
        
        // Check file size (max 10MB)
        if ($file_size > 10485760) {
            echo "File is too large. Max size is 10MB.";
            exit;
        }

        // Check file type (you can add more types if needed)
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword'];
        if (!in_array($file_type, $allowed_types)) {
            echo "Only PDF, Word, JPG, PNG files are allowed.";
            exit;
        }

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Insert file details into the database
            $sql = "INSERT INTO patient_reports (patient_id, doctor_id, report_type, report_file)
                    VALUES ('$patient_id', '$doctor_id', '$report_type', '$file_name')";
            
            if ($conn->query($sql) === TRUE) {
                echo "Report uploaded successfully!";
            } else {
                echo "Error uploading report: " . $conn->error;
            }
        } else {
            echo "Error uploading the file.";
        }
    } else {
        echo "Please choose a file to upload.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Patient Report</title>
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
    font-weight: 500;
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

        /* Form Styling */
        form {
            width: 60%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
        }

        input[type="text"], select, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <h2>Upload Patient Report</h2>
    <form action="upload_report.php" method="POST" enctype="multipart/form-data">
        <label for="patient_name">Patient Name:</label>
        <select id="patient_name" name="patient_name" required>
            <option value="">Select Patient</option>
            <?php while ($row = $patients_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($row['username']); ?>">
                    <?php echo htmlspecialchars($row['username']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>
        
        <label for="report_type">Report Type:</label>
        <input type="text" id="report_type" name="report_type" required><br><br>
        
        <label for="report_file">Choose Report File:</label>
        <input type="file" id="report_file" name="report_file" required><br><br>
        
        <input type="submit" value="Upload Report">
    </form>
</body>
</html>
