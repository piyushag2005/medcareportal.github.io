<?php
session_start();
if (!isset($_SESSION['patient_username'])) {
    header("Location: patient_login.php");
    exit;
}

$patient_username = $_SESSION['patient_username'];
$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;

$conn = new mysqli('localhost', 'root', '', 'medicare_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctor details using prepared statements
$doctor_sql = $conn->prepare("SELECT username FROM doctors WHERE id = ?");
$doctor_sql->bind_param("i", $doctor_id);
$doctor_sql->execute();
$doctor_result = $doctor_sql->get_result();

if ($doctor_result->num_rows == 0) {
    header("Location: patient_select_doctor.php"); // Redirect to doctor selection if doctor not found
    exit;
}
$doctor = $doctor_result->fetch_assoc();

// Fetch patient ID
$patient_sql = $conn->prepare("SELECT id FROM patients WHERE username = ?");
$patient_sql->bind_param("s", $patient_username);
$patient_sql->execute();
$patient_result = $patient_sql->get_result();
$patient = $patient_result->fetch_assoc();
$patient_id = $patient['id'];

// Fetch chat messages between the patient and doctor
$chat_sql = "SELECT m.message, m.timestamp, p.username AS patient_name, d.username AS doctor_name
             FROM chat_messages m
             LEFT JOIN patients p ON m.patient_id = p.id
             LEFT JOIN doctors d ON m.doctor_id = d.id
             WHERE (m.patient_id = ? AND m.doctor_id = ?)
             OR (m.patient_id = ? AND m.doctor_id = ?)
             ORDER BY m.timestamp";
$stmt = $conn->prepare($chat_sql);
$stmt->bind_param("iiii", $patient_id, $doctor_id, $doctor_id, $patient_id);
$stmt->execute();
$chat_result = $stmt->get_result();

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_chat'])) {
        // Clear chat: Delete all messages between the patient and doctor
        $delete_sql = "DELETE FROM chat_messages WHERE (doctor_id = ? AND patient_id = ?) 
                       OR (doctor_id = ? AND patient_id = ?)";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("iiii", $doctor_id, $patient_id, $patient_id, $doctor_id);
        $delete_stmt->execute();
        header("Location: patient_chat.php?doctor_id=$doctor_id");
        exit;
    } else {
        // Send new message
        $message = $_POST['message'];
        $insert_sql = "INSERT INTO chat_messages (patient_id, doctor_id, message, timestamp) 
                       VALUES (?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iis", $patient_id, $doctor_id, $message);
        $insert_stmt->execute();
        header("Location: patient_chat.php?doctor_id=$doctor_id");
        exit;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Dr. <?php echo htmlspecialchars(isset($doctor['username']) ? $doctor['username'] : 'Doctor not found'); ?></title>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f1f1f1; /* Lighter background for better contrast */
        color: #333; /* Dark text color for readability */
    }

    nav {
        background-color: #007bff; /* Vibrant blue */
        padding: 15px 20px;
        color: white;
        font-size: 18px;
    }

    nav ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
    }

    nav ul li {
        margin: 0 20px;
    }

    nav ul li a {
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    nav ul li a:hover {
        background-color: #0056b3; /* Darker blue for hover */
    }

    .chat-container {
        max-width: 900px;
        margin: 50px auto;
        background-color: white;
        padding: 30px;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        border-radius: 10px; /* Rounded corners for modern look */
    }

    .messages {
        border: 1px solid #ddd;
        padding: 20px;
        height: 500px; /* Increased height for better message visibility */
        overflow-y: auto;
        background-color: #fafafa; /* Slightly lighter background for message area */
        margin-bottom: 20px;
        border-radius: 8px;
    }

    .message {
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 8px; /* Rounded corners for each message */
    }

    .message p {
        margin: 0;
        line-height: 1.5;
    }

    .message span {
        font-size: 0.8em;
        color: #aaa;
    }

    .message .patient {
        background-color: #e1f5e1; /* Soft green background for patient messages */
        color: green;
        text-align: left;
    }

    .message .doctor {
        background-color: #e3f2fd; /* Soft blue background for doctor messages */
        color: blue;
        text-align: right;
    }

    textarea {
        width: 100%;
        height: 70px;
        margin-top: 10px;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #ccc;
        font-size: 16px;
        resize: none;
    }

    button {
        padding: 12px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #45a049;
    }

    .clear-chat-button {
        background-color: red;
        color: white;
        border: none;
        padding: 12px 20px;
        margin-top: 30px;
        cursor: pointer;
        border-radius: 8px;
    }

    .clear-chat-button:hover {
        background-color: darkred;
    }
</style>

</head>
<body>
    <!-- Navigation Bar -->
    <?php include('navbar_patient.php'); ?>
    <!-- Chat Container -->
    <div class="chat-container">
        <h2>Chat with Dr. <?php echo htmlspecialchars(isset($doctor['username']) ? $doctor['username'] : ''); ?></h2>
        <div class="messages">
            <?php while ($message = $chat_result->fetch_assoc()): ?>
                <div class="message">
                    <p class="<?php echo ($message['doctor_name'] === $doctor['username']) ? 'doctor' : 'patient'; ?>">
                        <strong><?php echo htmlspecialchars($message['doctor_name'] ?? $message['patient_name']); ?>:</strong> 
                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                    </p>
                    <span><?php echo htmlspecialchars($message['timestamp']); ?></span>
                </div>
            <?php endwhile; ?>
        </div>
        <form method="POST">
            <textarea name="message" placeholder="Type your message..." required></textarea><br>
            <button type="submit">Send Message</button>
        </form>
        <form method="POST">
            <button type="submit" name="clear_chat" class="clear-chat-button" onclick="return confirm('Are you sure you want to clear the chat?')">Clear Chat</button>
        </form>
    </div>
</body>
</html>
