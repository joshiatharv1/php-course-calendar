<?php

$servername = "mysql";
$username = "root";
$password = "root";
$database = "testdb";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS APPOINTMENTS (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        COURSE_NAME VARCHAR(255) NOT NULL,
        INSTRUCTOR_NAME VARCHAR(255) NOT NULL,
        START_DATE DATE NOT NULL,
        END_DATE DATE NOT NULL,
        start_time TIME,
        end_time TIME
    )");
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$successMsg = "";
$errorMsg = "";
$eventsFromDB = [];

if($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === 'add'){
    $course = trim($_POST["course_name"] ?? '');
    $instructor = trim($_POST["instructor_name"] ?? '');
    $start = $_POST["start_date"] ?? '';
    $end = $_POST["end_date"] ?? '';
    $startTime = $_POST['start_time'] ?? '';
    $endTime = $_POST["end_time"] ?? '';

    if($course && $instructor && $start && $end){
        $stmt = $pdo->prepare(
            "INSERT INTO APPOINTMENTS (COURSE_NAME, INSTRUCTOR_NAME, START_DATE, END_DATE, start_time, end_time) VALUES (?,?,?,?,?,?)"
        );
        $stmt->execute([$course, $instructor, $start, $end, $startTime, $endTime]);

        header("Location: " . $_SERVER["PHP_SELF"] . "?success=1");
        exit;
    } else {
        header("Location: " . $_SERVER["PHP_SELF"] . "?error=1");
        exit;
    }
}

// Handle Edit
if($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? '') === 'edit'){
    $id = $_POST["event_id"] ?? null;
    $course = trim($_POST["course_name"] ?? '');
    $instructor = trim($_POST["instructor_name"] ?? '');
    $start = $_POST['start_date'] ?? '';
    $end = $_POST['end_date'] ?? '';
    $startTime = $_POST['start_time'] ?? '';
    $endTime = $_POST['end_time'] ?? '';
    
    if($id && $course && $instructor && $start && $end){
        $stmt = $pdo->prepare(
            "UPDATE APPOINTMENTS SET COURSE_NAME=?, INSTRUCTOR_NAME=?, START_DATE=?, END_DATE=?, start_time=?, end_time=? WHERE ID=?"
        );
        $stmt->execute([$course, $instructor, $start, $end, $startTime, $endTime, $id]);

        header("Location: " . $_SERVER["PHP_SELF"] . "?success=2");
        exit;
    } else {
        header("Location: " . $_SERVER["PHP_SELF"] . "?error=2");
        exit;
    }
}

// Handle Delete
if($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? '') === 'delete'){
    $id = $_POST['event_id'] ?? null;
    if($id){
        $stmt = $pdo->prepare("DELETE FROM APPOINTMENTS WHERE ID=?");
        $stmt->execute([$id]);
        header("Location: " . $_SERVER["PHP_SELF"] . "?success=3");
        exit;
    }
}

// Handle success/error messages
if(isset($_GET["success"])){
    $successMsg = match($_GET['success']){
        '1' => "Appointment Added Successfully!",
        '2' => "Appointment Updated Successfully!",
        '3' => "Appointment Deleted Successfully!",
        default => "Operation completed successfully!"
    };
}

if(isset($_GET["error"])){
    $errorMsg = "! Error Occurred";
}

// Fetch All Appointments - FIXED LOGIC
$stmt = $pdo->query("SELECT * FROM APPOINTMENTS ORDER BY START_DATE, start_time");
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($appointments){
    foreach($appointments as $row){
        $startDate = new DateTime($row["START_DATE"]);
        $endDate = new DateTime($row["END_DATE"]);
        
        // Create event for each day in the range
        $currentDate = clone $startDate;
        while($currentDate <= $endDate){
            $eventsFromDB[] = [
                'id' => $row["ID"],
                'title' => $row['COURSE_NAME'],
                'instructor' => $row['INSTRUCTOR_NAME'],
                'date' => $currentDate->format("Y-m-d"),
                'start_date' => $row["START_DATE"],
                'end_date' => $row["END_DATE"],
                'start_time' => $row["start_time"],
                'end_time' => $row["end_time"],
                'is_start' => $currentDate->format("Y-m-d") === $row["START_DATE"],
                'is_end' => $currentDate->format("Y-m-d") === $row["END_DATE"],
                'is_single_day' => $row["START_DATE"] === $row["END_DATE"]
            ];
            
            $currentDate->modify('+1 day');
        }
    }
}

$pdo = null;

?>