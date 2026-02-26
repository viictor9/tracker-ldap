<?php
require 'connection.php';

$range = $_GET['range'] ?? 'daily';

if ($range === 'weekly') {
    $whereClause = "YEARWEEK(start_time, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($range === 'monthly') {
    $whereClause = "MONTH(start_time) = MONTH(CURDATE()) 
                    AND YEAR(start_time) = YEAR(CURDATE())";
} else {
    $whereClause = "DATE(start_time) = CURDATE()";
}

/* =============================
   TABLE DATA
============================= */

$tableQuery = "
SELECT 
    DATE(start_time) AS report_date,
    ticket_number,
    activity_type,
    SEC_TO_TIME(SUM(duration_seconds)) AS total_duration
FROM time_logs
WHERE $whereClause
GROUP BY report_date, ticket_number, activity_type
ORDER BY report_date DESC
";

$tableResult = mysqli_query($connection, $tableQuery);

/* =============================
   CHART DATA
============================= */

$chartQuery = "
SELECT 
    activity_type,
    ROUND(SUM(duration_seconds)/3600,2) AS total_hours
FROM time_logs
WHERE $whereClause
GROUP BY activity_type
";

$chartResult = mysqli_query($connection, $chartQuery);

$labels = [];
$data = [];

while ($row = mysqli_fetch_assoc($chartResult)) {
    $labels[] = $row['activity_type'];
    $data[] = $row['total_hours'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reports</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    </div>
</nav>

<div class="container">

    <h2>Productivity Reports</h2>

    <!-- FILTER -->
    <div class="report-controls">
        <select onchange="window.location='reports.php?range='+this.value">
            <option value="daily" <?= $range=='daily'?'selected':'' ?>>Daily</option>
            <option value="weekly" <?= $range=='weekly'?'selected':'' ?>>Weekly</option>
            <option value="monthly" <?= $range=='monthly'?'selected':'' ?>>Monthly</option>
        </select>

        <button>Send Report</button>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Ticket</th>
                <th>Type</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
        <?php if(mysqli_num_rows($tableResult) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($tableResult)): ?>
            <tr>
                <td><?= $row['report_date'] ?></td>
                <td><?= $row['ticket_number'] ?></td>
                <td><?= $row['activity_type'] ?></td>
                <td><?= $row['total_duration'] ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align:center;">No data found</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- CHART SECTION -->
    <div class="report-chart-section">

        <div class="chart-header">
            <h3>Productivity Analytics</h3>

            <select id="chartSelector" onchange="updateChart()">
                <option value="pie">Pie Chart</option>
                <option value="doughnut">Doughnut Chart</option>
                <option value="bar">Bar Chart</option>
            </select>
        </div>

        <div class="chart-card">
            <canvas id="productivityChart"></canvas>
        </div>

    </div>

</div>

<script src="assets/app.js"></script>

<script>
let ctx = document.getElementById('productivityChart').getContext('2d');

let productivityData = {
    labels: <?= json_encode($labels) ?>,
    datasets: [{
        label: 'Hours Spent',
        data: <?= json_encode($data) ?>,
        backgroundColor: ['#2563eb', '#10b981']
    }]
};

let currentChart = new Chart(ctx, {
    type: 'pie',
    data: productivityData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

function updateChart() {
    let selectedType = document.getElementById("chartSelector").value;
    currentChart.destroy();

    currentChart = new Chart(ctx, {
        type: selectedType,
        data: productivityData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
</script>

</body>
</html>