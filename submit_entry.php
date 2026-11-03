<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $competition_type = mysqli_real_escape_string($conn, $_POST['competition_type'] ?? 'story');
    $entry_title      = mysqli_real_escape_string($conn, trim($_POST['entry_title'] ?? ''));
    $entry_content    = mysqli_real_escape_string($conn, trim($_POST['entry_content'] ?? ''));

    if (empty($entry_title) || empty($entry_content)) {
        $error = "All fields are required.";
    } else {
        $sql = "INSERT INTO entries (user_id, competition_type, title, content, submitted_at)
                VALUES ($user_id, '$competition_type', '$entry_title', '$entry_content', NOW())";
        if (mysqli_query($conn, $sql)) {
            $success = "Your entry has been submitted successfully!";
        } else {
            $error = "Submission failed. Please try again.";
        }
    }
}

// Redirect back to competitions with result in session
if ($success) {
    $_SESSION['message'] = $success;
    header("Location: competitions.php");
} else {
    $_SESSION['message_error'] = $error;
    header("Location: competitions.php");
}
exit();
