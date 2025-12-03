<?php
// 1. Database Connection Details
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "MeetingBookings"; 

// 2. Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// 3. Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 4. Check if the form was submitted (POST request)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and collect data
    $name = $conn->real_escape_string($_POST['name']);
    $room = $conn->real_escape_string($_POST['room']);
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $meeting_date = $conn->real_escape_string($_POST['meeting_date']); // NEW: Capture meeting date

    // Simple validation
    if (empty($name) || empty($room) || empty($start_time) || empty($end_time) || empty($meeting_date)) {
        die("Error: All fields, including the booking date, are required.");
    }

    // 5. Time and Date Validation Check
    if (strtotime($start_time) >= strtotime($end_time)) {
        die("Error: Start time must be before end time. Please go back and correct your selection.");
    }
    
    // Optional: Prevent booking in the past
    if (strtotime($meeting_date) < strtotime(date('Y-m-d'))) {
        die("Error: Cannot book a room for a past date.");
    }

    // 6. Combine the times for the database field
    $timeslot = $start_time . " - " . $end_time;
    
    // 7. Construct the SQL INSERT statement (now includes meeting_date)
    $sql = "INSERT INTO bookings (room_booked, timeslot, booked_by, meeting_date) 
            VALUES ('$room', '$timeslot', '$name', '$meeting_date')";

    // 8. Execute the query and redirect
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?status=success");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    header("Location: index.php");
    exit();
}

$conn->close();
?>

