<?php
session_start();
if (!isset($_SESSION['patient_username']) && !isset($_SESSION['doctor_username'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit;
}

$user_id = isset($_SESSION['patient_username']) ? getPatientId($_SESSION['patient_username']) : getDoctorId($_SESSION['doctor_username']);
$receiver_id = $_GET['doctor_id']; // Get the recipient's ID
$message = $_POST['message']; // Get the message from form

// Database connection
$conn = new mysqli('localhost', 'root', '', 'medicare_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert message into the chat_messages table
$send_message_sql = "INSERT INTO chat_messages (sender_id, receiver_id, message) 
                     VALUES ('$user_id', '$receiver_id', '$message')";
$conn->query($send_message_sql);

$conn->close();
header("Location: chat.php?doctor_id=$receiver_id"); // Redirect back to chat page
