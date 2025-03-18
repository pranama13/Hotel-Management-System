<?php
    session_start();
    include '../config.php';

    // roombook
    $roombooksql = "Select * from roombook";
    $roombookre = mysqli_query($conn, $roombooksql);
    $roombookrow = mysqli_num_rows($roombookre);

    // staff
    $staffsql = "Select * from staff";
    $staffre = mysqli_query($conn, $staffsql);
    $staffrow = mysqli_num_rows($staffre);

    // room
    $roomsql = "Select * from room";
    $roomre = mysqli_query($conn, $roomsql);
    $roomrow = mysqli_num_rows($roomre);

    // roombook roomtype
    $chartroom1 = "SELECT * FROM roombook WHERE RoomType='Superior Room'";
    $chartroom1re = mysqli_query($conn, $chartroom1);
    $chartroom1row = mysqli_num_rows($chartroom1re);

    $chartroom2 = "SELECT * FROM roombook WHERE RoomType='Deluxe Room'";
    $chartroom2re = mysqli_query($conn, $chartroom2);
    $chartroom2row = mysqli_num_rows($chartroom2re);

    $chartroom3 = "SELECT * FROM roombook WHERE RoomType='Guest House'";
    $chartroom3re = mysqli_query($conn, $chartroom3);
    $chartroom3row = mysqli_num_rows($chartroom3re);

    $chartroom4 = "SELECT * FROM roombook WHERE RoomType='Single Room'";
    $chartroom4re = mysqli_query($conn, $chartroom4);
    $chartroom4row = mysqli_num_rows($chartroom4re);

    // Default total profit for the databox (static value)
    $tot = 125000.50; // Example default profit value
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holiday Inn - Admin Dashboard</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Morris.js -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Roboto', sans-serif;
    }

    body {
        background: #eef2f7;
        color: #2d3748;
        display: flex;
        min-height: 100vh;
        line-height: 1.6;
    }


    /* Main Content */
    .main-content {
        margin-left: 120px;
        padding: 40px;
        width: calc(100% - 250px);
        background: #f7fafc;
        min-height: 100vh;
    }

    .databox {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-bottom: 50px;
    }

    .box {
        background: #ffffff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #edf2f7;
    }

    .box:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .box h2 {
        font-size: 1.1rem;
        font-weight: 500;
        color: #718096;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .box h1 {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1a202c;
    }

    .box.profitbox h1 span {
        font-size: 1.6rem;
        color: #38a169;
        font-weight: 600;
    }

    .chartbox {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    .bookroomchart, .profitchart {
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #edf2f7;
        transition: all 0.3s ease;
    }

    .bookroomchart:hover, .profitchart:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .bookroomchart h3, .profitchart h3 {
        font-size: 1.4rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 20px;
        letter-spacing: 0.5px;
    }

    #bookroomchart {
        max-height: 400px;
    }

    #profitchart {
        height: 400px;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .databox {
            grid-template-columns: repeat(2, 1fr);
        }
        .chartbox {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
            padding: 20px;
        }
        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 20px;
        }
        .databox {
            grid-template-columns: 1fr;
        }
        .chartbox {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body>
   
    <!-- Main Content -->
    <div class="main-content">
        <div class="databox">
            <div class="box roombookbox">
                <h2>Total Booked Rooms</h2>  
                <h1><?php echo $roombookrow ?> / <?php echo $roomrow ?></h1>
            </div>
            <div class="box guestbox">
                <h2>Total Staff</h2>  
                <h1><?php echo $staffrow ?></h1>
            </div>
            <div class="box profitbox">
                <h2>Profit</h2>  
                <h1><span>$.</span><?php echo number_format($tot, 2); ?></h1>
            </div>
        </div>
        <div class="chartbox">
            <div class="bookroomchart">
                <h3>Booked Room Distribution</h3>
                <canvas id="bookroomchart"></canvas>
            </div>
            <div class="profitchart">
                <h3>Profit Over Time</h3>
                <div id="profitchart"></div>
            </div>
        </div>
    </div>

    <!-- Chart.js Doughnut Chart (unchanged) -->
    <script>
        const labels = [
            'Superior Room',
            'Deluxe Room',
            'Guest House',
            'Single Room',
        ];

        const data = {
            labels: labels,
            datasets: [{
                label: 'Booked Rooms',
                backgroundColor: [
                    '#2ecc71', // Green
                    '#3498db', // Blue
                    '#e74c3c', // Red
                    '#f1c40f', // Yellow
                ],
                borderColor: '#fff',
                borderWidth: 2,
                data: [<?php echo $chartroom1row ?>, <?php echo $chartroom2row ?>, <?php echo $chartroom3row ?>, <?php echo $chartroom4row ?>],
            }]
        };

        const doughnutChart = {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 14 },
                            color: '#333'
                        }
                    },
                    title: { display: false }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById('bookroomchart'),
            doughnutChart
        );
    </script>

    <!-- Morris.js Bar Chart with Default Data -->
    <script>
        Morris.Bar({
            element: 'profitchart',
            data: [
                { date: '2025-01-01', profit: 15000 },
                { date: '2025-01-02', profit: 20000 },
                { date: '2025-01-03', profit: 18000 },
                { date: '2025-01-04', profit: 22000 },
                { date: '2025-01-05', profit: 25000 },
                { date: '2025-01-06', profit: 30000 },
                { date: '2025-01-07', profit: 28000 }
            ],
            xkey: 'date',
            ykeys: ['profit'],
            labels: ['Profit (Rs.)'],
            hideHover: 'auto',
            stacked: true,
            barColors: ['#27ae60'],
            resize: true,
            gridTextColor: '#333',
            gridTextSize: 12,
            axes: true,
            grid: true,
            barSizeRatio: 0.4,
            xLabelAngle: 45
        });
    </script>
</body>
</html>