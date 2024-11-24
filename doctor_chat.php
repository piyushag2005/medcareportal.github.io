<?php
session_start();
if (!isset($_SESSION['doctor_username'])) {
    header("Location: doctor_login.php");
    exit;
}

$doctor_username = $_SESSION['doctor_username'];
$patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;

$conn = new mysqli('localhost', 'root', '', 'medicare_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch patient details
$patient_sql = "SELECT username FROM patients WHERE id = '$patient_id'";
$patient_result = $conn->query($patient_sql);

if ($patient_result->num_rows == 0) {
    header("Location: doctor_select_patient.php");
    exit;
}

$patient = $patient_result->fetch_assoc();

// Fetch doctor ID
$doctor_sql = "SELECT id FROM doctors WHERE username = '$doctor_username'";
$doctor_result = $conn->query($doctor_sql);
$doctor = $doctor_result->fetch_assoc();
$doctor_id = $doctor['id'];

// Fetch chat messages
$chat_sql = "SELECT m.message, m.timestamp, p.username AS patient_name, d.username AS doctor_name
             FROM chat_messages m
             LEFT JOIN patients p ON m.patient_id = p.id
             LEFT JOIN doctors d ON m.doctor_id = d.id
             WHERE (m.patient_id = '$patient_id' AND m.doctor_id = '$doctor_id')
             OR (m.patient_id = '$doctor_id' AND m.doctor_id = '$patient_id')
             ORDER BY m.timestamp";
$chat_result = $conn->query($chat_sql);

// Handle message submission or clear chat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_chat'])) {
        // Clear chat messages
        $delete_sql = "DELETE FROM chat_messages WHERE (patient_id = '$patient_id' AND doctor_id = '$doctor_id') 
                       OR (patient_id = '$doctor_id' AND doctor_id = '$patient_id')";
        $conn->query($delete_sql);
        header("Location: doctor_chat.php?patient_id=$patient_id");
        exit;
    } else {
        // Add new message
        $message = $_POST['message'];
        $insert_sql = "INSERT INTO chat_messages (patient_id, doctor_id, message, timestamp) 
                       VALUES ('$patient_id', '$doctor_id', '$message', NOW())";
        $conn->query($insert_sql);
        header("Location: doctor_chat.php?patient_id=$patient_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($patient['username']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .chat-container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .chat-header {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #4CAF50;
        }
        .messages {
            border: 1px solid #ccc;
            padding: 15px;
            height: 400px;
            overflow-y: scroll;
            background-color: #f4f4f4;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 15px;
        }
        .message p {
            margin: 0;
        }
        .message .doctor {
            color: #2196F3;
            font-weight: bold;
        }
        .message .patient {
            color: #4CAF50;
            font-weight: bold;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-bottom: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .clear-chat {
            background-color: red;
            margin-left: 10px;
        }
        .clear-chat:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div>Doctor Portal</div>
        <div>
            <a href="doctor_dashboard.php">Dashboard</a>
            <a href="doctor_select_patient.php">Select Patient</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Chat Container -->
    <div class="chat-container">
        <div class="chat-header">Chat with <?php echo htmlspecialchars($patient['username']); ?></div>

        <div class="messages">
            <?php while ($message = $chat_result->fetch_assoc()): ?>
                <div class="message">
                    <p class="<?php echo ($message['doctor_name'] === $doctor_username) ? 'doctor' : 'patient'; ?>">
                        <?php echo htmlspecialchars($message['doctor_name'] ?? $message['patient_name']); ?>: 
                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                    </p>
                    <small><?php echo htmlspecialchars($message['timestamp']); ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <form method="POST">
            <textarea name="message" placeholder="Type your message..." required></textarea>
            <button type="submit">Send Message</button>
            <button type="submit" name="clear_chat" class="clear-chat" onclick="return confirm('Are you sure you want to clear the chat?')">Clear Chat</button>
        </form>
    </div>
</body>
</html>
