<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Room Booking System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>🗓️ Meeting Room Booking</h1>
        
        <?php
            // Set timezone for accurate date
            date_default_timezone_set('Asia/Kolkata'); 
            $today = date("l, F j, Y");
            // Define today's date in SQL format (YYYY-MM-DD) for filtering
            $today_sql = date('Y-m-d'); 
            echo "<p style='text-align: center; font-size: 1.2em; font-weight: bold; color: #555;'>Today's Date: " . $today . "</p>";
        ?>

        <section class="booking-form-section">
            <h2>Book a Room</h2>
            <form action="book.php" method="POST" class="booking-form">
                
                <div class="form-group">
                    <label for="name">Your Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="room">Select Room(s):</label>
                    <select id="room" name="room" required>
                        <option value="">-- Choose a Room --</option>
                        <option value="A">Atharveda</option>
                        <option value="B">Yajurveda</option>
                        <option value="C">Samveda</option>
                        <option value="D">Rigveda</option>
                        <option value="A & B">Ayharveda & Yajurveda</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="meeting_date">Booking Date:</label>
                    <input type="date" id="meeting_date" name="meeting_date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group time-selection-group">
                    <label>Meeting Duration:</label>
                    <div style="display: flex; gap: 10px;">
                        
                        <div style="flex: 1;">
                            <label for="start_time" style="font-weight: normal;">Start Time:</label>
                            <select id="start_time" name="start_time" required>
                                <option value="">-- Select Start --</option>
                                <?php
                                    $startTime = strtotime('08:00');
                                    $endTime = strtotime('23:00');
                                    $interval = 15 * 60; 

                                    for ($time = $startTime; $time <= $endTime; $time += $interval) {
                                        $slot = date('H:i', $time);
                                        echo "<option value='{$slot}'>" . $slot . "</option>";
                                    }
                                ?>
                            </select>
                        </div>

                        <div style="flex: 1;">
                            <label for="end_time" style="font-weight: normal;">End Time:</label>
                            <select id="end_time" name="end_time" required>
                                <option value="">-- Select End --</option>
                                <?php
                                    $startTime = strtotime('08:00');
                                    $endTime = strtotime('23:00');
                                    $interval = 15 * 60; 

                                    for ($time = $startTime + $interval; $time <= $endTime; $time += $interval) {
                                        $slot = date('H:i', $time);
                                        echo "<option value='{$slot}'>" . $slot . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Confirm Booking</button>
            </form>
        </section>

        <hr>

        <section class="current-bookings-section">
            <h2>Current Bookings</h2>
            
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Room</th>
                        <th>Time Slot</th>
                        <th>Booked By</th>
                    </tr>
                </thead>
                <tbody id="bookings-list">
                    <?php
                    $servername = "localhost";
                    $username = "root"; 
                    $password = "";     
                    $dbname = "MeetingBookings"; 
                    
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    
                    if ($conn->connect_error) {
                        echo "<tr><td colspan='4'>Database connection error.</td></tr>";
                    } else {
                        // SQL QUERY CHANGE: Filter out past dates (meeting_date < $today_sql)
                        $sql = "SELECT meeting_date, room_booked, timeslot, booked_by 
                                FROM bookings 
                                WHERE meeting_date >= '$today_sql'
                                ORDER BY meeting_date ASC, timeslot ASC";
                        
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . date('M j, Y', strtotime(htmlspecialchars($row['meeting_date']))) . "</td>";
                                echo "<td>" . htmlspecialchars($row['room_booked']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['timeslot']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['booked_by']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No upcoming bookings found.</td></tr>";
                        }
                        $conn->close();
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>