<?php 
include "calender.php";  // Fixed spelling: calender -> calendar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- To Scale about different Mobile and Devices then add the viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Project</title>
    <meta name="description" content="My Own Calendar Project">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Course Calendar</h1>
        <br>
        <h1>My Calendar Project</h1>
    </header>
    
    <!-- Clock Area -->
    <div class="clock-container">
        <div id="clock"></div>
    </div>

    <div class="calendar">
        <div class="nav-btn-container">
            <button class="nav-btn">&lt;</button>
            <h2 id="monthYear" style="margin:0"></h2>
            <button class="nav-btn">&gt;</button>
        </div>
        <div class="calendar-grid" id="calendar"></div>
    </div>

    <!-- Model for ADD/Edit and Delete -->
    <div class="eventSelectorWrapper">
        <label for="eventSelector">
            <strong>Select Event: </strong>
        </label>
        <select name="eventSelector" id="eventSelector">
            <option disabled selected>Choose Event</option>
        </select>
    </div>

    <!-- Main Form -->
    <div class="modal" id="eventModal">
        <div class="modal-content">
            <form method="post" id="eventForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="event_id" id="eventId">
                
                <label for="courseName">Course Title:</label>
                <input type="text" name="course_name" id="courseName" required>
                
                <label for="instructorName">Instructor Name:</label>
                <input type="text" name="instructor_name" id="instructorName" required>
                
                <label for="startDate">Start Date:</label>
                <input type="date" name="start_date" id="startDate" required>
                
                <label for="endDate">End Date:</label>
                <input type="date" name="end_date" id="endDate" required>
                
                <label for="startTime">Start Time:</label>
                <input type="time" name="start_time" id="startTime" required>
                
                <label for="endTime">End Time:</label>
                <input type="time" name="end_time" id="endTime" required>
                
                <button type="submit">Save</button>
            </form>
            
            <!-- Delete Form -->
            <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this Event or Task?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="event_id" id="deleteEventId">
                <button type="submit" class="submit-btn">Delete</button>
            </form>

            <!-- Cancel Section -->
            <button type="button" class="submit-btn" onclick="closeModal()">Cancel</button>
        </div>
    </div>

    <!-- Display Success/Error Messages -->
    <?php if($successMsg): ?>
        <div class="success-message"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>
    
    <?php if($errorMsg): ?>
        <div class="error-message"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <script>
        const events = <?= json_encode($eventsFromDB, JSON_UNESCAPED_UNICODE) ?>;
        
        function closeModal() {
            document.getElementById('eventModal').style.display = 'none';
        }
    </script>
    <script src="calender.js"></script>  <!-- Fixed spelling: calender -> calendar -->
</body>
</html>