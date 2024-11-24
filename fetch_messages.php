<?php
session_start();
if (!isset($_SESSION['patient_username']) && !isset($_SESSION['doctor_username'])) {
    exit; // If not logged in, stop
}

$user_id = isset($_SESSION['patient_username']) ? getPatientId($_SESSION['patient_username']) : getDoctorId($_SESSION['doctor_username']);
$doctor_id = $_GET['doctor_id'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'medicare_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch new messages for the chat
$chat_sql = "SELECT * FROM chat_messages 
             WHERE (sender_id = '$user_id' AND receiver_id = '$doctor_id') 
             OR (sender_id = '$doctor_id' AND receiver_id = '$user_id') 
             ORDER BY sent_at ASC";
$chat_result = $conn->query($chat_sql);

while ($message = $chat_result->fetch_assoc()) {
    echo "<div class='message'><p><strong>" . htmlspecialchars($message['sender_id']) . ":</strong> " . nl2br(htmlspecialchars($message['message'])) . "</p><span>" . htmlspecialchars($message['sent_at']) . "</span></div>";
}

$conn->close();
