<?php
require 'connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $ticket = $_POST['ticket_number'];
    $activity = $_POST['activity_type'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Calculate duration in seconds
    $start = strtotime($start_time);
    $end = strtotime($end_time);
    $duration = $end - $start;

    if ($duration > 0) {
        $stmt = $connection->prepare("
            INSERT INTO time_logs 
            (user_id, ticket_number, activity_type, start_time, end_time, duration_seconds)
            VALUES (1, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssssi", $ticket, $activity, $start_time, $end_time, $duration);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="light">

<nav class="navbar">
    <div class="nav-left">
        <h3>Productivity</h3>
    </div>

    <div class="nav-right">
        <a href="dashboard.php">Dashboard</a>
        <a href="reports.php">Reports</a>
        <button onclick="toggleTheme()" class="theme-toggle">☾</button>
        <h5><?php echo $_SESSION['user']; ?></h5>    </div>
</nav>

<div class="container">
    <h2>Clock Activity</h2>

    <div class="clock-box">

        <form method="POST" id="timeForm">

            <select name="activity_type" required>
                <option value="Ticket Update">Ticket Update</option>
                <option value="Meeting">Meeting</option>
                <option value="Meeting">Reasearch</option>
                <option value="Meeting">Testing</option>
                <option value="Meeting">Ticket checking</option>
                <option value="Meeting">Training</option>
                <option value="Meeting">Upgrade</option>
                <option value="Meeting">Internal call</option>
                <option value="Meeting">Handover</option>
                <option value="Meeting">Activity</option>
                <option value="Meeting">Meeting</option>
                <option value="Meeting">Meeting</option>
                <option value="Meeting">Meeting</option>

            <input type="text" name="ticket_number" placeholder="Details" required>
                

            </select>

            <div class="timer" id="timer">00:00:00</div>

            <input type="hidden" name="start_time" id="start_time">
            <input type="hidden" name="end_time" id="end_time">

            <div class="buttons">
                <button type="button" onclick="startTimer()">Clock In</button>
                <button type="button" onclick="stopTimer()">Clock Out</button>
            </div>

        </form>

    </div>
</div>

<script>
let timer;
let seconds = 0;
let startTimestamp;

function startTimer() {
    if (!timer) {
        startTimestamp = new Date();
        document.getElementById("start_time").value = startTimestamp.toISOString().slice(0, 19).replace("T", " ");

        timer = setInterval(() => {
            seconds++;
            document.getElementById("timer").innerText = formatTime(seconds);
        }, 1000);
    }
}

function stopTimer() {
    if (timer) {
        clearInterval(timer);
        timer = null;

        let endTimestamp = new Date();
        document.getElementById("end_time").value = endTimestamp.toISOString().slice(0, 19).replace("T", " ");

        document.getElementById("timeForm").submit();
    }
}

function formatTime(sec) {
    let hrs = Math.floor(sec / 3600);
    let mins = Math.floor((sec % 3600) / 60);
    let secs = sec % 60;

    return (
        String(hrs).padStart(2, '0') + ":" +
        String(mins).padStart(2, '0') + ":" +
        String(secs).padStart(2, '0')
    );
}
</script>

<script src="assets/app.js"></script>

</body>
</html>